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

Contributing
------------
This open source project was started by Lingotek developers, but is now
open to developers world-wide.


Report Issues/Bugs
------------------
[Bugs](http://devzone.lingotek.com/contact)
[Questions](http://devzone.lingotek.com/contact)

---
Under *BSD 3-Clause License
