<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Users;
use App\lib\Auth;

class UsersFixtures extends Fixture
{
    private $passwordEncoder;


    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager)
    {
        $user = new Users();
        $user->setName('Internations');
        $user->setLastName('Company');
        $user->setEmail('internations@gmail.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setApiToken('hireme');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'hireme'));

        $manager->persist($user);
        $manager->flush();
    }
}
