<?php

namespace ProophessorTest\AppBundle\Controller;

use Prooph\EventStore\Stream\StreamName;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\User\Event\UserWasRegistered;
use Rhumsaa\Uuid\Uuid;

class ApiCommandControllerBaseTest extends ControllerBaseTestCase
{
    public function test_command_register_user_returns_http_status_202()
    {
        $client = $this->registerUser(Uuid::uuid4(), 'testUserName'.rand(10000, 1000000000), 'testUserEMail'.rand(10000, 1000000000).'@prooph.com');
        $this->assertEquals(202, $client->getResponse()->getStatusCode());
    }

    public function test_command_register_user_adds_UserWasRegistered_event_to_eventstream()
    {
        $this->registerUser(Uuid::uuid4(), 'testUserName'.rand(10000, 1000000000), 'testUserEMail'.rand(10000, 1000000000).'@prooph.com');

        $stream = $this->store->load(new StreamName('event'));
        $this->assertCount(1, $stream->streamEvents());
        $event = $stream->streamEvents()->current();
        $this->assertInstanceOf(UserWasRegistered::class, $event);
    }

    public function test_command_post_todo_returns_http_status_202()
    {
        $userId = Uuid::uuid4();
        $this->registerUser($userId, 'testUserName'.rand(10000, 1000000000), 'testUserEMail'.rand(10000, 1000000000).'@prooph.com');

        $payload = array(
            'assignee_id' => $userId->toString(),
            'todo_id' => Uuid::uuid4()->toString(),
            'text' => 'todoText_'.rand(10000, 1000000000)
        );

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/commands/post-todo',
            array(),
            array(),
            array(),
            json_encode($payload)
        );

        $this->assertEquals(202, $client->getResponse()->getStatusCode());
    }

    public function test_command_post_todo_adds_TodoWasPosted_event_to_eventstream()
    {
        $userId = Uuid::uuid4();
        $this->registerUser($userId, 'testUserName'.rand(10000, 1000000000), 'testUserEMail'.rand(10000, 1000000000).'@prooph.com');

        $payload = array(
            'assignee_id' => $userId->toString(),
            'todo_id' => Uuid::uuid4()->toString(),
            'text' => 'todoText_'.rand(10000, 1000000000)
        );

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/commands/post-todo',
            array(),
            array(),
            array(),
            json_encode($payload)
        );

        $this->assertEquals(202, $client->getResponse()->getStatusCode());
        $stream = $this->store->load(new StreamName('event'));
        $this->assertCount(2, $stream->streamEvents());
        $event = $stream->streamEvents()->current();
        $this->assertInstanceOf(UserWasRegistered::class, $event);
        $stream->streamEvents()->next();
        $event = $stream->streamEvents()->current();
        $this->assertInstanceOf(TodoWasPosted::class, $event);
    }

}
