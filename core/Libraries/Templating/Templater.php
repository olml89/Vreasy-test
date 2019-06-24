<?php declare(strict_types = 1);
namespace System\Libraries\Templating;


use System\Libraries\ErrorHandling\Exceptions\InvalidFileException;
use System\Libraries\Templating\Renderer;


final class Templater implements TemplaterInterface {


	private const TEMPLATEPATH 	= '/assets/templates';

	private $renderer = NULL; // \System\Libraries\Templating\Renderer;


	public function __construct(Renderer $renderer) {
		$this->renderer = $renderer;
	}


	public function template(string $template, array $data = []) : string {

		$file = ROOTPATH.self::TEMPLATEPATH.'/'.$template.'.php';

		if(!is_readable($file)) {
			throw InvalidFileException::unReadable($file);
		}

		if(!is_array($views = include $file)) {
			return '';
		}

		$rendered = '';

		foreach($views as $view=>$expectedData) {

			$viewData = [];

			foreach($expectedData as $requestedVar) {

				if(!array_key_exists($requestedVar, $data)) {
					continue;
				}

				$viewData[$requestedVar] = $data[$requestedVar];

			}

			$rendered .= $this->renderer->render($view, $viewData);

		}

		return $rendered;

	}


}