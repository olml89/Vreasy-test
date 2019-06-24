<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Mysql;


final class MysqlExceptionFactory {


	public static function create(\mysqli_sql_exception $mysqli_sql_exception) : MysqlExceptionInterface {
		return new MysqlException($mysqli_sql_exception);
	}


}