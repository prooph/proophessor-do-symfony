# Manual installation

This is the hard way. Please ensure that you not want to use Docker. ;-)

### Requirements:
 - PHP >= v7.1
 - MySql >= v5.7.9 (For JSON support)

### Step 1 - Get source code

`git clone https://github.com/prooph/proophessor-do-symfony.git` into the document root of a local web server.

### Step 2 - Configure Database

Before you can start you have to configure your database connection.
As this is an example application for a CQRS-driven application we are using two different persistence layers.
One is responsible for persisting the **write model**. This task is taken by prooph/event-store.
And the other one is responsible for persisting the **read model** aka **projections**.

### Step 3 - Configuration

#### 3.1 Email sending (mandatory):

tbd

#### 3.2 Read model (mandatory):

 - Copy `.env.dist` to `.env` and make your adjustments to the `DATABASE_URL`.
 - Execute `CREATE DATABASE todo;` on your MySQL instance.

#### 3.3 Event Store

 - Execute migrations by running `php bin/console doctrine:migrations:migrate`
 - Create empty stream: Run `php bin/console event-store:event-stream:create`

### Step 4 - Start the backend scripts

#### 4.1 - Start the projetions in different terminal windows

`php bin/console event-store:projection:run todo_projection`

`php bin/console event-store:projection:run todo_reminder_projection`

`php bin/console event-store:projection:run user_projection`

#### 4.2 Start snapshotters (only if you decided to use 3.4)

tbd

### Step 5 - View It

Open a terminal and navigate to the project root. Then start the PHP built-in web server with `php -S 0.0.0.0:8080 -t public`
and open [http://localhost:8080](http://localhost:8080/) in a browser.

*Note: You can also set the environmental variable `PROOPH_ENV` to `development`. That will forward exception messages to the client in case of an error.
When using the built-in web server you can set the variable like so: `PROOPH_ENV=development php -S 0.0.0.0:8080 -t public`*
