<?php
declare( strict_types = 1 );

/**
 * For each XYZ.php file in data/, run PHPCS, the output should match what is
 * in XYZ.report
 *
 * If there are any automatic fixes, the fixed version should be in
 * XYZ.php.fixed
 */

namespace DanielEScherzer\CommonPhpcs\Tests;

use FilesystemIterator;
use GlobIterator;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Reporter;
use PHP_CodeSniffer\Ruleset;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SniffOutputTest extends TestCase {

	private string $standard;
	private Config $phpcsConfig;

	protected function setUp(): void {
		parent::setUp();
		$this->standard = __DIR__ . '/..';

		$config = new Config( [
			"--standard=" . $this->standard,
			"-s",
		] );
		// for some reason passing `--tab-width=4` doesn't work, set manually
		$config->tabWidth = 4;
		// same with passing --report-width
		$config->reportWidth = 80;

		$this->phpcsConfig = $config;
	}

	public static function provideTestCases() {
		$iterator = new GlobIterator(
			__DIR__ . '/data/*.php',
			FilesystemIterator::CURRENT_AS_PATHNAME
		);
		foreach ( $iterator as $path ) {
			yield $path => [ $path ];
		}

		$iterator = new GlobIterator(
			__DIR__ . '/data/*/*.php',
			FilesystemIterator::CURRENT_AS_PATHNAME
		);
		foreach ( $iterator as $path ) {
			yield $path => [ $path ];
		}
	}

	#[DataProvider( 'provideTestCases' )]
	public function testCodesnifferRules( string $file ) {
		[ $report, $fixed ] = $this->processFile( $file );

		// expected output of processing should be in the corresponding file
		// `.php` -> `.report`
		$reportFile = substr( $file, 0, -3 ) . "report";
		$this->assertFileExists( $reportFile );
		$reportFileContents = file_get_contents( $reportFile );
		// Use a placeholder `{dir}` so that tests pass regardless of where
		// in the filesystem the code is located.
		$reportFileContents = str_replace(
			"FILE: {dir}/",
			"FILE: " . __DIR__ . "/",
			$reportFileContents
		);
		$this->assertSame( trim( $reportFileContents ), trim( $report ) );

		// .fixed file must exist and match the fixed output if there are
		// automatic fixes, and must not exist otherwise
		$fixedOutputFile = $file . ".fixed";
		if ( $fixed ) {
			$this->assertFileExists(
				$fixedOutputFile,
				'Automatic fixes need to be tested, .fixed file is required'
			);
			$this->assertSame( file_get_contents( $fixedOutputFile ), $fixed );
		} else {
			$this->assertFileDoesNotExist(
				$fixedOutputFile,
				'No automatic fixes to test, .fixed file should be deleted'
			);
		}
	}

	private function processFile( string $path ): array {
		$file = new LocalFile(
			$path,
			new Ruleset( $this->phpcsConfig ),
			$this->phpcsConfig
		);
		$file->process();

		// Easiest way to get the report: output it to a buffer and get the
		// contents of the buffer
		$reporter = new Reporter( $this->phpcsConfig );
		$reporter->cacheFileReport( $file );

		ob_start();
		$reporter->printReport( 'full' );
		$report = ob_get_clean();

		// Fixed contents, or false if there are no fixes
		if ( !$file->getFixableCount() ) {
			return [ $report, false ];
		}

		$file->fixer->fixFile();
		return [ $report, $file->fixer->getContents() ];
	}

}
