<?php

class GithubMarkdownRenderer implements IMarkdownRenderer {

	protected $useGFM = false;
	protected $useBasicAuth = false;
	protected $username = "";
	protected $password = "";

	public function __construct($useGFM = false) {
		$this->useGFM = $useGFM;
	}

	public function isSupported() {
		$supported = function_exists("curl_version");
		if (!$supported) {
			$supported = "CURL not found.";
		}
		return $supported;
	}

	public function getRenderedHTML($value) {
   		//Build object to send
        $sendObj=new stdClass();
        $sendObj->text=$value;
        $sendObj->mode=($this->useGFM ? 'gmf':'markdown');
        $content=json_encode($sendObj);

        //Build headers
        $headers = array("Content-type: application/json", "User-Agent: curl");
        if ($this->useBasicAuth) {
            $username = $this->username;
            $password = $this->password;
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

        return $response;
	}

    /**
     * Globally enable or disable github flavored markdown
     * @param {bool} $val Boolean true to enable false otherwise
     * @default true
     */
    public function setUseGFM($value) {
        $this->useGFM=$value;
    }
    
    /**
     * Gets if github flavored markdown is enabled or not globally
     * @return {bool} Returns boolean true if github flavored markdown is enabled false otherwise
     */
    public function getUseGFM() {
        return $this->useGFM;
    }

    /**
     * Sets whether or not to include the Authorization header in GitHub API requests
     * @param {bool} $use Boolean true to enable false otherwise
     */
    public function useBasicAuth($use = true) {
        $this->useBasicAuth = $use;
    }

    /**
     * Sets the GitHub username for Basic Auth
     * @param {string} $username Your GitHub username
     */
    public function setGithubUsername($username) {
        $this->username = $username;
    }

    /**
     * Sets the GitHub password for Basic Auth
     * @param {string} $password Your GitHub password
     */
    public function setGithubPassword($password) {
        $this->password = $password;
    }	
	
}