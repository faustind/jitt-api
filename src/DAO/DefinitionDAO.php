<?php

namespace Jitt\DAO;

use Jitt\Domain\Definition;
use Jitt\DAO\WordDAO;

class DefinitionDAO extends DAO {

  /**
   * @var WordDAO
  */
  private $WordDAO;

  /**
   * @param WordDAO
  */
  public function setWordDAO(WordDAO $WordDAO) {
    $this->wordDAO = $WordDAO;
  }

  /**
   * Finds all definitions for a word
   * Will return null if no definitions is found for $wordId
   * @param int the id of the word which definitions to look for
   * @return Jitt\Domain\Definition[]|null
  */
  public function findAllForWord($wordId){
    $sql = "select * from definitions where word_id = ? order by likes desc";
    $results = $this->getDb()->fetchAll($sql, array($wordId));

    if ($results) {
      $definitons = array();
      $word = $this->wordDAO->find($wordId);

      foreach ($results as $row) {
        $id = $row["id"];
        $row["word"] = $word;
        $definitions[$id] = $this->buildDomainObject($row);
      }

      // group definitions by language
      $jpDefs = array();
      $engDefs = array();

      foreach ($definitions as $definition) {
        if ($definition->getLanguage() == 'japanese'){
          $jpDefs[] = $definition;
        } else {
          $engDefs[] = $definition;
        }
      }

      $definitions = array(
        'eng_definitions' => $engDefs,
        'jp_definitions'  => $jpDefs
      );

      // return definitions grouped by language
      return $definitions;
    } else { // no definitions is registered for given $wordId
      return null;
    }
  }

  /**
   * Finds a definition by is id
   * @param int the id of the definition to look for
   * @return Jitt\Domain\Definition
  */
  public function find($id){
    $sql = "select * from definitions where id = ?";
    $row = $this->getDB()->fetchAssoc($sql, array($id));
    if($row && $row['word_id']){
      $row['word'] = $this->wordDAO->find($row['word_id']);
      return $this->buildDomainObject($row);
    } else {
      throw new \Exception("No Definition Matching id " . $id, 1);
    }
  }

  /**
   * Increments the definitions.likes column in the db
   * and return the updated definition data
   * or null if no definition matches $definitionId
   * @return Definition
  */
  public function incrementLikes($definitionId, $unlike = false){


    $increment = $unlike
      ? $this->getDb()->prepare('update definitions
      set likes = likes - 1 where id = ?')
      : $this->getDb()->prepare('update definitions
        set likes = likes + 1 where id = ?') ;

      $incremented = $increment->execute(array($definitionId));

      if ($incremented){
        try {
          return $this->find($definitionId);
        } catch (\Exception $e){
          return null;
        }
      } else {
        return null;
      }
  }

  /**
   * Adds or updates a definition in db
   * @var Jitt\Domain\Definition
  */
  public function add($definition){
    $definitionData = array(
      'word_id'   => $definition->getWord()->getWord_id(),
      'content'   => $definition->getContent(),
      'language'  => $definition->getLanguage(),
      'source'    => $definition->getSource()
    );

    if($definition->getId()){
      // update
      $this->getDb()->update(
        'definitions',
        $definitionData,
        array('id' => $definition->getId())
      );
    } else {
      // new insertion, set the id and return $definition
      $this->getDb()->insert('definitions', $definitionData);
      $id = $this->getDb()->lastInsertId();
      $insertedDefinition = $this->find($id);
    }

    // return the definition
    return $insertedDefinition;
  }
  /**
   * @inheritDoc
  */
  protected function buildDomainObject(array $row) {
    $definition = new Definition();

    $definition->setId($row["id"]);
    $definition->setWord($row["word"]);
    $definition->setContent($row["content"]);
    $definition->setLanguage($row["language"]);
    $definition->setSource($row["source"]);
    $definition->setLikes($row["likes"]);
    $definition->setSaved_date($row["saved_date"]);

    return $definition;
  }
}
