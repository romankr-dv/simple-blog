<?php

namespace App\Admin;

use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserAdmin extends AbstractAdmin
{
    public function toString($object)
    {
        return $object instanceof User
            ? $object->getEmail()
            : 'User';
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('email')
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('email')
        ;
    }

    public function prePersist($object)
    {
        $this->encodePassword($object);
    }

    public function preUpdate($object)
    {
        $this->encodePassword($object);
    }

    private function encodePassword($object)
    {
        /** @var User $user */
        $user = $object;

        $passwordEncoder = $this->getConfigurationPool()->getContainer()->get('security.password_encoder');

        $password = $user->getPassword();

        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $password
            )
        );
    }
}
