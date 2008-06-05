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
 * Renders an index of given terms
 *
 * @author	Jochen Rau <j.rau@web.de>
 * @package	TYPO3
 * @subpackage	F3_Contentparser
 */
class F3_Contentparser_Widget_Index extends F3_GimmeFive_MVC_View_Template {
	
	public $prefixId = 'f3_contentparser';
	public $scriptRelPath = 'Resources/Language/F3_LanguageDummy.php';
	public $extKey = 'Contentparser';
	
	public function initializeView() {
		$this->setTemplateResource('F3_Contentparser_Widget_Index.html', 'INDEX', TRUE);
		$this->index = $this->piVars['index'] ? urldecode($this->piVars['index']) : NULL;
	}
	
	public function render() {
		$this->subparts['item'] = $this->cObj->getSubpart($this->subparts['template'],'###ITEM###');
		$indexArray = $this->getIndexArray();
		// wrap index chars and add a class attribute if there is a selected index char.
		foreach ($indexArray as $indexChar => $link) {
			$cssClass = !empty($this->piVars['index']) && $this->piVars['index'] == $indexChar ? " class='f3-contentparser-widget-index-act'" : '';
			if (isset($link)) {
				$markerArray['###SINGLE_CHAR###'] = '<span' . $cssClass . '>' . $link . '</span>';
			} elseif ($this->settings->offsetGet('showOnlyMatchedIndexChars') == 0) {
				$markerArray['###SINGLE_CHAR###'] = '<span' . $cssClass . '>' . $indexChar . '</span>';
			} else {
				$markerArray['###SINGLE_CHAR###'] = '';
			}
			$subpartArray['###INDEX_CONTENT###'] .= $this->cObj->substituteMarkerArrayCached($this->subparts['item'], $markerArray);
		}

		// make "link to all entries"
		unset($typolinkConf);
		$typolinkConf['parameter.']['current'] = 1;
		$allLink = $this->cObj->typolink($this->pi_getLL('all'), $typolinkConf);
		$markerArray['###INDEX_ALL###'] = $allLink;
		return $this->cObj->substituteMarkerArrayCached($this->subparts['template'], $markerArray, $subpartArray);
	}
	
	protected function getIndexArray() {

		// Get localized index chars.
		foreach (t3lib_div::trimExplode(',', $this->pi_getLL('indexChars')) as $key => $value) {
			$subCharArray = t3lib_div::trimExplode('|', $value);
			$indexArray[$subCharArray[0]] = NULL;
	        foreach($subCharArray as $subChar) {
	            $reverseIndexArray[$subChar] = $subCharArray[0];
	        }
		}

		// The configuered subchars like Ö will be linked as O (see documentation and file "locallang.xml").
		unset($typolinkConf);
		$typolinkConf['parameter.']['current'] = 1;
		foreach ($this->model as $termKey => $term) {
			$sortField = $term['sortField'] ? $term['sortField'] : 'term';
			foreach ($reverseIndexArray as $subChar => $indexChar) {
				if (preg_match('/^' . preg_quote($subChar) . '/ui', $term[$sortField]) > 0) {
					$typolinkConf['additionalParams'] = '&' . $this->prefixId . '[index]=' . $indexChar;
					$indexArray[$indexChar] = $this->cObj->typolink($indexChar, $typolinkConf);
					$term['indexChar'] = $indexChar;
				}
			}
			// If the term matches no given index char, create one if desired and add it to the index
			if (!isset($term['indexChar'])) {					
				// get the first char of the term (UTF8)
				// TODO Make the RegEx configurable to make ZIP-Codes possible
				preg_match('/^./u', $term[$sortField], $match);
				$newIndexChar = $match[0];
				$indexArray[$newIndexChar] = NULL;
				$typolinkConf['additionalParams'] = '&' . $this->prefixId . '[index]=' . urlencode($newIndexChar);
				$indexArray[$newIndexChar] = $this->cObj->typolink($newIndexChar, $typolinkConf);
			}
		}
		
		// TODO Sorting of the index (UTF8)
		ksort($indexArray, SORT_LOCALE_STRING);
		return $indexArray;
	}
}
?>