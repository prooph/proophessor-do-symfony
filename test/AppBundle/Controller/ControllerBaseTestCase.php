<?php
declare(strict_types=1);

namespace ProophessorTest\AppBundle\Controller;

use Prooph\Common\Event\ActionEvent;
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

    /** @var array */
    protected $recordedEvents;

    /** @var Client */
    protected $client;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->store = $this->client->getContainer()
            ->get('prooph_event_store.todo_store')
        ;

        $this->recordedEvents = [];
        $this->store->getActionEventEmitter()->attachListener('commit.post',
            function (ActionEvent $event) {
                foreach ($event->getParam('recordedEvents', new \ArrayIterator()) as $recordedEvent) {
                    $this->recordedEvents[] = $recordedEvent;
                }
            }
        );
    }

    protected function registerUser(Uuid $id, string $name, string $email)
    {
        $payload = array(
            'user_id' => $id->toString(),
            'name' => $name,
            'email' => $email
        );

        $this->client->request(
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

        $this->client->request(
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

        $this->client->request(
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

        $this->client->request(
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

        $this->client->request(
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

        $this->client->request(
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

        $this->client->request(
            'POST',
            '/api/commands/add-reminder-to-todo',
            array(),
            array(),
            array(),
            json_encode($payload)
        );
    }
}
