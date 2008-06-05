<?php
/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 * A Repository for Terms
 *
 * @author	Jochen Rau <jochen.rau@typoplanet.de>
 * @package	TYPO3
 * @subpackage	F3_Contentparser
 */
class F3_Contentparser_Repository_Terms extends F3_Contentparser_Repository_AbstractRepository {
	// TODO Refactor the catalog to collection pattern
	protected $catalog = array();

	// TODO Sorting function
	// TODO Implement method findByQueryObject()

	/**
	 * Returns all (non-hidden an non deleted) terms in all configured sources.
	 */
	public function findAll() {
		// TODO Enable Lazy Loading
		return $this->getCatalog();
	}
	
	public function findByTermID($termID) {
		$catalog = $this->getCatalog();
		// TODO  Auto-fetch missing Terms from sources
		return array($termID => $catalog[$termID]);
	}

	public function findByPID($pid) {
		$catalog = $this->getCatalog();
		$subset = array();
		foreach ($catalog as $termID => $term) {
			if ((!isset($term['listPages']) || in_array($pid, $term['listPages'])) && ($term['listTerm'] > 0)) {
				$subset[$termID] = $term;
			}
		}
		return $subset;
	}
	
	public function findByIndex($index) {
		$catalog = $this->getCatalog();
		$subset = array();
		foreach ($catalog as $termID => $term) {
			$sortField = $term['sortField'] ? $term['sortField'] : 'term';
			if (preg_match('/^' . preg_quote($index) . '/ui', $term[$sortField])) {
				$subset[$termID] = $term;
			}
		}
		return $subset;
	}
	
	protected function getCatalog() {
		if (empty($this->catalog)) {
			$terms = array();
			foreach ($this->sources as $source) {
				$rows = $source->searchAll();
				if(isset($rows)) {
					$terms = array_merge($terms, $this->objectFactory->buildTerms($source, $rows));					
				}
			}			
			$this->catalog = $terms;			
		} else {
			$terms = $this->catalog;
		}
		return $terms;
	}
}	
?>