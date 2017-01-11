<?php

namespace ProophessorTest\AppBundle\Controller;

use Prooph\EventStore\Stream\StreamName;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\User\Event\UserWasRegistered;
use Rhumsaa\Uuid\Uuid;

class ApiCommandPostTodoTest extends ControllerBaseTestCase
{

    /**
     * @var Uuid
     */
    protected $userId;

    public function setUp()
    {
        parent::setUp();
        $this->userId = Uuid::uuid4();
    }

    public function test_command_post_todo_returns_http_status_202()
    {
        $todoId = Uuid::uuid4();
        $todoDescription = 'TodoDescription'.rand(10000000, 99999999);

        $this->registerUser($this->userId, 'testUserName'.rand(10000, 1000000000), 'testUserEMail'.rand(10000, 1000000000).'@prooph.com');
        $this->postTodo($this->userId, $todoId, $todoDescription);
        $this->assertEquals(202, self::$client->getResponse()->getStatusCode());
    }

    public function test_command_post_todo_adds_TodoWasPosted_event_to_eventstream()
    {
        $todoId = Uuid::uuid4();
        $todoDescription = 'TodoDescription'.rand(10000000, 99999999);
        $this->registerUser($this->userId, 'testUserName'.rand(10000, 1000000000), 'testUserEMail'.rand(10000, 1000000000).'@prooph.com');
        $this->postTodo($this->userId, $todoId, $todoDescription);

        $this->assertEquals(202, self::$client->getResponse()->getStatusCode());
        $stream = $this->store->load(new StreamName('event'));
        $this->assertCount(2, $stream->streamEvents());
        $event = $stream->streamEvents()->current();
        $this->assertInstanceOf(UserWasRegistered::class, $event);
        $stream->streamEvents()->next();
        $event = $stream->streamEvents()->current();
        $this->assertInstanceOf(TodoWasPosted::class, $event);
    }
}
