<?php

########################################################################
# Extension Manager/Repository config file for ext: "contentparser"
#
# Auto generated 12-05-2008 11:35
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Content parser and tagger',
	'description' => 'This package parses your content to tag, replace and link specific terms. It is useful to auto-generate a glossary - but not only. See \'ChangeLog\' and WiKi (\'http://wiki.typo3.org/index.php/Contentparser\'). Requires at least PHP 5.1',
	'category' => 'fe',
	'shy' => 0,
	'version' => '0.0.1',
	'state' => 'alpha',
	'dependencies' => 'gimmefive',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Jochen Rau',
	'author_email' => 'jochen.rau@typoplanet.de',
	'author_company' => 'typoplanet',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'gimmefive' => '',
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:30:{s:12:"ext_icon.gif";s:4:"50a3";s:14:"ext_tables.php";s:4:"f196";s:58:"Classes/Controller/F3_Contentparser_AbstractController.php";s:4:"d4c8";s:53:"Classes/Controller/F3_Contentparser_Controller_Configuration.php";s:4:"3731";s:59:"Classes/Controller/F3_Contentparser_ControllerInterface.php";s:4:"cca4";s:57:"Classes/Controller/F3_Contentparser_Controller_Parser.php";s:4:"19aa";s:56:"Classes/Controller/F3_Contentparser_Controller_Terms.php";s:4:"c26d";s:50:"Classes/Controller/tx_Contentparser_Dispatcher.php";s:4:"fe10";s:43:"Classes/Domain/F3_Contentparser_Domain_Content.php";s:4:"f5e0";s:42:"Classes/Domain/F3_Contentparser_Domain_Parser.php";s:4:"54be";s:51:"Classes/Domain/F3_Contentparser_Domain_ParserInterface.php";s:4:"6b03";s:40:"Classes/Domain/F3_Contentparser_Domain_Term.php";s:4:"1008";s:45:"Classes/GUI/F3_Contentparser_TypeSelector.php";s:4:"edae";s:54:"Classes/Repository/F3_Contentparser_Repository_Source_AbstractSource.php";s:4:"3f48";s:50:"Classes/Repository/F3_Contentparser_Repository_Terms.php";s:4:"56df";s:50:"Classes/Repository/F3_Contentparser_Repository_Source_CSV.php";s:4:"fb67";s:51:"Classes/Repository/F3_Contentparser_Repository_Source_SOAP.php";s:4:"6178";s:50:"Classes/Repository/F3_Contentparser_Repository_Source_SQL.php";s:4:"f845";s:51:"Classes/Repository/F3_Contentparser_Repository_AbstractRepository.php";s:4:"d63d";s:46:"Classes/View/F3_Contentparser_View_AbstractView.php";s:4:"a500";s:48:"Classes/View/F3_Contentparser_View_TermsList.php";s:4:"52ff";s:46:"Classes/View/F3_Contentparser_Widget_Index.php";s:4:"32e3";s:47:"Classes/View/F3_Contentparser_Widget_Search.php";s:4:"692f";s:34:"Configuration/Components/setup.txt";s:4:"0270";s:27:"Configuration/CSS/setup.txt";s:4:"200d";s:32:"Configuration/Settings/setup.txt";s:4:"9b68";s:41:"Documentation/Manual/en/Contentparser.xml";s:4:"4e7c";s:32:"Resources/Language/locallang.xml";s:4:"3c73";s:55:"Resources/Template/F3_Contentparser_View_TermsList.html";s:4:"f48f";s:53:"Resources/Template/F3_Contentparser_Widget_Index.html";s:4:"e8f6";}',
	'suggests' => array(
	),
);

?>