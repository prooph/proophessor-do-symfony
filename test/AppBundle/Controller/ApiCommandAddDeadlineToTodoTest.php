<?php

declare(strict_types=1);

namespace ProophessorTest\AppBundle\Controller;

use Prooph\ProophessorDo\Model\Todo\Event\DeadlineWasAddedToTodo;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\User\Event\UserWasRegistered;
use Rhumsaa\Uuid\Uuid;

class ApiCommandAddDeadlineToTodoTest extends ControllerBaseTestCase
{
    public function setUp()
    {
        parent::setUp();
        $userId = Uuid::uuid4();
        $todoId = Uuid::uuid4();
        $todoDescription = 'TodoDescription'.rand(10000000, 99999999);
        $deadline = new \DateTime('now');
        $deadline = $deadline->add(\DateInterval::createFromDateString('+1 Year'));

        $this->registerUser($userId, 'testUserName'.rand(10000, 1000000000), 'testUserEMail'.rand(10000, 1000000000).'@prooph.com');
        $this->postTodo($userId, $todoId, $todoDescription);
        $this->client = $this->addDeadlineToTodo($userId, $todoId, $deadline);
    }

    public function test_command_add_deadline_to_todo_returns_http_status_202()
    {
        $this->assertEquals(202, $this->client->getResponse()->getStatusCode());
    }

    public function test_command_post_todo_emits_DeadlineWasAddedToTodo_event()
    {
        $this->assertCount(3, $this->recordedEvents);
        $this->assertInstanceOf(UserWasRegistered::class,$this->recordedEvents[0]);
        $this->assertInstanceOf(TodoWasPosted::class,$this->recordedEvents[1]);
        $this->assertInstanceOf(DeadlineWasAddedToTodo::class,$this->recordedEvents[2]);
    }
}
