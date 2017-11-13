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
  public function findWord($word){
    $sql = "select * from words where word = ?";
    $row = $this->getDB()->fetchAssoc($sql, array($word));
    if($row){
      return $this->buildDomainObject($row);
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
    if (!empty($newTags)){
      $tagsToInsertForWord = array_diff($newTags, $formerTags);
    }


    // all tags that have been removed in the new selection
    if (!empty($formerTags)){
        $tagsToRemoveForWord = array_diff($formerTags, $newTags);
    }


    if (!empty($tagsToRemoveForWord)){
      foreach ($tagsToRemoveForWord as $tag) {
        $delete = $this->getDb()->prepare('delete from word_tag where word_id = :word_id and tag_id = :tag_id');

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
