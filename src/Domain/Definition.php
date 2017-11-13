<?php

namespace Jitt\Domain;

/**
 * jitt word class
 */
class Definition
{

  private $id;
  private $word;
  private $content;
  private $language;
  private $source;
  private $likes;
  private $saved_date;

  public function getId(){
    return $this->word_id;
  }
  public function getWord(){
    return $this->word;
  }
  public function getContent(){
    return $this->content;
  }
  public function getLanguage(){
    return $this->language;
  }
  public function getSource(){
    return $this->source;
  }
  public function getLikes(){
    return $this->likes;
  }
  public function getSaved_date(){
    return $this->saved_date;
  }

  public function setId($id){
    $this->word_id = $id;
  }
  public function setWord($word){
    $this->word = $word;
  }
  public function setContent($content){
    $this->content = $content;
  }
  public function setLanguage($language){
    $this->language = $language;
  }
  public function setSource($source){
    $this->source = $like;
  }
  public function setLikes($likes){
    $this->likes = $likes;
  }
  public function setSaved_date($saved_date){
    $this->saved_date = $saved_date;
  }
}
