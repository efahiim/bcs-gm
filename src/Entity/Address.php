<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Your first name cannot contain a number.',    
    )]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Your last name cannot contain a number.',    
    )]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\Type('string')]
    private ?string $street = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Your city cannot contain a number.',    
    )]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Assert\Type('string')]
    private ?string $zipCode = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Your country cannot contain a number.',    
    )]
    private ?string $country = null;

    #[ORM\OneToOne(inversedBy: 'address', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
