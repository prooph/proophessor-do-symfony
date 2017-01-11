<?php
declare(strict_types=1);

namespace ProophessorTest\AppBundle\Controller;

use Prooph\EventStore\Stream\StreamName;
use Prooph\ProophessorDo\Model\Todo\Event\ReminderWasAddedToTodo;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\User\Event\UserWasRegistered;
use Rhumsaa\Uuid\Uuid;

class ApiCommandAddReminderToTodoTest extends ControllerBaseTestCase
{
    public function setUp()
    {
        parent::setUp();
        $userId = Uuid::uuid4();
        $todoId = Uuid::uuid4();
        $todoDescription = 'TodoDescription'.rand(10000000, 99999999);
        $reminder = new \DateTime('now');
        $reminder = $reminder->add(\DateInterval::createFromDateString('+1 Year'));

        $this->registerUser($userId, 'testUserName'.rand(10000, 1000000000), 'testUserEMail'.rand(10000, 1000000000).'@prooph.com');
        $this->postTodo($userId, $todoId, $todoDescription);
        $this->addReminderToTodo($userId, $todoId, $reminder);
    }

    public function test_command_add_reminder_to_todo_returns_http_status_202()
    {
        $this->assertEquals(202, self::$client->getResponse()->getStatusCode());
    }

    public function test_command_add_reminder_to_todo_adds_ReminderWasAddedToTodo_event_to_eventstream()
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
        $this->assertInstanceOf(ReminderWasAddedToTodo::class, $event);
    }
}
