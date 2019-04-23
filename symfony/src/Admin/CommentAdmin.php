<?php
/**
 * Created by PhpStorm.
 * User: yamadote
 * Date: 1/12/19
 * Time: 11:30 AM.
 */

namespace App\Admin;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CommentAdmin extends AbstractAdmin
{
    public function toString($object)
    {
        return $object instanceof Comment
            ? $object->getContent()
            : 'Comment';
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('content', TextType::class)
            ->add('post', EntityType::class, [
                'class' => Post::class,
                'choice_label' => 'title',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
            ])
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('content');
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('content')
            ->add('post.title')
            ->add('user.email')
        ;
    }
}
