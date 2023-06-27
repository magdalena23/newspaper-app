<?php
/**
 * Category Service Test.
 */

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Article;
use App\Repository\CategoryRepository;
use App\Repository\ArticleRepository;
use App\Service\CategoryService;
use App\Service\CategoryServiceInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class CategoryServiceTest.
 */
class CategoryServiceTest extends KernelTestCase
{
    /**
     * Category repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Category service.
     */
    private ?CategoryServiceInterface $categoryService;

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
        $this->categoryService = $container->get(CategoryService::class);
    }

    /**
     * Test save.
     *
     * @throws ORMException
     */
    public function testSave(): void
    {
        // given
        $expectedCategory = new Category();
        $expectedCategory->setTitle('Test Category 0');

        // when
        $this->categoryService->save($expectedCategory);

        // then
        $expectedCategoryId = $expectedCategory->getId();
        $resultCategory = $this->entityManager->createQueryBuilder()
            ->select('category')
            ->from(Category::class, 'category')
            ->where('category.id = :id')
            ->setParameter(':id', $expectedCategoryId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedCategory, $resultCategory);
    }

    /**
     * Test delete.
     *
     * @throws OptimisticLockException|ORMException
     */
    public function testDelete(): void
    {
        $categoryTitle = 'Test Category ' . uniqid();

        // Create a test category
        $categoryToDelete = new Category();
        $categoryToDelete->setTitle($categoryTitle);
        $this->entityManager->persist($categoryToDelete);

        $this->entityManager->flush();
        $deletedCategoryId = $categoryToDelete->getId();

        // when
        $this->categoryService->delete($categoryToDelete);

        // then
        $resultCategory = $this->entityManager->createQueryBuilder()
            ->select('category')
            ->from(Category::class, 'category')
            ->where('category.id = :id')
            ->setParameter(':id', $deletedCategoryId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultCategory);
    }

    /**
     * Test pagination empty list.
     */
    public function testGetPaginatedList(): void
    {
        // given
        $page = 1;
        $dataSetSize = 10;
        $expectedResultSize = 10;

        $counter = 0;
        while ($counter < $dataSetSize) {
            $category = new Category();
            $category->setTitle('Test Category #'.$counter);
            $this->categoryService->save($category);

            ++$counter;
        }

        // when
        $result = $this->categoryService->getPaginatedList($page);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }


    /**
     * Test can be deleted when article exists.
     *
     */
    public function testCanBeDeleted(): void
    {
        $categoryTitle = 'Test Category ' . uniqid();

        // Create a test category
        $category = new Category();
        $category->setTitle($categoryTitle);
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $article = new Article();
        $article->setTitle('Title');
        $article->setContent('Content');
        $article->setCategory($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $result = $this->categoryService->canBeDeleted($category);

        // then
        $this->assertFalse($result);
    }

    /**
     * Test can be deleted when exception.
     *
     */
    public function testCanBeDeleted2(): void
    {
        // given
        $category = new Category();

        $articleRepository = $this->createMock(ArticleRepository::class);
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $paginator = $this->createMock(PaginatorInterface::class);

        $articleRepository->expects($this->any())
            ->method('countByCategory')
            ->willThrowException(new NoResultException());

        $service = new CategoryService($categoryRepository, $articleRepository, $paginator);

        // when
        $result = $service->canBeDeleted($category);

        $this->assertFalse($result);
    }

    /**
     * Test find one by id.
     */
    public function testFindOneById(): void
    {
        $categoryTitle = 'Test Category ' . uniqid();

        // Create a test category
        $category = new Category();
        $category->setTitle($categoryTitle);
        $this->entityManager->persist($category);
        $this->entityManager->flush();
        $categoryId = $category->getId();

        // when
        $result = $this->categoryService->findOneById($categoryId);

        // then
        $this->assertEquals($category, $result);
    }
}