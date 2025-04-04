<?php
declare( strict_types = 1 );

// phpcs:disable MediaWiki.Commenting,Squiz.WhiteSpace.FunctionSpacing.Before
// phpcs:disable MediaWiki.Files.ClassMatchesFilename
// phpcs:disable Generic.WhiteSpace.ScopeIndent

 #[MyAttribute]
function demo1() {
}

function demo2(
#[MyAttribute]
	$param1,
		#[MyAttribute]
	$param2
) {
}

 #[MyAttribute]
class Demo {
#[MyAttribute]
	private $prop1;

	 #[MyAttribute]
	private $prop2;

#[MyAttribute]
	private const CONST_1 = 'foo';

	 #[MyAttribute]
	private const CONST_2 = 'bar';

#[MyAttribute]
	public function test1() {
	}

	 #[MyAttribute]
	public function test2() {
	}

}

#[First]
	#[Second]
function demo4() {
}

	#[First]
	#[Second]
function demo5() {
}

  #[TwoSpaces]
function demo6() {
}

// Not fixable
#[MyAttribute]

/* ignored */ function demo7() {
}

/* not fixable */ #[MyAttribute]
function demo8() {
}

// Not covered
#[MyAttribute] function demo9() {
}
