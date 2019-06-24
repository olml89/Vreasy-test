<?php declare(strict_types = 1);
namespace System\Libraries\Database;


interface DatabaseInterface {
	public function getResult(string $query) : bool;
	public function getRow(string $query) : ?array;
	public function getRows(string $query) : array;
}
