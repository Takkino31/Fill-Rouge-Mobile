<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommissionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 * )
 * @ORM\Entity(repositoryClass=CommissionRepository::class)
 */
class Commission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $inf;

    /**
     * @ORM\Column(type="integer")
     */
    private $sup;

    /**
     * @ORM\Column(type="integer")
     */
    private $frais;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInf(): ?int
    {
        return $this->inf;
    }

    public function setInf(int $inf): self
    {
        $this->inf = $inf;

        return $this;
    }

    public function getSup(): ?int
    {
        return $this->sup;
    }

    public function setSup(int $sup): self
    {
        $this->sup = $sup;

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

}
