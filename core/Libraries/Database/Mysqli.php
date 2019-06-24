<?php declare(strict_types = 1);
namespace System\Libraries\Database;


use System\Libraries\ErrorHandling\Exceptions\InvalidFileException;


final class Mysqli implements DatabaseInterface {


	private const DEFAULT_CHARSET 	= 'utf8';
	private const FILES_DIR			= ROOTPATH.'/db';

	private $connection 			= NULL; // \mysqli


	public function __construct(array $connectionData, string $dbFile = '') {

		//https://stackoverflow.com/questions/18457821/how-to-make-mysqli-throw-exceptions-using-mysqli-report-strict
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 

		$this->connection = $this->connect($connectionData['host'], 
										   $connectionData['username'], 
										   $connectionData['password'], 
										   $connectionData['database'], 
										   $connectionData['charset'] ?? self::DEFAULT_CHARSET,
										   $dbFile
		);

	}


	private function connect(string $host, string $username, string $password, string $database, string $charset, string $dbFile) : ?\mysqli {

		$connection = NULL;

		try {
			$connection = new \mysqli($host, $username, $password, $database);
		}

		catch(\mysqli_sql_exception $exceptionConnecting) {

			//probably a mysqli_sql_exception: 2002 bad host, 1044 bad user, 1045 bad password...
			if($exceptionConnecting->getCode() !== 1049 || empty($dbFile)) {
				throw $exceptionConnecting;
			}

			//1049: unknown database, try to create the db from a .sql file
			try {

				$connection = new \mysqli($host, $username, $password);
				$this->createDatabase($connection, $dbFile);
				$connection->connect($host, $username, $password, $database);

			}

			catch(\mysqli_sql_exception $exceptionCreating) {
				throw $exceptionCreating;
			}

		}

		if(!empty($connection)) {
			$connection->set_charset($charset);
		}

		return $connection;

	}


	private function createDatabase(\mysqli $connection, string $dbFile) : void {

		//open the file with the SQL syntax;
		if(!is_readable(self::FILES_DIR.'/'.$dbFile)) {
			InvalidFileException::unReadable(self::FILES_DIR.'/'.$dbFile);
		}

		$fileContents = file_get_contents(self::FILES_DIR.'/'.$dbFile);

		//https://stackoverflow.com/a/24780977
		//$mysqliObject->query does not accept multiple queries, it gives a SQL error. One option could be to use the procedural method
		//mysqli_multi_query, the other one to split the file into its instructions and execute them one by one
		$instructions = array_filter(

			array_map(

				function(string $sentence) : string {

					$lines = explode("\n", $sentence);
					$instruction_parts = [];
					$instruction = '';

					foreach($lines as $line) {

						$line = trim($line);
						
						//omit empty lines and comments
						if(strlen($line) === 0 || substr($line, 0, 2) === '--') {
							continue;
						}

						$instruction_parts[] = $line;

					}

					$instruction = implode(' ', $instruction_parts);

					if(!empty($instruction)) {
						$instruction .= ';';
					}

					return $instruction;

				},

				explode(';', $fileContents)

			)

		);

		//try to execute them one by one
		foreach($instructions as $instruction) {
			$connection->query($instruction);	//may raise a 1064 mysqli error (bad syntax), catched by ErrorHandler
		}

	}


	//pain in the ass... https://phpdelusions.net/mysqli/wtf
	/*
		parameters array form:

		[
			['i' => 12],
			['s' => 'abc'],
			['d' => 3.1416]
		]

	*/
	private function getPreparedStatement(string $query, array $parameters) : \mysqli_stmt {

		$types 	= '';
		$params = [];

		foreach($parameters as $parameter) {
			$type = key($parameter);
			$types .= $type;
			$params[] = $parameter[$type];
		}

		/*

			types form: 'isd'
			params form: [12, 'abc', 3.1416]

		*/

		//$types = implode('', array_keys($parameters)); //join
		//$params = array_values($parameters);

		$statement = $this->connection->prepare($query);
		$statement->bind_param($types, ...$params);

		return $statement; 

	}


	//we can assure we always receive a mysqli_result (full or empty), FALSE only returns on error and we have mysql_report
	//set to catch errors so we don't have to check manually for them
	private function getMysqliResult(string $query, array $parameters) : \mysqli_result {

		$statement = $this->getPreparedStatement($query, $parameters);
		$statement->execute();
		$mysql_result = $statement->get_result();
		$statement->close();

		return $mysql_result;

	}

	//WARNING: mysqli_query returns mysqli_result|FALSE on SELECT, and TRUE|FALSE on INSERT, UPDATE, DELETE, REPLACE...
	//but WTF, mysqli_stmt_get_result returns mysqli_result|FALSE on select, and FALSE on every other query... so you have to
	//check manually the result of the insertion, deletion, with affected_rows... LOL
	private function getBooleanResult(string $query, array $parameters) : bool {

		$statement = $this->getPreparedStatement($query, $parameters);
		$bool = $statement->execute() && $statement->affected_rows > 0;
		$statement->close();

		return $bool;

	}


	public function getResult(string $query, array $parameters = []) : bool {
		$result = empty($parameters)? $this->connection->query($query) : $this->getBooleanResult($query, $parameters); // bool
		return $result;
	}


	public function getRow(string $query, array $parameters = []) : ?array {
		$result = empty($parameters)? $this->connection->query($query) : $this->getMysqliResult($query, $parameters); // \mysqli_result
		//return (!$result || $result->num_rows === 0)? NULL : $result->fetch_assoc(); //avoidable as we have mysql_report set on
		return ($result->num_rows === 0)? NULL : $result->fetch_assoc();
	}	


	public function getRows(string $query, array $parameters = []) : array {

		$results = empty($parameters)? $this->connection->query($query) : $this->getMysqliResult($query, $parameters); // \mysqli_result
		
		//if(!$results || $results->num_rows === 0) { ... } //avoidable as we have mysql_report set on

		$fetched = [];

		while($result = $results->fetch_assoc()) {
			$fetched[] = $result;
		}

		return $fetched;

	}


	public function insertId() : int {
		return $this->connection->insert_id;
	}


	public function affectedRows() : int {
		return $this->connection->affected_rows;
	}


	public function escape(string $string, string $charlist = '') : string {

		$escaped = $this->connection->real_escape_string($string);

		if(!empty($charlist)) {
			$escaped = addcslashes($escaped, $charlist);
		}

		return $escaped;

	}


}





