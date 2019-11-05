<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups("main")
	 * @var string|null
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;
	
	 /**
     * @ORM\Column(type="string", length=255, nullable=false)
	 * @var string|null
     * @Assert\NotBlank()
     * @Assert\Regex(
     *  pattern="/[0-9]{10}/"
     * )
     */
    private $phone;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("main")
	  * @var string|null
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=100)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

   

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ApiToken", mappedBy="user", orphanRemoval=true, cascade={"persist", "remove"}))
     */
    private $apiTokens;
	
	 /**
     * @ORM\OneToMany(targetEntity="App\Entity\Discussion", mappedBy="user", fetch="EXTRA_LAZY", cascade={"persist", "remove"}))
     */
    private $discussions;
	
	 /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bien", mappedBy="user", fetch="EXTRA_LAZY", cascade={"persist", "remove"}))
     */
    private $biens;
	
	 /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", fetch="EXTRA_LAZY", cascade={"persist", "remove"}))
     */
    private $comments;
	
	

	 
	

    public function __construct()
    {
        $this->apiTokens = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->biens = new ArrayCollection();
        $this->discussions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using bcrypt or argon
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getTwitterUsername(): ?string
    {
        return $this->twitterUsername;
    }

    public function setTwitterUsername(?string $twitterUsername): self
    {
        $this->twitterUsername = $twitterUsername;

        return $this;
    }

    public function getAvatarUrl(string $size = null): string
    {
        $url = 'https://robohash.org/'.$this->getEmail();

        if ($size)
            $url .= sprintf('?size=%dx%d', $size, $size);

        return $url;
    }

    /**
     * @return Collection|ApiToken[]
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function addApiToken(ApiToken $apiToken): self
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens[] = $apiToken;
            $apiToken->setUser($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): self
    {
        if ($this->apiTokens->contains($apiToken)) {
            $this->apiTokens->removeElement($apiToken);
            // set the owning side to null (unless already changed)
            if ($apiToken->getUser() === $this) {
                $apiToken->setUser(null);
            }
        }

        return $this;
    }


	 public function __toString()
    {
        return $this->getFirstName();
    }

 







  public function getPhone(): ?string
  {
      return $this->phone;
  }

  public function setPhone(string $phone): self
  {
      $this->phone = $phone;

      return $this;
  }

  /**
   * @return Collection|Discussion[]
   */
  public function getDiscussions(): Collection
  {
      return $this->discussions;
  }

  public function addDiscussion(Discussion $discussion): self
  {
      if (!$this->discussions->contains($discussion)) {
          $this->discussions[] = $discussion;
          $discussion->setUser($this);
      }

      return $this;
  }

  public function removeDiscussion(Discussion $discussion): self
  {
      if ($this->discussions->contains($discussion)) {
          $this->discussions->removeElement($discussion);
          // set the owning side to null (unless already changed)
          if ($discussion->getUser() === $this) {
              $discussion->setUser(null);
          }
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
          $comment->setUser($this);
      }

      return $this;
  }

  public function removeComment(Comment $comment): self
  {
      if ($this->comments->contains($comment)) {
          $this->comments->removeElement($comment);
          // set the owning side to null (unless already changed)
          if ($comment->getUser() === $this) {
              $comment->setUser(null);
          }
      }

      return $this;
  }

  /**
   * @return Collection|Bien[]
   */
  public function getBiens(): Collection
  {
      return $this->biens;
  }

  public function addBien(Bien $bien): self
  {
      if (!$this->biens->contains($bien)) {
          $this->biens[] = $bien;
          $bien->setUser($this);
      }

      return $this;
  }

  public function removeBien(Bien $bien): self
  {
      if ($this->biens->contains($bien)) {
          $this->biens->removeElement($bien);
          // set the owning side to null (unless already changed)
          if ($bien->getUser() === $this) {
              $bien->setUser(null);
          }
      }

      return $this;
  }


	
		
}
