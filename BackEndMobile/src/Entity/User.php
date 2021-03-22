<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * ApiFilter(SearchFilter::class, properties={"username"})
 * @ApiResource(
 *attributes={
 *          "security"="is_granted('ROLE_ADMIN_AGENCE')",
 *          "security_message"= "Vous n'avez pas accès à cette ressource"
 *     },
 *     collectionOperations={
 *          "findByUsername"={
 *              "method"="GET",
 *              "path"="admin/users/{username}",
 *              "security"="is_granted('ROLE_UTILISATEUR_AGENCE')",
 *           },
 *           "add_users"={
 *               "method"="POST",
 *               "path"="admin/users",
 *           }
 *       },
 *     itemOperations={
 *          "get_user"={
 *              "method"="GET",
 *              "path"="admin/users/{id}",
 *           },
 *          "put_user"={
 *              "method"="PUT",
 *              "path"="admin/users/{id}"
 *           },
 *          "delete_user"={
 *              "method"="DELETE",
 *              "path"="admin/users/{id}"
 *           },
 *       }
 * )
 *              
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"findByUsername"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telephone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBlocked=false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDrop=false;

    /**
     * @ORM\ManyToOne(targetEntity=Profile::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $profil;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="users")
     */
    private $adminAgence;

    /**
     * @ORM\OneToMany(targetEntity=Compte::class, mappedBy="user")
     */
    private $comptes;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="userDepot")
     */
    private $transactionsDepot;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $avatar;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="userRetrait")
     */
    private $transactionsRetrait;

    public function __construct()
    {
        $this->comptes = new ArrayCollection();
        $this->transactionsDepot = new ArrayCollection();
        $this->transactionsRetrait = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_'.strtoupper(($this->profil->getLibelle()));

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getIsBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;

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

    public function getProfil(): ?Profile
    {
        return $this->profil;
    }

    public function setProfil(?Profile $profil): self
    {
        $this->profil = $profil;
        return $this;
    }

    public function getAdminAgence(): ?Agence
    {
        return $this->adminAgence;
    }

    public function setAdminAgence(?Agence $adminAgence): self
    {
        $this->adminAgence = $adminAgence;

        return $this;
    }

    /**
     * @return Collection|Compte[]
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setUser($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->removeElement($compte)) {
            // set the owning side to null (unless already changed)
            if ($compte->getUser() === $this) {
                $compte->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactionsDepot(): Collection
    {
        return $this->transactionsDepot;
    }

    public function addTransactionsDepot(Transaction $transactionsDepot): self
    {
        if (!$this->transactionsDepot->contains($transactionsDepot)) {
            $this->transactionsDepot[] = $transactionsDepot;
            $transactionsDepot->setUserDepot($this);
        }

        return $this;
    }

    public function removeTransactionsDepot(Transaction $transactionsDepot): self
    {
        if ($this->transactionsDepot->removeElement($transactionsDepot)) {
            // set the owning side to null (unless already changed)
            if ($transactionsDepot->getUserDepot() === $this) {
                $transactionsDepot->setUserDepot(null);
            }
        }

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactionsRetrait(): Collection
    {
        return $this->transactionsRetrait;
    }

    public function addTransactionsRetrait(Transaction $transactionsRetrait): self
    {
        if (!$this->transactionsRetrait->contains($transactionsRetrait)) {
            $this->transactionsRetrait[] = $transactionsRetrait;
            $transactionsRetrait->setUserRetrait($this);
        }

        return $this;
    }

    public function removeTransactionsRetrait(Transaction $transactionsRetrait): self
    {
        if ($this->transactionsRetrait->removeElement($transactionsRetrait)) {
            // set the owning side to null (unless already changed)
            if ($transactionsRetrait->getUserRetrait() === $this) {
                $transactionsRetrait->setUserRetrait(null);
            }
        }

        return $this;
    }

}
