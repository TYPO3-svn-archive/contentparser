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
 * A SQL based source for the Contentparser
 *
 * @author	Jochen Rau <jochen.rau@typoplanet.de>
 * @package	TYPO3
 * @subpackage	F3_Contentparser
 */
class F3_Contentparser_Repository_Source_SQL extends F3_Contentparser_Repository_Source_AbstractSource {
	
	public function searchAll() {
		// Build WHERE-clause
		$whereClause = '1=1';
		$whereClause .= !empty($this->setup['storagePids']) ? ' AND pid IN (' . $this->setup['storagePids'] . ')' : '';
		$whereClause .= $this->hasSysLanguageUid($this->sourceName) ? ' AND (sys_language_uid='.intval($GLOBALS['TSFE']->sys_language_uid) . ' OR sys_language_uid=-1)' : '';
		$whereClause .= tslib_cObj::enableFields($this->sourceName);

		// execute SQL-query
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*', // SELECT ...
			$this->setup['sourceName'], // FROM ...
			$whereClause // WHERE ..
			);
		foreach ($res as $row) {
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	protected function hasSysLanguageUid($sourceName) {
		$fields = $GLOBALS['TYPO3_DB']->admin_get_fields($sourceName);
		if (array_key_exists('sys_language_uid', $fields)) {
			return TRUE;			
		} else {
			return FALSE;
		}
	}
}	
?>