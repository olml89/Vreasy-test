<?php declare(strict_types = 1);
namespace System\Libraries\Templating;


interface TemplaterInterface {
	public function template(string $template, array $data = []): string;
}