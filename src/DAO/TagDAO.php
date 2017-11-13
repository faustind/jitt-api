<?php

namespace Jitt\DAO;

use Jitt\Domain\Tag;

/**
 *
 */
class TagDAO extends DAO
{
    public function findAll()
    {
      $sql = "select * from tags";
      $results = $this->getDb()->fetchAll($sql);

      $tags = array();

      foreach ($results as $row) {
        $id = $row["tag_id"];
        $tags[$id] = $this->buildDomainObject($row);
      }
      return $tags;
    }

    public function findById($id){
      $sql = "select * from tags where tag_id = ?";
      $row = $this->getDB()->fetchAssoc($sql, array($id));
      if($row){
        return $this->buildDomainObject($row);
      } else {
        throw new \Exception("No Tgg Matching id " . $id, 1);

      }
    }

    public function findTagsForWordId($wordId){
      $sql = "select tags.*
              from tags
              inner join word_tag
                on word_tag.tag_id = tags.tag_id
              where word_tag.word_id = ?";
      $results = $this->getDb()->fetchAll($sql, array($wordId));

      $tags = array();

      foreach ($results as $row) {
        $id = $row["tag_id"];
        $tags[$id] = $this->buildDomainObject($row);
      }
      return $tags;
    }



    public function buildDomainObject(array $row)
    {
      $tag = new Tag();

      $tag->setId($row["tag_id"]);
      $tag->setTitle($row["title"]);
    //  $tag->setDescription = $row["description"] || '';

      return $tag;
    }
}
