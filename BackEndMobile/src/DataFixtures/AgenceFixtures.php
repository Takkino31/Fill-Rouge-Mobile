<?php

namespace App\DataFixtures;

use App\Entity\Agence;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use App\DataFixtures\ProfileFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AgenceFixtures extends Fixture
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $adminAgences = $this->userRepository->findBy(["profil"=>2]);
        $key = 1;
        for ($i=0; $i < count($adminAgences); $i++) { 
            $agence = new Agence();
            $agence->setAdresse("Adresse agence $key");
            $agence->setTelephone("33".rand(900,999).rand(10,99).rand(10,99));
            $agence->setLatitude("Latitude agence $key");
            $agence->setLongitude("Longitude agence $key");

            $adminAgence = $adminAgences[$i];
            $adminAgence->setAdminAgence($agence);
           
            $manager->persist($agence);
            $manager->persist($adminAgence);
            $key++;
        }
        $manager->flush();
    }
    public function getDependencies () {
        return array(ProfileFixtures::class);
    }
}
