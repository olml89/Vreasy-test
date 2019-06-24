<?php declare(strict_types = 1);
namespace System\Services\ApiConsumer;


use Psr\Http\Message\ResponseInterface;


interface ApiConsumerInterface {
	public function get(string $request_uri) : ResponseInterface;
	//public function post(string $request_uri, array $post_data = []) : ResponseInterface; //Not needed in this app
}
