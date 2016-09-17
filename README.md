# Proophessor Do Symfony
**prooph components in action**

[![Build Status](https://travis-ci.org/prooph/proophessor-do-symfony.svg)](https://travis-ci.org/prooph/proophessor-do-symfony)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/prooph/improoph)

Proophessor Do Symfony (short *Do*) demonstrates the power of prooph components in conjunction with Symfony.

> This is a clone of [proophessor-do](https://github.com/prooph/proophessor-do) to demonstrate the *Symfony way* with prooph components.

## Business Domain

The business logic implemented in this educational project is very simple and should be known by everybody in one way or the other.
It is about managing todo lists for users whereby a todo can have a deadline and the assigned user can add a reminder to get notified when
time has passed.

## Installation

Please refer to the [installation instructions](https://github.com/prooph/proophessor-do/blob/master/docs/installation.md).

If you have problems with cache files run `sudo chmod 777 var -R`

If you run Symfony proophessor-do app in production mode, you have to compile the assets!

```php
$ docker-compose run --rm php php bin/console assetic:dump --env=prod
```

## Running the app

```php
docker-compose up -d
docker-compose run --rm php php bin/console doctrine:migrations:migrate -n
```

## Learning by doing!

When you play around with the application you will notice missing functionality. This has a simple reason. You explore
a learning application and what is the best way to learn? Right! **Learning by doing!** So if you want to learn something about
CQRS and Event Sourcing:

1. Pick up an open task listed below
2. Get us a note in the corresponding issue that you accept the challenge
3. Ask if you need help -> [![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/prooph/improoph)
4. Have fun and learn!


*Note: Some tasks depend on others and some can be split into sub tasks. Let's discuss this in the issues. And of course you
can also work together. Sharing work doubles knowledge!*

## HALL OF FAME

A successfully merged pull request will add you to the HALL OF FAME!

### Features

- [x] Project set up, register user, post todo - done by [people at prooph](https://github.com/orgs/prooph/people)
- [x] [Mark a todo as done](https://github.com/prooph/proophessor-do/issues/1) - done by [Danny van der Sluijs](https://github.com/DannyvdSluijs)
- [x] [Reopen a todo](https://github.com/prooph/proophessor-do/issues/2) - done by [Bas Kamer](https://github.com/basz)
- [x] [Add deadline to todo](https://github.com/prooph/proophessor-do/issues/35) - done by [Wojtek Gancarczyk](https://github.com/theDisco)
- [x] [Add reminder for assignee](https://github.com/prooph/proophessor-do/issues/60) - done by [Roman Sachse](https://github.com/rommsen)
- [x] [Mark a todo as expired](https://github.com/prooph/proophessor-do/issues/75) - done by [Dranzd Viper](https://github.com/dranzd)
- [ ] Notify assignee when todo deadline is expired - done by [your name here]
- [ ] Notify assignee when reminder time is reached - done by [your name here]
- [ ] Implement a dedicated event bus to replay events as reminders and deadline notifications should not be sent to users again - done by [your name here]
- more features will follow ...

## Tutorials

- [Replay History](https://github.com/prooph/proophessor-do/docs/tutorials/replay_history.md)
- [Take Snapshots](https://github.com/prooph/proophessor-do/docs/tutorials/take_snapshots.md)

## Technology Stack

[We <3 Open Source](https://github.com/prooph/proophessor-do/docs/technology_stack.md)

## Support

- Ask questions on [prooph-users](https://groups.google.com/forum/?hl=de#!forum/prooph) mailing list.
- File issues at [https://github.com/prooph/proophessor-do/issues](https://github.com/prooph/proophessor-do/issues).
- Say hello in the [prooph gitter](https://gitter.im/prooph/improoph) chat.

Happy messaging!
