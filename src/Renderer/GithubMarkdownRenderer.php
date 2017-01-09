<?php

namespace UndefinedOffset\Markdown\Renderer;

use stdClass;

class GithubMarkdownRenderer implements IMarkdownRenderer
{
    private static $useGFM=false;
    private static $useBasicAuth=false;
    private static $username=null;
    private static $password=null;


    /**
     * Detects if curl is supported which is required for this renderer
     * @return {bool} Detects if curl is supported
     */
    public function isSupported()
    {
        $supported=function_exists('curl_version');
        if (!$supported) {
            $supported='CURL not found';
        }

        return $supported;
    }

    /**
     * Returns the supplied Markdown as rendered HTML
     * @param {string} $markdown The markdown to render
     * @return {string} The rendered HTML
     */
    public function getRenderedHTML($value)
    {
        //Build object to send
        $sendObj=new stdClass();
        $sendObj->text=$value;
        $sendObj->mode=(self::$useGFM ? 'gmf':'markdown');
        $content=json_encode($sendObj);

        //Build headers
        $headers=array("Content-type: application/json", "User-Agent: curl");
        if (self::$useBasicAuth) {
            $encoded=base64_encode(self::$username.':'.self::$password);
            $headers[]="Authorization: Basic $encoded";
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
        if ($status!=200) {
            user_error("Error: Call to api.github.com failed with status $status, response $response, curl_error ".curl_error($curl).", curl_errno ".curl_errno($curl), E_USER_WARNING);
        }

        //Close curl connection
        curl_close($curl);

        return $response;
    }

    /**
     * Globally enable or disable github flavored markdown
     * @param {bool} $val Boolean true to enable false otherwise
     */
    public static function setUseGFM($value = true)
    {
        self::$useGFM=$value;
    }

    /**
     * Gets if github flavored markdown is enabled or not globally
     * @return {bool} Returns boolean true if github flavored markdown is enabled false otherwise
     */
    public static function getUseGFM()
    {
        return self::$useGFM;
    }

    /**
     * Sets whether or not to include the Authorization header in GitHub API requests, both parameters are required to enable basic auth
     * @param {string} $username Github Username
     * @param {string} $password Github Password
     */
    public function useBasicAuth($username = false, $password = false)
    {
        self::$useBasicAuth=($username!==false && $password!==false);
        self::$username=$username;
        self::$password=$password;
    }
}
