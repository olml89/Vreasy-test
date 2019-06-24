<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Whoops;


use Whoops\Run;
use Whoops\Handler\HandlerInterface;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;

use System\Libraries\Templating\Templater;
use System\Libraries\Configuration\Configuration;
use System\Libraries\ErrorHandling\Whoops\Handler\PrettyPageProductionHandler;
use System\Libraries\ErrorHandling\Whoops\Handler\JsonResponseProductionHandler;


final class WhoopsWrapper {


	private $whoops 	= NULL; // \Whoops\Run
	private $templater 	= NULL; // \System\Libraries\Templating\Templater
	private $config 	= NULL; // \System\Libraries\Configuration\Configuration


	public function __construct(Run $whoops, Templater $templater, Configuration $config) {

		$whoops->allowQuit(FALSE);
		$whoops->writeToOutput(FALSE);
		$this->whoops = $whoops;

		$this->templater = $templater;
		$this->config 	 = $config;

	}


	public function bootstrap(bool $cli, bool $ajax, bool $debug) : WhoopsWrapper {
		$this->whoops->pushHandler($this->getHandler($cli, $ajax, $debug));
		return $this;
	}


	private function getHandler(bool $cli, bool $ajax, bool $debug) : HandlerInterface {
		return $cli ? new PlainTextHandler : ($ajax? $this->getJsonHandler($debug) : $this->getHtmlHandler($debug));
	}


	private function getConsoleHandler() : HandlerInterface {
		return new PlainTextHandler;
	}


	private function getJsonHandler(bool $debug) : HandlerInterface {
		$handler = $debug? new JsonResponseHandler : new JsonResponseProductionHandler;
		$handler->setJsonApi(TRUE);
		return $handler;
	}


	private function getHtmlHandler(bool $debug) : HandlerInterface {
		$handler = $debug? new PrettyPageHandler : new PrettyPageProductionHandler($this->templater, $this->config); 
		$handler->handleUnconditionally(TRUE);
		return $handler;
	}


	public function handleException(\Throwable $exception) : string {
		return $this->whoops->handleException($exception);
	}


}