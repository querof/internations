# int-api

Hi Internations!

Here you can find:

-   API description.
-   Install instructions.
-   Database script.
-   Endpoints doc.
-   Domain Model
-   Test.
-   Improvements.
-   For your code evaluation.

## API Description

User managment system, JWT based auth. With user, groups and userGroups CRUD actions. REST Api access.

## Install.

-   Run composer to install dependencies:  composer install
-   Run sql script: db/internations.sql
-   Run fixtures: bin/console doctrine:fixtures:load
-   Start symfony server: bin/console serve:start.
-   Enjoy :-).

## Database Schema Script (Mysql) creation DML Script:

Located in: ./db/internations.sql

## Endpoints.

| Name              | Method      | URL                    | Protected | Params                                                                                                               |
| ----------------- | ----------- | ---------------------- | --------- | -------------------------------------------------------------------------------------------------------------------- |
| app_login         | `GET`       | `/v1/login`            | ✘         | "username" : string, "password":string                                                                               |
| user_list         | `GET`       | `/v1/users`            | ✓         | -                                                                                                                    |
| user_search       | `GET`       | `/v1/users/search`     | ✓         | param:{"name" : "Inernations","order":"ASC"}                                                                         |
| user_find         | `GET`       | `/v1/users/{id}`       | ✓         | id: integer                                                                                                          |
| user_create       | `POST`      | `/v1/users/`           | ✓         | name: string, lastname: string,  email:string, roles: array(string), ApiToken: string, password: string              |
| user_update       | `PUT/PATCH` | `/v1/users/{id}`       | ✓         | id: integer, name: string, lastname: string,  email:string, roles: array(string), ApiToken: string, password: string |
| user_delete       | `delete`    | `/v1/users/{id}`       | ✓         | id: integer                                                                                                          |
| group_list        | `GET`       | `/v1/groups`           | ✓         | -                                                                                                                    |
| group_search      | `GET`       | `/v1/groups/search`    | ✓         | param:{"name" : string,"order" : "ASC"}                                                                              |
| group_find        | `GET`       | `/v1/groups/{id}`      | ✓         | id: integer                                                                                                          |
| group_create      | `POST`      | `/v1/groups/`          | ✓         | name: string, description: string,                                                                                   |
| group_update      | `PUT/PATCH` | `/v1/groups/{id}`      | ✓         | id: integer, name: string, description: string                                                                       |
| group_delete      | `delete`    | `/v1/groups/{id}`      | ✓         | id: integer                                                                                                          |
| user_group_list   | `GET`       | `/v1/user/groups`      | ✓         | -                                                                                                                    |
| user_group_create | `POST`      | `/v1/user/groups/`     | ✓         | userId: integer, groupId: integer                                                                                    |
| user_group_delete | `delete`    | `/v1/user/groups/{id}` | ✓         | id: integer                                                                                                          |

## Domain model

Location: doc/UML.pdf

## Test

  vendor/phpunit/phpunit/phpunit tests/

## Improvements:

  Of course the solution can have more features, even some new components to make it
  more “generic” , I think this following will be nice:

-   User register maybe, I only assume it's not part of this scope.
-   Some front-end implementations.
-   Some email validations, like the use of a valid(real) email.
-   Expiration time for user password.

## For your code evaluation

### Stories

-   As an admin I can add users — a user has a name. _** DONE! **._
-   As an admin I can delete users. _** DONE! **._
-   As an admin I can assign users to a group they aren’t already part of. _** DONE! **._
-   As an admin I can remove users from a group. _** DONE! **._
-   As an admin I can create groups. _** DONE! **._
-   As an admin I can delete groups when they no longer have members. _** DONE! **._

### Artifacts

-   Please implement the design using a modern PHP framework. Bonus points if that happens to
    be Symfony 4. Focus equally on software design and code. _** DONE! **._
-   Show me a small domain model for the processes above (in UML or anything else). _** DONE! **._
-   Show me a database model. _** DONE! **._
-   Design a convenient API that other developers would love to use for the tasks above. _** DONE! **._

### Extras

-   _** It's totally OOP oriented; including the use of design patterns(MVC,dependency injection, front controller, singletons) **._
-   _** JWT **._
-   _** Content negotiation **._
-   _** PHPUnit (✓) **._
-   _** And more! **._

Thanks again for the opportunity.

Best.
