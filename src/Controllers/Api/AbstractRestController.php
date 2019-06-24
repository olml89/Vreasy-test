<?php declare(strict_types = 1);
namespace Application\Controllers\Api;


use System\Controllers\AbstractBaseController;
use System\Libraries\Validation\RequestValidator;


abstract class AbstractRestController extends AbstractBaseController {


	public function __construct(?RequestValidator $requestValidator = NULL) {

		if(!empty($requestValidator)) {
			$requestValidator->setContentType(['application/json' => 'utf-8']);
			$requestValidator->setAccept(['application/json' => 'utf-8']);
		}

		parent::__construct($requestValidator);
		
	}


}

