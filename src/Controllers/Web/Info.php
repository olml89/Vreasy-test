<?php declare(strict_types = 1);
namespace Application\Controllers\Web;


use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;


use System\Libraries\Configuration\Configuration;
use System\Libraries\Templating\TemplaterInterface;


final class Info extends AbstractWebController {


	public function __construct(Configuration $config, TemplaterInterface $templater) {
		parent::__construct($config, $templater);
	}


	// GET /
	public function index(SymfonyRequest $request) : SymfonyResponse {

		//base url and sidebar
		$base_url = base_url();
		$sidebar = $this->getSidebar('application');

		//display views
		$data = [
			'base_url'	=> $base_url,
			'sidebar'	=> $sidebar,
		];

		$html = $this->templater->template('index', $data);
		return SymfonyResponse::create($html);

	}


	// GET /rest-api
	public function printApiInfo(SymfonyRequest $request) : SymfonyResponse {

		//base url and sidebar
		$base_url = base_url();
		$sidebar = $this->getSidebar('rest-api');

		//display views
		$data = [
			'base_url'	=> $base_url,
			'sidebar'	=> $sidebar,
		];

		$html = $this->templater->template('api-info', $data);
		return SymfonyResponse::create($html);

	}


}