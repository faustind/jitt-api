<?php

namespace Jitt\Domain;

/**
 * jitt word class
 */
class Definition
{
  /**
   * @var int
  */
  private $id;

  /**
   * @var Jitt\Domain\Word
  */
  private $word;

  /**
   * @var string
  */
  private $content;

  /**
   * @var string
  */
  private $language;

  /**
   * @var string
  */
  private $source;

  /**
   * @var int
  */
  private $likes;

  /**
   * @var string
  */
  private $saved_date;

  /**
   * The id from db. null if not set.
   * @return int
  */
  public function getId(){
    return $this->word_id ? $this->word_id : null;
  }

  /**
   * The word for which this is a definition
   * @return Jitt\Domain\Word
  */
  public function getWord(){
    return $this->word;
  }

  /**
   * The "actual" content of this definition
   * @return string
  */
  public function getContent(){
    return $this->content;
  }

  /**
   * The language in which the content is written
   * @return string
  */
  public function getLanguage(){
    return $this->language;
  }

  /**
   * The source of the definition
   * @return string
  */
  public function getSource(){
    return $this->source;
  }

  /**
   * The number of likes
   * @return int
  */
  public function getLikes(){
    return $this->likes;
  }

  /**
   * The date the definition has been saved to db
   * @return string
  */
  public function getSaved_date(){
    return $this->saved_date;
  }

  /**
   * Sets the id
   * @param int
  */
  public function setId($id){
    $this->word_id = $id;
  }

  /**
   * Sets the word for which this is a Definition
   * @param Jitt\Domain\Word
  */
  public function setWord($word){
    $this->word = $word;
  }

  /**
   * Sets the "actual" content of the Definition
   * @param string
  */
  public function setContent($content){
    $this->content = $content;
  }

  /**
   * Sets the language in which the content is written
   * @param string
  */
  public function setLanguage($language){
    $this->language = $language;
  }

  /**
   * Sets the source of the content
   * @param string
  */
  public function setSource($source){
    $this->source = $source;
  }

  /**
   * Sets the number of likes
   * @param int
  */
  public function setLikes($likes){
    $this->likes = $likes;
  }

  /**
   * Sets the date the definition has been saved to db
   * @param string
  */
  public function setSaved_date($saved_date){
    $this->saved_date = $saved_date;
  }
}
