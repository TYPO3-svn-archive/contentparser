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
 * The list view renders a list of given terms
 *
 * @author	Jochen Rau <j.rau@web.de>
 * @package	TYPO3
 * @subpackage	F3_Contentparser
 */
class F3_Contentparser_View_Terms_Default extends F3_GimmeFive_MVC_View_Template {

	public $prefixId = 'f3_contentparser';
	public $scriptRelPath = 'Resources/Language/F3_LanguageDummy.php';
	public $extKey = 'Contentparser';
	
	protected $templateCode;

	protected $backPid; // pid of the last visited page (from piVars)
	protected $indexChar; // char of the given index the user has clicked on (from piVars)
	protected $termKey; // local key for each term (not related to the uid in the database)

	public function initializeView() {
		$this->backPid = (int)$this->piVars['backPid'] ? (int)$this->piVars['backPid'] : NULL;
		$this->index = $this->piVars['index'] ? urldecode($this->piVars['index']) : NULL;
	}

	public function render() {
		$markerArray = array();
		$wrappedSubpartArray = array();
		$this->subparts['item'] = $this->cObj->getSubpart($this->subparts['template'],'###ITEM###');
		$this->subparts['template_no_result'] = $this->cObj->getSubpart($this->templateCode,'###TEMPLATE_NO_RESULT###');
		$this->renderLinks($markerArray, $wrappedSubpartArray);
		
		if (empty($this->model)) {
			$markerArray['###NO_RESULT###'] = $this->pi_getLL('no_result');
			$content = $this->cObj->substituteMarkerArrayCached($this->subparts['template_no_result'], $markerArray);
			return $this->pi_wrapInBaseClass($content);
		}

		$indexWidget = $this->componentManager->getComponent('F3_Contentparser_Widget_Index');
		$indexWidget->setModel($this->componentManager->getComponent('F3_Contentparser_Repository_Terms')->findByPID($GLOBALS['TSFE']->id));		
		$markerArray['###INDEX###'] = $indexWidget->render();

		foreach ($this->model as $termID => $term) {
			if (!isset($term)) continue;
			if ( ($term['exclude'] != 1) && $term['listTerm'] != 0 && in_array($GLOBALS['TSFE']->id, $term['listPages']) ) {
				$this->fillMarker($term, $markerArray, $wrappedSubpartArray);
				$subpartArray['###LIST###'] .= $this->cObj->substituteMarkerArrayCached($this->subparts['item'],$markerArray,$subpartArray,$wrappedSubpartArray);
			}
		}
		
		$content = $this->cObj->substituteMarkerArrayCached($this->subparts['template'],$markerArray,$subpartArray,$wrappedSubpartArray);
		$content = $this->removeUnfilledMarker($content);
		return $this->pi_wrapInBaseClass($content);
	}
	
	protected function renderLinks(&$markerArray, &$wrappedSubpartArray) {
		// make "back to..." link
		if ($this->backPid) {
			if($this->settings->offsetGet('addBackLinkDescription') > 0) {
				
				$backPage = $this->pi_getRecord('pages', $this->backPid);
				$markerArray['###BACK_TO###'] = $this->pi_getLL('backToPage') . " \"" . $backPage['title'] . "\"";
			} else {
				$markerArray['###BACK_TO###'] = $this->pi_getLL('back');
			}
		} else {
			$markerArray['###BACK_TO###'] = '';
		}
		unset($typolinkConf);
		$typolinkConf['parameter'] = $this->backPid;
		$wrappedSubpartArray['###LINK_BACK_TO###'] = $this->cObj->typolinkWrap($typolinkConf);

		// // make "link to all entries"
		// 	    $markerArray['###INDEX_ALL###'] = $this->pi_linkTP($this->pi_getLL('all'));

		// make "to list ..." link
		unset($typolinkConf);
		$markerArray['###TO_LIST###'] = $this->pi_getLL('toList');
		$typolinkConf = $this->typolinkConf;
		$typolinkConf['parameter.']['wrap'] = "|,".$GLOBALS['TSFE']->type;
		$wrappedSubpartArray['###LINK_TO_LIST###'] = $this->cObj->typolinkWrap($typolinkConf);		
	}

	protected function fillMarker($term, &$markerArray, &$wrappedSubpartArray) {
		$labelWrap['noTrimWrap'] = $this->settings->offsetGet('labelWrap') ? $this->settings->offsetGet('labelWrap') : NULL;
		foreach ($term as $property => $value) {
			// TODO Improve pre-processing of property-values 
			if (is_array($value)) {
				$value = implode(', ', $value);
			}
			$propertyMarker = '###' . $this->getUpperCase($property) . '###';
			$markerArray[$propertyMarker] = $term[$property] ? $value : $this->pi_getLL('na');
			$labelMarker = '###LABEL_' . $this->getUpperCase($property) . '###';
			$markerArray[$labelMarker] = $this->cObj->stdWrap($this->pi_getLL($property), $labelWrap);
		}

		// make "more..." link
		$markerArray['###DETAILS###'] = $this->pi_getLL('details');
		unset($typolinkConf);
		$typolinkConf = $this->typolinkConf;
		$typolinkConf['additionalParams'] .= '&' . $this->prefixId . '[termID]=' . $term['termID'];
		$typolinkConf['parameter.']['wrap'] = "|,".$GLOBALS['TSFE']->type;
		$wrappedSubpartArray['###LINK_DETAILS###'] = $this->cObj->typolinkWrap($typolinkConf);
	}
	
	protected function removeUnfilledMarker($content) {
		return preg_replace('/###.*?###/', '', $content);
	}
	
	protected function getUpperCase($camelCase) {
		return strtoupper(preg_replace('/\p{Lu}+(?!\p{Ll})|\p{Lu}/u', '_$0', $camelCase));
	}
}
?>