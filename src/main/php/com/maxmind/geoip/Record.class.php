<?php namespace com\maxmind\geoip;

class Record extends \lang\Object {
  private $map;

  public function __construct($map) {
    $this->map= $map;
  }

  public function city() { return isset($this->map['city']) ? new Name($this->map['city']) : Name::$UNKNOWN; }

  public function country() { return isset($this->map['country']) ? new Name($this->map['country']) : Name::$UNKNOWN; }

  public function continent() { return isset($this->map['continent']) ? new Name($this->map['continent']) : Name::$UNKNOWN; }

  public function location() { return isset($this->map['location']) ? $this->map['location'] : null; }

  public function postal() { return isset($this->map['postal']) ? $this->map['postal'] : null; }

  public function subdivisions() {
    return isset($this->map['subdivisions']) ? array_map(
      function($subdivision) { return new Name($subdivision); },
      $this->map['subdivisions']
    ) : array();
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
      str_replace("\n", "\n  ", \xp::stringOf($this->postal())),
      str_replace("\n", "\n  ", \xp::stringOf($this->location())),
      \xp::stringOf($this->subdivisions())
    );
  }
}