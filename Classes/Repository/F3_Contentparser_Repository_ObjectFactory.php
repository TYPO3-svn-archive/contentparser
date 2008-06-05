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
 * A Factory class to build term objects for the contentparser
 *
 * @author	Jochen Rau <jochen.rau@typoplanet.de>
 * @package	TYPO3
 * @subpackage	F3_Contentparser
 */
class F3_Contentparser_Repository_ObjectFactory {
	protected $settings;
	protected $cObj; // local cObj

	public function __construct(F3_FLOW3_Component_ManagerInterface $componentManager, F3_GimmeFive_Configuration_Manager $configurationManager) {
		$this->componentManager = $componentManager;
		$this->settings = $configurationManager->getConfiguration('Contentparser', 'Settings');
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');
	}
	
	public function buildTerms($source, $rows) {
		$sourceSetup = $source->getSetup();
		$dataMap = $source->getDataMap();
		$fieldsToMap = $this->getFieldsToMap($dataMap);
		foreach ($rows as $row) {
			$mappedData = $this->map($sourceSetup, $fieldsToMap, $dataMap, $row);
			$termObjects[$mappedData['termID']] = $this->componentManager->getComponent('F3_Contentparser_Domain_Term', $mappedData);
		}
		return $termObjects;
	}
	
	
	protected function map($sourceSetup, $fieldsToMap, $dataMap, $row) {
		$mappedData = array();
		foreach ($fieldsToMap as $field) {
			$value = $dataMap[$field];
			if (isset($value['value'])) {
				$mappedData[$field] = $value['value'];
			} elseif (isset($value['field'])) {;
				$mappedData[$field] = $row[$value['field']];
			} else {
				$mappedData[$field] = NULL;
			}
			if (isset($value['stdWrap'])) {
				$mappedData[$field] = $this->cObj->stdWrap($mappedData[$field], $value['stdWrap']->toTypoScriptArray());
			}
			$GLOBALS['TSFE']->register['contentparser_' . $field] = $mappedData[$field];
		}
		$mappedData['term'] = $mappedData['replacement'] ? $mappedData['replacement'] : $mappedData['mainTerm'];

		$typeSetup = $this->getTermConfiguration($mappedData['termType']);
		foreach ($fieldsToMap as $field) {
			 if (isset($typeSetup[$field])) {
				$mappedData[$field] = $typeSetup[$field];
			} elseif (isset($sourceSetup[$field])) {
				$mappedData[$field] = $sourceSetup[$field];
			} elseif (isset($this->settings[$field])) {
				$mappedData[$field] = $this->settings[$field];
			}
		}
		// TODO Refactor to a Filter Chain
		// $desc_long = preg_replace('/(\015\012)|(\015)|(\012)/ui','<br />',$row['desc_long']);
		
		$mappedData['synonyms'] = $mappedData['synonyms'] ? t3lib_div::trimExplode(chr(10),$mappedData['synonyms'],1) : array();
		$mappedData['terms'] = array_merge(array($mappedData['mainTerm']), $mappedData['synonyms']);
		// sort the array descending by length of the value, so the longest term will match
		// TODO Make the sorting of the terms configurable
		usort($mappedData['terms'],array($this,'sortArrayByLengthDescending'));
		$mappedData['listPages'] = t3lib_div::trimExplode(',',$mappedData['listPages'],1);
		$mappedData = array_merge(array('termID' => $sourceSetup->sourceName . ':' . $mappedData['uid']), $mappedData);		
		return $mappedData;
	}
	
	protected function sortArrayByLengthDescending($a,$b) {
		// TODO Make the sorting UTF8-safe
		if (strlen($a) == strlen($b)) {
			return 0;
		}
		return strlen($a) < strlen($b) ? 1 : -1;
	}
	
	protected function getFieldsToMap($dataMap) {
		$fieldsToMap = array();
		foreach ($dataMap as $field => $value) {
				$fieldsToMap[] = $field;
		}
		$termProperties = t3lib_div::trimExplode(',',$this->settings->offsetGet('termProperties'),1);
		foreach ($termProperties as $property) {
			if (!in_array($property, $fieldsToMap)) {
				$fieldsToMap[] = $property;
			}
		}
		return $fieldsToMap;
	}
	
	public function getTermConfiguration($termType) {
		$termsConfiguration = $this->settings->types;
		$termConfiguration = isset($termsConfiguration->$termType) ? $termsConfiguration->$termType : $termsConfiguration->offsetGet($this->settings->defaultType);
		return $termConfiguration;
	}
}	
?>