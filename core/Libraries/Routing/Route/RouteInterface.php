<?php declare(strict_types = 1);
namespace System\Libraries\Routing\Route;


interface RouteInterface {
	public function isValidMethod() : bool;	
}