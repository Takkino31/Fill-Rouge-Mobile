<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
            $ref= ["adminsystem","adminagence","caissier"];
            for ($i=0; $i < count($ref); $i++) { 
                for ($j=0; $j < 5; $j++) { 
                    $faker = Factory::create();
                    $user=new User();
                    $profil=$this->getReference($ref[$i]);
                    $password = $ref[$i];
                    $password = $this->encoder->encodePassword($user, $password);
                    $user->setNom($faker->lastname);
                    $user->setPrenom($faker->firstName);
                    $user->setEmail($faker->email);
                    $user->setUsername($faker->username);
                    $user->setPassword($password);
                    $user->setProfil($profil);
                    $user->setTelephone('77777777');
                    
                    $manager->persist($user);
                }
            }
        $manager->flush();
    }

    public function getDependencies () {
        return array(ProfilFixtures::class);
    }
    
}
