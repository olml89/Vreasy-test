<?php declare(strict_types = 1);
namespace System\Libraries\Pagination;


use Symfony\Component\HttpFoundation\ParameterBag as SymfonyParameterBag;


final class Pagination {


	//default config
	private $limit 				= 25;
	private $contextual_pages	= 1;
	private $separators 		= TRUE;


	//context to set
	private $current_page 		= 0;
	private $num_pages 			= 0;


	public function __construct(int $limit = 25, array $config = []) {

		//set limit
		$this->setLimit($limit);

		//retrieve optional configuration values
		if(!empty($config)) {
			$this->setConfig($config);
		}
		
	}


	public function setLimit(int $limit) : void {
		$this->limit = $limit;
	}


	public function setConfig(array $config) : void {
		$this->contextual_pages = $config['contextual_pages']?? $this->contextual_pages;
		$this->separators 		= $config['separators']?? $this->separators;
	}


	public function setCurrentPage(int $current_page) : void {

		//no 0 or negative pages
		if($current_page < 1) {
			$current_page = 1;
		}

		$this->current_page = $current_page;

	}


	public function setNumPages(int $total_items) : void {

		//calculate number of pages accounting the current limit
		$this->num_pages = (int)ceil($total_items / $this->limit);

		//rectify current page if over the rank (and if pagination needed: when num_pages is 0, page must remain 1)
		if($this->num_pages > 0 && $this->current_page > $this->num_pages) {
			$this->current_page = $this->num_pages;
		}

	}


	public function setContext(int $current_page, int $total_items) : void {
		$this->setCurrentPage($current_page);
		$this->setNumPages($total_items);
	}


	public function getLimit() : int {
		return $this->limit;
	}


	public function getNumPages() : int {
		return $this->num_pages;
	}


	public function getCurrentPage() : int {
		return $this->current_page;
	}


	public function getPrevPage() : int {
		return $this->current_page - 1;
	}


	public function getNextPage() : int {

		if($this->current_page === $this->num_pages) {
			return 0;
		}

		return $this->current_page + 1;

	}


	/*
		returns an array with the form:

		1 					=> FALSE
		'left-separator'	=> FALSE (...)
		15					=> FALSE
		16					=> TRUE  (current page)
		17					=> FALSE 
		'right-separator'	=> FALSE (...)
		62					=> FALSE
	*/
	public function getPages() : array {

		//initialize
		$pages = [];

		//limits for the contextual links
		$start 	= max([$this->current_page - $this->contextual_pages, 1]);
		$end 	= min([$this->current_page + $this->contextual_pages, $this->num_pages]);

		//start with a first page and a left separator (if needed) if first contextual page > 1
		if($start > 1) {

			$pages[] = 1;

			if(($this->current_page - $this->contextual_pages - 1) > 1 && $this->separators) {
				$pages[] = 'left-separator';
			}

		}

		//contextual links
		for($i = $start; $i <= $end; ++$i) {
			$pages[] = $i;
		}

		//end with a right separator (if needed and last page if last contextual page < num_pages
		if($end < $this->num_pages) {

			if($this->current_page + $this->contextual_pages + 1 < $this->num_pages && $this->separators) {
				$pages[] = 'right-separator';
			}     

			$pages[] = $this->num_pages;

		}

		//set to TRUE the current page
		$pages = array_combine($pages, array_fill(0, count($pages), FALSE));

		if(!empty($pages)) {
			$pages[$this->current_page] = TRUE;
		}

		//return
		return $pages;

	}


	/*
		returns an array with the form:
		
		first: http://mywebsite/api/items?page=1
		prev: http://mywebsite/api/items?page=2
		(current page = 3)
		next: http://mywebsite/api/items?page=4
		last: http://mywebsite/api/items?page=5
	*/
	public function getNavigationLinks(string $base_url, SymfonyParameterBag $parameterBag) : array {

		//initialize
		$pages = [];

		//limits for the contextual pages: in the API we only use 1 contextual link each side (prev, next)
		$start 	= max([$this->current_page - 1, 1]);
		$end 	= min([$this->current_page + 1, $this->num_pages]);

		//start with a first page if first contextual page > 1
		if($start > 1) {
			$pages['first'] = 1;
		}

		//contextual pages: can be the current page, so escape it
		for($i = $start; $i <= $end; ++$i) {

			if($i === $this->current_page) {
				continue;
			}

			$index = $i < $this->current_page? ($i === 1? 'first' : 'prev') : ($i === $this->num_pages? 'last' : 'next');
			$pages[$index] = $i;

		}

		//end with a last link if last contextual page < num_pages
		if($end < $this->num_pages) { 
			$pages['last'] = $this->num_pages;
		}

		//build the links
		$navigation = [];

		foreach($pages as $index=>$page) {

			$parameterBag->set('page', $page);
			$link = $base_url.'?'.http_build_query($parameterBag->all());
			$navigation[$index] = $link;

		}

		return $navigation;

	}


}