<?php

namespace Jitt\DAO;

use Jitt\Domain\Word;

class WordDAO extends DAO {

  /**
   * Finds all words in db
   * Definitions are grouped by language
   * @return Jitt\Domain\Word[]
  */
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

  /**
   * Finds the word with matching $id
   * raise an exception if no match found
   * @param int $id
   * @return Jitt\Domain\Word
  */
  public function find($id){
    $sql = "select * from words where word_id = ?";
    $row = $this->getDB()->fetchAssoc($sql, array($id));
    if($row){
      return $this->buildDomainObject($row);
    } else {
      throw new \Exception("No Word Matching id " . $id, 1);
    }
  }

  /**
   * Finds all words with word | kana | translation matching $word
   * @param string
   * @return Jitt\Domain\Word[]
  */
  public function findMatch($word){
    $find = $this->getDB()->prepare("select * from words where word like :wd or kana like :wd or translation like :wd");
    $find->execute(array(':wd' => '%'.$word.'%'));
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


  public function wordsHavingTags($tagsId){
    $sql = "SELECT words.* from words
    	inner JOIN word_tag on word_tag.word_id = words.word_id WHERE word_tag.tag_id in (". implode(',', $tagsId).")
      GROUP by word_tag.word_id
    	 having COUNT(word_tag.tag_id) >= ?";

    $tags_tt = count($tagsId);
    $find = $this->getDB()->prepare($sql);
    //$find->bindValue(':num', $tags_tt, \PDO::PARAM_INT);
    $find->execute(
      array(sizeof($tagsId))
    );
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
  /**
   * Saves the word in db. if the word has an id, update it.
   * @param Jitt\Domain\Word
   * @return int the id of the word in db
  */
  public function saveWord($word){
    $wordData = array(
      'word' => $word->getWord(),
      'kana' => $word->getKana(),
      'translation' => $word->getTranslation(),
      //'saved_date' => date("Y-m-d H:i:s"),
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

    $tagsToRemoveForWord = array();
    $tagsToInsertForWord = array();

    // all of the new tags that weren't present
    if (!empty($newTags) && empty($formerTags)){
      // insert newTags and remove nothing
      $tagsToInsertForWord = $newTags;

    } else if(!empty($newTags) && !empty($formerTags)) {
      // insert tags in newTags not in formerTags
      $tagsToInsertForWord = $this->tagsDifference($newTags, $formerTags);
      // remove tags in formerTags not in newTags
      $tagsToRemoveForWord = $this->tagsDifference($formerTags, $newTags);

    } else if(empty($newTags) && !empty($formerTags)) {
      // insert nothing and remove all former tags
      $tagsToRemoveForWord = $formerTags;
    } else if(empty($newTags) && empty($formerTags)) {
      // nothing to do
    }

    // remove word_tag references for tags to remove
    if (!empty($tagsToRemoveForWord)){
      foreach ($tagsToRemoveForWord as $tag) {
        // TODO: Move the preparation before foreach
        $delete = $this->getDb()
          ->prepare('delete from word_tag
                    where word_id = :word_id and tag_id = :tag_id');

        $delete->execute(array(
          'word_id' => $word->getWord_id(),
          'tag_id' => $tag->getId()
        ));
      }
    }

    // insert word_tag references for tags to insert
    if (!empty($tagsToInsertForWord)){
      foreach ($tagsToInsertForWord as $tag) {
        // TODO: Move the preparation before foreach
        $insert = $this->getDb()
          ->prepare('insert into word_tag (word_id, tag_id)
        values (:word_id, :tag_id)');

        $insert->execute(array(
          'word_id' => $word->getWord_id(),
          'tag_id' => $tag->getId()
        ));
      }
    }

    return $word->getWord_id();
  }

  /**
  * Returns an array of elements in tags1 not present in tags2
  * @param Jitt\Domain\Tag[] $tag1
  * @param Jitt\Domain\Tag[] $tag2
  * @return Jitt\Domain\Tag[]
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

  /**
   * @inheritDoc
  */
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
