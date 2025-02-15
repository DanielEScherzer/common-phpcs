<?php
declare( strict_types = 1 );

/**
 * Confirm that docs/rules.txt has the current list of sniffs.
 */

namespace DanielEScherzer\CommonPhpcs\Tests;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHPUnit\Framework\TestCase;

class DocsListTest extends TestCase {

	public function testDocsList() {
		$rulesFile = dirname( __DIR__, 2 ) . '/docs/rules.txt';

		$this->assertFileExists(
			$rulesFile,
			'docs/rules.txt should exist'
		);

		$explainOutput = $this->getExplainOutput();

		$docsFileContents = file_get_contents( $rulesFile );
		$this->assertSame( trim( $docsFileContents ), trim( $explainOutput ) );
	}

	private function getExplainOutput(): string {
		$config = new Config( [
			"--standard=" . __DIR__ . '/..',
		] );
		$ruleset = new Ruleset( $config );

		ob_start();
		$ruleset->explain();
		$output = ob_get_clean();

		return $output;
	}

}
