<?php

namespace App\Entity;

use App\Repository\KindergartenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Kindergarten
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $doucmentEvidenceTva;

    /**
     * @ORM\Column(type="integer")
     */
    private $ratingNote;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $avalability;

    /**
     * @ORM\Column(type="text")
     */
    private $address;

    /**
     * @ORM\Column(type="text")
     */
    private $tags;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $capacity;

    /**
     * @ORM\Column(type="integer")
     */
    private $nb_children_registered;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $picture;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_date;

    /**
     * @ORM\OneToMany(targetEntity="Club", mappedBy="kindergarten")
     */
    private $clubs;

    /**
     * @ORM\ManyToOne(targetEntity="Owner", inversedBy="kindergartens")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    public function __construct()
    {
        $this->clubs = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDoucmentEvidenceTva(): ?string
    {
        return $this->doucmentEvidenceTva;
    }

    public function setDoucmentEvidenceTva(string $doucmentEvidenceTva): self
    {
        $this->doucmentEvidenceTva = $doucmentEvidenceTva;

        return $this;
    }

    public function getRatingNote(): ?int
    {
        return $this->ratingNote;
    }

    public function setRatingNote(int $ratingNote): self
    {
        $this->ratingNote = $ratingNote;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAvalability(): ?int
    {
        return $this->avalability;
    }

    public function setAvalability(int $avalability): self
    {
        $this->avalability = $avalability;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getCapacity(): ?string
    {
        return $this->capacity;
    }

    public function setCapacity(string $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getNbChildrenRegistered(): ?int
    {
        return $this->nb_children_registered;
    }

    public function setNbChildrenRegistered(int $nb_children_registered): self
    {
        $this->nb_children_registered = $nb_children_registered;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->created_date;
    }

    public function setCreatedDate(\DateTimeInterface $created_date): self
    {
        $this->created_date = $created_date;

        return $this;
    }

    /**
     * @return Collection|Club[]
     */
    public function getClubs(): Collection
    {
        return $this->clubs;
    }

    public function addClub(Club $club): self
    {
        if (!$this->clubs->contains($club)) {
            $this->clubs[] = $club;
            $club->setKindergarten($this);
        }

        return $this;
    }

    public function removeClub(Club $club): self
    {
        if ($this->clubs->contains($club)) {
            $this->clubs->removeElement($club);
            // set the owning side to null (unless already changed)
            if ($club->getKindergarten() === $this) {
                $club->setKindergarten(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?Owner
    {
        return $this->owner;
    }

    public function setOwner(?Owner $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
