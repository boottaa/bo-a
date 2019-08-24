<?php

namespace App\Form;

use App\Entity\News;
use App\Form\Type\TagsInputType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddEditNewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label'  => 'Название', 'attr' => ['autofocus' => true]])
            ->add('description', TextareaType::class, [
                'label'  => 'Краткое содержание',
                'help' => 'В кратком содержании не допускается использование Markdown разметки и HTML-тегов, только простой текст.'
            ])
            ->add('text', TextareaType::class, ['label' => 'Содержание'])
//            ->add('created_at')
//            ->add('updated_at')
            ->add('tags', TagsInputType::class, ['label' => 'Теги'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
