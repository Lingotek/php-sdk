Lingotek PHP SDK
===

The [Lingotek Platform](http://devzone.lingotek.com/) is
a set of APIs that allow you to push and pull multilingual
content from the translation hub.

This repository contains an open source Lingotek SDK that allows you to
access the Lingotek platform using PHP. 


Usage
-----

The [examples][examples] are a good place to start. Here is an example snippet you'll need to
started:

```php
require 'Lingotek.php';

$client = new /Lingotek/RestClient(array(
  'access_token'  => 'YOUR_ACCESS_TOKEN'
));

// Get Community
$response = $client->get('community');
```

You can make api calls by choosing the `HTTP method` and setting optional `parameters`:

```php
$params = array(
  'id' => PROJECT_ID
);
$client->get('project', $params);
```

With Composer:

- Add the `"lingotek/php": "@stable"` into the `require` section of your `composer.json`.
- Run `composer install`.
- The example will look like

```php
if (($loader = require_once __DIR__ . '/vendor/autoload.php') == null)  {
  die('Vendor directory not found, Please run composer install.');
}

$client = new /Lingotek/RestClient(array(
  'access_token'  => 'YOUR_ACCESS_TOKEN'
));

// Get Community
$response = $client->get('community');
```

[examples]: /examples/example.php
[API]: http://devzone.lingotek.com

Tests
-----

In order to keep us nimble and allow us to bring you new functionality, without
compromising on stability, we have ensured full test coverage of the SDK.
We are including this in the open source repository to assure you of our
commitment to quality, but also with the hopes that you will contribute back to
help keep it stable. The easiest way to do so is to file bugs and include a
test case.

The tests can be executed by using this command from the base directory:

    phpunit --stderr --bootstrap tests/bootstrap.php tests/tests.php


Contributing
===========
This open source project was started by Lingotek developers, but is now
open to developers world-wide.


Report Issues/Bugs
===============
[Bugs](http://devzone.lingotek.com/contact)

[Questions](http://devzone.lingotek.com/contact)

---
Under *BSD 3-Clause License
