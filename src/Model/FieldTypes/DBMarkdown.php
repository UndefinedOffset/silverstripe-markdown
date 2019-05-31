<?php

namespace UndefinedOffset\Markdown\Model\FieldTypes;

use Psr\SimpleCache\CacheInterface;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\FieldType\DBText;
use UndefinedOffset\Markdown\Renderer\GithubMarkdownRenderer;
use UndefinedOffset\Markdown\Renderer\IMarkdownRenderer;

/**
 * Class Markdown
 * @package UndefinedOffset\Markdown\Model\FieldTypes
 */
class DBMarkdown extends DBText
{
    /**
     * @var string
     */
    private $cache_key;

    /**
     * @var int Cache length for field value in seconds
     */
    private static $cache_seconds = 86400;

    /**
     * {@inheritDoc}
     */
    private static $casting = [
        'AsHTML' => 'HTMLText',
        'Markdown' => 'DBText',
    ];

    /**
     * @var string
     */
    private static $escape_type = 'xml';

    /**
     * @var string
     */
    private static $renderer = GithubMarkdownRenderer::class;

    /**
     * @var IMarkdownRenderer
     */
    private $renderInst;

    /**
     * @var string
     */
    protected $parsedHTML = false;


    /**
     * Checks cache to see if the contents of this field have already been loaded from github, if they haven't
     * then a request is made to the github api to render the markdown
     * @param bool $useGFM Use Github Flavored Markdown or render using plain markdown defaults to false just like
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
        $cache = Injector::inst()->get(CacheInterface::class . '.dbMarkdownCache');

        //Check cache, if it's good use it instead
        if ($cachedHTML = $cache->get($cacheKey)) {
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
        $cache->set($cacheKey, $this->parsedHTML, $this->config()->get('cache_seconds'));

        //Reset GFM
        if ($renderer instanceof GithubMarkdownRenderer && isset($beforeUseGFM)) {
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
        if (ClassInfo::classImplements($renderer, IMarkdownRenderer::class)) {
            self::$renderer = $renderer;
        } else {
            user_error('The renderer ' . $renderer . ' does not implement IMarkdownRenderer', E_USER_ERROR);
        }
    }

    /**
     * Gets the active markdown renderer
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
        if (!$this->cache_key) {
            $this->setCacheKey();
        }

        return $this->cache_key;
    }

    /**
     * @param null $key
     * @return $this
     */
    public function setCacheKey($key = null)
    {
        if ($key === null) {
            $key = md5('Markdown_' . $this->tableName . '_' . $this->name . '_' . $this->value);
        }

        $this->cache_key = $key;

        return $this;
    }
}
