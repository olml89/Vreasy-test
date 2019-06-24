<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Whoops\Handler;


use Whoops\Handler\Handler;
use Whoops\Handler\PrettyPageHandler;

use System\Libraries\Templating\Templater;
use System\Libraries\Configuration\Configuration;
use Application\Controllers\Web\WebPageTrait;


final class PrettyPageProductionHandler extends PrettyPageHandler {

	use WebPageTrait;


	private $templater 	= NULL; // \System\Libraries\Templating\Templater
	private $config  	= NULL; // \System\Libraries\Configuration\Configuration


	public function __construct(Templater $templater, Configuration $config) {
		$this->templater = $templater;
		$this->config  	 = $config;
		$this->setWebPageComponents($this->config->get('sidebar'));
	}


	public function handle() : int {

		$data = [
			'base_url' 		 => base_url(),
			'sidebar'		 => $this->getSidebar(),
			'exception' 	 => $this->getException()
		];

		echo $this->templater->template($this->config->get('application.errors.template'), $data); // /assets/templates/error.php
        return Handler::QUIT;

	}


}