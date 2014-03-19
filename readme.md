Lingotek PHP SDK
===
[examples]: /examples/example.php
[API]: http://devzone.lingotek.com
[website]: http://devzone.lingotek.com

The [Lingotek Platform][website] is
a set of APIs that allow you to push and pull multilingual
content from the translation hub.

This repository contains an open source Lingotek SDK that allows you to
access the Lingotek platform using PHP. 


Usage
-----

The [examples][examples] are a good place to start. Here is an example snippet you might try to
started:

```php
require 'Lingotek.php';

$client = new /Lingotek/RestClient(array(
  'access_token'  => YOUR_ACCESS_TOKEN
));

// Get Community
$response = $client->get('community');
```

You can make api calls by choosing the `HTTP method` (e.g., GET, POST, PATCH, PUT, DELETE), the `resource` (e.g., project), and setting optional `parameters`:

```php
$params = array(
  'id' => YOUR_PROJECT_ID
);
$client->get('project', $params);
```

Contributing
------------
This open source project was started by Lingotek developers, but is now
open to developers world-wide.


Report Issues/Bugs
------------------
- [Bugs](http://devzone.lingotek.com/contact)
- [Questions](http://devzone.lingotek.com/contact)

---
Under *BSD 3-Clause License
