<?php

namespace App\DataFixtures;

use App\Entity\Compte;
use App\DataFixtures\AgenceFixtures;
use App\Repository\AgenceRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CompteFixtures extends Fixture
{
    private $agenceRepository;
    public function __construct(AgenceRepository $agenceRepository)
    {
        $this->agenceRepository = $agenceRepository;
    }
    public function load(ObjectManager $manager)
    {
        $agences = $this->agenceRepository->findAll();
        for ($i=0; $i < count($agences); $i++) { 
            $compte = new Compte();
            $compte->setNumeroCompte("COMPTE$i");
            $compte->setSolde(rand(700000,5000000));
            $agence = $agences[$i];
            $compte->setAgence($agence);
            $agence->setCompte($compte);
            // dd($compte);
            $manager->persist($agence);

            $manager->persist($compte);
        }

        $manager->flush();
    }
    public function getDependencies () {
        return array(AgenceFixtures::class);
    }
}
