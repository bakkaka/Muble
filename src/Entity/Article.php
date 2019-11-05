<?php

namespace App\Entity;
use DateTime;
use App\Repository\CommentRepository;
use App\Service\UploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 
 */
class Article
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
    private $title;

   
	
	/**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
	
	/**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

   

    /**
     * @ORM\Column(type="integer")
     */
    private $heartCount = 0;
	
	
	/**
   * @ORM\OneToOne(targetEntity="App\Entity\Image",  cascade={"persist", "remove"})
   * @Assert\Valid()
   */
   private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="article", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $comments;

    /*
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="articles")
     *
    private $tags;
	*/

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Please set an author")
     */
    private $author;
	
	



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ArticleReference", mappedBy="article")
     * @ORM\OrderBy({"position"="ASC"})
     */
    private $articleReferences;

    public function __construct()
    {
	     $this->date = new DateTime();
        $this->createdAt = new DateTime();
		$this->updatedAt = new DateTime();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->articleReferences = new ArrayCollection();
		$this->publishedAt = new DateTime();
    }

    public function getId()
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




    //public function getContent(): ?string
    //{
    //    return $this->content;
    //}
	
	public function getContent($length = null)
                          {
                      if (false === is_null($length) && $length > 0)
                          return substr($this->content, 0, $length);
                      else
                          return $this->content;
                  }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    

   

    public function getHeartCount(): ?int
    {
        return $this->heartCount;
    }

    public function setHeartCount(int $heartCount): self
    {
        $this->heartCount = $heartCount;

        return $this;
    }

    public function incrementHeartCount(): self
    {
        $this->heartCount = $this->heartCount + 1;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getNonDeletedComments(): Collection
    {
        $criteria = CommentRepository::createNonDeletedCriteria();

        return $this->comments->matching($criteria);
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }


    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (stripos($this->getTitle(), 'the borg') !== false) {
            $context->buildViolation('Um.. the Bork kinda makes us nervous')
                ->atPath('title')
                ->addViolation();
        }
    }


    /**
     * @return Collection|ArticleReference[]
     */
    public function getArticleReferences(): Collection
    {
        return $this->articleReferences;
    }

    public function addArticleReference(ArticleReference $articleReference): self
    {
        if (!$this->articleReferences->contains($articleReference)) {
            $this->articleReferences[] = $articleReference;
            $articleReference->setArticle($this);
        }

        return $this;
    }

    public function removeArticleReference(ArticleReference $articleReference): self
    {
        if ($this->articleReferences->contains($articleReference)) {
            $this->articleReferences->removeElement($articleReference);
            // set the owning side to null (unless already changed)
            if ($articleReference->getArticle() === $this) {
                $articleReference->setArticle(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function setArticleReferences(?string $articleReferences): self
    {
        $this->articleReferences = $articleReferences;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    



}
