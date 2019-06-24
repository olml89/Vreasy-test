<?php declare(strict_types = 1);
namespace Application\Controllers\Api;


use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\JsonResponse as SymfonyJsonResponse;

use System\Libraries\Configuration\Configuration;


final class EntryPoint extends AbstractRestController {


	public function __construct(Configuration $config) {
		parent::__construct();
		$this->config = $config;
	}


	// GET /api
	public function index(SymfonyRequest $request) : SymfonyJsonResponse {
		$version = $this->config->get('application.version');
		return new SymfonyJsonResponse(['version' => $version], 200);
	}


}

