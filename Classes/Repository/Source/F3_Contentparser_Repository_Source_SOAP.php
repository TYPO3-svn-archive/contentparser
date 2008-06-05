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
class F3_Contentparser_Repository_Source_SOAP extends F3_Contentparser_Repository_Source_AbstractSource {
	
	function searchALL() {	
		$client = &new SOAPClient("http://typo3.org/wsdl/tx_ter_wsdl.php"); 
		$accountData = array(
			'username' => '', 
			'password' => '',
		);
		$extensionKeyFilterOptions = array();
		debug($client->ping(': I was here!'));
		// $result = $client->__soapCall('getExtensionKeys', array('accountData' => $accountData, 'extensionKeyFilterOptions' => $extensionKeyFilterOptions)); 
		// $result = $this->object2array($result);
		// debug($result);
	    // return $result;
	}
	
	function object2array($object) {
		if (!is_object($object) && !is_array($object)) {
			return $object;
		}

		$array = (array)$object;
		foreach ($array as $key => $value) {
			$array[$key] = $this->object2array($value);
		}
		return $array;
	}
}	
?>