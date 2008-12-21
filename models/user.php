<?php

class ViddlerUser extends ViddlerBase {
  public static $attributes = array('username', 'first_name', 'last_name', 'about_me',
                                    'avatar', 'age', 'video_upload_count', 'video_watch_count',
                                    'homepage', 'gender', 'company', 'city', 'friend_count',
                                    'favourite_video_count');
  
  public function videos($page=1, $per_page=200) {
    $xml = $this->api->videos_listbyuser($this->username, $page, $per_page, $this->sessionid);
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