<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * @Route("/api/admin/users/{username}", name="findByUsername",methods={"GET"})
     */

    public function getUserByUsername($username, SerializerInterface $serializer){
        $user = $this->userRepository->findOneBy(["username"=>$username]);
        $user = $serializer->normalize($user,"json",["groups"=>"findByUsername"]);
        return $this->json($user,Response::HTTP_OK);
    }

        /**
     * @Route("/api/users/solde", name="getSolde",methods={"GET"})
     */

    public function getSolde(){
        $solde= $this->getUser()->getAdminAgence()->getCompte()->getSolde();
        return $this->json($solde,Response::HTTP_OK);
    }
}
