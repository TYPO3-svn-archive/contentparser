<?php
declare(ENCODING="utf-8");

# basic settings
$c->termProperties = 'uid,pid,term,synonyms,termType,replacement,language,shortDescription,longDescription,link,exclude,sortField,linkToListPage,listPages,listTerm,checkPreAndPostMatches,addTitleAttribute,addLangAttribute,addCssClassAttribute,replaceTerm,tag,cssClass,updateKeywords,termIsRegEx,termStdWrap,preStdWrap,postStdWrap';
$c->defaultType = '';
$c->defaultSourceKey = '';

# controller settings
$c->listPages = NULL;
$c->storagePids = NULL;
$c->includeRootPages = NULL;
$c->excludeRootPages = NULL;
$c->includePages = NULL;
$c->excludePages = NULL;

# matcher settings
$c->checkPreAndPostMatches = TRUE;
$c->excludeTags = 'h1,h2,h3,h4,h5,h6,a';
$c->modifier = 'Uuis';

# tagger settings
$c->linkToListPage = TRUE;
$c->addTitleAttribute = TRUE;
$c->addLangAttribute = TRUE;
$c->addCssClassAttribute = TRUE;
$c->replaceTerm = TRUE;

# view settings
$c->templateFile = NULL;
$c->addBackLinkDescription = TRUE;
$c->showOnlyMatchedIndexChars = FALSE;
$c->labelWrap = '|<strong>|:</strong> |';
$c->listTerm = TRUE;

$c->types = NULL;

$c->sources = NULL;

?>