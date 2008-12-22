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
  
  /*****************
   DISPLAY FUNCTIONS
   *****************/
   
  public function embed($options=array()) {
    $default_options = array(
        'type'   => 'player',
        'html_id'     => 'viddler_' . $this->id
      );
    
    $o = array_merge($default_options, $options);

    // if no height or width set, set width to default
    if(!isset($o['width']) && !isset($o['height'])) $o['width'] = 450;
    
    // auto size correctly
    if(!isset($o['width'])) {
      // scale width
      $size_array = $this->autoSize($o['height'], $o['type'], 'height');
      $o['width'] = $size_array['width'];
    } elseif(!isset($o['height'])) {
      // scale height
      $size_array = $this->autoSize($o['width'], $o['type'], 'width');
      $o['height'] = $size_array['height'];
    }
    
    $code = <<<EOC
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{$o['width']}" height="{$o['height']}" id="{$o['html_id']}">
  <param name="movie" value="http://www.viddler.com/{$o['type']}/{$this->id}/" />
  <param name="allowScriptAccess" value="always" />
  <param name="allowFullScreen" value="true" />
  <embed src="http://www.viddler.com/{$o['type']}/{$this->id}/" width="{$o['width']}" height="{$o['height']}" type="application/x-shockwave-flash" allowScriptAccess="always" allowFullScreen="true" name="{$o['html_id']}"></embed>
</object>
EOC;

    return $code;
  }
  
  // axis is the defined axis
  private function autoSize($size, $type='player', $axis='height') {
    $ratio = $this->width/$this->height;
    
    if($axis == 'height') {
      $height = $size;
      $width  = floor($ratio*$height);
    } else {
      $width  = $size;
      $height = floor($width/$ratio);
    }
    
    if($type == 'player') $height += 42;
    else $height += 21;
    
    return array('height' => $height, 'width' => $width);
  }
  
  public static function recordEmbed($token=null, $sessionid=null) {
    $v = new ViddlerVideo();
    
    if(!$token) {
      $v->sessionid = $sessionid;
      $token = $v->api->video_getrecordtoken($sessionid);
    }
    
    return $v->api->video_getRecordEmbed($token);
  }
}
?>