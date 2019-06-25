<?php declare(strict_types = 1);
namespace Application\Controllers\Web;


use System\Controllers\AbstractBaseController;
use System\Libraries\Configuration\Configuration;
use System\Libraries\Templating\TemplaterInterface;
use System\Libraries\Validation\RequestValidator;


abstract class AbstractWebController extends AbstractBaseController {

	use WebPageTrait;


	protected $config 	 = NULL; // \System\Libraries\Configuration\Configuration
	protected $templater = NULL; // \System\Libraries\Templating\TemplaterInterface


	public function __construct(Configuration $config, TemplaterInterface $templater, ?RequestValidator $requestValidator = NULL) {

		if(!empty($requestValidator)) {
			$requestValidator->setRequestContentType(['application/x-www-form-urlencoded' => 'utf-8']);
			$requestValidator->setResponseAcceptTypes(['text/html' => 'utf-8']);	
		}

		parent::__construct($requestValidator);

		$this->config 	 = $config;
		$this->templater = $templater;
		$this->setWebPageComponents($this->config->get('sidebar'));

	}


}

