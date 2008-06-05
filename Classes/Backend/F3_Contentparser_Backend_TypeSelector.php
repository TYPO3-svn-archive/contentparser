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

require_once (PATH_t3lib.'class.t3lib_page.php');
require_once (PATH_t3lib.'class.t3lib_tstemplate.php');
require_once (PATH_t3lib.'class.t3lib_tsparser_ext.php');
require_once (PATH_typo3.'sysext/lang/lang.php');

/**
 * Returns an index of term types to be used as "itemsProcFunc" in $TCA
 *
 * @author	Jochen Rau <j.rau@web.de>
 * @package	TYPO3
 * @subpackage	F3_Contentparser
 */
class F3_Contentparser_Backend_TypeSelector {
	protected $configuration;

	public function __construct(F3_Contentparser_Controller_Configuration $configuration) {
		$this->configuration = $configuration;
	}

	function user_addTermTypes(&$params,&$pObj) {
		global $BE_USER;
		$BE_USER->uc['lang'] = $BE_USER->uc['lang'] ? $BE_USER->uc['lang'] : 'default';

		// get extension configuration
		$extConfArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->configuration->getPackageKeyLowercase()]);
		if ( (int)$extConfArray['mainConfigStoragePid']>0 ) {
			$mainConfigStoragePid = intval($extConfArray['mainConfigStoragePid']);
		} else {
			// TODO parse static setup
		}

		$rootLine = t3lib_BEfunc::BEgetRootLine($mainConfigStoragePid);
		$TSObj = t3lib_div::makeInstance('t3lib_tsparser_ext');
		$TSObj->tt_track = 0;
		$TSObj->init();
		$TSObj->runThroughTemplates($rootLine);
		$TSObj->generateConfig();
		$conf = $TSObj->setup['plugin.'][$this->configuration->getPrefixedPackageKey() . '.'];

		// make localized labels
		$LANG = t3lib_div::makeInstance('language');
		$LANG->init($BE_USER->uc['lang']);
		$LOCAL_LANG_ARRAY = array();

		if (!empty($conf['types.'])) {
			foreach ($conf['types.'] as $typeName => $typeConfigArray ) {
				unset($LOCAL_LANG_ARRAY);
				if ( !$typeConfigArray['hideSelection']>0 && !$typeConfigArray['dataSource'] ) {
					foreach ($typeConfigArray['label.'] as $langKey => $labelText) {
						$LOCAL_LANG_ARRAY[$langKey]['label'] = $labelText;
					}
					// $LOCAL_LANG_ARRAY['default']['label'] = $typeConfigArray['label'] ? $typeConfigArray['label'] : $typeConfigArray['label.']['default'];
					$params['items'][]= array( $LANG->getLLL('label',$LOCAL_LANG_ARRAY), substr($typeName,0,-1) );
				}
			}
		}
	}
}
?>