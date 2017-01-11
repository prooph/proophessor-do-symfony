<?php
declare(strict_types=1);

namespace ProophessorTest\AppBundle\Controller;

use Rhumsaa\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\MigrationsBundle\Command\MigrationsMigrateDoctrineCommand;
use Prooph\EventStore\EventStore;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;

abstract class ControllerBaseTestCase extends WebTestCase
{
    /** @var EventStore */
    protected $store;

    /** @var Client */
    protected static $client;

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

        self::$client = static::createClient();
    }

    protected function registerUser(Uuid $id, string $name, string $email)
    {
        $payload = array(
            'user_id' => $id->toString(),
            'name' => $name,
            'email' => $email
        );

        self::$client->request(
            'POST',
            '/api/commands/register-user',
            array(),
            array(),
            array(),
            json_encode($payload)
        );
    }

    protected function postTodo(Uuid $assigneeId, Uuid $todoId, string $todoDescription)
    {
        $payload = array(
            'assignee_id' => $assigneeId->toString(),
            'todo_id' => $todoId->toString(),
            'text' => $todoDescription
        );

        self::$client->request(
            'POST',
            '/api/commands/post-todo',
            array(),
            array(),
            array(),
            json_encode($payload)
        );
    }

    protected function markTodoAsDone(Uuid $todoId, string $status)
    {
        $payload = array(
            'todo_id' => $todoId->toString(),
            'status' => $status
        );

        self::$client->request(
            'POST',
            '/api/commands/mark-todo-as-done',
            array(),
            array(),
            array(),
            json_encode($payload)
        );
    }

    protected function markTodoAsExpired(Uuid $todoId)
    {
        $payload = array(
            'todo_id' => $todoId->toString()
        );

        self::$client->request(
            'POST',
            '/api/commands/mark-todo-as-expired',
            array(),
            array(),
            array(),
            json_encode($payload)
        );
    }

    protected function reopenTodo(Uuid $todoId)
    {
        $payload = array(
            'todo_id' => $todoId->toString(),
        );

        self::$client->request(
            'POST',
            '/api/commands/reopen-todo',
            array(),
            array(),
            array(),
            json_encode($payload)
        );
    }

    protected function addDeadlineToTodo(Uuid $userId, Uuid $todoId, \DateTime $deadline)
    {
        $payload = array(
            'todo_id' => $todoId->toString(),
            'user_id' => $userId->toString(),
            'deadline' => $deadline->format("Y/m/d H:i:s")
        );

        self::$client->request(
            'POST',
            '/api/commands/add-deadline-to-todo',
            array(),
            array(),
            array(),
            json_encode($payload)
        );
    }

    protected function addReminderToTodo(Uuid $userId, Uuid $todoId, \DateTime $reminder)
    {
        $payload = array(
            'todo_id' => $todoId->toString(),
            'user_id' => $userId->toString(),
            'reminder' => $reminder->format("Y/m/d H:i:s")
        );

        self::$client->request(
            'POST',
            '/api/commands/add-reminder-to-todo',
            array(),
            array(),
            array(),
            json_encode($payload)
        );
    }
}
