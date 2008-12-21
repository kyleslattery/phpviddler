<?php

class ViddlerVideo extends ViddlerBase {
  public static $attributes = array('author', 'id', 'title', 'length_seconds', 'width', 'height',
                                    'description', 'view_count', 'upload_time', 'comment_count',
                                    'tags', 'url', 'thumbnail_url', 'permalink', 'update_time',
                                    'permissions', 'comments');
  
  /**************
   DATA FUNCTIONS
   **************/
   
  // Find a video given an id
  // returns ViddlerVideo object
  public static function find($id, $sessionid=false) {
    $video = new ViddlerVideo();
    $video->id = $id;
    
    if($sessionid) $video->sessionid = $sessionid;
    
    $video->fetch();
    return $video;
  }
  
  public static function findByUser($username, $sessionid=false) {
    $user = new ViddlerUser();
    $user->username = $username;
    $user->sessionid = $sessionid;
    
    return $user->videos();
  }
  
  public static function findByTag($tag, $sessionid=false) {
    $t = new ViddlerTag();
    $t->name = $tag;
    $t->sessionid = $sessionid;
    
    return $t->videos();
  }
  
  public static function featured($sessionid=null) {
    $vb = new ViddlerBase();
    $xml = $vb->api->videos_listfeatured();
    $videos = array();
    
    foreach($xml['video_list']['video'] as $vid) {
      $v = new ViddlerVideo();
      $v->parseXML($vid);
      $v->sessionid = $sessionid;
      $videos[] = $v;
    }
    
    return $videos;
  }
  
  // Update attributes using $this->id
  public function fetch() {
    if($this->id) {
      $xml = $this->api->video_details($this->id, $this->sessionid);
      $this->parseXml($xml);
    }
  }
  
  // Assigns attributes from XML
  public function parseXml($xml) {
    // TODO: Fill in comments, permission, etc.
    if(isset($xml['video'])) $video = $xml['video'];
    else $video = $xml;
    
    foreach($video as $key => $value) {
      if(!is_array($value) && in_array($key, ViddlerVideo::$attributes)) {
        $this->{$key} = $value;
      }
    }
  }
  
  public function save() {
    if(!$this->sessionid) throw("You need a sessionid to do that!");

    $data = array();
    foreach(ViddlerVideo::$attributes as $attr) {
      if(isset($this->{$attr})) $data[$attr] = $this->{$attr};
    }
    $data['video_id'] = $this->id;
    $data['sessionid'] = $this->sessionid;
    
    $result = $this->api->video_setdetails($data);
    print_r($result);
  }
}
?>