<?php

namespace App\Entity;

use App\Repository\SizeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SizeRepository::class)]
class Size
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $sizes = null;

    /**
     * @var Collection<int, Stock>
     */
    #[ORM\OneToMany(targetEntity: Stock::class, mappedBy: 'size')]
    private Collection $size;

    public function __construct()
    {
        $this->size = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSizes(): ?string
    {
        return $this->sizes;
    }

    public function setSizes(string $sizes): static
    {
        $this->sizes = $sizes;

        return $this;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getSize(): Collection
    {
        return $this->size;
    }

    public function addSize(Stock $size): static
    {
        if (!$this->size->contains($size)) {
            $this->size->add($size);
            $size->setSize($this);
        }

        return $this;
    }

    public function removeSize(Stock $size): static
    {
        if ($this->size->removeElement($size)) {
            // set the owning side to null (unless already changed)
            if ($size->getSize() === $this) {
                $size->setSize(null);
            }
        }

        return $this;
    }
}
