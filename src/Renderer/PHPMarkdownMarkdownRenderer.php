<?php

namespace UndefinedOffset\Markdown\Renderer;

use Michelf\Markdown;

/**
 * Class PHPMarkdownMarkdownRenderer
 * @package UndefinedOffset\Markdown\Renderer
 */
class PHPMarkdownMarkdownRenderer implements IMarkdownRenderer
{
    /**
     * Returns the supplied Markdown as rendered HTML
     * @param string $markdown The markdown to render
     * @return string The rendered HTML
     */
    public function isSupported()
    {
        $exists = class_exists(Markdown::class);
        if (!$exists) {
            return "Unable to find the php-markdown class (\Michelf\Markdown) on the classpath.";
        }

        return $exists;
    }

    /**
     * Returns the supplied Markdown as rendered HTML
     * @param string $markdown The markdown to render
     * @return string The rendered HTML
     */
    public function getRenderedHTML($markdown)
    {
        return Markdown::defaultTransform($markdown);
    }
}
