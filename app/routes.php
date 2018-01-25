<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Jitt\Domain\Word;
use Jitt\Domain\Definition;
use Jitt\Form\Type\WordType;
// Home page
$app->get('/words/{search}', function ($search) use ($app){
    $words = [];

    if ($search){
      $words = $app['dao.word']->findMatch($search);
    } else {
      $words = $app['dao.word']->findall();
    }

    foreach ($words as $word) {
        $wordTags = $app['dao.tag']->findTagsForWordId($word->getWord_id());
        $word->setTags($wordTags);
    }
    return $app['twig']->render('words-list.html.twig',array('words' => $words));
})->bind('word-list')->value('search', null);

$app->match('/word-detail/{id}', function ($id, Request $request) use ($app){
    $word = $app['dao.word']->find($id);
    $wordTags = $app['dao.tag']->findTagsForWordId($id);

    $word->setTags($wordTags);
    $word->setFormerTags($wordTags);

    $wordForm = $app["form.factory"]->create(WordType::class, $word, array(
      'tagChoices' => $app['dao.tag']->findAll()
    ));
    $wordForm->handleRequest($request);
    if ($wordForm->isSubmitted() && $wordForm->isValid()) {
      $app['dao.word']->saveWord($word);
      $app['session']->getFlashBag()->add('success', 'The word was successfully updated.');
    }
    return $app['twig']->render('word-details.html.twig',array(
      // 'word' => $word,
      // 'dbtags' => $app['dao.tag']->findAll(),
      'wordForm' => $wordForm->createView()
    ));
})->bind('word-details');


// routes definitions for the api
include ('api_routes.php');
