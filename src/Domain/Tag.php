<?php

namespace Jitt\Domain;

/**
 * jitt Tag class
 */
class Tag
{
  /**
   * @var int
  */
  private $id;

  /**
   * @var string
  */
  private $title;

  /**
   * @var string
  */
  private $description;

  /**
   * The id of the tag in db or null
   * @return int|null
  */
  public function getId(){
    return $this->id ? $this->id : null;
  }

  /**
   * The title of the tag
   * @return string
  */
  public function getTitle(){
    return $this->title;
  }

  /**
   * The description of the tag
   * @return string
  */
  public function getDescription(){
    return $this->description;
  }

  /**
   * Sets the id
   * @param int
  */
  public function setId($id){
    $this->id = $id;
  }

  /**
   * Sets the title
   * @param string
  */
  public function setTitle($title){
    $this->title = $title;
  }

  /**
   * Sets the description
   * @param string
  */
  public function setDescription($id){
    $this->id = $id;
  }
}
