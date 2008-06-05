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
 * Abstract data source for the Contentparser
 *
 * @author	Jochen Rau <jochen.rau@typoplanet.de>
 * @package	TYPO3
 * @subpackage	F3_Contentparser
 */
abstract class F3_Contentparser_Repository_Source_AbstractSource {
	protected $setup;
	protected $configuration;
	
	protected $sourceName;
	protected $dataMap; // data map holding the mapping rules

	public function __construct($setup) {
		$this->setup = $setup;
		$this->sourceName = $this->setup->sourceName;
		foreach ($setup->mapping as $field => $mappingRule) {
			$this->dataMap[$field] = $mappingRule;
		}
	}
	
	abstract public function searchAll();
	
	public function getSetup() {
		return $this->setup;
	}
	
	public function setSourceName($sourceName) {
		$this->sourceName = $sourceName;
	}
	
	public function getSourceName() {
		return $this->sourceName;
	}	
	
	public function setDataMap(array $dataMap) {
		$this->dataMap = $dataMap;
	}
	
	public function getDataMap() {
		return $this->dataMap;
	}
}	
?>