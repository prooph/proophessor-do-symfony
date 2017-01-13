<?php

declare(strict_types=1);

namespace ProophessorTest\AppBundle\Controller;

use Prooph\ProophessorDo\Model\User\Event\UserWasRegistered;
use Rhumsaa\Uuid\Uuid;

class ApiCommandRegisterUserTest extends ControllerBaseTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->client = $this->registerUser(
            Uuid::uuid4(),
            'testUserName'.rand(10000, 1000000000),
            'testUserEMail'.rand(10000, 1000000000).'@prooph.com'
        );
    }

    public function test_command_register_user_returns_http_status_202()
    {
        $this->assertEquals(202, $this->client->getResponse()->getStatusCode());
    }

    public function test_command_post_todo_emits_UserWasRegistered_event()
    {
        $this->assertCount(1, $this->recordedEvents);
        $this->assertInstanceOf(UserWasRegistered::class,$this->recordedEvents[0]);
    }
}
