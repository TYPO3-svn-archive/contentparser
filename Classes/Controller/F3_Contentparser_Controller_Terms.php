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
 * Controller of the terms for the extension 'contentparser'
 *
 * @author	Jochen Rau <jochen.rau@typoplanet.de>
 * @package	TYPO3
 * @subpackage	F3_Contentparser
 */
class F3_Contentparser_Controller_Terms extends F3_FLOW3_MVC_Controller_ActionController {
	protected $termRepository;
	protected $piVars;
	protected $backPid;
	protected $index;
	protected $termID;

	/**
	 * Sets the term repository
	 *
	 * @param  F3_Contentparser_Repository_Terms $termRepository: The term repository
	 * @return void
	 * @required
	 */
	public function injectTermRepository(F3_Contentparser_Repository_Terms $termRepository) {
		$this->termRepository = $termRepository;
	}

	/**
	 * Initializes this controller
	 *
	 * @return void
	 * @author Jochen Rau <jochen.rau@typoplanet.de>
	 */	
	public function initializeController() {
		$this->supportedRequestTypes = array('F3_FLOW3_MVC_Web_Request');
		
		// $this->arguments->addNewArgument('backPid')->setShortHelpMessage('The ID of the Page you came from.')->setShortName('b');
		// $this->arguments->addNewArgument('index')->setShortHelpMessage('The first char(s) of a term.')->setShortName('i');
		// $this->arguments->addNewArgument('termID')->setShortHelpMessage('The ID of a term.')->setShortName('t');

		$this->piVars = t3lib_div::GParrayMerged('f3_contentparser');
		$this->backPid = isset($this->piVars['backPid']) ? (int)$this->piVars['backPid'] : NULL;
		$this->index = isset($this->piVars['index']) ? urldecode($this->piVars['index']) : NULL;
		$this->termID = isset($this->piVars['termID']) ? urldecode($this->piVars['termID']) : NULL;
	}

	/**
	 * Handles a request. The result output is returned by altering the given response.
	 *
	 * @param F3_FLOW3_MVC_Request $request The request object
	 * @return string The altered content
	 * @author Jochen Rau <jochen.rau@typoplanet.de>
	 */
	public function defaultAction() {
		// $this->response->setContent('Hello World!' . $request->getArgument('content'));
		// return $this->response;
		$view = $this->componentManager->getComponent('F3_Contentparser_View_Terms_Default');
		if (isset($this->termID)) {
			$view->setModel($this->termRepository->findByTermID($this->termID));
			$view->setTemplateResource('F3_Contentparser_View_Terms_Default.html', 'SINGLE');
		} elseif ($this->index) {
			$view->setModel($this->termRepository->findByIndex($this->index));
			$view->setTemplateResource('F3_Contentparser_View_Terms_Default.html', 'LIST');
		} else {
			$view->setModel($this->termRepository->findByPID($GLOBALS['TSFE']->id));
			$view->setTemplateResource('F3_Contentparser_View_Terms_Default.html', 'LIST');
		}
		
		$this->response->setContent($view->render());
		
		return $this->response;
	}
	

}
?>