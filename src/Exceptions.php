<?php

/**
 * Lingotek API Client
 *
 * Copyright (c) 2014 Lingotek
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 *
 * Usage: readme.md
 */

namespace Lingotek;

class Exception extends \Exception {}

class InvalidArgumentException extends \Exception {}

class RestClientException extends \Exception {}

class NotFound extends \Lingotek\RestClientException {}

class Unauthorized extends \Lingotek\RestClientException {}

class InvalidUrlPatternException extends \Lingotek\RestClientException {}
