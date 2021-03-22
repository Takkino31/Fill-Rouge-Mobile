<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Profile;
use App\Repository\AgenceRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAgenceFixtures extends Fixture
{
    private $agenceRepository;
    private $encoder;

    public function __construct(AgenceRepository $agenceRepository,UserPasswordEncoderInterface $encoder)
    {
        $this->agenceRepository = $agenceRepository;
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {   
        $profil = new Profile();
        $profil->setLibelle('utilisateur_agence');
        $manager->persist($profil);

        $agences = $this->agenceRepository->findAll();
        foreach ($agences as $agence) {
            $nbrUsers = rand(1,6);
            for ($i=0; $i < $nbrUsers; $i++) { 
                $faker = Factory::create();
                    $user=new User();
                    $password = 'utilisateur_agence';
                    $password = $this->encoder->encodePassword($user, $password);
                    $user->setNom($faker->lastname);
                    $user->setPrenom($faker->firstName);
                    $user->setEmail($faker->email);
                    $user->setUsername($faker->username);
                    $user->setPassword($password);
                    $user->setProfil($profil);
                    $user->setTelephone(rand(770000000,789999999));
                    $user->setAdminAgence($agence);
                    $manager->persist($user);
            }
        }

        $manager->flush();
    }
    public function getDependencies () {
        return array(AgenceFixtures::class);
    }
}
