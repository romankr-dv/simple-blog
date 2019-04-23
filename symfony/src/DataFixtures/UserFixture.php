<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    public const USER_USER_REFERENCE = 'user-user';
    public const ADMIN_USER_REFERENCE = 'user-admin';

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $items = [
            [
                'email' => 'roman-kr-2017@ukr.net',
                'password' => 'password',
                'roles' => [],
                'reference' => self::USER_USER_REFERENCE,
            ],
            [
                'email' => 'admin.user@email.com',
                'password' => 'password',
                'roles' => ['ROLE_ADMIN'],
                'reference' => self::ADMIN_USER_REFERENCE,
            ],
        ];

        foreach ($items as $item) {
            $user = new User();
            $manager->persist($user);
            $user->setEmail($item['email']);
            $user->setRoles($item['roles']);
            $user->setApiToken(uniqid($user->getSalt()));
            $user->setPassword($this->encoder->encodePassword($user, $item['password']));

            if ($item['reference'] !== null) {
                $this->setReference($item['reference'], $user);
            }
        }

        $manager->flush();
    }
}
