<?php
declare( strict_types = 1 );

function usesDeprecated() {
	utf8_encode( "foo" );
}

#[FirstAttrib, SecondAttrib]
function doNothing() {
	$a = true;
}
