<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Services\TransactionServices;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionController extends AbstractController
{
    private $transactionServices;
    private $transactionRepository;
    private $entityManager;

    public function __construct(TransactionServices $transactionServices, TransactionRepository $transactionRepository,
    EntityManagerInterface $entityManager)
    {
        $this->transactionServices = $transactionServices;
        $this->transactionRepository = $transactionRepository;
        $this->entityManager=$entityManager;
    }

    /**
     * @Route("/api/admin/user/transactions", name="transactionDepot",methods={"POST"})
     */

    public function transactionDepot(Request $request){
        $idUser= $this->getUser()->getId();
        $transaction= $this->transactionServices->addTransaction($request,$idUser);
        if ($transaction instanceof Transaction) {
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
            return $this->json($transaction,Response::HTTP_OK);
        }
        else {
            return $this->json($transaction,Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/admin/user/transactions", name="allTransactions",methods={"GET"})
     */

    public function allTransactions(){
        $idUser= $this->getUser()->getId();
        $transactions= $this->transactionServices->getAllTransactions($idUser);
        if (!empty($transactions)) {
            return $this->json($transactions,Response::HTTP_OK);
        }
        else {
            return $this->json($transaction,Response::HTTP_BAD_REQUEST);
        }
    }
    
    /**
     * @Route("/api/admin/user/transactions/{codeTransaction}", name="transactionsAllAgence",methods={"GET"})
     */

    public function transactionsAllAgence($codeTransaction,TransactionRepository $transactionRepository,SerializerInterface $serializer){
        $transaction = $transactionRepository->findOneBy(["codeTransaction" => $codeTransaction]);
        $transaction = $serializer->normalize($transaction,"json",["groups"=>"transactionByCode"]);
        return $this->json($transaction,Response::HTTP_OK);
    }

    /**
     * @Route("/api/admin/user/transactions/{id}", name="transactionRetrait",methods={"PUT"})
     */

    public function transactionRetrait($id,Request $request){
        $idUser= $this->getUser()->getId();
        $transaction= $this->transactionServices->putTransaction($request,$idUser,$id);
        if ($transaction instanceof Transaction) {
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
            return $this->json($transaction,Response::HTTP_OK);
        }
        else {
            return $this->json($transaction,Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @Route("/api/user/transactions/depot", name="transactionsDepotUserAgence",methods={"GET"})
     */

    public function getUserTransactionDepot(SerializerInterface $serializer){
        $idUser= $this->getUser();
        $transactionsDepot=$this->transactionRepository->findBy(["userDepot"=>$idUser]);
        $transactionsDepot = $serializer->normalize($transactionsDepot,"json",["groups"=>"UserDepot"]);
        return $this->json($transactionsDepot,Response::HTTP_OK);
    }
    /**
     * @Route("/api/user/transactions/retrait", name="transactionsRetraitUserAgence",methods={"GET"})
     */

    public function getUserTransactionRetrait(SerializerInterface $serializer){
        $idUser= $this->getUser();
        $transactionsRetrait=$this->transactionRepository->findBy(["userRetrait"=>$idUser]);
        $transactionsRetrait = $serializer->normalize($transactionsRetrait,"json",["groups"=>"UserRetrait"]);
        return $this->json($transactionsRetrait,Response::HTTP_OK);
    }
}
