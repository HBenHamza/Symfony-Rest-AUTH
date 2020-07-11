<?php

namespace App\Entity;

use App\Repository\ClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Owner
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
    private $first_name = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $last_name = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password = '';

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    
     /**
     * @ORM\OneToMany(targetEntity="Kindergarten", mappedBy="owner")
     */
    private $kindergartens;

    public function __construct()
    {
        $this->kindergartens = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
 
    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set createdAt
     *
     * @param \TimeStamp $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \TimeStamp
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * @return Collection|Kindergarten[]
     */
    public function getKindergartens(): Collection
    {
        return $this->kindergartens;
    }

    public function addKindergarten(Kindergarten $kindergarten): self
    {
        if (!$this->kindergartens->contains($kindergarten)) {
            $this->kindergartens[] = $kindergarten;
            $kindergarten->setOwner($this);
        }

        return $this;
    }

    public function removeKindergarten(Kindergarten $kindergarten): self
    {
        if ($this->kindergartens->contains($kindergarten)) {
            $this->kindergartens->removeElement($kindergarten);
            // set the owning side to null (unless already changed)
            if ($kindergarten->getOwner() === $this) {
                $kindergarten->setOwner(null);
            }
        }

        return $this;
    }
}

