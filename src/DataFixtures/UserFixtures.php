<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $data = [
            [
                'name' => 'userPI17',
                'email' => 'user@test.com',
                'password' => 'user@test.com',
                'roles' => ['ROLE_USER'],
            ],
            [
                'name' => 'adminPI17',
                'email' => 'admin@test.com',
                'password' => 'admin@test.com',
                'roles' => ['ROLE_ADMIN'],
            ],
        ];
        foreach ($data as $value) {
            $user = $this->createUser($value);
            $manager->persist($user);
        }
        $manager->flush();
    }

    private function createUser(array $data): User
    {
        $user = new User();

        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $data['password']
        ));
        $user->setRoles($data['roles']);

        $this->addReference($data['name'], $user);

        return $user;
    }
}
