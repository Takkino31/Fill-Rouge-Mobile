<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\FraisRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=FraisRepository::class)
 */
class Frais
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $fraisEtat;

    /**
     * @ORM\Column(type="float")
     */
    private $fraisDepot;

    /**
     * @ORM\Column(type="float")
     */
    private $fraisRetrait;

    /**
     * @ORM\Column(type="float")
     */
    private $fraisSysteme;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDrop=false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFraisEtat(): ?float
    {
        return $this->fraisEtat;
    }

    public function setFraisEtat(float $fraisEtat): self
    {
        $this->fraisEtat = $fraisEtat;

        return $this;
    }

    public function getFraisDepot(): ?float
    {
        return $this->fraisDepot;
    }

    public function setFraisDepot(float $fraisDepot): self
    {
        $this->fraisDepot = $fraisDepot;

        return $this;
    }

    public function getFraisRetrait(): ?float
    {
        return $this->fraisRetrait;
    }

    public function setFraisRetrait(float $fraisRetrait): self
    {
        $this->fraisRetrait = $fraisRetrait;

        return $this;
    }

    public function getFraisSysteme(): ?float
    {
        return $this->fraisSysteme;
    }

    public function setFraisSysteme(float $fraisSysteme): self
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
}
