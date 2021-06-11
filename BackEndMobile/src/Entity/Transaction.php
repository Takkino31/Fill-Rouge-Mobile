<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TransactionRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 * @ApiFilter(SearchFilter::class,properties={"codeTransaction"})
 * @ApiResource(
 *     attributes={
 *          "security_message"= "Vous n'avez pas accès à cette ressource",
 *          "security"="is_granted('ROLE_UTILISATEUR_AGENCE')"
 *          },
 *      collectionOperations={
 *          "allTransactions"={
 *              "method"="GET",
 *              "path"="/api/admin/user/transactions",
 *              "security"="is_granted('ROLE_ADMIN_AGENCE')"
 *           },
 *          "transactionsAllAgence"={
 *              "method"="GET",
 *              "path"="/api/admin/user/transactions/{codeTransaction}"
 *          },
 *          "transactionsDepotUserAgence"={
 *              "method"="GET",
 *              "path"="/api/user/transactions/depot"
 *          },
 *          "transactionsRetraitUserAgence"={
 *              "method"="GET",
 *              "path"="/api/user/transactions/retrait"
 *          },
 *          "transactionsUsersAgence"={
 *              "method"="GET",
 *              "path"="/api/usersagence/transactions"
 *          },
 *          "getTransactionPeriodeUser"={
 *              "method"="GET",
 *              "path"="/api/admin/usersagence/{dateDebut}/{dateFin}"
 *          },
 *          "transactionDepot"={
 *              "method"="POST",
 *              "path"="/api/admin/user/transactions"
 *           }
 *     },
 *      itemOperations={
 *          "transaction"={
 *              "method"="GET",
 *              "path"="/api/admin/user/transactions/{id}"
 *          },
 *          "transactionRetrait"={
 *              "method"="PUT",
 *              "path"="/api/admin/user/transactions/{id}"
 *          }
 *      }
 * )
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"transactionByCode"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transactionByCode","UserDepot","UserRetrait","usersAgencesDepot","usersAgencesRetrait"})
     */
    private $montant;

    /**
     * @ORM\Column(type="date")
     * @Groups({"transactionByCode","UserDepot","usersAgencesDepot"})
     */
    private $dateDepot;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"UserRetrait","usersAgencesRetrait"})
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"transactionByCode"})
     */
    private $codeTransaction;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transactionByCode"})
     */
    private $frais;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transactionByCode"})
     */
    private $fraisDepot;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transactionByCode"})
     */
    private $fraisRetrait;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transactionByCode"})
     */
    private $fraisEtat;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transactionByCode"})
     */
    private $fraisSysteme;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"transactionByCode"})
     */
    private $isDrop=false;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $compte;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactionsDepot")
     * @Groups({"transactionByCode"})
     */
    private $clientDepot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactionsDepot")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"usersAgencesDepot"})
     */
    private $userDepot;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactionsRetrait")
     * @Groups({"transactionByCode"})
     */
    private $clientRetrait;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactionsRetrait")
     * @Groups({"usersAgencesRetrait"})
     */
    private $userRetrait;

    public function __construct()
    {
        $this->dateDepot = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->dateDepot;
    }

    public function setDateDepot(\DateTimeInterface $dateDepot): self
    {
        $this->dateDepot = $dateDepot;

        return $this;
    }

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(?\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getCodeTransaction(): ?string
    {
        return $this->codeTransaction;
    }

    public function setCodeTransaction(string $codeTransaction): self
    {
        $this->codeTransaction = $codeTransaction;

        return $this;
    }

    public function getFrais(): ?int
    {
        return $this->frais;
    }

    public function setFrais(int $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getFraisDepot(): ?int
    {
        return $this->fraisDepot;
    }

    public function setFraisDepot(int $fraisDepot): self
    {
        $this->fraisDepot = $fraisDepot;

        return $this;
    }

    public function getFraisRetrait(): ?int
    {
        return $this->fraisRetrait;
    }

    public function setFraisRetrait(int $fraisRetrait): self
    {
        $this->fraisRetrait = $fraisRetrait;

        return $this;
    }

    public function getFraisEtat(): ?int
    {
        return $this->fraisEtat;
    }

    public function setFraisEtat(int $fraisEtat): self
    {
        $this->fraisEtat = $fraisEtat;

        return $this;
    }

    public function getFraisSysteme(): ?int
    {
        return $this->fraisSysteme;
    }

    public function setFraisSysteme(int $fraisSysteme): self
    {
        $this->fraisSysteme = $fraisSysteme;

        return $this;
    }

    public function getIsDrop(): ?bool
    {
        return $this->isDrop;
    }

    public function setIsDrop(bool $isDrop): self
    {
        $this->isDrop = $isDrop;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }

    public function getClientDepot(): ?Client
    {
        return $this->clientDepot;
    }

    public function setClientDepot(?Client $clientDepot): self
    {
        $this->clientDepot = $clientDepot;

        return $this;
    }

    public function getUserDepot(): ?User
    {
        return $this->userDepot;
    }

    public function setUserDepot(?User $userDepot): self
    {
        $this->userDepot = $userDepot;

        return $this;
    }

    public function getClientRetrait(): ?Client
    {
        return $this->clientRetrait;
    }

    public function setClientRetrait(?Client $clientRetrait): self
    {
        $this->clientRetrait = $clientRetrait;

        return $this;
    }

    public function getUserRetrait(): ?User
    {
        return $this->userRetrait;
    }

    public function setUserRetrait(?User $userRetrait): self
    {
        $this->userRetrait = $userRetrait;

        return $this;
    }
}
