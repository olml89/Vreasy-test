<?php declare(strict_types = 1);
namespace Application\Repositories;


use System\Libraries\Database\DatabaseInterface;
use System\Libraries\Pagination\Pagination;

use Application\Entities\City\CityModel;
use Application\Entities\City\CityFactory;


final class CityRepository {


	private $db 			= NULL; 	// \System\Libraries\Database\DatabaseInterface
	private $cityFactory 	= NULL; 	// \Application\Entities\City\CityFactory;


	public function __construct(DatabaseInterface $db, CityFactory $cityFactory) {
		$this->db 			= $db;	
		$this->cityFactory 	= $cityFactory;
	}	


	private function getNumResults(string $query, array $parameters = []) : int {
		return (int)($this->db->getRow($query, $parameters)['num_cities'] ?? 0);
	}


	private function getNumCities() : int {
		$query = "SELECT COUNT(`id`) AS num_cities FROM cities;";
		return $this->getNumResults($query);
	}


	public function getList(?Pagination $pagination = NULL) : array { // [CityModel]

		$query = "SELECT `id`, `name`, `latitude`, `longitude` FROM cities ORDER BY `name` ASC";

		if(!empty($pagination)) {

			$numCities = $this->getNumCities();
			$pagination->setNumPages($numCities);

			$limit = $pagination->getLimit();
			$offset = ($pagination->getCurrentPage() - 1) * $limit;
			$query .= " LIMIT $limit OFFSET $offset";

		}

		$query .= ";";
		$rows = $this->db->getRows($query);
		$cities = [];

		foreach($rows as $row) {
			$cities[] = $this->cityFactory->createFromDatabaseRow($row);
		}

		return $cities;

	}


	public function getById(int $id) : ?CityModel {

		$query = "SELECT `id`, `name`, `latitude`, `longitude` FROM cities WHERE id = ? LIMIT 1;";

		$row = $this->db->getRow($query, [
			['i' => $id]
		]);

		if(empty($row)) {
			return NULL;
		}

		$city = $this->cityFactory->createFromDatabaseRow($row); 
		return $city;

	}


	//if searchParameters are empty, it is equivalent to getList()
	//normally the results are paginated and thus limited
	public function search(array $searchParameters, ?Pagination $pagination = NULL) : array { // [CityModel]

		$query = "SELECT `id`, `name`, `latitude`, `longitude` FROM cities"; 
		$preparedStatements = [];
		$clauses = '';
		$previousClauses = FALSE;

		if(array_key_exists('name', $searchParameters)) {

			$clauses .= " WHERE `name` = ?";
			$preparedStatements[] = ['s' => $searchParameters['name']];
			$previousClauses = TRUE;

		}

		if(array_key_exists('latitude', $searchParameters)) {

			$clauses .= $previousClauses? " AND `latitude` = ?" :  " WHERE `latitude` = ?";
			$preparedStatements[] = ['d' => $searchParameters['latitude']];
			$previousClauses = TRUE;

		}

		if(array_key_exists('longitude', $searchParameters)) {

			$clauses .= $previousClauses? " AND `longitude` = ?" :  " WHERE `longitude` = ?";
			$preparedStatements[] = ['d' => $searchParameters['longitude']];

		}

		$query .= $clauses." ORDER BY `name` ASC";

		if(!empty($pagination)) {

			$numResultsQuery = "SELECT COUNT(`id`) AS num_cities FROM cities".$clauses.';';
			$numResults = $this->getNumResults($numResultsQuery, $preparedStatements);
			$pagination->setNumPages($numResults);

			$limit = $pagination->getLimit();
			$offset = ($pagination->getCurrentPage() - 1) * $limit;	
			$query .= " LIMIT $limit OFFSET $offset";

		}

		$query .= ";";
		$rows = $this->db->getRows($query, $preparedStatements);
		$cities = [];

		foreach($rows as $row) {
			$cities[] = $this->cityFactory->createFromDatabaseRow($row);
		}

		return $cities;

	}


	public function save(CityModel $city) : bool {

		$query = "INSERT INTO cities(`name`, `latitude`, `longitude`) VALUES (?, ?, ?);";

		$result = $this->db->getResult($query, [
			['s' => $city->getName()],
			['d' => $city->getLatitude()],
			['d' => $city->getLongitude()]
		]);

		if($result) {
			$city->setId($this->db->insertId()); //update id for the current object
		}

		return $result;

	}


	public function edit(CityModel $city) : bool {

		$query = "UPDATE cities SET `name` = ?, `latitude` = ?, `longitude` = ? WHERE `id` = ?;";

		$result = $this->db->getResult($query, [
			['s' => $city->getName()],
			['d' => $city->getLatitude()],
			['d' => $city->getLongitude()],
			['i' => $city->getId()]
		]);

		return $result;

	}


	public function delete(CityModel $city) : bool {

		$query = "DELETE FROM cities WHERE `id` = ?;";

		$result = $this->db->getResult($query, [
			['i' => $city->getId()]
		]);

		return $result;

	}


}