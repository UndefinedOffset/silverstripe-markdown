<?php
class Markdown extends Text {
    public static $casting=array(
        'AsHTML'=>'HTMLText',
        'Markdown'=>'Text'
    );
    
    protected $parsedHTML=false;
    public static $escape_type='xml';

    private static $renderer = null;
    
    /**
     * Checks cache to see if the contents of this field have already been loaded from github, if they haven't then a request is made to the github api to render the markdown
     * @param {bool} $useGFM Use Github Flavored Markdown or render using plain markdown defaults to false just like how readme files are rendered on github
     * @return {string} Markdown rendered as HTML
     */
    public function AsHTML($useGFM=false) {
        
        if($this->parsedHTML!==false) {
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
            $renderer->setUseGFM($useGFM);
        }
        
        //Init cache stuff
        $cacheKey=md5('Markdown_'.$this->tableName.'_'.$this->name.':'.$this->value);
        $cache=SS_Cache::factory('Markdown');
        $cachedHTML=$cache->load($cacheKey);
        
        //Check cache, if it's good use it instead
        if($cachedHTML!==false) {
            $this->parsedHTML=$cachedHTML;
            return $this->parsedHTML;
        }      
        
        //If empty save time by not attempting to render
        if(empty($this->value)) {
            return $this->value;
        }
        
        //Get rendered HTML
        $response = $renderer->getRenderedHTML($this->value);
        
        //Store response in memory
        $this->parsedHTML=$response;
        
        //Cache response to file system
        $cache->save($this->parsedHTML, $cacheKey);
                
        //Return response
        return $this->parsedHTML;
    }
    
    
    /**
     * Renders the field used in the template
     * @return {string} HTML to be used in the template
     *
     * @see GISMarkdown::AsHTML()
     */
    public function forTemplate() {
        return $this->AsHTML();
    }

    /**
     * Sets the renderer for markdown fields to use
     * 
     * @param IMarkdownRenderer An implementation of IMarkdownRenderer
     */
    public static function setRenderer(IMarkdownRenderer $renderer) {
        self::$renderer = $renderer;
    }

    private function getRenderer() {
        if (!self::$renderer) {
            self::$renderer = new GithubMarkdownRenderer();
        }
        return self::$renderer;
    }
}
?>