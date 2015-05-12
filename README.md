# Dox-vagrant

Dox-vagrant is a proof-of-concept vagrant provisioner. The concept is based on an idea for a spec
called Doxfile. This is a simple YAML file defining a specific architecture for your project.

It currently supports web components based on Nginx and app components based on PHP.

# Doxfile concept

A Doxfile is a (simple) YAML file living in the root of a project, defining a specific architecture
for it.

The architecture is based on a group of components. Here is a very basic example:

```yaml
web1: ~
app1: ~
```

This defines a web and an app component. The `~` means it uses it's standard configuration. In this
case that would mean that there is one web instance and one app instance. Both instances form a
group because they share the same number suffix (1). You can define multiple groups like this:

```yaml
web1: ~
app1: ~

web2: ~
```

Grouped instances are allowed to communicate with each other.

## Example

Here is an example of what I have in mind for a final Doxfile.

```yaml
web1:
  nginx:
    webroot: /web
    controller: app.php

app1:
  php:
    version: ~5.5
    modules:
      - mysql
      - curl

fs1:
  fs:
    mounts:
      - /mnt/some_shared_folder

db1:
  mysql: ~

cache1:
  redis: ~

queue1:
  rabbitmq: ~

worker1:
  supervisor: ~
  app1:
    - php app/console consume some_job
```

# Getting started

1. Install dependencies

    ```sh
    $ composer install
    ```

2. Bring vagrant development environment up

    ```sh
    $ vagrant up
    ```

3. Ssh into development environment

    ```sh
    $ vagrant ssh
    ```

4. Run tests

    ```sh
    $ cd apps/dox-vagrant
    $ ./bin/vendor/behat
    $ ./vendor/bin/phpunit tests/
    ```

