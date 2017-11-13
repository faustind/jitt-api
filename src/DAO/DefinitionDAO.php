<?php

namespace Jitt\DAO;

use Jitt\Domain\Word;

class DefinitionDAO extends DAO {

  private $WordDAO;

  public function setWordDAO(WordDAO $WordDAO) {
    $this->wordDAO = $WordDAO;
  }

  public function findAllByWord($wordId){
    $sql = "select * from definitions where word_id = ? group by language";
    $results = $this->getDb()->fetchAll($sql);

    $words = array();

    foreach ($results as $row) {
      $word_id = $row["word_id"];
      $words[$word_id] = $this->buildWord($row);
    }
    return $words;
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
