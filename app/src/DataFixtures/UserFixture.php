<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    /**
     * Just created first users to test the app
     * It can be removed
     *
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('dar@kamon.lt');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'mikiss'));
        $user->setFullName('Dar Tomukas Juciukas');
        $user->setPhone('860144030');
        $manager->persist($user);

        $user2 = new User();
        $user2->setEmail('pop@kamon.lt');
        $user2->setPassword($this->passwordEncoder->encodePassword($user2, 'mikiss'));
        $user2->setFullName('Tomukas Juciukas');
        $user2->setPhone('864700556');
        $manager->persist($user2);

        $manager->flush();
    }
}
