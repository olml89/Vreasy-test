<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling;


use Psr\Log\LoggerInterface;

use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\JsonResponse as SymfonyJsonResponse;

use System\Application;
use System\Libraries\ErrorHandling\Whoops\WhoopsWrapper;
use System\Controllers\BaseControllerInterface;

use System\Libraries\ErrorHandling\Exceptions\Http\HttpExceptionInterface;
use System\Libraries\ErrorHandling\Exceptions\Http\HttpExceptionFactory;


final class ErrorHandler {


	private $whoops  	= NULL; // \Whoops\Run
	private $log 	 	= NULL; // \Psr\Log\LoggerInterface (\Monolog\logger)
	private $app 		= NULL; // \System\Application

	private $debug 		= TRUE;
	private $isAjax  	= FALSE;


	public function __construct(WhoopsWrapper $whoops, LoggerInterface $log) {
		$this->whoops 	= $whoops;
		$this->log 		= $log;
	}


	public function bootstrap(Application $app) : ErrorHandler {

		//save the app instance
		$this->app = $app;

		//set the environment
		$this->debug 	= !$app->isEnvironment(Application::PRODUCTION);
		$this->isAjax 	= $app->getRequest()->isXmlHttpRequest();

		$this->whoops->bootstrap(
			$app->isRunningInConsole(),
			$this->isAjax,
			$this->debug
		);

		//return self to chain calls
		return $this;

	}


	public function registerHandlers() : ErrorHandler {

		//set error reporting
        ini_set('display_errors', 'On');
		ini_set('display_startup_errors', 'On');
		error_reporting(E_ALL);

		//register handlers
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);

        /*
        if(!$app->environment('testing')) {
            ini_set('display_errors', 'Off');
        }
        */

        return $this;

	}


	public function restoreHandlers() : void {
		restore_error_handler();
		restore_exception_handler();
	}


	private function getUserContext() : array {

		//[...] provide logged user context. Not needed in this app
		$context = [];

		return array_filter($context);

	}


    private function isFatal(int $type) : bool {
		return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
    }


    private function getFatalExceptionFromError(array $error, int $traceOffset = NULL) {
        return new FatalErrorException(
            $error['message'], $error['type'], 0, $error['file'], $error['line'], $traceOffset
        );
    }


	public function handleShutdown() : void {
		if(!is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
			$this->handleException($this->getFatalExceptionFromError($error, 0));
		}
	}


    public function handleError(int $level, string $message, string $file = '', int $line = 0, array $context = []) : void {
        if(error_reporting() & $level) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }


	public function handleException(\Throwable $exception) : void {

		//meaningful exceptions. mysqli error 1062 = duplicated entry => HTTP 409 Conflict
		if($exception instanceof \mysqli_sql_exception && $exception->getCode() === 1062) {
			$exception = HttpExceptionFactory::conflict($exception->getMessage());
		}

		//always log the exception
		$this->log->critical($exception->getMessage(), array_merge(
			$this->getUserContext(),
			['exception' => $exception]
		)); 

		//in production, convert all exceptions into a HTTP exception to display it in a friendly way
		if(!$this->debug) {

			//mysqli generic database exception: HTTP 500 specifying that it is a database issue
			if($exception instanceof \mysqli_sql_exception) {
				$exception = HttpExceptionFactory::failure('Database error');
			}

			//program generic exception: HTTP 500 internal server error
			if(!$exception instanceof HttpExceptionInterface) {
				//$exception = HttpExceptionFactory::internalServerError($exception);
				$exception = HttpExceptionFactory::internalServerError();
			}

		}

		//get the processed string of the exception to make the content of the response: we use whoops for this
		$content = $this->whoops->handleException($exception); 

		//get the status code to output: always a 500 Internal Server Error, unless exception is an HttpException and has his
		//own status code
		$status = $exception instanceof HttpExceptionInterface? $exception->getCode() : 500;

		//make the response
		$response = $this->isAjax? SymfonyJsonResponse::fromJsonString($content, $status) : SymfonyResponse::create($content, $status);

		//send the response and terminate the application gracefully with an error code 1
		$response->prepare($this->app->getRequest())->send();
		exit(Application::EXIT_ERROR);

	}


}