<?php

namespace UndefinedOffset\Markdown\Model\FieldTypes;

use SilverStripe\Core\Cache;
use SilverStripe\Core\ClassInfo;
use SilverStripe\ORM\FieldType\DBText;
use UndefinedOffset\Markdown\Renderer\GithubMarkdownRenderer;

class Markdown extends DBText
{
    /**
     * {@inheritDoc}
     */
    public static $casting = array(
        'AsHTML' => 'HTMLText',
        'Markdown' => 'DBText'
    );

    /**
     * @var string
     */
    public static $escape_type = 'xml';

    /**
     * @var string
     */
    private static $renderer = 'UndefinedOffset\\Markdown\\Renderer\\GithubMarkdownRenderer';

    /**
     * @var \UndefinedOffset\Markdown\Renderer\IMarkdownRenderer
     */
    private $renderInst;

    /**
     * @var string
     */
    protected $parsedHTML = false;


    /**
     * Checks cache to see if the contents of this field have already been loaded from github, if they haven't
     * then a request is made to the github api to render the markdown
     * @param  bool $useGFM Use Github Flavored Markdown or render using plain markdown defaults to false just like
     *                      how readme files are rendered on github
     * @return string Markdown rendered as HTML
     */
    public function AsHTML($useGFM = false)
    {
        if ($this->parsedHTML !== false) {
            return $this->parsedHTML;
        }

        //Setup renderer
        $renderer = $this->getRenderer();
        $supported = $renderer->isSupported();
        if ($supported !== true) {
            $class_name = get_class($renderer);
            user_error("Renderer $class_name is not supported on this system: $supported");
        }

        if ($renderer instanceof GithubMarkdownRenderer) {
            $beforeUseGFM = GithubMarkdownRenderer::getUseGFM();

            GithubMarkdownRenderer::setUseGFM($useGFM);
        }

        //Init cache stuff
        $cacheKey = $this->getCacheKey();
        $cache = Cache::factory('Markdown');
        $cachedHTML = $cache->load($cacheKey);

        //Check cache, if it's good use it instead
        if ($cachedHTML !== false) {
            $this->parsedHTML = $cachedHTML;
            return $this->parsedHTML;
        }

        //If empty save time by not attempting to render
        if (empty($this->value)) {
            return $this->value;
        }

        //Get rendered HTML
        $response = $renderer->getRenderedHTML($this->value);

        //Store response in memory
        $this->parsedHTML = $response;

        //Cache response to file system
        $cache->save($this->parsedHTML, $cacheKey);

        //Reset GFM
        if ($renderer instanceof GithubMarkdownRenderer) {
            GithubMarkdownRenderer::setUseGFM($beforeUseGFM);
        }

        //Return response
        return $this->parsedHTML;
    }

    /**
     * Renders the field used in the template
     * @return string HTML to be used in the template
     *
     * @see GISMarkdown::AsHTML()
     */
    public function forTemplate()
    {
        return $this->AsHTML();
    }

    /**
     * Sets the renderer for markdown fields to use
     * @param string $renderer Class Name of an implementation of IMarkdownRenderer
     */
    public static function setRenderer($renderer)
    {
        if (ClassInfo::classImplements($renderer, 'SilverStripe\\Markdown\\Renderer\\IMarkdownRenderer')) {
            self::$renderer = $renderer;
        } else {
            user_error('The renderer ' . $renderer . ' does not implement IMarkdownRenderer', E_USER_ERROR);
        }
    }

    /**
     * Gets the active mardown renderer
     * @return IMarkdownRenderer An implementation of IMarkdownRenderer
     */
    private function getRenderer()
    {
        if (!is_object($this->renderInst)) {
            $class = self::$renderer;
            $this->renderInst = new $class();
        }

        return $this->renderInst;
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return md5('Markdown_' . $this->tableName . '_' . $this->name . '_' . $this->value);
    }
}
