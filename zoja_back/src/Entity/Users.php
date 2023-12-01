<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[UniqueEntity(fields: ["email", "authentification_name"], message: "This email or authentication name is already in use.")]
class Users implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $authentification_name = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Post::class)]
    private Collection $user_posts;

    #[ORM\OneToMany(mappedBy: 'author_id', targetEntity: Comments::class, orphanRemoval: true)]
    private Collection $user_comment;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profile_picture = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $roles = [];

    public function __construct()
    {
        $this->user_posts = new ArrayCollection();
        $this->user_comment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getAuthentificationName(): ?string
    {
        return $this->authentification_name;
    }

    public function setAuthentificationName(string $authentification_name): static
    {
        $this->authentification_name = $authentification_name;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getUserPosts(): Collection
    {
        return $this->user_posts;
    }

    public function addUserPost(Post $userPost): static
    {
        if (!$this->user_posts->contains($userPost)) {
            $this->user_posts->add($userPost);
            $userPost->setAuthor($this);
        }

        return $this;
    }

    public function removeUserPost(Post $userPost): static
    {
        if ($this->user_posts->removeElement($userPost)) {
            // set the owning side to null (unless already changed)
            if ($userPost->getAuthor() === $this) {
                $userPost->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getUserComment(): Collection
    {
        return $this->user_comment;
    }

    public function addUserComment(Comments $userComment): static
    {
        if (!$this->user_comment->contains($userComment)) {
            $this->user_comment->add($userComment);
            $userComment->setAuthorId($this);
        }

        return $this;
    }

    public function removeUserComment(Comments $userComment): static
    {
        if ($this->user_comment->removeElement($userComment)) {
            // set the owning side to null (unless already changed)
            if ($userComment->getAuthorId() === $this) {
                $userComment->setAuthorId(null);
            }
        }

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profile_picture;
    }

    public function setProfilePicture(?string $profile_picture): static
    {
        $this->profile_picture = $profile_picture;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles ?? [];
        $roles[] = $this->roles;
        return $roles;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }
}
