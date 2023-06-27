<?php
/**
 * Comment Service Test.
 */

namespace Service;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Article;
use App\Service\CommentService;
use App\Service\CommentServiceInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class CommentServiceTest.
 */
class CommentServiceTest extends KernelTestCase
{
    /**
     * Comment repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Comment service.
     */
    private ?CommentServiceInterface $commentService;

    /**
     * Set up test.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->commentService = $container->get(CommentService::class);
    }

    /**
     * Test save.
     *
     * @throws ORMException
     */
    public function testSave(): void
    {
        // given
        $category = new Category();
        $category->setTitle('Category 0');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $article = new Article();
        $article->setTitle('Test Comment 0');
        $article->setContent('Content 0');
        $article->setCategory($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $expectedComment = new Comment();
        $expectedComment->setContent('content');
        $expectedComment->setUserEmail('email');
        $expectedComment->setUserNick('nick');
        $expectedComment->setArticle($article);

        // when
        $this->commentService->save($expectedComment);

        // then
        $expectedCommentId = $expectedComment->getId();
        $resultComment = $this->entityManager->createQueryBuilder()
            ->select('comment')
            ->from(Comment::class, 'comment')
            ->where('comment.id = :id')
            ->setParameter(':id', $expectedCommentId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedComment, $resultComment);
    }

    /**
     * Test delete.
     *
     * @throws OptimisticLockException|ORMException
     */
    public function testDelete(): void
    {
        // given
        $category = new Category();
        $category->setTitle('Category 1');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $article = new Article();
        $article->setTitle('Test Comment 1');
        $article->setContent('Content 1');
        $article->setCategory($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $commentToDelete = new Comment();
        $commentToDelete->setContent('content');
        $commentToDelete->setUserEmail('email');
        $commentToDelete->setUserNick('nick');
        $commentToDelete->setArticle($article);
        $this->entityManager->persist($commentToDelete);
        $this->entityManager->flush();
        $deletedCommentId = $commentToDelete->getId();

        // when
        $this->commentService->delete($commentToDelete);

        // then
        $resultComment = $this->entityManager->createQueryBuilder()
            ->select('comment')
            ->from(Comment::class, 'comment')
            ->where('comment.id = :id')
            ->setParameter(':id', $deletedCommentId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultComment);
    }

}