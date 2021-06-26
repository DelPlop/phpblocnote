<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
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
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $visibility;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class)
     *  @ORM\JoinTable(name="note_category",
     *         joinColumns = {@ORM\JoinColumn(name="note_id", referencedColumnName="id", onDelete="CASCADE")},
     *         inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     *      )
     */
    private $categories;


    /**
     * @ORM\OneToMany(targetEntity=NoteCategory::class, mappedBy="note", orphanRemoval=true)
     */
    private $noteCategories;

    /**
     * @ORM\ManyToOne(targetEntity=RegisteredUser::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->noteCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content ?: '';

        return $this;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getUser(): ?RegisteredUser
    {
        return $this->user;
    }

    public function setUser(?RegisteredUser $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|NoteCategory[]
     */
    public function getNoteCategories(): Collection
    {
        return $this->noteCategories;
    }

    public function clearNoteCategories(): self
    {
        $this->noteCategories = new ArrayCollection();

        return $this;
    }

    public function addNoteCategory(NoteCategory $noteCategory): self
    {
        if (!$this->noteCategories->contains($noteCategory)) {
            $this->noteCategories[] = $noteCategory;
            $noteCategory->setNote($this);
        }

        return $this;
    }

    public function removeNoteCategory(NoteCategory $noteCategory): self
    {
        if ($this->noteCategories->removeElement($noteCategory)) {
            // set the owning side to null (unless already changed)
            if ($noteCategory->getNote() === $this) {
                $noteCategory->setNote(null);
            }
        }

        return $this;
    }
}
