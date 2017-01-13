<?php

declare(strict_types=1);

namespace ProophessorTest\AppBundle\Controller;

use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsDone;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasReopened;
use Prooph\ProophessorDo\Model\User\Event\UserWasRegistered;
use Rhumsaa\Uuid\Uuid;

class ApiCommandReopenTodoTest extends ControllerBaseTestCase
{
    public function setUp()
    {
        parent::setUp();
        $userId = Uuid::uuid4();
        $todoId = Uuid::uuid4();

        $this->registerUser($userId, 'testUserName'.rand(10000, 1000000000), 'testUserEMail'.rand(10000, 1000000000).'@prooph.com');
        $this->postTodo($userId, $todoId, 'TodoDescription'.rand(10000000, 99999999));
        $this->markTodoAsDone($todoId, 'done');
        $this->client = $this->reopenTodo($todoId);
    }

    public function test_command_reopen_todo_returns_http_status_202()
    {
        $this->assertEquals(202, $this->client->getResponse()->getStatusCode());
    }

    public function test_command_post_todo_emits_TodoWasReopened_event()
    {
        $this->assertCount(4, $this->recordedEvents);
        $this->assertInstanceOf(UserWasRegistered::class,$this->recordedEvents[0]);
        $this->assertInstanceOf(TodoWasPosted::class,$this->recordedEvents[1]);
        $this->assertInstanceOf(TodoWasMarkedAsDone::class,$this->recordedEvents[2]);
        $this->assertInstanceOf(TodoWasReopened::class,$this->recordedEvents[3]);
    }
}
