<?php
/**
 * Task Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use DateTimeImmutable;

/**
 * Class ArticleControllerTest.
 */
class ArticleControllerTest extends WebTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/article';

    /**
     * Test client.
     */
    private KernelBrowser $httpClient;

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * Test article route for anonymous user.
     */
    public function testTaskRouteAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 200;

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test article route for admin user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testTaskRouteAdminUser(): void
    {
        // given
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    private function removeUser(string $email): void
    {

        $userRepository = static::getContainer()->get(UserRepository::class);
        $entity = $userRepository->findOneBy(array('email' => $email));


        if ($entity !== null) {
            $userRepository->delete($entity);
        }
    }

    /**
     * Create user.
     *
     * @param array $roles User roles
     *
     * @return User User entity
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    private function createUser(array $roles): User
    {
        $this->removeUser('user@example.com');
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles($roles);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                'p@55w0rd'
            )
        );
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->save($user);

        return $user;
    }

    /**
     * Test create route for anonymous user.
     */
    public function testNewRouteAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 302;

        // when
        $this->httpClient->request('GET', '/article/create');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test create route for admin user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testNewRouteAdminUser(): void
    {
        // given
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', '/article/create');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test show route for admin user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testShowRouteAdminUser(): void
    {
        // given
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        $categoryTitle = 'Test Category ' . uniqid();

        $category = new Category();
        $category->setTitle($categoryTitle);
        $this->entityManager->persist($category);

        $article = new Article();
        $article->setTitle('Test Article');
        $article->setContent('Content');
        $article->setCategory($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', '/article/' . $article->getId());
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test show route for anonymous user.
     */
    public function testShowRouteAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 200;

        $categoryTitle = 'Test Category ' . uniqid();

        $category = new Category();
        $category->setTitle($categoryTitle);
        $this->entityManager->persist($category);

        $article = new Article();
        $article->setTitle('Test Article');
        $article->setContent('Content');
        $article->setCategory($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->followRedirects(false);
        $this->httpClient->request('GET', '/article/' . $article->getId());
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test edit route for admin user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testEditRouteAdminUser(): void
    {
        // given
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        $categoryTitle = 'Test Category ' . uniqid();

        $category = new Category();
        $category->setTitle($categoryTitle);
        $this->entityManager->persist($category);

        $article = new Article();
        $article->setTitle('Test Article');
        $article->setContent('Content');
        $article->setCategory($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', '/article/' . $article->getId() . '/edit');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test edit route for anonymous user.
     */
    public function testEditRouteAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 302;

        $categoryTitle = 'Test Category ' . uniqid();

        $category = new Category();
        $category->setTitle($categoryTitle);
        $this->entityManager->persist($category);

        $article = new Article();
        $article->setTitle('Test Article');
        $article->setContent('Content');
        $article->setCategory($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->followRedirects(false);
        $this->httpClient->request('GET', '/article/' . $article->getId() . '/edit');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test delete route for admin user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testDeleteRouteAdminUser(): void
    {
        // given
        $expectedStatusCode = 200;
        $categoryTitle = 'Test Category ' . uniqid();

        $category = new Category();
        $category->setTitle($categoryTitle);
        $this->entityManager->persist($category);

        $article = new Article();
        $article->setTitle('Test Article');
        $article->setContent('Content');
        $article->setCategory($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', '/article/' . $article->getId() . '/delete');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test delete route for anonymous user.
     */
    public function testDeleteRouteAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 302;
        $categoryTitle = 'Test Category ' . uniqid();

        $category = new Category();
        $category->setTitle($categoryTitle);
        $this->entityManager->persist($category);

        $article = new Article();
        $article->setTitle('Test Article');
        $article->setContent('Content');
        $article->setCategory($category);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // when
        $this->httpClient->followRedirects(false);
        $this->httpClient->request('GET', '/article/' . $article->getId() . '/delete');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

}