<?php

namespace ProophessorTest\AppBundle\Controller;

use Prooph\EventStore\EventStore;
use Rhumsaa\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiCommandControllerTest extends WebTestCase
{
    /** @var EventStore */
    protected $store;

    public function setUp()
    {
        self::bootKernel();
        $this->store = static::$kernel->getContainer()
            ->get('prooph_event_store.todo_store')
        ;
    }

    public function testCommandRegisterUserReturnsHttpStatus202()
    {
        $client = $this->addUser(Uuid::uuid4(), 'testUserName'.rand(10000, 1000000000), 'testUserEMail'.rand(10000, 1000000000).'@prooph.com');
        $this->assertEquals(202, $client->getResponse()->getStatusCode());
    }

    public function testCommandAddTodoReturnsStatusCode202()
    {
        $userId = Uuid::uuid4();
        $client = $this->addUser($userId, 'testUserName'.rand(10000, 1000000000), 'testUserEMail'.rand(10000, 1000000000).'@prooph.com');
        $this->assertEquals(202, $client->getResponse()->getStatusCode());

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

    protected function addUser(Uuid $id, string $name, string $email){
        $payload = array(
            'user_id' => $id->toString(),
            'name' => $name,
            'email' => $email
        );

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/commands/register-user',
            array(),
            array(),
            array(),
            json_encode($payload)
        );

        return $client;
    }
}
