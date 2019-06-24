<?php declare(strict_types = 1);
namespace System\Libraries\Templating;


use System\Libraries\ErrorHandling\Exceptions\InvalidFileException;


final class Renderer {


	const VIEWPATH = '/assets/views';


	public function render(string $view, array $data = []) : string {

		$file = ROOTPATH.self::VIEWPATH.'/'.$view.'.php';

		if(!is_readable($file)) {
			throw InvalidFileException::unReadable($file);
		}

		if((is_array($data) || is_object($data)) && !empty($data)) {

			foreach($data as $key=>$value) {
				$$key = $value;
			}

		}

		ob_start();
		include $file;
		$rendered = ob_get_contents();
		ob_end_clean();

		return $rendered;

	}


}