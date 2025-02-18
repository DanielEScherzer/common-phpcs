# PHP Codesniffer configuration

## About

This composer library implements a set of rules for [PHP Codesniffer](https://packagist.org/packages/squizlabs/php_codesniffer).

It is based on the ruleset provided by the `mediawiki/mediawiki-codesniffer` package,
but with the MediaWiki-specific parts disabled and some other modifications.

See [docs/rules.txt](docs/rules.txt) for the list of included sniffs.

## Usage

```xml
<?xml version="1.0"?>
<ruleset>
	<rule ref="./vendor/danielescherzer/common-phpcs/src"/>
	<file>.</file>
	<arg name="extensions" value="php"/>
	<arg name="encoding" value="UTF-8"/>
</ruleset>
```
