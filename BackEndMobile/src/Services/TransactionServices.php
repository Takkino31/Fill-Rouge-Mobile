<?php

namespace App\Services;

use DateTime;
use App\Entity\Client;
use App\Entity\Transaction;
use App\Repository\UserRepository;
use App\Repository\FraisRepository;
use App\Repository\CompteRepository;
use App\Repository\CommissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TransactionServices{
    
    private $serializer;
    private $validator;
    private $encoder;
    private $userRepository;
    private $compteRepository;
    private $entityManager;
    private $transactionRepository;
    private $commissionRepository;
    private $fraisRepository;

    public function __construct(
            SerializerInterface $serializer,
            UserPasswordEncoderInterface $encoder,
            ValidatorInterface $validator,
            UserRepository $userRepository,
            CompteRepository $compteRepository,
            EntityManagerInterface $entityManager,
            TransactionRepository $transactionRepository,
            CommissionRepository $commissionRepository,
            FraisRepository $fraisRepository
        )
    {
        $this->serializer = $serializer;
        $this->encoder = $encoder;
        $this->validator =$validator;
        $this->userRepository = $userRepository;
        $this->compteRepository=$compteRepository;
        $this->entityManager=$entityManager;
        $this->transactionRepository=$transactionRepository;
        $this->commissionRepository=$commissionRepository;
        $this->fraisRepository=$fraisRepository;
     
    }
    
    public function generateTransactionCode(){
        return substr(md5(time()), 0, 9);
    }

    public function verifTelephone($tel){
        $exp = "#^(70|76|77|78)[0-9]{7}$#";
        if (preg_match($exp,$tel)){
            return true;
        }
        else{
            return false;
        }
    }

    public function validateCNI($cni){
        $exp = "#^(1|2)[0-9]{12}$#";
        if (preg_match($exp,$cni)){
            return true;
        }
        else {
            return false;
        }
    }

    public function commission($frais){
        $tabFrais = $this->fraisRepository->find(1);

        $fraisEtat = $frais*$tabFrais->getFraisEtat();
        $fraisDepot = $frais*$tabFrais->getFraisDepot();
        $fraisRetrait = $frais*$tabFrais->getFraisRetrait();
        $fraisSystem = $frais*$tabFrais->getFraisSysteme();
        return $commission= [
            "frais" => $frais,
            "etat"=>$fraisEtat,
            "depot"=>$fraisDepot,
            "retrait"=>$fraisRetrait,
            "system"=>$fraisSystem
        ];
    }
    public function calculateFrais($montant){
        $tabFrais = $this->commissionRepository->findAll();
        foreach ($tabFrais as $frais ){
            if ($frais->getInf()<$montant && $frais->getSup()>=$montant) {
                return $frais->getFrais();
            }
            if($montant > 2000000){
                return $montant*(2/100);
            }
        }
    }
    
    public function addTransaction($request,$idUser){

        $data = $request->toArray();
        $compte = $this->userRepository->find($idUser)->getAdminAgence()->getCompte();
        $compte->setSolde($compte->getSolde()+$data["montant"]);
        $this->entityManager->persist($compte);

        $clientDepot = new Client();
        $clientDepot->setNom($data["senderName"]);
        $clientDepot->setTelephone($data["senderTelephone"]);
        $clientDepot->setCni($data["senderCni"]);
        $this->entityManager->persist($clientDepot);

        $clientRetrait = new Client();
        $clientRetrait->setNom($data["receiverName"]);
        $clientRetrait->setTelephone($data["receiverTelephone"]);
        $this->entityManager->persist($clientRetrait);

        $userDepot = $this->userRepository->find($idUser);

        $frais = $this->calculateFrais($data["montant"]);
        $frais = $this->commission($frais);

        $codeTransaction = $this->generateTransactionCode();

        $transactionDepot = new Transaction();
        $transactionDepot->setMontant($data["montant"]);
        $transactionDepot->setCodeTransaction($codeTransaction);
        $transactionDepot->setFrais($frais["etat"]);
        $transactionDepot->setFraisEtat($frais["etat"]);
        $transactionDepot->setFraisDepot($frais["depot"]);
        $transactionDepot->setFraisRetrait($frais["retrait"]);
        $transactionDepot->setFraisSysteme($frais["system"]);

        $transactionDepot->setCompte($compte);
        $transactionDepot->setClientDepot($clientDepot);
        $transactionDepot->setClientRetrait($clientRetrait);
        $transactionDepot->setUserDepot($userDepot);
        $this->entityManager->persist($transactionDepot);

        $userDepot->addTransactionsDepot($transactionDepot);
        $this->entityManager->persist($userDepot);

        $errors=$this->validator->validate($transactionDepot);
            if (count($errors)>0) {
                return $errors;
            }

        return $transactionDepot;
    } 


    

    public function updateTransaction($id, $request){
    $transaction=$this->transactionRepository->find($id);
    if (empty($transaction)) {
            return $transaction;
    }
    dd("test");
    $data = $request->toArray();
    $montant= $transaction->getMontant();
    $solde=$transaction->getCompte()->getSolde()-$montant;
    $compte=$transaction->getCompte();
    $compte->setSolde($solde);
    $this->entityManager->persist($compte);

    $clientRetrait= new Client();
    $clientRetrait->setNomComplet($data ["nomComplet"]);
    $clientRetrait->setTelephone($data ["telephone"]);
    $clientRetrait->setnumCNI($data ["numCNI"]);
    $this->entityManager->persist($clientRetrait);
    
    $userRetrait=$this->userRepository->find($data["userId"]);
    
    $dateRetrait=new DateTime();
    
    $transaction->setClientRetrait($clientRetrait);
    $transaction->setUserRetrait($userRetrait);
    $transaction->setDateRetrait($dateRetrait);
    return $transaction;
    }

    public function getTransactionPeriod($id, $request){
        $data = $request->toArray();
        $dateDebut=strtotime($data["dateDebut"]);
        $dateDebut = date(‘d-m-Y’,strtotime($data["dateDebut"]));
        dd($dateDebut);
        $transaction=$this->transactionRepository->findBy([
            "id"=>$id,
            "dateDepot"=>$dateDebut
        ]);
        dd($transaction);
        $transactions = $user->getTransactions();
        dd($transactions);
    }

    public function getAllTransactions($idUser){
        $idCompte = $this->userRepository->find($idUser)->getAdminAgence()->getCompte()->getId();
        $transactions = $this->transactionRepository->findBy(["compte"=>$idCompte]);
        return $transactions;
    }

    
    public function putTransaction($request,$idUser,$id){
        $data = $request->toArray();
        $receverCni = $data["receiverCni"];
        
        $transaction = $this->transactionRepository->find($id);
        if (($transaction->getDateRetrait())!==NULL) {
            return "déjà retiré";
        }
        $userRetrait= $this->userRepository->find($idUser);
        $clientRetrait = $transaction->getClientRetrait();
        $clientRetrait->setCni($receverCni);
        $this->entityManager->persist($clientRetrait);

        $transaction->setDateRetrait(new DateTime());
        $transaction->setUserRetrait($userRetrait);
        $this->entityManager->persist($transaction);
        // $this->entityManager->flush();
        return $transaction;

    }

}
