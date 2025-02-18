<?php
declare( strict_types = 1 );

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

require_once __DIR__ . '/../../vendor/autoload.php';

// For some reason the classes from codesniffer need to be loaded separately
require_once __DIR__ . '/../../vendor/squizlabs/php_codesniffer/autoload.php';

// Make sure that the tokens are known - trigger autoload
new PHP_CodeSniffer\Util\Tokens();

// Some constants need to be defined, see codesniffer's src/Runner.php or
// its tests/bootstrap.php
if ( !defined( 'PHP_CODESNIFFER_VERBOSITY' ) ) {
	define( 'PHP_CODESNIFFER_VERBOSITY', false );
}
if ( !defined( 'PHP_CODESNIFFER_CBF' ) ) {
	define( 'PHP_CODESNIFFER_CBF', false );
}
