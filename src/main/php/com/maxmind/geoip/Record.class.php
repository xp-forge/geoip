<?php namespace com\maxmind\geoip;

class Record extends \lang\Object {
  private $map;

  /** @param [:var] $map */
  public function __construct($map) { $this->map= $map; }

  /** @return com.maxmind.geoip.Name */
  public function city() { return isset($this->map['city']) ? new Name($this->map['city']) : Name::$UNKNOWN; }

  /** @return com.maxmind.geoip.Name */
  public function country() { return isset($this->map['country']) ? new Name($this->map['country']) : Name::$UNKNOWN; }

  /** @return com.maxmind.geoip.Name */
  public function continent() { return isset($this->map['continent']) ? new Name($this->map['continent']) : Name::$UNKNOWN; }

  /** @return com.maxmind.geoip.Location */
  public function location() { return isset($this->map['location']) ? new Location($this->map['location']) : Location::$UNKNOWN; }

  /** @return [:var] */
  public function postal() { return isset($this->map['postal']) ? $this->map['postal'] : null; }

  /** @return com.maxmind.geoip.Name[] */
  public function subdivisions() {
    return isset($this->map['subdivisions']) ? array_map(
      function($subdivision) { return new Name($subdivision); },
      $this->map['subdivisions']
    ) : array();
  }

  /**
   * Gets a specific attribute, or NULL if the attribute does not exist
   *
   * @param  string $name
   * @return string
   */
  public function attribute($name) {
    return isset($this->map[$name]) ? $this->map[$name] : null;
  }

  /**
   * Creates a string representation of this name
   *
   * @return string
   */
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