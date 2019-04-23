<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostLikedByUserRepository")
 */
class PostLikedByUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="postsLikedByUser")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var Post
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="postLikedByUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        if ($user === null) {
            $this->user->removePostsLikedByUser($this);
        }

        $this->user = $user;

        if ($user instanceof User && !$user->getPostsLikedByUser()->contains($this)) {
            $user->addPostsLikedByUser($this);
        }

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        if ($post === null) {
            $this->post->removePostLikedByUser($this);
        }

        $this->post = $post;

        if ($post instanceof Post && !$post->getPostLikedByUsers()->contains($this)) {
            $post->addPostLikedByUser($this);
        }

        return $this;
    }
}
