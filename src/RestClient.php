<?php

namespace Lingotek;

use Lingotek\RestClientException;

/**
 * Lingotek SDK
 *
 * Copyright (c) 2014 Lingotek, Inc.
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 *

 *
 * Usage: readme.md
 */

/**
 * Class RestClient
 * @package Lingotek
 *
 * It provides all standard REST call like GET, POST, PUT, DELETE
 * usage:
 * $client = new \Lingotek\RestClient( {options} );
 * $response = $client->put($endpoint, $data);
 */
class RestClient implements \Iterator, \ArrayAccess {

  /** @const DEBUG enable debug to screen */
  const DEBUG = false;

  /** @const VERBOSE if is debug enabled, you can choose verbose level 0=less|1=more */
  const VERBOSE = 0;

  /** @const NO_FORMAT used when no format is detected */
  const NO_FORMAT = 'none';

  /** @const URL_SANDBOX the Lingotek Sandbox endpoint */
  const URL_SANDBOX = "https://cms.lingotek.com/api";

  /** @const URL_PRODUCTION the Lingotek production endpoint */
  const URL_PRODUCTION = "https://myaccount.lingotek.com/api";

  /** @var array  */
  public $options;

  /** @var object cURL resource */
  public $handle;

  /** @var string $response body */
  public $response;

  /** @var array $headers parsed response header object */
  public $headers;

  /** @var array $info response object */
  public $info;

  /** @var string $error response error string */
  public $error;

  /** @var mixed $decoded_response */
  public $decoded_response;

  /** @var int */
  private $iterator_positon;

  /**
   * @param array $options = array(
   *      'headers' => array(),
   *      'parameters' => array(),
   *      'curl_options' => array(),
   *      'user_agent' => "PHP RestClient/0.1.1",
   *      'base_url' => NULL,
   *      'format' => NULL,
   *      'format_regex' => "/(\w+)\/(\w+)(;[.+])?/",
   *      'decoders' => array(
   *          'json' => 'json_decode',
   *          'php' => 'unserialize'
   *      ),
   *      'username' => NULL,
   *      'password' => NULL
   *      );
   */
  public function __construct($options = array()) {
    $default_options = array(
      'headers' => array(),
      'parameters' => array(),
      'curl_options' => array(),
      'user_agent' => "Lingotek PHP SDK/1.0",
      'base_url' => self::URL_SANDBOX,
      'format' => NULL,
      'format_regex' => "/(\w+)\/(\w+)(;[.+])?/",
      'decoders' => array(
        'json' => 'json_decode',
        'php' => 'unserialize'
      ),
      'form_encode_resources' => array(
        'document'
      ),
      'username' => NULL,
      'password' => NULL
    );

    $this->options = array_merge($default_options, $options);
    if (isset($this->options['access_token'])) {
      if (!isset($this->options['headers']['Authorization'])) {
        $this->options['headers']['Authorization'] = 'bearer ' . $this->options['access_token'];
      }
    }
    if (array_key_exists('decoders', $options))
      $this->options['decoders'] = array_merge(
          $default_options['decoders'], $options['decoders']);
  }

  public function set_option($key, $value) {
    $this->options[$key] = $value;
  }

  /**
   * Decoder callbacks must adhere to the following pattern:
   * @param $format
   * @param $method
   */
  public function register_decoder($format, $method) {
    //   array my_decoder(string $data)
    $this->options['decoders'][$format] = $method;
  }

  // Iterable methods:
  public function rewind() {
    $this->decode_response();
    return reset($this->decoded_response);
  }

  public function current() {
    return current($this->decoded_response);
  }

  public function key() {
    return key($this->decoded_response);
  }

  public function next() {
    return next($this->decoded_response);
  }

  public function valid() {
    return is_array($this->decoded_response) && (key($this->decoded_response) !== NULL);
  }

  // ArrayAccess methods:
  public function offsetExists($key) {
    $this->decode_response();
    return is_array($this->decoded_response) ?
        isset($this->decoded_response[$key]) : isset($this->decoded_response->{$key});
  }

  public function offsetGet($key) {
    $this->decode_response();
    if (!$this->offsetExists($key))
      return NULL;

    return is_array($this->decoded_response) ?
        $this->decoded_response[$key] : $this->decoded_response->{$key};
  }

  public function offsetSet($key, $value) {
    throw new RestClientException("Decoded response data is immutable.");
  }

  public function offsetUnset($key) {
    throw new RestClientException("Decoded response data is immutable.");
  }

  /**
   * Parameterize URL by inserting id parameters that match the resource into the URL.
   * Additionally, any single mustache pattern variables (e.g., "{my_var}") will be replaced by matching parameters
   * @param string $url
   * @param string $method (e.g., GET, POST, PUT, PATCH)
   * @param mixed $parameters (array or string)
   */
  private function parameterize_url(&$url, $method, &$parameters) {
    if (is_string($parameters))
      return;
    foreach ($parameters as $parameter_key => $value) {
      $placeholder = "{" . $parameter_key . "}";
      if (strpos($url, $placeholder) !== FALSE) {
        $url = str_replace($placeholder, $value, $url);
        unset($parameters[$parameter_key]);
      }
    }
    if (strpos($url, "{") !== FALSE) {
      throw new InvalidUrlPatternException("Pattern URLs require matching parameter keys be sent in for variable replacements.\n URL: " . $url . "\n Parameters: " . json_encode($parameters));
    }

    $resource = strpos($url, '/') === FALSE ? $url : substr($url, 0, strpos($url, '/'));
    $id_keys = array('id', $resource . '_id');
    foreach ($id_keys as $id_key) {
      if (key_exists($id_key, $parameters)) {
        $url = $url . "/" . $parameters[$id_key];
        unset($parameters[$id_key]);
      }
    }
    $this->format_as_needed($resource, $method, $parameters);
  }

  /**
   * Format parameters using "multipart/form-data" encoding as indicated in the options['form_encode_resources'] array
   * Otherwise use standard format_query encoding (e.g., "var1=A&var2=B")
   * @param string $resource
   * @param string $method
   * @param mixed $parameters (array or string)
   */
  private function format_as_needed($resource, $method, &$parameters) {
    if (in_array($method, array('POST', 'PUT', 'PATCH')) && !in_array($resource, $this->options['form_encode_resources'])) {
      $parameters = $this->format_query($parameters); // parameters will be formatted in the standard way
    }
    else {
      // parameters will be formatted using multipart/form-data
    }
  }

  /**
   * Request method GET
   * @param $url
   * @param array $parameters
   * @param array $headers
   * @return RestClient
   */
  public function get($url, $parameters = array(), $headers = array()) {
    return $this->execute($url, 'GET', $parameters, $headers);
  }

  /**
   * Request method POST
   * @param $url
   * @param array $parameters
   * @param array $headers
   * @return RestClient
   */
  public function post($url, $parameters = array(), $headers = array()) {
    return $this->execute($url, 'POST', $parameters, $headers);
  }

  /**
   * Request method PUT
   * @param $url
   * @param array $parameters
   * @param array $headers
   * @return RestClient
   */
  public function put($url, $parameters = array(), $headers = array()) {
    return $this->execute($url, 'PUT', $parameters, $headers);
  }

  /**
   * Request method PATCH
   * @param $url
   * @param array $parameters
   * @param array $headers
   * @return RestClient
   */
  public function patch($url, $parameters = array(), $headers = array()) {
    return $this->execute($url, 'PATCH', $parameters, $headers);
  }

  /**
   * Request method DELETE
   * @param $url
   * @param array $parameters
   * @param array $headers
   * @return RestClient
   */
  public function delete($url, $parameters = array(), $headers = array()) {
    return $this->execute($url, 'DELETE', $parameters, $headers);
  }

  /**
   * Request method LINK
   * @param $url
   * @param array $parameters
   * @param array $headers
   * @return RestClient
   */
  public function link($url, $parameters = array(), $headers = array()) {
    return $this->execute($url, 'LINK', $parameters, $headers);
  }

  /**
   * Request method UNLINK
   * @param $url
   * @param array $parameters
   * @param array $headers
   * @return RestClient
   */
  public function unlink($url, $parameters = array(), $headers = array()) {
    return $this->execute($url, 'UNLINK', $parameters, $headers);
  }

  /**
   * Executing CURL request
   * @param $url
   * @param string $method
   * @param array $parameters
   * @param array $headers
   * @return RestClient
   * @throws RestClientException
   */
  protected function execute($url, $method = 'GET', $parameters = array(), $headers = array()) {
    $this->debugCounter = 0;
    $this->parameterize_url($url, $method, $parameters);
    if (self::DEBUG)
      $this->debug(0, 'executing curl: ' . $method . ' ' . $this->options['base_url'] . '/' . $url);
    $client = clone $this;
    $client->url = $url;
    $client->handle = curl_init();
    $curlopt = array(
      'CURLOPT_HEADER' => TRUE,
      'CURLOPT_RETURNTRANSFER' => TRUE,
      'CURLOPT_USERAGENT' => $client->options['user_agent']
    );

    if ($client->options['username'] && $client->options['password'])
      $curlopt['CURLOPT_USERPWD'] = sprintf("%s:%s", $client->options['username'], $client->options['password']);

    if (count($client->options['headers']) || count($headers)) {
      $curlopt['CURLOPT_HTTPHEADER'] = array();
      $headers = array_merge($client->options['headers'], $headers);
      foreach ($headers as $key => $value) {
        $curlopt['CURLOPT_HTTPHEADER'][] = sprintf("%s:%s", $key, $value);
      }
    }

    if ($client->options['format'])
      $client->url .= '.' . $client->options['format'];

    if (is_string($parameters)) {
      $parameters = implode('&', $client->options['parameters']) . $parameters;
    }
    else {
      $parameters = array_merge($client->options['parameters'], $parameters);
    }
    if (in_array(strtoupper($method), array('POST', 'DELETE', 'PUT'))) {
      $curlopt['CURLOPT_CUSTOMREQUEST'] = strtoupper($method);
      $curlopt['CURLOPT_POST'] = TRUE;
      $curlopt['CURLOPT_POSTFIELDS'] = is_string($parameters) ? $parameters : $parameters;
    }
    elseif (count($parameters)) {
      $client->url .= strpos($client->url, '?') ? '&' : '?';
      $client->url .= is_string($parameters) ? $parameters : http_build_query($parameters);
    }

    if ($client->options['base_url']) {
      if ($client->url[0] != '/' || substr($client->options['base_url'], -1) != '/')
        $client->url = '/' . $client->url;
      $client->url = $client->options['base_url'] . $client->url;
    }
    $curlopt['CURLOPT_URL'] = $client->url;

    if ($client->options['curl_options']) {
      $curlopt = array_merge($curlopt, $client->options['curl_options']);
    }

    if (self::DEBUG)
      $this->debug(0, 'curl options', $curlopt);
    $curloptparsed = array();
    foreach ($curlopt as $key => $value) {
      $curloptparsed[constant($key)] = $value;
    }
    curl_setopt_array($client->handle, $curloptparsed);

    $client->parse_response(curl_exec($client->handle));
    $client->info = (object) curl_getinfo($client->handle);
    $client->error = curl_error($client->handle);
    curl_close($client->handle);

    $client->decode_response();
    if (self::DEBUG)
      $this->debug(0, 'decoded response', $client->decoded_response);

    //echo "code: [" . $client->info->http_code . " " . $client->headers->http_code . "] ";
    if (substr($client->headers->http_code, 0, 1) !== "2") {// 2xx Success
      $message = '';
      // special cases
      switch ($client->headers->http_code) {
        case 302: $httpcode = 'HTTP/1.1 302 Found';
          $message = 'Redirected to: ' . $client->info->redirect_url;
          break;
        default:
          $message = ' ' . (isset($client->decoded_response->messages) && is_array($client->decoded_response->messages) ? implode("\n", $client->decoded_response->messages) : 'Unknown');
      }

      throw new RestClientException(
      $client->headers->http_code_string . '. ' . $message, $client->headers->http_code
      );
    }

    return $client;
  }

  public function parse_response($response) {
    $headers = null;
    $parts = explode("\r\n\r\n", $response);
    $body = '';
    if (self::DEBUG)
      $this->debug(1, 'curl response', $response);
    foreach ($parts as $index => $part) {
      if (self::DEBUG)
        $this->debug(1, 'parsing part');
      if (preg_match('/^http/i', $part)) {
        if (self::DEBUG)
          $this->debug(1, 'part ' . $index . ' is header', $part);
        $http_ver = strtok($part, "\n");
        if (isset($headers)) {
          if (!array_key_exists('previous', $headers)) {
            $headers = array('previous' => $headers);
          }
          else {
            $tmp = $headers;
            unset($tmp['previous']);
            $headers['previous'][] = $tmp;
          }
        }
        $headers['http_code_string'] = trim($http_ver);
        $http_code_string_arr = explode(" ", $headers['http_code_string']);
        $headers['http_code'] = $http_code_string_arr[1];
        while (($line = strtok("\n")) !== false) {
          if (strlen(trim($line)) == 0)
            break;
          list($key, $value) = explode(':', $line, 2);
          $key = trim(strtolower(str_replace('-', '_', $key)));
          $value = trim($value);
          if (empty($headers[$key])) {
            $headers[$key] = $value;
          }
          elseif (is_array($headers[$key])) {
            $headers[$key][] = $value;
          }
          else {
            $headers[$key] = array($headers[$key], $value);
          }
        }
      }
      else {
        if (self::DEBUG)
          $this->debug(1, 'part ' . $index . ' is body', $part);
        $body = $part;
      }
    }
    if (self::DEBUG)
      $this->debug(0, 'decoded header', $headers);
    $this->headers = (object) $headers;
    $this->response = $body;
  }

  public function get_response_format() {
    if (!property_exists($this, 'response')) //!$this->response
      throw new RestClientException(
      "A response must exist before it can be decoded.");

    // User-defined format.
    if (!empty($this->options['format']))
      return $this->options['format'];
    // Extract format from response content-type header.
    if (!empty($this->headers->content_type)) {
      if (preg_match($this->options['format_regex'], $this->headers->content_type, $matches)) {
        return $matches[2];
      }
    }

    return self::NO_FORMAT; // No response format could not be determined
  }

  public function format_query($parameters, $primary = '=', $secondary = '&') {
    $query = "";
    foreach ($parameters as $key => $value) {
      $pair = array(urlencode($key), urlencode($value));
      $query .= implode($primary, $pair) . $secondary;
    }
    return rtrim($query, $secondary);
  }

  public function decode_response() {
    if (empty($this->decoded_response)) {
      $format = $this->get_response_format();
      if (array_key_exists($format, $this->options['decoders'])) {
        $this->decoded_response = call_user_func(
            $this->options['decoders'][$format], $this->response);
      }
      else {
        $this->decoded_response = $this->response; // assume no decoding is necessary when no response format is provided
      }
    }

    return $this->decoded_response;
  }

  /**
   * Debug function
   */
  private function debug() {
    $args = func_get_args();
    if ($args[0] > self::VERBOSE)
      return;
    if (func_num_args() > 2) {
      print str_pad('=== ' . strtoupper($args[1]) . ' =', 80, '=', STR_PAD_RIGHT) . "\n";
      if (is_array($args[2])) {
        print_r($args[2]);
        print "\n";
      }
      else {
        var_dump($args[2]);
      }
    }
    else {
      print str_pad('=== ' . ($args[1]) . ' =', 80, '=', STR_PAD_RIGHT) . "\n";
    }
  }

}
