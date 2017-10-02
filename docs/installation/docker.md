# Installation using Docker

First ensure [Docker](https://docs.docker.com/engine/installation/ubuntulinux/) and [Docker Compose](https://docs.docker.com/compose/install/) 
are installed. It's recommended to use the latest version of Docker and Docker Compose. Docker will download all 
dependencies and starts the containers.

### Step 1 - Get the source code
The application runs as a Docker volume. First download the source to a local directory:
`git clone https://github.com/prooph/proophessor-do-symfony.git`

If you are using a Linux distribution running SELinux then set the security context to allow Docker access to the volume:
`chcon -Rt svirt_sandbox_file_t proophessor-do-symfony/`

All the Docker commands are run from the source directory, so also do:
`cd proophessor-do-symfony/`

### Step 2 - Install dependencies

To ensure you have the latest Docker images for the default application execute:

```bash
$ docker pull prooph/php:7.1-fpm && docker pull prooph/composer:7.1 && docker pull prooph/nginx:www
```

Install PHP dependencies via Composer

#### Note for Windows: Replace `$(pwd)` with `%cd%` in all commands

```bash
$ docker run --rm -it --volume $(pwd):/app prooph/composer:7.1 install -o --prefer-dist

# respectively Windows:
$ docker run --rm -it --volume %cd%:/app prooph/composer:7.1 install -o --prefer-dist
```

### Step 3 - Configure

#### 3.1 Email sending (mandatory):

tbd

#### 3.2 Read model (mandatory):

Copy `.env.dist` to `.env`. The file already contains the correct DATABASE_URL to connect to the MySql container.

#### 3.3 Start your Docker Containers

```bash
$ docker-compose up -d
```

#### 3.4 Create the initial event stream with the already started container:

```bash
$ docker-compose run --rm php php bin/console event-store:event-stream:create
```

### Step 4 - That's it!
Now open [http://localhost:8080](http://localhost:8080/) and have fun.
