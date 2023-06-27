<?php
namespace App\Tests\Form\Type;

use App\Entity\Comment;
use App\Form\Type\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;

class CommentTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = [
            'userNick' => 'Nick',
            'userEmail' => 'nick@example.com',
            'content' => 'Test Comment',
        ];

        $commentToCompare = new Comment();

        $form = $this->factory->create(CommentType::class, $commentToCompare);

        $expectedComment = new Comment();
        $expectedComment->setUserNick($formData['userNick']);
        $expectedComment->setUserEmail($formData['userEmail']);
        $expectedComment->setContent($formData['content']);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedComment, $commentToCompare);
    }
}