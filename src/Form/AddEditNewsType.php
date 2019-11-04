<?php

namespace App\Form;

use App\Entity\News;
use App\Form\Type\TagsInputType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class AddEditNewsType
 * @package App\Form
 */
class AddEditNewsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('img', FileType::class, [
                'mapped' => false,
                'label' => 'Изображение',
                'attr' => ['class' => 'uploadPreview'],
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new Image()
                ]
            ])
            ->add('title', TextType::class, [
                'label'  => 'Название',
                'attr' => ['autofocus' => true],
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'max' => 255,
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label'  => 'Краткое содержание',
                'attr' => ['style' => "height: 300px"],
                'help' => 'В кратком содержании не допускается использование Markdown разметки и HTML-тегов, только простой текст.',
                'constraints' => [
                    new Length([
                        'min' => 200,
                        'max' => 2000,
                    ])
                ]
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Содержание',
                'attr' => ['class' => 'jodit', 'style' => "height: 500px"],
                'constraints' => [
                    new Length([
                        'min' => 700,
                        'max' => 64000,
                    ])
                ]
            ])
            ->add('publishedAt', DateTimeType::class, [
                'label' => 'Дата и время публикации',
                'help' => 'Укажите дату и время для отложенной побликации',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy H:mm',
                'attr' => [
                    'class' => 'sonataDatetime'
                ]
            ])
            ->add('tags', TagsInputType::class, [
                'label' => 'Теги',
                'attr' => ['class' => 'tokenfield']
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class
        ]);
        parent::configureOptions($resolver);
    }
}
