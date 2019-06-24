<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Mysql;


final class MysqlException extends \Exception implements MysqlExceptionInterface {


	public function __construct(\mysqli_sql_exception $mysql_sql_exception) {
		parent::__construct(
			'Database error ('.$mysql_sql_exception->getCode().')',
			$mysql_sql_exception->getCode()
		);
	}


}