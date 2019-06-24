<?php declare(strict_types = 1);

//global constants. The root path of the application and the default environment (can be overriden if specified in a .env file)
define('ROOTPATH', dirname(__FILE__, 2));
define('ENVIRONMENT', 'development');  

//load Composer dependencies
require_once ROOTPATH.'/vendor/autoload.php';

//build container for dependencies injection
$builder = new \DI\ContainerBuilder();

$builder->addDefinitions([

    //environment
    \Dotenv\Dotenv::class => \DI\factory(function(\Psr\Container\ContainerInterface $c) {
        $dotEnv = \Dotenv\Dotenv::create(ROOTPATH);
        $dotEnv->load();
        return $dotEnv;
    }),

    //enable logging
	\Psr\Log\LoggerInterface::class => \DI\factory(function(\Psr\Container\ContainerInterface $c) {

		$logPath 	= ROOTPATH.'/data/log.txt';
		$dateFormat = 'd/m/Y H:i:s';
		$output 	= '[%datetime%] %channel%.%level_name%: %message% %context% %extra%'.PHP_EOL;

		$monolog 	= new \Monolog\Logger('debug');
		$stream 	= new \Monolog\Handler\StreamHandler($logPath, \Monolog\Logger::DEBUG);
		$formatter 	= new \Monolog\Formatter\LineFormatter($output, $dateFormat);

		$stream->setFormatter($formatter);
		$monolog->pushHandler($stream);
		return $monolog;

    }),

    //enable error handling
    'errorHandler' => \DI\factory(function(\Psr\Container\ContainerInterface $c) { 

        $whoops = new \System\Libraries\ErrorHandling\Whoops\WhoopsWrapper(
            new \Whoops\Run,
            $c->get(\System\Libraries\Templating\Templater::class),
            $c->get(\System\Libraries\Configuration\Configuration::class)
        );

        return new \System\Libraries\ErrorHandling\ErrorHandler(
            $whoops, 
            $c->get(\Psr\Log\LoggerInterface::class)
        );

    }),

    //load configuration
	\System\Libraries\Configuration\Configuration::class => \DI\factory(function(\Psr\Container\ContainerInterface $c) {

		$encoderSettings = [
			'array.inline' 			=> TRUE,
			'array.indent' 			=> 4,
			'boolean.capitalize' 	=> TRUE,
			'null.capitalize' 		=> TRUE
		];

		$phpEncoder = new \Riimu\Kit\PHPEncoder\PHPEncoder($encoderSettings);
        return new \System\Libraries\Configuration\Configuration($phpEncoder);

    }),

    //instantiate database access class
    \System\Libraries\Database\DatabaseInterface::class => \DI\factory(function(\Psr\Container\ContainerInterface $c) {

        //DB connection-related parameters
        //$dotEnv = Dotenv\Dotenv::create(ROOTPATH);
        //$dotEnv->load();
        //DB connection-related parameters: loaded from the environment
        $dbConfig = [
            'host'      => getenv('DB_HOST') ?? '',
            'username'  => getenv('DB_USER') ?? '',
            'password'  => getenv('DB_PASSWORD') ?? '',
            'database'  => getenv('DB_NAME') ?? '',
            'charset'   => getenv('DB_CHARSET') ?? ''
        ];

        //DB additional parameters: file to bootstrap the database (if not exists)
    	$dbFile = $c->get(\System\Libraries\Configuration\Configuration::class)->get('database.file');

    	return new \System\Libraries\Database\Mysqli(
            $dbConfig, 
            $dbFile
        );

    }),

    //instantiate routing
    'router' => \DI\factory(function(\Psr\Container\ContainerInterface $c) {

    	$config = $c->get(\System\Libraries\Configuration\Configuration::class);

    	return new \System\Libraries\Routing\CustomAltoRouter(
            new \System\Libraries\Routing\Route\RouteFactory,
            base_path(), 
            $config->get('routes')
        );

    }),

    //instantiate HTTP client to communicate with the Sunrise-Sunset API
    \GuzzleHttp\Client::class => \DI\factory(function(\Psr\Container\ContainerInterface $c) {
        return new \GuzzleHttp\Client([
            'user_agent'        => 'vreasy test',
            'http_errors'       => FALSE, 
            'allow_redirects'   => ['track_redirects' => TRUE]
        ]);
    }),

    //instantiate pagination
    \System\Libraries\Pagination\Pagination::class => \DI\factory(function(\Psr\Container\ContainerInterface $c) { 

        $config = $c->get(\System\Libraries\Configuration\Configuration::class);

        return new \System\Libraries\Pagination\Pagination(
            $config->get('pagination.limit'),
            $config->get('pagination.config')
        );

    }),

    //instantiate templating to show HTML pages
    \System\Libraries\Templating\TemplaterInterface::class => \DI\factory(function(\Psr\Container\ContainerInterface $c) { 
        $renderer = new \System\Libraries\Templating\Renderer;
        return new \System\Libraries\Templating\Templater($renderer);
    })

]);

$container = $builder->build();

//load the environment
$container->get(\Dotenv\Dotenv::class);

//launch: process the request, send the response
$application = new \System\Application($container);
$application->run(\Symfony\Component\HttpFoundation\Request::createFromGlobals())->send();

//stop (unregister handlers and send exit signal)
$application->stop();



