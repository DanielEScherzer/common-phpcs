<?php
declare( strict_types = 1 );

namespace DanielEScherzer\CommonPhpcs\Sniffs\Attributes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Make sure that the start of #[MyAttribute] is aligned to the same place as
 * the target
 */
class AttributeAlignmentSniff implements Sniff {

	public function register(): array {
		return [ T_ATTRIBUTE ];
	}

	/**
	 * @param File $phpcsFile
	 * @param int $stackPtr
	 *
	 * @return void|int
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		$closer = $tokens[$stackPtr]['attribute_closer'];
		$target = $phpcsFile->findNext(
			Tokens::$emptyTokens,
			$closer + 1,
			null,
			true
		);

		if ( $tokens[$stackPtr]['column'] === $tokens[$target]['column'] ) {
			return;
		}
		// Skip attributes on the same line as the target, those obviously
		// cannot be aligned
		if ( $tokens[$stackPtr]['line'] === $tokens[$target]['line'] ) {
			return;
		}

		// Issue can be fixed if both the attribute and the target are the first
		// things on their lines, because then the issue is just inserting the
		// correct amount of whitespace.
		$attribLineStart = $this->getLineStart( $phpcsFile, $stackPtr );
		$targetLineStart = $this->getLineStart( $phpcsFile, $target );

		if ( $attribLineStart === false || $targetLineStart === false ) {
			$phpcsFile->addWarning(
				'Attributes should be aligned with their targets',
				$stackPtr,
				'NotAligned'
			);
			return;
		}

		$fix = $phpcsFile->addFixableWarning(
			'Attributes should be aligned with their targets',
			$stackPtr,
			'NotAligned'
		);
		if ( !$fix ) {
			return;
		}

		// Get the content of all whitespace tokens before the target
		$desiredWhitespace = $phpcsFile->getTokensAsString(
			$targetLineStart,
			$target - $targetLineStart,
			// keep tabs
			true
		);

		$phpcsFile->fixer->beginChangeset();

		if ( $stackPtr === $attribLineStart ) {
			// Add the desired whitespace to the start of the attribute token
			$phpcsFile->fixer->addContentBefore(
				$stackPtr,
				$desiredWhitespace
			);
		} else {
			// Make the whitespace token before the attribute contain all of
			// the desired whitespace
			$phpcsFile->fixer->replaceToken(
				$stackPtr - 1,
				$desiredWhitespace
			);
		}

		// Apparently we don't need to worry about the other whitespace tokens
		// that came before the stack pointer, I could not figure out a way to
		// trigger needing to remove them, so the code for that was removed
		// since it was untested and potentially unneeded.

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * Given a file and an index for a token
	 *   - if that token is the first non-whitespace on the line, return the
	 *	 index of start of the line
	 *   - if that token is not the first non-whitespace on the line, return
	 *	 false
	 */
	private function getLineStart( File $phpcsFile, int $stackPtr ): int|false {
		$prevNonWhitespace = $phpcsFile->findPrevious(
			T_WHITESPACE,
			$stackPtr - 1,
			null,
			true
		);
		if ( $prevNonWhitespace === false ) {
			// There were no tokens before this in the file, should be
			// impossible
			// @codeCoverageIgnoreStart
			return false;
			// @codeCoverageIgnoreEnd
		}
		$tokens = $phpcsFile->getTokens();
		$stackPtrLine = $tokens[$stackPtr]['line'];
		if ( $tokens[$prevNonWhitespace]['line'] === $stackPtrLine ) {
			return false;
		}

		$lineStart = $prevNonWhitespace + 1;
		while ( $tokens[$lineStart]['line'] !== $stackPtrLine ) {
			$lineStart++;
		}
		return $lineStart;
	}

}
