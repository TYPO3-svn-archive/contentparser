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
 * Controller of the parser 'contentparser'
 *
 * @author	Jochen Rau <jochen.rau@typoplanet.de>
 * @package	TYPO3
 * @subpackage	F3_Contentparser
 */
class F3_Contentparser_Controller_Parser extends F3_FLOW3_MVC_Controller_AbstractController {
	protected $parser;
	protected $cObj;
		
	/**
	 * Sets the parser used in the controller
	 *
	 * @param  F3_Contentparser_Domain_Parser $parser: The parser
	 * @return void
	 * @required
	 */
	public function injectParser(F3_Contentparser_Domain_Parser $parser) {
		$this->parser = $parser;
	}
	
	/**
	 * Initializes this controller
	 *
	 * @return void
	 * @author Jochen Rau <jochen.rau@typoplanet.de>
	 */	
	public function initializeController() {
		// $this->supportedRequestTypes = array('F3_FLOW3_MVC_Web_Request');
		// $this->cObj = t3lib_div::makeInstance('tslib_cObj');
		// $this->cObj->setCurrentVal($GLOBALS['TSFE']->id);
	}

	public function processRequest(F3_FLOW3_MVC_Request $request, F3_FLOW3_MVC_Response $response) {
		// $response->setContent('Hello World!' . $request->getArgument('content'));
		// return $response;
		$this->content = $request->getArgument('content');
		$this->settings = $request->getArgument('settings');
		
		if ($this->isContentToBeParsed()) {
			$response->setContent($this->parser->parse($this->content));
			// TODO Implement Inverse of Control for Parser/Content
			// $processorChain = $this->componentManager->getComponent('F3_Contentparser_ProcessorChain');
			// $processorChain->addProcessor($this->parser);
			// $processorChain->addProcessor($this->postProcessor);
			// $this->content->setProcessorChain($processorChain);
			// return $this->content->processChain();
		} else {
			$response->setContent($this->content);
		}
		return $response;
	}
	
	
	/**
	 * Test, if the content should be parsed
	 *
	 * @return	boolean	True if the content should be parsed
	 */
	protected function isContentToBeParsed() {
		$result = FALSE;
		$currentPageUid = $GLOBALS['TSFE']->id;
		// get rootline of the current page
		$rootline = $GLOBALS['TSFE']->sys_page->getRootline($currentPageUid);
		// build an array of uids of pages the rootline
		for ($i=count($rootline)-1; $i>=0; $i--) {
			$pageUidsInRootline[] = $rootline["$i"]['uid'];
		}
		// check if the root page is in the rootline of the current page
		foreach (t3lib_div::trimExplode(',',$this->settings['includeRootPages'],1) as $includeRootPageUid) {
			if (t3lib_div::inArray((array)$pageUidsInRootline,$includeRootPageUid))
				$result = TRUE;
		}
		foreach (t3lib_div::trimExplode(',',$this->settings['excludeRootPages'],1) as $excludeRootPageUid) {
			if (t3lib_div::inArray((array)$pageUidsInRootline,$excludeRootPageUid))
				$result = FALSE;
		}
		if (t3lib_div::inList($this->settings['includePages'],$currentPageUid)) {
			$result = TRUE;
		}
		if (t3lib_div::inList($this->settings['excludePages'],$currentPageUid)) {
			$result = FALSE;
		}
		if ( $GLOBALS['TSFE']->page['f3_contentparser_dont_parse'] == 1) {
			$result = FALSE;
		}
		// if ( $this->cObj->getFieldVal('f3_contentparser_dont_parse') == 1) {
		// 	$result = FALSE;
		// }

		return $result;
	}
}	
?>