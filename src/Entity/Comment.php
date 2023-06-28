<?php
/**
 * Comment entity.
 */

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Comment.
 *
 * @psalm-suppress MissingConstructor.
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'comments')]
class Comment
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * User nick.
     */
    #[ORM\Column(type: 'string', length: 25)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 25)]
    private ?string $userNick = null;

    /**
     * User email.
     */
    #[ORM\Column(type: 'string', length: 128)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 128)]
    private ?string $userEmail = null;

    /**
     * Content.
     */
    #[ORM\Column(type: 'string', length: 1020)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 1020)]
    private ?string $content = null;

    /**
     * Article.
     */
    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'comment')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    /**
     * Getter for Id.
     *
     * @return int|null id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for user nick.
     *
     * @return string|null user nick
     */
    public function getUserNick(): ?string
    {
        return $this->userNick;
    }

    /**
     * Setter for user nick.
     *
     * @param string|null $userNick user nick
     */
    public function setUserNick(?string $userNick): void
    {
        $this->userNick = $userNick;
    }

    /**
     * Getter for user email.
     *
     * @return string|null user email
     */
    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    /**
     * Setter for user email.
     *
     * @param string|null $userEmail user email
     */
    public function setUserEmail(?string $userEmail): void
    {
        $this->userEmail = $userEmail;
    }

    /**
     * Getter for content.
     *
     * @return string|null content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Setter for content.
     *
     * @param string|null $content content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * Getter for article.
     *
     * @return Article|null article
     */
    public function getArticle(): ?Article
    {
        return $this->article;
    }

    /**
     * Setter for article.
     *
     * @param Article|null $article article
     *
     * @return $this
     */
    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }
}
