<?php

namespace ProophessorTest\AppBundle\Controller;

use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\MigrationsBundle\Command\MigrationsMigrateDoctrineCommand;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream\StreamName;
use Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted;
use Prooph\ProophessorDo\Model\User\Event\UserWasRegistered;
use Rhumsaa\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;

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

        $application = new Application(static::$kernel);

        $command = new DropDatabaseDoctrineCommand();
        $application->add($command);
        $input = new ArrayInput(array(
            'command' => 'doctrine:database:drop',
            '--force' => true,
        ));
        $command->run($input, new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET));

        // add the database:create command to the application and run it
        $command = new CreateDatabaseDoctrineCommand();
        $application->add($command);
        $input = new ArrayInput(array(
            'command' => 'doctrine:database:create',
        ));
        $command->run($input, new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET));

        // add doctrine:migrations:migrate
        $command = new MigrationsMigrateDoctrineCommand();
        $application->add($command);
        $input = new ArrayInput(array(
            'command' => 'doctrine:migrations:migrate',
            '--quiet' => true,
            '--no-interaction' => true
        ));
        $input->setInteractive(false);
        $command->run($input, new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET));
    }

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
        $client = $this->registerUser($userId, 'testUserName'.rand(10000, 1000000000), 'testUserEMail'.rand(10000, 1000000000).'@prooph.com');
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

    public function test_command_post_todo_adds_TodoWasPosted_event_to_eventstream()
    {
        $userId = Uuid::uuid4();
        $client = $this->registerUser($userId, 'testUserName'.rand(10000, 1000000000), 'testUserEMail'.rand(10000, 1000000000).'@prooph.com');
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
        $stream = $this->store->load(new StreamName('event'));
        $this->assertCount(2, $stream->streamEvents());
        $event = $stream->streamEvents()->current();
        $this->assertInstanceOf(UserWasRegistered::class, $event);
        $stream->streamEvents()->next();
        $event = $stream->streamEvents()->current();
        $this->assertInstanceOf(TodoWasPosted::class, $event);
    }

    protected function registerUser(Uuid $id, string $name, string $email){
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
