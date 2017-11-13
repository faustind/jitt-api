<?php

namespace Jitt\Domain;

/**
 * jitt word class
 */
class Word
{

  private $word_id;
  private $word;
  private $kana;
  private $translation;
  private $saved_date;

  /**
  * @var \Jitt\Domain\Tag[]
  */
  private $tags;
  /**
  * @var \Jitt\Domain\Tag[]
  */
  private $formerTags;

  public function getWord_id(){
    return $this->word_id;
  }
  public function getWord(){
    return $this->word;
  }
  public function getKana(){
    return $this->kana;
  }
  public function getTranslation(){
    return $this->translation;
  }
  public function getSaved_date(){
    return $this->saved_date;
  }

  public function getTags(){
    return $this->tags;
  }
  public function getFormerTags(){
    return $this->formerTags;
  }

  public function hasTag($tag){
    return in_array($tag, $this->tags);
  }

  public function setWord_id($id){
    $this->word_id = $id;
  }
  public function setWord($word){
    $this->word = $word;
  }
  public function setKana($kana){
    $this->kana = $kana;
  }
  public function setTranslation($translation){
    $this->translation = $translation;
  }
  public function setSaved_date($saved_date){
    $this->saved_date = $saved_date;
  }
  public function setTags($tags){
    $this->tags = $tags;
  }

  public function setFormerTags($tags){
    $this->formerTags = $tags;
  }
}
