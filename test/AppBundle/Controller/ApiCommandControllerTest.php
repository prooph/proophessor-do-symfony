<?php

namespace ProophessorTest\AppBundle\Controller;

use Rhumsaa\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

class ApiCommandControllerTest extends WebTestCase
{

     /** @var  Client $client */
    protected $client;

    public function setUp()
    {
        $this->client = self::createClient();
        $this->runCommand($this->client, "doctrine:database:drop --force -e=test -n");
        $this->runCommand($this->client, "doctrine:database:create -e=test -n");
        $this->runCommand($this->client, "doctrine:migrations:migrate -e=test -n");
    }

    public function testCommandRegisterUser()
    {
        $payload = array(
            'user_id' => Uuid::uuid4()->toString(),
            'name' => 'testUserName',
            'email' => 'testUserEMail@mail.com'
        );

        $this->client = static::createClient();
        $this->client->request(
            'POST',
            '/api/commands/register-user',
            array(),
            array(),
            array(),
            json_encode($payload)
        );

        $this->assertEquals(202, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/user-list');
        $this->assertContains($payload['user_id'], $this->client->getResponse()->getContent());
        $this->assertContains($payload['name'], $this->client->getResponse()->getContent());
    }

    /**
     * @param Client $client
     * @param $command
     * @return string|StreamOutput
     */
    public function runCommand(Client $client, $command)
    {
        $application = new Application($client->getKernel());
        $application->setAutoExit(false);

        $fp = tmpfile();
        $input = new StringInput($command);
        $output = new StreamOutput($fp);

        $application->run($input, $output);
        $application->run($input, $output);

        fseek($fp, 0);
        $output = '';
        while (!feof($fp)) {
            $output = fread($fp, 4096);
        }
        fclose($fp);
        return $output;
    }
}
