<?php

namespace Jitt\DAO;

use Jitt\Domain\Word;

class WordDAO extends DAO {


  public function findAll(){
    $sql = "select * from words order by word_id";
    $results = $this->getDb()->fetchAll($sql);

    $words = array();

    foreach ($results as $row) {
      $word_id = $row["word_id"];
      $words[$word_id] = $this->buildDomainObject($row);
    }
    return $words;
  }

  public function find($id){
    $sql = "select * from words where word_id = ?";
    $row = $this->getDB()->fetchAssoc($sql, array($id));
    if($row){
      return $this->buildDomainObject($row);
    } else {
      throw new \Exception("No Word Matching id " . $id, 1);
    }
  }

  public function findMatch($word){
    $find = $this->getDB()->prepare("select * from words where word = :wd or kana = :wd or translation = :wd");
    $find->execute(array(':wd' => $word));
    $results = $find->fetchAll();

    if($results){
      $words = array();
      foreach ($results as $row) {
        $word_id = $row["word_id"];
        $words[$word_id] = $this->buildDomainObject($row);
      }
      return $words;

    } else {
      throw new \Exception("No Word Matching " . $word, 1);
    }
  }

  public function saveWord($word){
    $wordData = array(
      'word' => $word->getWord(),
      'kana' => $word->getKana(),
      'translation' => $word->getTranslation(),
      'saved_date' => date("Y-m-d H:i:s"),
    );

    if ($word->getWord_id()){
      // update
      $this->getDb()->update(
        'words',
         $wordData,
         array('word_id' => $word->getWord_id())
       );
    } else {
      // insert
      $this->getDb()->insert('words', $wordData);
      $id = $this->getDb()->lastInsertId();
      $word->setWord_id($id);
    }

    $newTags = $word->getTags();
    $formerTags = $word->getFormerTags();

    // all of the new tags that weren't present
    if (!empty($newTags) && empty($formerTags)){
      // insert newTags and remove nothing
      $tagsToInsertForWord = $newTags;
      $tagsToRemoveForWord = array();

    } else if(!empty($newTags) && !empty($formerTags)) {
      // insert tags in newTags not in formerTags
      $tagsToInsertForWord = $this->tagsDifference($newTags, $formerTags);
      // remove tags in formerTags not in newTags
      $tagsToRemoveForWord = $this->tagsDifference($formerTags, $newTags);

    } else if(empty($newTags) && !empty($formerTags)) {
      // insert nothing and remove all former tags
      $tagsToInsertForWord = array();
      $tagsToRemoveForWord = $formerTags;
    } else if(empty($newTags) && empty($formerTags)) {
      // nothing to do
    }


    if (!empty($tagsToRemoveForWord)){
      foreach ($tagsToRemoveForWord as $tag) {
        $delete = $this->getDb()
          ->prepare('delete from word_tag
                    where word_id = :word_id and tag_id = :tag_id');

        $delete->execute(array(
          'word_id' => $word->getWord_id(),
          'tag_id' => $tag->getId()
        ));

      }
    }

    if (!empty($tagsToInsertForWord)){
      foreach ($tagsToInsertForWord as $tag) {
        $insert = $this->getDb()
          ->prepare('insert into word_tag (word_id, tag_id)
        values (:word_id, :tag_id)');

        $insert->execute(array(
          'word_id' => $word->getWord_id(),
          'tag_id' => $tag->getId()
        ));

      }

    }
  }

  /**
  * return elements in tags1 not present in tags2
  */
  private function tagsDifference($tags1, $tags2){
    $diff=array();
         foreach($tags1 as $t1){
             if(!in_array($t1,$tags2)){
                 array_push($diff,$t1);
             }
         }
         return $diff;
  }

  protected function buildDomainObject(array $row) {
    $word = new Word();
    $word->setWord_id($row["word_id"]);
    $word->setWord($row["word"]);
    $word->setKana($row["kana"]);
    $word->setTranslation($row["translation"]);
    $word->setSaved_date($row["saved_date"]);

    return $word;
  }
}
