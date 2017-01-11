<?php

namespace ProophessorTest\AppBundle\Controller;

use Prooph\EventStore\Stream\StreamName;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsDone;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\User\Event\UserWasRegistered;
use Rhumsaa\Uuid\Uuid;

class ApiCommandMarkTodoAsDoneTest extends ControllerBaseTestCase
{

    /**
     * @var Uuid
     */
    protected $userId;

    /**
     * @var Uuid
     */
    protected $todoId;

    public function setUp()
    {
        parent::setUp();
        $this->userId = Uuid::uuid4();
        $this->todoId = Uuid::uuid4();

        $this->registerUser($this->userId, 'testUserName'.rand(10000, 1000000000), 'testUserEMail'.rand(10000, 1000000000).'@prooph.com');
        $this->postTodo($this->userId, $this->todoId, 'TodoDescription'.rand(10000000, 99999999));
        $this->markTodoAsDone($this->todoId, 'done');
    }

    public function test_command_mark_todo_as_done_returns_http_status_202()
    {
        $this->assertEquals(202, self::$client->getResponse()->getStatusCode());
    }

    public function test_command_mark_todo_as_done_adds_TodoWasMarkedAsDone_event_to_eventstream()
    {
        $stream = $this->store->load(new StreamName('event'));
        $this->assertCount(3, $stream->streamEvents());
        $event = $stream->streamEvents()->current();
        $this->assertInstanceOf(UserWasRegistered::class, $event);
        $stream->streamEvents()->next();
        $event = $stream->streamEvents()->current();
        $this->assertInstanceOf(TodoWasPosted::class, $event);
        $stream->streamEvents()->next();
        $event = $stream->streamEvents()->current();
        $this->assertInstanceOf(TodoWasMarkedAsDone::class, $event);
    }
}
