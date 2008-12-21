<?php
include_once('../phpviddler.php');

class ViddlerVideo extends ViddlerBase {
  // attributes
  var $author, $id, $title, $length_seconds, $width, $height,
      $description, $view_count, $upload_time, $comment_count,
      $tags, $url, $thumbnail_url, $permalink, $update_time,
      $permissions, $comments;
  
  /**************
   DATA FUNCTIONS
   **************/
   
  // Find a video given an id
  // returns ViddlerVideo object
  public static function find($id) {
    $video = new ViddlerVideo();
    $video->id = $id;
    $video->fetch();
    return $video;
  }
  
  // Update attributes using $this->id
  public function fetch() {
    if($this->id) {
      $xml = $this->api->video_details($this->id);
      $this->parseXml($xml);
    }
  }
  
  // Assigns attributes from XML
  public function parseXml($xml) {
    // TODO: Fill in comments, permission, etc.
    foreach($xml['video'] as $key => $value) {
      if(!is_array($value)) {
        $this->{$key} = $value;
      }
    }
  }
}

$vid = ViddlerVideo::find('202b6dc5');
?>