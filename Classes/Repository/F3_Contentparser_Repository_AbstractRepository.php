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
 * @package TYPO3
 * @subpackage F3_Contentparser
 * @version $Id:$
 */

/**
 * An abstract repository
 *
 * @package TYPO3
 * @subpackage F3_Contentparser
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class F3_Contentparser_Repository_AbstractRepository {

	/**
	 * @var F3_FLOW3_Component_ManagerInterface The component manager
	 */
	protected $components;

	/**
	 * @var F3_Contentparser_Controller_Configuration The package configuration
	 */
	protected $configuration;
	
	/**
	 * @var F3_Contentparser_ObjcetFactory A factory to build domain objects
	 */
	protected $objectFactory;
	
	/**
	 * @var array A collection of sources
	 */
	protected $sources;

	/**
	 * Constructs the repository.
	 *
	 * @param F3_FLOW3_Component_ManagerInterface $componentManager A reference to the Component Manager
	 * @param F3_Contentparser_Controller_Configuration A reference to the package configuration
	 * @param F3_Contentparser_ObjcetFactory A reference to the factory object that builds domain objects
	 * @author Jochen Rau
	 */
	public function __construct(F3_FLOW3_Component_ManagerInterface $componentManager, F3_GimmeFive_Configuration_Manager $configurationManager, F3_Contentparser_Repository_ObjectFactory $objectFactory) {
		$this->componentManager = $componentManager;
		$this->settings = $configurationManager->getConfiguration('Contentparser', 'Settings');
		$this->objectFactory = $objectFactory;
		$this->sources = $this->fetchSources();
	}

	/**
	 * Fetches all Sources as objects and stores them into an array
	 *
	 * @return array $sources: an array containing the sources
	 * @author Jochen Rau
	 */
	protected function fetchSources() {
		$sources = array();
		foreach ($this->settings['sources'] as $sourceKey => $sourceSetup) {
			$sourceSetup->sourceKey = $sourceKey;
			if ($sourceSetup['type'] === 'sql' && $this->sqlSourceExists($sourceSetup)) {
				$sources[] = $this->componentManager->getComponent('F3_Contentparser_Repository_Source_SQL', $sourceSetup);
			} elseif ($sourceSetup['type'] === 'soap') {
				$sources[] = $this->componentManager->getComponent('F3_Contentparser_Repository_Source_SOAP', $sourceSetup);					
			}
		}
		return $sources;
	}
	
	/**
	 * Tests if a sql-ressource exists.
	 *
	 * @return bool True, if the table exists in the $TCA 
	 * @author Jochen Rau
	 */	
	protected function sqlSourceExists($sourceSetup) {
		global $TCA;
		return array_key_exists($sourceSetup['sourceName'], $TCA) ? TRUE : FALSE;
	}
}	
?>