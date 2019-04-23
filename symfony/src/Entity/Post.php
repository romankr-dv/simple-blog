<?php
/**
 * Created by PhpStorm.
 * User: yamadote
 * Date: 1/15/19
 * Time: 11:33 AM.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
    public const QUANTITY_PER_PAGE = [
        'table' => 10,
        'list' => 4,
        'api' => 5,
    ];

    public const NOTIFICATION_QUANTITY_PER_PAGE = 5;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"api"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Title is empty")
     * @Groups({"api"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Description is empty")
     * @Groups({"api"})
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Content is empty")
     * @Groups({"api"})
     */
    private $content;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="posts")
     * @Assert\NotNull(message="Category is null")
     * @Groups({"post"})
     */
    private $category;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=128, unique=true)
     * @Groups({"api"})
     */
    private $slug;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups({"api"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @Groups({"api"})
     */
    private $updatedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post", orphanRemoval=true)
     * @Groups({"post"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PostLikedByUser", mappedBy="post", orphanRemoval=true)
     */
    private $postLikedByUsers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", mappedBy="posts")
     */
    private $tags;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->postLikedByUsers = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Post
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Post
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Post
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Post
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     * @return Post
     */
    public function setCategory(?Category $category): self
    {
        if ($category === null) {
            $this->category->removePost($this);
        }

        $this->category = $category;

        if ($category instanceof Category && !$category->getPosts()->contains($this)) {
            $category->addPost($this);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return Post
     */
    public function setUser(?User $user): self
    {
        if ($user === null) {
            $this->user->removePost($this);
        }

        $this->user = $user;

        if ($user instanceof User && !$user->getPosts()->contains($this)) {
            $user->addPost($this);
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            if ($comment->getPost() !== $this) {
                $comment->setPost($this);
            }
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PostLikedByUser[]
     */
    public function getPostLikedByUsers(): Collection
    {
        return $this->postLikedByUsers;
    }

    public function getCountOfLikes(): int
    {
        return $this->postLikedByUsers->count();
    }

    public function addPostLikedByUser(PostLikedByUser $postLikedByUser): self
    {
        if (!$this->postLikedByUsers->contains($postLikedByUser)) {
            $this->postLikedByUsers[] = $postLikedByUser;
            $postLikedByUser->setPost($this);
        }

        return $this;
    }

    public function removePostLikedByUser(PostLikedByUser $postLikedByUser): self
    {
        if ($this->postLikedByUsers->contains($postLikedByUser)) {
            $this->postLikedByUsers->removeElement($postLikedByUser);
            // set the owning side to null (unless already changed)
            if ($postLikedByUser->getPost() === $this) {
                $postLikedByUser->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addPost($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            $tag->removePost($this);
        }

        return $this;
    }
}
