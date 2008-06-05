<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');
t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/', 'Setup');
t3lib_extMgm::addPlugin(array('LLL:EXT:Contentparser/Resources/Language/locallang.xml:terms_list', $_EXTKEY), 'list_type');

// Add a field  "exclude this page from parsing" to the table "pages" and "tt_content"
$tempColumns = Array (
    "f3_contentparser_dont_parse" => Array (
        "exclude" => 1,
        "label" => "LLL:EXT:Contentparser/Resources/Language/locallang.xml:dont_parse",
        "config" => Array (
            "type" => "check",
        )
    ),
);

t3lib_div::loadTCA("pages");
t3lib_extMgm::addTCAcolumns("pages",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("pages","f3_contentparser_dont_parse;;;;1-1-1");

t3lib_div::loadTCA("tt_content");
t3lib_extMgm::addTCAcolumns("tt_content",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("tt_content","f3_contentparser_dont_parse;;;;1-1-1");

?>