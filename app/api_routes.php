<?php

use Symfony\Component\HttpFoundation\Request;
use Jitt\Domain\Definition;

// return all words by matching p_word to word, kana or translation
$app->get('/api/words/{p_word}', function ($p_word) use ($app) {

    $words = $app['dao.word']->findMatch($p_word);

    foreach ($words as $word) {

      $tags = $app['dao.tag']->findTagsForWordId($word->getWord_id());
      // prepare tags data necessary to the response
      $tags = array_map("tagData", $tags);


      $definitions = $app['dao.definition']->findAllForWord($word->getWord_id());

      // prepare definition's data necessary to the response
      $definitions['eng_definitions'] = array_map("defData", $definitions['eng_definitions']);
      $definitions['jp_definitions'] = array_map("defData", $definitions['jp_definitions']);


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

// return all words in db
$app->get('/api/words', function () use ($app) {

  $words = $app['dao.word']->findAll();

  foreach ($words as $word) {

    $tags = $app['dao.tag']->findTagsForWordId($word->getWord_id());
    // prepare tags data necessary to the response
    $tags = array_map("tagData", $tags);

    $definitions = $app['dao.definition']->findAllForWord($word->getWord_id());

    // prepare definition's data necessary to the response
    $definitions['eng_definitions'] = array_map("defData", $definitions['eng_definitions']);
    $definitions['jp_definitions'] = array_map("defData", $definitions['jp_definitions']);

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
     'error' => 'Couldn\'t increment likes of definition with id '. $id
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

  $definitionData = json_decode( $request->request->get('definition') );

  foreach ($definitionData as $key => $value) {
    if (empty($value)){
      return $app->json(
        array("error" => "Definition is missing parameter " . $key),
        400 // bad request response code
      );
    }
  }

  // Here the definition wordId, content, language and source are sets
  $newDefinition = new Definition();

  $newDefinition->setWord(
    $app['dao.word']->find($definitionData->word_id)
  );
  $newDefinition->setContent($definitionData->content);
  $newDefinition->setSource($definitionData->source);
  $newDefinition->setLanguage($definitionData->language);

  $newDefinition = $app['dao.definition']->add($newDefinition);

  $newDefinitionData = defData($newDefinition);

  $responseData['data'] = $newDefinitionData;

  return $app->json($responseData);
});

// save a new word to db
$app->post('/api/word', function (Request $request) use ($app){

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
