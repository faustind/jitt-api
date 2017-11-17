<?php

use Symfony\Component\HttpFoundation\Request;
use Jitt\Domain\Definition;
use Jitt\Domain\Word;
use Jitt\Domain\Tag;

// return all words by matching p_word to word, kana or translation
$app->get('/api/words/match/{p_word}', function ($p_word) use ($app) {


    try {
        $words = $app['dao.word']->findMatch($p_word);
    } catch (\Exception $e){
      return $app->json(array(
        'error' => $e.getMessage()
      ), 404, array('Access-Control-Allow-Origin' => '*'));
    }


    foreach ($words as $word) {

      $tags = $app['dao.tag']->findTagsForWordId($word->getWord_id());
      // map tags to their data usin tagData() helper defined at the end of the file
      $tags = array_map("tagData", $tags);


      $definitions = $app['dao.definition']->findAllForWord($word->getWord_id());

      // prepare definition's data necessary to the response
      $definitions = array_merge(
        array_map("defData", $definitions['eng_definitions']),
        array_map("defData", $definitions['jp_definitions'])
      );


      $responseData['data'][] = array (
          'word_id' => $word->getWord_id(),
          'word' => $word->getWord(),
          'kana' => $word->getKana(),
          'translation' => $word->getTranslation(),
          'saved_date' => $word->getSaved_date(),
          'tags' => $tags,
          'definitions' => $definitions
        );
    }

     return $app->json(
       $responseData, 200,
       array(
         'Access-Control-Allow-Origin'   => '*',
         // 'Access-Control-Allow-Headers'  => 'Origin, X-Requested-With, Content-Type, Accept, Authorization',
         // 'Access-Control-Allow-Methods'  => 'GET, POST, PUT'
       )
     );
});

// return all words in db
$app->get('/api/words', function () use ($app) {

  $words = $app['dao.word']->findAll();

  foreach ($words as $word) {

    $tags = $app['dao.tag']->findTagsForWordId($word->getWord_id());
    // prepare tags data necessary to the response
    $tags = array_map("tagData", $tags);

    $definitions = $app['dao.definition']->findAllForWord($word->getWord_id());

    // prepare definition's data necessary to the response
    $definitions = array_merge(
      array_map("defData", $definitions['eng_definitions']),
      array_map("defData", $definitions['jp_definitions'])
    );

    $responseData['data'][] = array (
        'word_id' => $word->getWord_id(),
        'word' => $word->getWord(),
        'kana' => $word->getKana(),
        'translation' => $word->getTranslation(),
        'saved_date' => $word->getSaved_date(),
        'tags' => $tags,
        'definitions' => $definitions
      );
  }

   return $app->json($responseData);
});


// return all tags in db
$app->get('/api/tags', function () use ($app) {
  $tags = $app['dao.tag']->findAll();

  $responseData = array(
    'data' => array()
  );

  foreach ($tags as $tag) {
    $responseData['data'][] = array(
        'id' => $tag->getId(),
        'title' => $tag->getTitle()
    );
  }

 return $app->json($responseData);
});



// increments the likes of definition matching $id
$app->get('/api/definition/like/{id}', function($id) use ($app){
 if ($likes = $app['dao.definition']->incrementLikes($id)){
   return $app->json(array(
     'data' => array(
       'likes' => $likes
     )
   ));
 } else {
   return $app->json(array(
     'error' => 'Cannot increment likes of definition with id '. $id
   ));
 }
});

// add definition for a words
$app->post('api/definition/add', function(Request $request) use ($app){
  if (!$request->request->has('definition')) {
    return $app->json(
      array("error" => "Missing parameter definition"),
      400 // bad request response code
    );
  }

  $definitionData = json_decode( $request->request->get('definition'), true );

  foreach ($definitionData as $key => $value) {
    if (empty($value)){
      return $app->json(
        array("error" => "Definition is missing key " . $key),
        400 // bad request response code
      );
    }
  }

  // Here the definition wordId, content, language and source are sets
  $newDefinition = new Definition();

  $newDefinition->setWord(
    $app['dao.word']->find($definitionData['word_id'])
  );
  $newDefinition->setContent($definitionData['content']);
  $newDefinition->setSource($definitionData['source']);
  $newDefinition->setLanguage($definitionData['language']);

  $newDefinition = $app['dao.definition']->add($newDefinition);

  $newDefinitionData = defData($newDefinition);

  $responseData['data']['definition'] = $newDefinitionData;

  return $app->json($responseData);
});

// save a new word to db
$app->post('/api/word/add', function (Request $request) use ($app){
  if (!$request->request->has('word')){
    return $app->json(
      array("error" => "Missing parameter word"),
      400 // bad request response code
    );
  }

  $wordData = json_decode( $request->request->get('word'), true );

  // Check validity of word
  if(!$wordData["word"] || empty($wordData["word"])){
    return $app->json(
      array("error" => "Parameter word is missing key word"),
      400 // bad request response code
    );
  }

  // Check validity of tags
  $hasTags = false;
  // verified tags will be kept in checkedTags
  $checkedTags = array();
  if ($wordData['tags'] && !empty($wordData['tags'])){
    foreach ($wordData['tags'] as $index => $tag) {
      if (!$tag['tag_id'] || empty($tag['tag_id']) ||
          !$tag['title']  || empty($tag['title'])
        ) {
          return $app->json(
            array("error" => "Invalid data type in tags at index ". $index),
            400 // bad request response code
          );
      }
      $checkedTags[] = $app['dao.tag']->find($tag['tag_id']);
    }
    $hasTags = true;
  }


  // check validity of jp_definitions
  $hasDefinitions = false;
  if ($wordData['definitions'] && !empty($wordData['definitions'])) {
    foreach ($wordData['definitions'] as $index => $definition) {
      if (!$definition['content']  || empty($definition['content']) ||
          !$definition['language'] || empty($definition['language']) ||
          !$definition['source']   || empty($definition['source'])
        ) {
          return $app->json(
            array("error" => "Invalid data type in defininitions at index ". $index),
            400 // bad request response code
          );
      }
    }
    $hasDefinitions = true;
  }

  // Provided data are valid

  // Prepare the word for insertion
  $word = new Word();

  $word->setWord($wordData['word']);
  // Set kana and tranlation and tags if present
  if ($wordData['kana'] && !empty($wordData['kana'])){
    $word->setKana($wordData['kana']);
  }
  if ($wordData['translation'] && !empty($wordData['translation'])){
    $word->setTranslation($wordData['translation']);
  }
  if($hasTags && !empty($checkedTags)){
    $word->setTags($checkedTags);
  }

  // save the word with its tags
  try {
    $wordId = $app['dao.word']->saveWord($word);
  } catch (Exception $e) {
    return $app->json(
      array(
        'error' => "Error while saving word : " .$e->getMessage(),
      ),
      500 // internal server error
    );
  }

  $insertedWord = $app['dao.word']->find($wordId);
  $insertedWord->setTags($app['dao.tag']->findTagsForWordId($wordId));

  // Begin preparation of response data
  $responseData['data']['word'] = array(
    'word_id' => $insertedWord->getWord_id(),
    'word' => $insertedWord->getWord(),
    'kana' => $insertedWord->getKana(),
    'translation' => $insertedWord->getTranslation(),
    'saved_date' => $insertedWord->getSaved_date(),
    'tags' => array_map("tagData", $insertedWord->getTags())
  );

  // Save the definitions if any
  if($hasDefinitions){
    $newDefs = array();
    foreach ($wordData['definitions'] as $definition) {
      $newDef = definitionObject($definition, $insertedWord);
      // keep the saved definition data
      $newDefs[] = $app['dao.definition']->add($newDef);
    }
    // add definitions to responseData
    $responseData['data']['word']['definitions'] = array_map("defData", $newDefs);
  }

  // Send response: informations on the insertedWord
  return $app->json($responseData);
});


// Handles options request
$app->options('{anyRoute}', function() use ($app){
  // Send response with empty body and appropiate headers
  return $app->json(array(), 204, array(
    'Access-Control-Allow-Origin'   => '*',
    'Access-Control-Allow-Headers'  => 'Origin, X-Requested-With, Content-Type, Accept, Authorization',
    'Access-Control-Allow-Methods'  => 'GET, POST, PUT'
  ));
})->assert("anyRoute", ".*");


// Helper functions

function defData($definition){
  return array(
    'id' => $definition->getId(),
    'word_id' => $definition->getWord()->getWord_id(),
    'content' => $definition->getContent(),
    'language' => $definition->getLanguage(),
    'source' => $definition->getSource(),
    'likes' => $definition->getLikes(),
    'saved_date' => $definition->getSaved_date()
  );
}

function tagData($tag){
    return array(
      'tag_id' => $tag->getId(),
      'title' => $tag->getTitle()
    );
}

/**
 * Creates a Tag object with provided id and title
 * @param array having keys title and tag_id
 * @return Jitt\Domain\Tag
*/
function tagObject(array $tag){
  $tagEntity = new Tag();

  $tagEntity->setTitle($tag['title']);
  $tagEntity->setId($tag['tag_id']);

  return $tagEntity;
}

/**
 * Creates a Definition object from array of definition data and word
 * @param array $definition array of definition data
 * @param Jitt\Domain\Word $word optional the word for which to create the definition
*/
function definitionObject(array $definition, Word $word = null){
  $newDef = new Definition();

  $newDef->setContent($definition['content']);
  $newDef->setLanguage($definition['language']);
  $newDef->setSource($definition['source']);
  if($word){ $newDef->setWord($word); }

  return $newDef;
}
