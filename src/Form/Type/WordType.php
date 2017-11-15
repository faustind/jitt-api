<?php

namespace Jitt\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class WordType extends AbstractType
{

  public function buildForm(FormBuilderInterface $builder, array $options){
    $builder
    ->add('word', TextType::class)
    ->add('kana', TextType::class, array(
      'required' => false
    ))
    ->add('translation', TextType::class);

    $tags = array(); // all tags in database
    $activeTags = array(); // tags set for the word
    foreach ($options["tagChoices"] as $id => $tag) {
      // push each tag to the available tags array
      $tags[] = $tag;
      if($builder->getData()->hasTag($tag)){
        $activeTags[] = $tag;
      }

    }


    $builder->add('tags', ChoiceType::class, array(
        'expanded' => true,
        'multiple' => true,
        'choices' => $tags,
        'data' => $activeTags,
        'choice_label' => function($tag, $key, $index){
            return ucfirst($tag->getTitle());
          },
     ))
    ;
  }

  public function getName(){
    return 'word';
  }

  public function configureOptions(OptionsResolver $resolver){
   $resolver->setRequired(array(
      'tagChoices',
     ));
  }
}
