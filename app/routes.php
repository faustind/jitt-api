<?php
use Symfony\Component\HttpFoundation\Request;
use Jitt\Domain\Word;
use Jitt\Form\Type\WordType;
// Home page
$app->get('/', function () use ($app){

    $words = $app['dao.word']->findall();
    return $app['twig']->render('words-list.html.twig',array('words' => $words));
})->bind('word-list');

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
      'word' => $word,
      'dbtags' => $app['dao.tag']->findAll(),
      'wordForm' => $wordForm->createView()
    ));
})->bind('word-details');
