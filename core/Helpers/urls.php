<?php declare(strict_types = 1);


//base_path = /mywebsite
function base_path() : string {

	$script_path_components = explode('/', dirname($_SERVER['SCRIPT_NAME']));
	$request_uri_components = explode('/', $_SERVER['REQUEST_URI']);
	
	$base_path = implode('/', 
						array_filter(array_intersect($script_path_components, $request_uri_components), function($item) {
							return !empty($item);
						})
					); 
	
	if(!empty($base_path)) {
		$base_path = '/'.$base_path;
	}

	return $base_path;

}


//base_url = mywebsite.com/mywebsite
function base_url() : string {
	$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)? 'https' : 'http';
	return $protocol.'://'.$_SERVER['HTTP_HOST'].base_path();
}


//request_uri = /mywebsite/users/12
function request_uri() : string {
	return str_replace(base_path(), '', $_SERVER['REQUEST_URI']);
}


//current_url = mywebsite.com/mywebsite/users/12
function current_url() : string {
	$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)? 'https' : 'http';
	return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}