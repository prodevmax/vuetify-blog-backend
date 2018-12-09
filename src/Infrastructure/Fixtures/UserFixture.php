<?php

namespace App\Infrastructure\Fixtures;

use App\Domain\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User('admin');
        $password = $this->encoder->encodePassword($user, 'pass12345');
        $user->setPassword($password);
        $manager->persist($user);
        for ($i = 0; $i < 10; $i++) {
            $user = new User('user_' . $i);
            $password = $this->encoder->encodePassword($user, 'pass' . $i);
            $user->setPassword($password);
            $manager->persist($user);
        }
        $manager->flush();
    }
}