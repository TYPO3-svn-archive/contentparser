<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Jochen Rau <jochen.rau@typoplanet.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Parser of the extension 'contentparser'
 *
 * @author	Jochen Rau <jochen.rau@typoplanet.de>
 * @package	TYPO3
 * @subpackage	F3_Contentparser
 */
class F3_Contentparser_Domain_Parser {
	protected $settings;
	protected $catalog;
	protected $cObj; // local cObj
	
	protected $content;
	
	public function __construct(F3_GimmeFive_Configuration_Manager $configurationManager, F3_Contentparser_Repository_Terms $termRepository) {
		$this->settings = $configurationManager->getConfiguration('Contentparser', 'Settings');
		$this->termRepository = $termRepository;
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');
	}
	
	public function parse($content) {
		$htmlParser = t3lib_div::makeInstance('t3lib_parsehtml');
		$splittedContent = $htmlParser->splitIntoBlock($this->settings->excludeTags, $content);
		for ($offset=0; $offset<count($splittedContent); $offset=$offset+2) {
			$matches = $this->getMatches($splittedContent[$offset]);
			$splittedContent[$offset] = $this->tagMatches($splittedContent[$offset], $matches);			
		}
		return implode('',$splittedContent);
	}
	
	protected function getMatches($content) {
		$matches = array();
		foreach ($this->termRepository->findAll() as $termKey => $term) {
			foreach ($term['terms'] as $string) {
				if (!isset($string)) continue;
				$regEx = $this->getRegEx($term, $string);
				preg_match_all($regEx, $content, $matchesArray, PREG_OFFSET_CAPTURE);
				$matchesArray = $matchesArray[0]; // only take the full pattern matches of the regEx
				for ($i=0; $i < count($matchesArray); $i++) {

					$preContent = substr($content, 0, $matchesArray[$i][1]);
					$postContent = substr($content, strlen($matchesArray[$i][0]) + $matchesArray[$i][1]);

					// Flag: $inTag=true if we are inside a tag < here we are >
					$inTag = FALSE;
					if ((preg_match('/<[^<>]*$/u', $preContent) > 0) && (preg_match('/^[^<>]*>/u', $postContent) > 0) ) {
						$inTag = TRUE;
					}
					if (!$inTag) {
						// support for joined words (with a dashes)
						$preMatch = '';
						$postMatch = '';
						if ($this->settings['checkPreAndPostMatches']>0) {
							preg_match('/(?<=\P{L})[\p{L}\p{Pd}]*\p{Pd}$/Uuis', $preContent, $preMatch);
							preg_match('/^\p{Pd}[\p{L}\p{Pd}]*(?=\P{L})/Uuis', $postContent, $postMatch);
						}
						$matchedTerm = $preMatch[0].$matchesArray[$i][0].$postMatch[0];
						$matchStart = $matchesArray[$i][1] - strlen($preMatch[0]);
						$matchEnd = $matchStart + strlen($matchedTerm);

						$isNested = FALSE;
						$checkArray = $matches;
						foreach ($checkArray as $start => $value) {
							$length = strlen($value['matchedTerm']);
							$end = $start + $length;
							if ( ($matchStart >= $start && $matchStart < $end) || ($matchEnd > $start && $matchEnd <= $end) ) {
								$isNested = TRUE;
							}
						}

						// change the sign of the matchStart if the matchedTerm is nested
						$matchStart = $isNested ? -$matchStart : $matchStart;
						$matches[$matchStart] = array(
							'term' => $term,
							'matchedTerm' => $matchedTerm,
							'preMatch' => $preMatch[0],
							'postMatch' => $postMatch[0]
							);
					}
				}
			}
		}
		// Sort the matches by the position in the text.
		ksort($matches);

		// debug($content);
		// debug($matches,2);
		
		return $matches;
	}

	protected function getRegEx($term, $string) {
		// stdWrap for the term to search for; usefull to realize custom tags like <person>|</person>
		$regExTerm = isset($term['termStdWrap']) ? $this->cObj->stdWrap($string, $term['termStdWrap']) : $string;
		if ($term['termIsRegEx'] > 0) {
			$regEx = '/' . $string . '/' . $this->settings['modifier'];
		} else {
			$regEx = '/(?<=\P{L}|^)' . preg_quote($regExTerm,'/') . '(?=\P{L}|$)/' . $this->settings['modifier'];
		}
		return $regEx;
	}

	protected function tagMatches($content, $matches) {
		$posStart = 0;
		$newContent = '';
		if(is_array($matches)){
			foreach ($matches as $matchStart => $matchArray) {
				if ($matchStart >= 0) { // ignore nested matches
					$matchLength = strlen($matchArray['matchedTerm']);
					$replacement = $this->getTaggedMatch($matchArray['term'], $matchArray['matchedTerm'], $matchArray['preMatch'], $matchArray['postMatch']);
					$replacementLength = strlen($replacement);
					$newContent = $newContent . substr($content, $posStart, $matchStart - $posStart) . $replacement;
					$posStart = $matchStart + $matchLength;
				}
			}
			$newContent = $newContent . substr($content, $posStart);
		} else {
			$newContent = $content;
		}		
		return $newContent;
	}
	
	function getTaggedMatch($term, $matchedTerm, $preMatch, $postMatch) {
		$replacement = $matchedTerm;
		// $this->registerFields($term);
		// build tag
		if ($term['tag'] !== NULL) {
			// get the attributes
			$langAttribute = $this->getLangAttribute($term);
			$titleAttribute = $this->getTitleAttribute($term);
			$cssClassAttribute = $this->getCssClassAttribute($term);
			$before = '<' . $term['tag'] . $titleAttribute . $cssClassAttribute . $langAttribute . '>';
			$after = '</' . $term['tag'] . '>';
		}
		
		// replace matched term
		if ($this->settings['replaceTerm'] && $term['replacement']) {
			// if the first letter of the matched term is upper case
			// make the first letter of the replacing term also upper case
			// (\p{Lu} stands for "unicode letter uppercase")
			if ( preg_match('/^\p{Lu}/u',$replacement)>0 ) {
				$replacement = $preMatch . ucfirst($term['replacement']) . $postMatch;
				// TODO ucfirst is not UTF8 safe; it depends on the locale settings (could be ASCII)
			} else {
				$replacement = $preMatch . $term['replacement'] . $postMatch;
			}
		}
		$GLOBALS['TSFE']->register['contentparser_matchedTerm'] = $replacement;
		if ( !$term['exclude'] && !$term['dontListTerms'] ) {
			$GLOBALS['TSFE']->register['f3_contentparser_termsFound'][] = strip_tags($replacement);
		}
		
		// call stdWrap to handle the matched term via TS BEFORE it is wraped with a-tags
		if ($term['preStdWrap']) {
			$replacement = $this->cObj->stdWrap($replacement, $term['preStdWrap']);
		}
		
		$replacement = $this->linkTerm($term,$replacement);
		
		// call stdWrap to handle the matched term via TS AFTER it was wrapped with a-tags
		if ($term['postStdWrap']) {
			$replacement = $this->cObj->stdWrap($replacement, $term['postStdWrap']);
		}
		
		if ($term['tag'] !== NULL) {
			$replacement = $before . $replacement . $after;
		}
		return $replacement;
	}
			
	/**
	 * Register the fields in $GLOBALS['TSFE] to be used in the TS Setup 
	 */
	protected function registerFields($term) {
		// TODO Strip or replace all block-tags
		// if ($term['stripBlockTags'] > 0) {
		// 	$term->offsetSet('longDescription',preg_replace('/<p[^<>]*>(.*?)<\/p\s*>/ui','$1<br />',$term['longDescription']));
		// }
		foreach ($term as $label => $value) {
			$GLOBALS['TSFE']->register['contentparser_' . $label] = $value;
		}
	}

	protected function linkTerm($term,$replacement) {
		// debug($term);
		// check conditions if the term should be linked to a list page
		$makeLink = ($term['linkToListPage'] == 0) || ($term['exclude'] == 1) ? FALSE : TRUE;
		// link the matched term to the front-end list page
		if ($makeLink) {
		    $urlParameters = array(
				'f3_contentparser' => array(
					'backPid' => $GLOBALS['TSFE']->id,
					'termID' => $term['termID']
					)
				);			
			if ($term['link']) {
				$pageId = $term['link']; // ID of the target page
			} elseif (is_array($term['listPages'])) {
				$pageId = $term['listPages'][0];
			} else {
				$pageId = $GLOBALS['TSFE']->id;
			}
			// 	$GLOBALS['TSFE']->register['contentparser_page'] = $altPageId;
			//     $matchedTerm = $this->pi_linkTP_keepPIvars($matchedTerm, $overrulePIvars, $cache, $clearAnyway, $altPageId);
			$typolinktypolinkConfigurationiguration = array();
			$typolinkConfiguration['useCacheHash'] = 0;
			$typolinkConfiguration['no_cache'] = 0;
			$typolinkConfiguration['parameter'] = $pageId;
			$typolinkConfiguration['additionalParams'] = t3lib_div::implodeArrayForUrl('',$urlParameters,'',1);
			$replacement = $this->cObj->typoLink($replacement, $typolinkConfiguration);
		}
		return $replacement;
	}

	protected function getLangAttribute($term) {
		// get page language
		if ($GLOBALS['TSFE']->config['config']['language']) {
			$pageLanguage = $GLOBALS['TSFE']->config['config']['language'];
		} else {
			$pageLanguage = substr($GLOBALS['TSFE']->config['config']['htmlTag_langKey'],0,2);
		}
		// build language attribute if the page language is different from the terms language
		if ( $term['addLangAttribute'] && !empty($term['language']) && ( $pageLanguage!=$term['language'] ) ) {
			$langAttribute = ' lang="' . $term['language'] . '"';
			$langAttribute .= ' xml:lang="' . $term['language'] . '"';
		}
		return $langAttribute;
	}

	protected function getTitleAttribute($term) {
		$shortDescription = $term['shortDescription'];
		if ($term['addTitleAttribute'] && !empty($shortDescription)) {
			$titleAttribute = ' title="' . $shortDescription . '"';
		}
		return $titleAttribute;
	}

	protected function getCssClassAttribute($term) {
		if (($term['addCssClassAttribute'] > 0) && ($term['cssClass'] !== NULL)) {
			if (isset($term['cssClass'])) {
				$cssClassAttribute = ' class="f3_contentparser_' . $term['cssClass'] . '"';
			} else {
				$cssClassAttribute = ' class="f3_contentparser_' . $term['termType'] . '"';
			}
		}
		return $cssClassAttribute;
	}

}	
?>