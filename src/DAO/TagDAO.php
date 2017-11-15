<?php

namespace Jitt\DAO;

use Jitt\Domain\Tag;

class TagDAO extends DAO
{
    /**
     * Finds all the tags in the db
     * @return Jitt\Domain\Tag[]
    */
    public function findAll() {
      $sql = "select * from tags";
      $results = $this->getDb()->fetchAll($sql);

      $tags = array();

      foreach ($results as $row) {
        $id = $row["tag_id"];
        $tags[$id] = $this->buildDomainObject($row);
      }
      return $tags;
    }

    /**
     * Finds a tag with id $id or throw an Exception
     * @param int the id of the tag to look for in db
     * @return Jitt\Domain\Tag[]|\Exception
    */
    public function findById($id) {
      $sql = "select * from tags where tag_id = ?";
      $row = $this->getDB()->fetchAll($sql, array($id));
      if($row){
        return $this->buildDomainObject($row);
      } else {
        throw new \Exception("No Tag Matching id " . $id, 1);
      }
    }

    /**
     * Finds all tags for a given wordDAO
     * @param int id of the word for which to get findTagsForWordId
     * @return Jitt\Domain\Tag[]
    */
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

    /**
     * @inheritDoc
    */
    public function buildDomainObject(array $row)
    {
      $tag = new Tag();

      $tag->setId($row["tag_id"]);
      $tag->setTitle($row["title"]);
    //  $tag->setDescription = $row["description"] || '';

      return $tag;
    }
}
