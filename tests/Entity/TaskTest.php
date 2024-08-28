<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class TaskTest extends TestCase
{


    public function testValidTaskCreation()
    {

        $task = new Task();
        $this->assertInstanceOf(Task::class, $task);
        $task->setTitle('Test Task');
        $this->assertEquals('Test Task', $task->getTitle());
        $task->setDescription('Test about tasks');
        $this->assertEquals('Test about tasks', $task->getDescription());
        $task->setDueDate(new \DateTime('tomorrow'));
        $expectedDate = new \DateTime('tomorrow');
        $this->assertEquals($expectedDate->format('Y-m-d'), $task->getDueDate()->format('Y-m-d'));
    }

    public function testGettersAndSetters()
    {
        // Arrange
        $taskTest = new Task();
        $this->assertInstanceOf(Task::class, $taskTest);

        $taskTest->setTitle('Test Task Title');
        $this->assertEquals('Test Task Title', $taskTest->getTitle());

        $taskTest->setDescription('About Task Description');
        $this->assertEquals('About Task Description', $taskTest->getDescription());
        $taskTest->setDueDate(new \DateTime('now'));
        $expectedDate = new \DateTime('now');
        $this->assertEquals($expectedDate->format('Y-m-d'), $taskTest->getDueDate()->format('Y-m-d'));
    }

    public function testValidationConstraints()
    {
        $taskConstraint = new Task();
        $this->assertInstanceOf(Task::class, $taskConstraint);
        $taskConstraint->setTitle('');
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()  // Enables annotation mapping for validation
            ->getValidator();
        $errors = $validator->validate($taskConstraint);
        $this->assertGreaterThan(0, count($errors));
    }
}