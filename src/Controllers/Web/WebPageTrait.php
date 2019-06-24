<?php declare(strict_types = 1);
namespace Application\Controllers\Web;


trait WebPageTrait {


	protected $sidebar 	= [];


	protected function setWebPageComponents(array $sidebar) : void {
		$this->sidebar = $sidebar;
	}


	protected function getSidebar(string $page='') : array {

		if(!empty($this->sidebar[$page])) {
			$this->sidebar[$page]['active'] = TRUE;
		}

		return $this->sidebar;

	}


}

