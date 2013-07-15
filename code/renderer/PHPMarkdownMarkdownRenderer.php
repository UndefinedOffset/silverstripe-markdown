<?php

class PHPMarkdownMarkdownRenderer implements IMarkdownRenderer {

	public function isSupported() {
		$exists = class_exists("\Michelf\Markdown");
		if (!$exists) {
			return "Unable to find the php-markdown class (\Michelf\Markdown) on the classpath.";
		}
		return $exists;
	}

	public function getRenderedHTML($markdown) {
		return \Michelf\Markdown::defaultTransform($markdown);
	}

}