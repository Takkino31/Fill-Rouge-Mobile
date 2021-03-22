<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Transaction;
use App\Repository\UserRepository;
use App\Repository\FraisRepository;
use App\Repository\CompteRepository;
use App\Services\TransactionServices;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\UserAgenceFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserAgenceDepotFixtures extends Fixture
{
    private $userRepository;
    private $compteRepository;
    private $fraisRepository;
    private $transactionServices;

    public function __construct(UserRepository $userRepository, CompteRepository $compteRepository,FraisRepository $fraisRepository ,TransactionServices $transactionServices)
    {
        $this->userRepository = $userRepository;
        $this->compteRepository = $compteRepository;
        $this->fraisRepository = $fraisRepository;
        $this->transactionServices = $transactionServices;
    }
    public function load(ObjectManager $manager)
    {
        $userAgences = $this->userRepository->findBy(["profil"=>4]);
        for ($i=0; $i < count($userAgences); $i++) { 
            for ($j=0; $j < 3; $j++) { 
                $userAgence = $userAgences[$i];
                $compte = $userAgence->getAdminAgence()->getCompte();

                $clientDepot = new Client();
                $clientDepot->setNom("Client depot userAgence $i.$j");
                $clientDepot->setTelephone("7700000".$i.$j);
                $clientDepot->setCni("12511999033".$i.$j);
                $manager->persist($clientDepot);

                $clientRetrait = new Client();
                $clientRetrait->setNom("Client Retrait userAgence $i.$j");
                $clientRetrait->setTelephone("771111111".$i.$j);
                $manager->persist($clientRetrait);

                $montant = rand (2000,2500000);

                $compte->setSolde($compte->getSolde()+$montant);
                $manager->persist($compte);
                
                $frais = $this->transactionServices->calculateFrais($montant);
                $frais = $this->transactionServices->commission($frais);

                $codeTransaction = $this->transactionServices->generateTransactionCode();

                $transactionDepot = new Transaction();

                $transactionDepot->setMontant($montant);
                $transactionDepot->setCodeTransaction($codeTransaction);
                $transactionDepot->setFrais($frais["etat"]);
                $transactionDepot->setFraisEtat($frais["etat"]);
                $transactionDepot->setFraisDepot($frais["depot"]);
                $transactionDepot->setFraisRetrait($frais["retrait"]);
                $transactionDepot->setFraisSysteme($frais["system"]);

                $transactionDepot->setCompte($compte);
                $transactionDepot->setClientDepot($clientDepot);
                $transactionDepot->setClientRetrait($clientRetrait);
                $transactionDepot->setUserDepot($userAgence);
                $manager->persist($transactionDepot);

                $userAgence->addTransactionsDepot($transactionDepot);
                $manager->persist($userAgence);
            }
        }
        $manager->flush();
    }
    public function getDependencies () {
        return array(UserAgenceFixtures::class);
    }
}
