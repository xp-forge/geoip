<?php namespace com\maxmind\geoip;

class Record extends \lang\Object {
  private $map;

  public function __construct($map) {
    $this->map= $map;
  }

  public function city() { return new Name($this->map['city']); }

  public function continent() { return new Name($this->map['continent']); }

  public function country() { return new Name($this->map['country']); }

  public function location() { return new Name($this->map['location']); }

  public function postal() { return $this->map['postal']; }

  public function subdivisions() {
    return array_map(
      function($subdivision) { return new Name($subdivision); },
      $this->map['subdivisions']
    );
  }

  public function toString() {
    return sprintf(
      "%s@{\n".
      "  [city        ] %s\n".
      "  [country     ] %s\n".
      "  [continent   ] %s\n".
      "  [postal      ] %s\n".
      "  [location    ] %s\n".
      "  [subdivisions] %s\n".
      "}",
      $this->getClassName(),
      \xp::stringOf($this->city()),
      \xp::stringOf($this->country()),
      \xp::stringOf($this->continent()),
      str_replace("\n", "\n  ", \xp::stringOf($this->map['postal'])),
      str_replace("\n", "\n  ", \xp::stringOf($this->map['location'])),
      \xp::stringOf($this->subdivisions())
    );
  }
}