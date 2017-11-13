<?php
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

// save a new word to db
$app->post('/api/word', function (Request $request) use ($app){

});

// increments the likes of definition matching id
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
$app->post('api/definition/', function() use ($app){

});

// handle options request from users
$app->options('*', function() use ($app){

});

function defData($definition){
  return array(
    'id' => $definition->getId(),
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
