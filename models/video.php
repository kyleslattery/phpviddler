<?php
include_once('../phpviddler.php');

class ViddlerVideo extends ViddlerBase {
  // attributes
  var $author, $id, $title, $length_seconds, $width, $height,
      $description, $view_count, $upload_time, $comment_count,
      $tags, $url, $thumbnail_url, $permalink, $update_time,
      $permissions, $comments;
      
  public static function find($id) {
    $video = new ViddlerVideo();
    $video->id = $id;
    $video->update();
    return $video;
  }
  
  public function update() {
    if($this->id) {
      $xml = $this->api->video_details($this->id);
      $this->parseXml($xml);
    }
  }
  
  public static function parseXml($xml) {
    print_r($xml);
  }
}

ViddlerVideo::find('202b6dc5');
?>