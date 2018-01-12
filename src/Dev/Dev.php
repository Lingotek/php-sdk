<?php
namespace Lingotek\Dev;

class Dev {

  static function d($data, $label_or_die = NULL, $die = FALSE) {
    if (is_string($label_or_die))
      echo $label_or_die . "\n";
    print_r($data);
    echo "\n";
    if ($die || is_bool($label_or_die) && $label_or_die)
      die();
  }

  static function dh($data, $label = NULL, $die = FALSE) {
    echo '<pre style="background: #f3f3f3; color: #000">';
    if (is_string($label))
      echo '<h1>' . $label . '</h1>';
    print_r($data);
    echo '</pre>';
    if ($die || is_bool($label) && $label)
      die();
  }

}
