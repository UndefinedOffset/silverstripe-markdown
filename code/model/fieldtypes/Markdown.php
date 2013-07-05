<?php
class Markdown extends Text {
    public static $casting=array(
                            'AsHTML'=>'HTMLText',
                            'Markdown'=>'Text'
                        );
    
    
    protected static $useGFM=false;
    protected $parsedHTML=false;
    public static $escape_type='xml';

    protected static $useBasicAuth = false;
    protected static $username = "";
    protected static $password = "";
    
    
    /**
     * Checks cache to see if the contents of this field have already been loaded from github, if they haven't then a request is made to the github api to render the markdown
     * @param {bool} $useGFM Use Github Flavored Markdown or render using plain markdown defaults to false just like how readme files are rendered on github
     * @return {string} Markdown rendered as HTML
     */
    public function AsHTML($useGFM=false) {
        //$this->value
        if($this->parsedHTML!==false) {
            return $this->parsedHTML;
        }
        
        
        if($useGFM==false) {
            $useGFM=self::$useGFM;
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
        
        
        //If empty save time by not calling github's api
        if(empty($this->value)) {
            return $this->value;
        }
        
        
        //Build object to send
        $sendObj=new stdClass();
        $sendObj->text=$this->value;
        $sendObj->mode=($useGFM ? 'gmf':'markdown');
        $content=json_encode($sendObj);

        //Build headers
        $headers = array("Content-type: application/json", "User-Agent: curl");
        if (self::$useBasicAuth) {
            $username = self::$username;
            $password = self::$password;
            $encoded = base64_encode("$username:$password");
            $headers[] = "Authorization: Basic $encoded";
        }        
        
        //Build curl request to github's api
        $curl=curl_init('https://api.github.com/markdown');
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        
        //Send request and verify response
        $response=curl_exec($curl);
        $status=curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if($status!=200) {
            user_error("Error: Call to api.github.com failed with status $status, response $response, curl_error ".curl_error($curl).", curl_errno ".curl_errno($curl), E_USER_WARNING);
        }
        
        //Close curl connection
        curl_close($curl);
        
        
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
     * Globally enable or disable github flavored markdown
     * @param {bool} $val Boolean true to enable false otherwise
     * @default true
     */
    public static function setUseGFM($value) {
        self::$useGFM=$value;
    }
    
    /**
     * Gets if github flavored markdown is enabled or not globally
     * @return {bool} Returns boolean true if github flavored markdown is enabled false otherwise
     */
    public static function getUseGFM() {
        return self::$useGFM;
    }

    /**
     * Sets whether or not to include the Authorization header in GitHub API requests
     * @param {bool} $use Boolean true to enable false otherwise
     */
    public static function useBasicAuth($use = true) {
        self::$useBasicAuth = $use;
    }

    /**
     * Sets the GitHub username for Basic Auth
     * @param {string} $username Your GitHub username
     */
    public static function setGithubUsername($username) {
        self::$username = $username;
    }

    /**
     * Sets the GitHub password for Basic Auth
     * @param {string} $password Your GitHub password
     */
    public static function setGithubPassword($password) {
        self::$password = $password;
    }
}
?>