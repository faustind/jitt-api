<?php

namespace Jitt\Domain;

/**
 * jitt word class
 */
class Word
{

  /**
   * @var int
  */
  private $word_id;

  /**
   * The original word. Generally in Japanese
   * @var string
  */
  private $word;

  /**
   * The Japanese reading of the original word
   * @var string
  */
  private $kana;

  /**
   * The translation in English of the original word
   * @var string
  */
  private $translation;

  /**
   * The date of last edition
   * @var string
  */
  private $saved_date;

  /**
   * An array of the tags currently set to the word
   *
   * These tags are used to update those that are currently set in db
   * Tags currently set in db can be accessed through $formerTags
   *
   * @var \Jitt\Domain\Tag[]
   */
  private $tags;

  /**
   * An array of tags fetched from db
   *
   * These are the current tags set for the word in db
   *
   * @var \Jitt\Domain\Tag[]
   */
  private $formerTags;

  /**
   * The word id in db or null if not setTags
   * @var int|null
   */
  public function getWord_id(){
    return $this->word_id ? $this->word_id : null;
  }

  /**
   * The original word
   *
   * Generally in Japanese. But can also be in English for compagnies names
   * and initials or acronyms for example.
   *
   * @return string
   */
  public function getWord(){
    return $this->word;
  }

  /**
   * The reading of the word in hiragana
   * @return string
   */
  public function getKana(){
    return $this->kana;
  }

  /**
   * The English translation of the word
   * @return string
   */
  public function getTranslation(){
    return $this->translation;
  }

  /**
   * The date of last edition
   * @return string
   */
  public function getSaved_date(){
    return $this->saved_date;
  }

  /**
   * The tags currently set to this word
   * @return Jitt\Domain\Tag[]
   */
  public function getTags(){
    return $this->tags;
  }

  /**
   * The tags currently set to this word in db
   * @return Jitt\Domain\Tag[]
   */
  public function getFormerTags(){
    return $this->formerTags;
  }

  /**
   * Wheter or not a $tag is in the array of tags currently set to the word
   * @return boolean true if it is, false if not
  */
  public function hasTag($tag){
    return in_array($tag, $this->tags);
  }

  /**
   * Sets the word_id
   * @param int
  */
  public function setWord_id($id){
    $this->word_id = $id;
  }

  /**
   * Sets the original word
   * @param string
  */
  public function setWord($word){
    $this->word = $word;
  }

  /**
   * Sets the reading
   * @param string
  */
  public function setKana($kana){
    $this->kana = $kana;
  }

  /**
   * Sets the translation
   * @param string
  */
  public function setTranslation($translation){
    $this->translation = $translation;
  }

  /**
   * Sets the date of last edition
   * @param string
  */
  public function setSaved_date($saved_date){
    $this->saved_date = $saved_date;
  }

  /**
   * Sets updated tags
   * @param Jitt\Domain\Tag[]
  */
  public function setTags($tags){
    $this->tags = $tags;
  }

  /**
   * Sets former tags
   * @param Jitt\Domain\Tag[]
  */
  public function setFormerTags($tags){
    $this->formerTags = $tags;
  }
}
