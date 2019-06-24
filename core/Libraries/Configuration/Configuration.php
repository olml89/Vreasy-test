<?php declare(strict_types = 1);
namespace System\Libraries\Configuration;


use Riimu\Kit\PHPEncoder\PHPEncoder;

use System\Libraries\ErrorHandling\Exceptions\InvalidFileException;


final class Configuration {


	private const FILES_DIR = ROOTPATH.'/config';

	private $encoder 	= NULL; // \Riimu\Kit\PHPEncoder\PHPEncoder
	private $config 	= [];
	

	public function __construct(PHPEncoder $encoder) {
		$this->encoder = $encoder;
	}


	public static function getDefaultEncoderSettings() : array {
		return [
			'array.inline' 			=> TRUE,
			'array.indent' 			=> 4,
			'boolean.capitalize' 	=> TRUE,
			'null.capitalize' 		=> TRUE
		];
	}


	private static function header() : string {
		return "<?php declare(strict_types = 1);\n\n\n";
	}


	private function build($config, array $selectors, $item) {

		//no hay selectores:
		if(empty($selectors)) {
			return $item;
		}

		//obtenemos el primer selector (y reducimos la lista de selectores)
		$selector = array_shift($selectors);

		//el selector no existe o no es un array: se crea un subíndice con el valor pedido (se crea array) y se retorna el array
		if(!isset($config[$selector]) || !is_array($config[$selector])) {
			$config[$selector] = $this->build([], $selectors, $item);
			return $config;
		}

		//llamada recursiva sobre el subíndice
		$config[$selector] = $this->build($config[$selector], $selectors, $item);
		return $config;

	}


	private static function getBasePath(string $baseDir='') : string {
		$path = self::FILES_DIR;
		return empty($baseDir)? $path.'/' : $path.$baseDir.'/';
	}


	public function set(string $level, $item) : bool {

		//obtiene los selectores (si no hay, sólo se obtiene el nombre de archivo config)
		$selectors = explode('.', $level);
		$fileName = array_shift($selectors);

		//reconstruye el array de configuración
		$config = $this->build(self::get($fileName), $selectors, $item);

		//retorna el resultado de escribir el nuevo array en disco
		$written = file_put_contents(self::getBasePath().$fileName.'.php', self::header().'return '.$this->encoder->encode($config).';');
		return $written !== FALSE;

	}


	public function get(string $level, bool $object = FALSE) {

		//obtiene los selectores (si no hay, sólo se obtiene el nombre de archivo config)
		$selectors = explode('.', $level);
		$fileName = array_shift($selectors);
		$dirName = '';
		
		//comprobar si hay selectores que representen subdirectorios de /config
		while(is_dir(self::getBasePath($dirName).'/'.$fileName)) {
			$dirName .= $fileName.'/';
			$fileName = array_shift($selectors);	//captura el siguiente ítem como nombre de archivo. ej: /config/routes/web.php
		}

		//obtiene el ámbito de config: /config/routes/api.php = routes.api, /config/api.php -> version = api
		$configScope = str_replace('/', '.', $dirName).$fileName;

		//si la configuración no está cargada, carga el archivo solicitado
		if(!isset($this->config[$configScope])) {

			//intenta cargarla desde el fichero
			$filePath = self::getBasePath($dirName).$fileName.'.php';

			//si no existe en el directorio /config general
			if(!is_readable($filePath)) {

				//lo busca dentro del específico del environment...
				if(defined('ENVIRONMENT')) {

					$filePath = self::getBasePath().ENVIRONMENT.'/'.$fileName.'.php';

					if(!is_readable($filePath)) {
						throw InvalidFileException::unReadable($filePath);
					}

				}

			}

			//example of level: system_config, bots, routes...
			$levelConfig = require $filePath; //contents

			//ahora TODOS los configs en disco son arrays
			if(!is_array($levelConfig)) {
				throw InvalidFileException::invalidConfigArray($filePath);
			}

			//load the configuration into the Config array
			$this->config[$configScope] = $levelConfig;

		}

		//devuelve sólo los ítems especificados por los selectores, en lugar del array entero
		$item = $this->config[$configScope];

		foreach($selectors as $selector) {

			if(!in_array($selector, array_keys($item))) {
				return NULL;
			}

			$item = $item[$selector];

		}

		//si el flag de object es true, hacemos casting a objeto
		if($object) {
			$item = (object)$item;
		}

		//return self::$config[$level];
		return $item;

	}


	public function search(string $word, string $level) : string {

		$arrayKeys = $this->get($level);
		$foundKey = '';

		foreach($arrayKeys as $key=>$values) {

			$found = array_search($word, $values);

			if($found !== FALSE) {
				$foundKey = $key;
				break;
			}

		}

		return $foundKey;

	}


}