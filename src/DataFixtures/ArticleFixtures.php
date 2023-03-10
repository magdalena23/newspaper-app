<?php
/**
 * Article fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Article;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/**
 * Class ArticleFixtures.
 */
class ArticleFixtures extends Fixture
{
    /**
     * Faker.
     *
     * @var Generator
     */
    protected Generator $faker;

    /**
     * Persistence object manager.
     *
     * @var ObjectManager
     */
    protected ObjectManager $manager;

    /**
     * Load.
     *
     * @param ObjectManager $manager Persistence object manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        for ($i = 0; $i < 10; ++$i) {
            $article = new Article();
            $article->setTitle($this->faker->sentence);
            $article->setContent($this->faker->text);
            $article->setCreatedAt(
                DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $article->setUpdatedAt(
                DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $manager->persist($article);
        }

        $manager->flush();
    }
}