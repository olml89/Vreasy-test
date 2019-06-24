<?php declare(strict_types = 1);
namespace System\Libraries\Routing\Route;


interface ValidRouteInterface extends RouteInterface {
	public function isCallable() : bool;	
}