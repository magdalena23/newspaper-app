<?php
/**
 * Comment entity.
 */

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Comment.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'comments')]
class Comment
{
    /**
     * Primary key.
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * User nick.
     *
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 25)]
    private ?string $userNick = null;

    /**
     * User email.
     *
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 128)]
    private ?string $userEmail = null;

    /**
     * Content.
     *
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 1020)]
    private ?string $content = null;

    /**
     * Article
     *
     * @var Article|null
     */
    #[ORM\ManyToOne(inversedBy: 'comment')]
    private ?Article $article = null;


    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for user nick.
     *
     * @return string|null User nick
     */
    public function getUserNick(): ?string
    {
        return $this->userNick;
    }

    /**
     * Setter for user nick.
     *
     * @param string|null $userNick User nick
     */
    public function setUserNick(?string $userNick): void
    {
        $this->userNick = $userNick;
    }

    /**
     * Getter for user email.
     *
     * @return string|null User email
     */
    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    /**
     * Setter for user email.
     *
     * @param string|null $userEmail User email
     */
    public function setUserEmail(?string $userEmail): void
    {
        $this->userEmail = $userEmail;
    }

    /**
     * Getter for content.
     *
     * @return string|null Content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Setter for content.
     *
     * @param string|null $content Content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * Getter for article
     *
     * @return string|null Article
     */
    public function getArticle(): ?Article
    {
        return $this->article;
    }

    /**
     * Setter for article.
     *
     * @param string|null $article Article
     */
    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

}
