<?php

namespace UndefinedOffset\Markdown\Renderer;

interface IMarkdownRenderer {
	/**
	 * Performs the necessary checks to determine if the markdown renderer is supported on the system (eg, check necessary libraries are available etc)
	 * @return {mixed} True if the renderer is supported, or string error message detailing why the renderer isnt supported
	 */
	public function isSupported();

	/**
	 * Returns the supplied Markdown as rendered HTML
	 * @param {string} $markdown The markdown to render
	 * @return {string} The rendered HTML
	 */
	public function getRenderedHTML($markdown);
}
