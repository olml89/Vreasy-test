<?php declare(strict_types = 1);
namespace System\Libraries\Routing\Route;


abstract class AbstractValidRoute implements RouteInterface {


	protected $group  = '';
	protected $params = [];


	public function __construct(array $params, ?string $group) {
		$this->params = $params;		
		$this->group  = $group ?: $this->group;
	}


	public function isValidMethod() : bool {
		return TRUE;
	}


	public function inGroup(string $group) : bool {
		return $this->group === $group;
	}


	public function getParams() : array {
		return $this->params;
	}


}