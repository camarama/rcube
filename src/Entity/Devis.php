<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DevisRepository")
 */
class Devis
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     *
     */
    private $reference;

    /**
     * @ORM\Column(type="datetime")
     *
     */
    private $date;

    /**
     * @ORM\Column(type="array")
     *
     */
    private $prestation = [];

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="devis")
     *
     */
    private $client;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    private $valider;

    /**
     * @ORM\OneToOne(targetEntity=Facturation::class, mappedBy="devis", cascade={"persist", "remove"})
     */
    private $facture;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?int
    {
        return $this->reference;
    }

    public function setReference(int $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPrestation(): ?array
    {
        return $this->prestation;
    }

    public function setPrestation(array $prestation): self
    {
        $this->prestation = $prestation;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getValider(): ?bool
    {
        return $this->valider;
    }

    public function setValider(bool $valider): self
    {
        $this->valider = $valider;

        return $this;
    }

    public function getFacture(): ?Facturation
    {
        return $this->facture;
    }

    public function setFacture(?Facturation $facture): self
    {
        $this->facture = $facture;

        // set (or unset) the owning side of the relation if necessary
        $newDevis = null === $facture ? null : $this;
        if ($facture->getDevis() !== $newDevis) {
            $facture->setDevis($newDevis);
        }

        return $this;
    }
}
