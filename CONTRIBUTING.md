# Contributing

Visit [github.com/prooph/service-bus-symfony-bundle/](https://github.com/prooph/service-bus-symfony-bundle/ "Project Website") for the project website.

- Make sure you have execute `composer install`
- Be sure you are in the root directory

## Resources

If you wish to contribute to `service-bus-symfony-bundle`, please be sure to read to the following resources:

 -  Coding Standards: [PSR-0/1/2/4](https://github.com/php-fig/fig-standards/tree/master/accepted)
 -  Git Guide: [README-GIT.md](https://github.com/prooph/service-bus-symfony-bundle/blob/master/README-GIT.md)

If you are working on new features, or refactoring an existing component, please create an issue first, so we can discuss
it.

## Running tests

To run tests execute *phpunit*:

  ```sh
  $ ./vendor/bin/phpunit
  ```

## Running PHPCodeSniffer

To check coding standards execute *phpcs*:

  ```sh
  $ ./vendor/bin/phpcs
  ```

To auto fix coding standard issues execute:

  ```sh
  $ ./vendor/bin/phpcbf
  ```

## Generate documentation

To generate the documentation execute *bookdown*:

```sh
$ ./vendor/bin/bookdown doc/bookdown.json
```

## Composer shortcuts

For every program above there are shortcuts defined in the `composer.json` file.

* `check`: Executes PHPCodeSniffer and PHPUnit
* `cs`: Executes PHPCodeSniffer
* `cs-fix`: Executes PHPCodeSniffer and auto fixes issues
* `test`: Executes PHPUnit
* `test-coverage`: Executes PHPUnit with code coverage
* `docs`: Generates awesome Bookdown.io docs
