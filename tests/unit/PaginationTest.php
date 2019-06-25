<?php declare(strict_types = 1);


use PHPUnit\Framework\TestCase;

use System\Libraries\Pagination\Pagination;


class PaginationTest extends TestCase {


	protected $pagination = NULL; // \System\Libraries\Pagination\Pagination


	protected function setUp() : void {

		//set up the pagination configuration
		$limit = 10;

		$config = [
			'contextual_pages'	=> 1,
			'separators'		=> TRUE
		];

		$this->pagination = new Pagination($limit, $config);

	}


	public function testAssertGetLimitIsCorrect() : void {
		$this->assertEquals(10, $this->pagination->getLimit());
	}


	/*
		Test edge case 1: page > number of pages -> result set: 45 results (-> 5 pages)
	*/


	//number of pages have to be 5
	public function testAssertNumPagesAfterSettingPaginatedResultSetIsCorrect() : void {

		$num_items = 45;
		$this->pagination->setNumPages($num_items);
		$this->assertEquals(5, $this->pagination->getNumPages());

	}


	//ask for page 6: current page have to be rectified to 5
	public function testAssertCurrentPageAfterSettingPaginatedResultSetIsCorrect() : void {

		$page = 6;
		$num_items = 45;

		$this->pagination->setCurrentPage($page);
		$this->pagination->setNumPages($num_items);

		$this->assertEquals(5, $this->pagination->getCurrentPage());

	}


	//ask for page 4: previous page have to be 4
	public function testAssertPrevPageAfterSettingPaginatedResultSetIsCorrect() : void {

		$page = 6;
		$num_items = 45;

		$this->pagination->setCurrentPage($page);
		$this->pagination->setNumPages($num_items);

		$this->assertEquals(4, $this->pagination->getPrevPage());

	}


	//ask for page 6: next page has to be NULL (we can't go forward)
	public function testAssertNextPageAfterSettingPaginatedResultSetIsCorrect() : void {

		$page = 6;
		$num_items = 45;

		$this->pagination->setCurrentPage($page);
		$this->pagination->setNumPages($num_items);

		$this->assertNull($this->pagination->getNextPage());

	}


	/*
		Test edge case 2: resultset < limit -> num_pages = 1 -> result set: 5 results (-> 1 page), ask for page 6
	*/


	//number of pages have to be 1
	public function testAssertNumPagesAfterSettingUnpaginatedResultSetIsCorrect() : void {

		$num_items = 5;
		$this->pagination->setNumPages($num_items);
		$this->assertEquals(1, $this->pagination->getNumPages());

	}


	//ask for page 6: current page have to be rectified to 1
	public function testAssertCurrentPageAfterSettingUnpaginatedResultSetIsCorrect() : void {

		$page = 6;
		$num_items = 5;

		$this->pagination->setCurrentPage($page);
		$this->pagination->setNumPages($num_items);

		$this->assertEquals(1, $this->pagination->getCurrentPage());

	}


	//ask for page 4: previous page have to be NULL (we can't go backward)
	public function testAssertPrevPageAfterSettingUnpaginatedResultSetIsCorrect() : void {

		$page = 6;
		$num_items = 5;

		$this->pagination->setCurrentPage($page);
		$this->pagination->setNumPages($num_items);

		$this->assertNull($this->pagination->getPrevPage());

	}


	//ask for page 6: next page has to be NULL (we can't go forward)
	public function testAssertNextPageAfterSettingUnpaginatedResultSetIsCorrect() : void {

		$page = 6;
		$num_items = 5;

		$this->pagination->setCurrentPage($page);
		$this->pagination->setNumPages($num_items);

		$this->assertNull($this->pagination->getNextPage());

	}


}