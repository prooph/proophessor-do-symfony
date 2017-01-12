<?php
declare(strict_types=1);

namespace ProophessorTest\AppBundle\Controller;

use Prooph\Common\Event\ActionEvent;
use Rhumsaa\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Prooph\EventStore\EventStore;

abstract class ControllerBaseTestCase extends WebTestCase
{
    /** @var  Client  */
    protected $client;

    /** @var EventStore */
    protected $store;

    /** @var array */
    protected $recordedEvents = [];

    public function setUp()
    {}

    protected function registerUser(Uuid $id, string $name, string $email)
    {
        $payload = array(
            'user_id' => $id->toString(),
            'name' => $name,
            'email' => $email
        );

        return $this->request('POST', '/api/commands/register-user', $payload);
    }

    protected function postTodo(Uuid $assigneeId, Uuid $todoId, string $todoDescription)
    {
        $payload = array(
            'assignee_id' => $assigneeId->toString(),
            'todo_id' => $todoId->toString(),
            'text' => $todoDescription
        );

        return $this->request('POST', '/api/commands/post-todo', $payload);
    }

    protected function markTodoAsDone(Uuid $todoId, string $status)
    {
        $payload = array(
            'todo_id' => $todoId->toString(),
            'status' => $status
        );

        return $this->request('POST', '/api/commands/mark-todo-as-done', $payload);
    }

    protected function markTodoAsExpired(Uuid $todoId)
    {
        $payload = array(
            'todo_id' => $todoId->toString()
        );

        return $this->request('POST', '/api/commands/mark-todo-as-expired', $payload);
    }

    protected function reopenTodo(Uuid $todoId)
    {
        $payload = array(
            'todo_id' => $todoId->toString(),
        );

        return $this->request('POST', '/api/commands/reopen-todo', $payload);
    }

    protected function addDeadlineToTodo(Uuid $userId, Uuid $todoId, \DateTime $deadline)
    {
        $payload = array(
            'todo_id' => $todoId->toString(),
            'user_id' => $userId->toString(),
            'deadline' => $deadline->format("Y/m/d H:i:s")
        );

        return $this->request('POST', '/api/commands/add-deadline-to-todo', $payload);
    }

    protected function addReminderToTodo(Uuid $userId, Uuid $todoId, \DateTime $reminder)
    {
        $payload = array(
            'todo_id' => $todoId->toString(),
            'user_id' => $userId->toString(),
            'reminder' => $reminder->format("Y/m/d H:i:s")
        );

        return $this->request('POST', '/api/commands/add-reminder-to-todo', $payload);
    }

    private function request(string $type, string $url, array $payload){
        $client = $this->client();
        $client->request(
            $type,
            $url,
            array(),
            array(),
            array(),
            json_encode($payload)
        );
        return $client;
    }

    private function client(){
        $client = static::createClient();
        $this->store = $client->getContainer()->get('prooph_event_store.todo_store');
        $this->store->getActionEventEmitter()->attachListener('commit.post',
            function (ActionEvent $event) {
                foreach ($event->getParam('recordedEvents', new \ArrayIterator()) as $recordedEvent) {
                    $this->recordedEvents[] = $recordedEvent;
                }
            }
        );

        return $client;
    }
}
