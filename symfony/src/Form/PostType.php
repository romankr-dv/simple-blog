<?php
/**
 * Created by PhpStorm.
 * User: yamadote
 * Date: 1/16/19
 * Time: 8:07 PM.
 */

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description', null, ['attr' => ['class' => 'ckeditor']])
            ->add('content', null, ['attr' => ['class' => 'ckeditor']])
            ->add('category', EntityType::class, [
                'class' => Category::class, 'choice_label' => 'title',
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'title',
                'expanded'  => true,
                'multiple'  => true,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
