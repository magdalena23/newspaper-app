<?php
/**
 * Article Service Test.
 */

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Article;
use App\Service\ArticleService;
use App\Service\ArticleServiceInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class ArticleServiceTest.
 */
class ArticleServiceTest extends KernelTestCase
{
    /**
     * Article repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Article service.
     */
    private ?ArticleServiceInterface $articleService;

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
        $this->articleService = $container->get(ArticleService::class);
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
        $category->setTitle('Test Category 4');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $expectedArticle = new Article();
        $expectedArticle->setTitle('Test Article');
        $expectedArticle->setContent('Content');
        $expectedArticle->setCategory($category);

        // when
        $this->articleService->save($expectedArticle);

        // then
        $expectedArticleId = $expectedArticle->getId();
        $resultArticle = $this->entityManager->createQueryBuilder()
            ->select('article')
            ->from(Article::class, 'article')
            ->where('article.id = :id')
            ->setParameter(':id', $expectedArticleId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedArticle, $resultArticle);
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
        $category->setTitle('Test Category 5');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $articleToDelete = new Article();
        $articleToDelete->setTitle('Test Article');
        $articleToDelete->setContent('Content');
        $articleToDelete->setCategory($category);
        $this->entityManager->persist($articleToDelete);
        $this->entityManager->flush();
        $deletedArticleId = $articleToDelete->getId();

        // when
        $this->articleService->delete($articleToDelete);

        // then
        $resultArticle = $this->entityManager->createQueryBuilder()
            ->select('article')
            ->from(Article::class, 'article')
            ->where('article.id = :id')
            ->setParameter(':id', $deletedArticleId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultArticle);
    }

    /**
     * Test pagination empty list.
     */
    public function testGetPaginatedList(): void
    {
        // given
        $category = new Category();
        $category->setTitle('Test Category 7');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $page = 1;
        $dataSetSize = 10;
        $expectedResultSize = 10;

        $counter = 0;
        while ($counter < $dataSetSize) {
            $article = new Article();
            $article->setTitle('Test Article #'.$counter);
            $article->setContent('Content');
            $article->setCategory($category);
            $this->articleService->save($article);

            ++$counter;
        }

        $filters = array(
            'category_id' => $category->getId()
        );

        // when
        $result = $this->articleService->getPaginatedList($page, $filters);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }

    /**
     * Test prepare filters.
     */
    public function testPrepareFilters(): void
    {
        // given
        $category = new Category();
        $category->setTitle('Test Category 2');
        $this->entityManager->persist($category);
        $this->entityManager->flush();
        $categoryId = $category->getId();

        $filters = array(
            'category_id' => $categoryId,
        );

        // when
        $result = $this->articleService->prepareFilters($filters);

        // then
        $this->assertEquals($result, array('category' => $category));
    }
}