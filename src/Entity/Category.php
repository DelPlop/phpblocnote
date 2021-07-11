<?php

namespace DelPlop\PbnBundle\Entity;

use App\Entity\ApplicationUser;
use DelPlop\PbnBundle\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @ORM\Table(name="category")
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position;

    /**
     * @ORM\OneToMany(targetEntity=DelPlop\PbnBundle\Entity\NoteCategory::class, mappedBy="category", orphanRemoval=true)
     */
    private $noteCategories;

    /**
     * @ORM\ManyToOne(targetEntity=App\Entity\ApplicationUser::class, inversedBy="categories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->noteCategories = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection|NoteCategory[]
     */
    public function getNoteCategories(): Collection
    {
        return $this->noteCategories;
    }

    public function addNoteCategory(NoteCategory $noteCategory): self
    {
        if (!$this->noteCategories->contains($noteCategory)) {
            $this->noteCategories[] = $noteCategory;
            $noteCategory->setCategory($this);
        }

        return $this;
    }

    public function removeNoteCategory(NoteCategory $noteCategory): self
    {
        if ($this->noteCategories->removeElement($noteCategory)) {
            // set the owning side to null (unless already changed)
            if ($noteCategory->getCategory() === $this) {
                $noteCategory->setCategory(null);
            }
        }

        return $this;
    }

    public function getUser(): ?ApplicationUser
    {
        return $this->user;
    }

    public function setUser(?ApplicationUser $user): self
    {
        $this->user = $user;

        return $this;
    }
}
