<?php

class ViddlerTag extends ViddlerBase {
  public $name;
  
  public function videos($page=1, $per_page=200) {
    $xml = $this->api->videos_listbytag($this->name, $page, $per_page, $this->sessionid);
    $videos = array();
    
    foreach($xml['video_list']['video'] as $vid) {
      $v = new ViddlerVideo();
      $v->parseXml($vid);
      $v->sessionid = $this->sessionid;
      $videos[] = $v;
    }
    
    return $videos;
  }
}

?>