<?php

class ViddlerBase {  
  public $api;
  public $sessionid;
  
  function __construct() {
    $this->api = new Phpviddler(VIDDLER_API_KEY);
  }
  
  function auth($username, $password) {
    $auth = $this->api->user_authenticate($username, $password);
    return $auth['auth']['sessionid'];
  }
}

?>