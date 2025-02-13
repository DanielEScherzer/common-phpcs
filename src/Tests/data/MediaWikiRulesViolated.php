<?php
declare( strict_types = 1 );

/**
 * This function violates some MediaWiki rules that are excluded
 */
function wrongPrefix() {
	$a = $_GET['a'];
}

/**
 * But here is a violation of rules from the MediaWiki ruleset that are kept
 */
function missingReturnType() {

	return true;

}
