<?php

namespace Jitt\Domain;

/**
 * jitt Tag class
 */
class Tag
{
  private $id;
  private $title;
  private $description;

  public function getId(){
    return $this->id;
  }

  public function getTitle(){
    return $this->title;
  }

  public function getDescription(){
    return $this->description;
  }

  public function setId($id){
    $this->id = $id;
  }
  public function setTitle($title){
    $this->title = $title;
  }
  public function setDescription($id){
    $this->id = $id;
  }
}
