<?php
namespace App\Tests\Form\Type;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use Symfony\Component\Form\Test\TypeTestCase;

class CategoryTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = [
            'title' => 'Test Category',
        ];

        $categoryToCompare = new Category();

        $form = $this->factory->create(CategoryType::class, $categoryToCompare);

        $expectedCategory = new Category();
        $expectedCategory->setTitle($formData['title']);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedCategory, $categoryToCompare);
    }
}