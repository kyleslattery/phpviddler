<?php
include_once('../phpviddler.php');

class ViddlerBase {
  public $api;
  
  function __construct() {
    $this->api = new Phpviddler(VIDDLER_API_KEY);
  }
}

?>