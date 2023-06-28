<?php
/**
 * Comment service.
 */

namespace App\Service;

use App\Entity\Comment;
use App\Repository\CommentRepository;

/**
 * Class CommentService.
 */
class CommentService implements CommentServiceInterface
{
    /**
     * Article service.
     */
    private ArticleServiceInterface $articleService;

    /**
     * Comment repository.
     */
    private CommentRepository $commentRepository;

    /**
     * Constructor.
     *
     * @param CommentRepository       $commentRepository Comment repository
     * @param ArticleServiceInterface $articleService    Article Service
     */
    public function __construct(CommentRepository $commentRepository, ArticleServiceInterface $articleService)
    {
        $this->commentRepository = $commentRepository;
        $this->articleService = $articleService;
    }

    /**
     * Save entity.
     *
     * @param Comment $comment Comment entity
     */
    public function save(Comment $comment): void
    {
        $this->commentRepository->save($comment);
    }

    /**
     * Delete entity.
     *
     * @param Comment $comment Comment entity
     */
    public function delete(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }
}
