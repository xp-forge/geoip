<?php namespace com\maxmind\geoip;

use util\Objects;

class Record implements \lang\Value {
  private $map;

  /** @param [:var] $map */
  public function __construct($map) { $this->map= $map; }

  /** @return com.maxmind.geoip.Name */
  public function city() {
    return isset($this->map['city'])
      ? new Name($this->map['city']['geoname_id'], $this->map['city']['names'])
      : Name::$UNKNOWN
    ;
  }

  /** @return com.maxmind.geoip.Name */
  public function country() {
    return isset($this->map['country'])
      ? new Name($this->map['country']['geoname_id'], $this->map['country']['names'], $this->map['country']['iso_code'])
      : Name::$UNKNOWN
    ;
  }

  /** @return com.maxmind.geoip.Name */
  public function continent() {
    return isset($this->map['continent'])
      ? new Name($this->map['continent']['geoname_id'], $this->map['continent']['names'], $this->map['continent']['code'])
      : Name::$UNKNOWN
    ;
  }

  /** @return com.maxmind.geoip.Location */
  public function location() {
    return isset($this->map['location'])
      ? new Location($this->map['location']['latitude'], $this->map['location']['longitude'], $this->map['location'])
      : Location::$UNKNOWN
    ;
  }

  /** @return [:var] */
  public function postalCode() {
    return isset($this->map['postal'])
      ? $this->map['postal']['code']
      : null
    ;
  }

  /** @return com.maxmind.geoip.Name[] */
  public function subdivisions() {
    $newName= function($map) {
      return new Name($map['geoname_id'], $map['names'], isset($map['iso_code']) ? $map['iso_code'] : null);
    };
    return isset($this->map['subdivisions']) ? array_map($newName, $this->map['subdivisions']) : [];
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
   * Creates a string representation of this record
   *
   * @return string
   */
  public function toString() {
    return sprintf(
      "%s@{\n".
      "  [city        ] %s\n".
      "  [country     ] %s\n".
      "  [continent   ] %s\n".
      "  [postalCode  ] %s\n".
      "  [location    ] %s\n".
      "  [subdivisions] %s\n".
      "}",
      nameof($this),
      \xp::stringOf($this->city()),
      \xp::stringOf($this->country()),
      \xp::stringOf($this->continent()),
      str_replace("\n", "\n  ", \xp::stringOf($this->postalCode())),
      str_replace("\n", "\n  ", \xp::stringOf($this->location())),
      isset($this->map['subdivisions']) ? \xp::stringOf($this->subdivisions()) : '[]'
    );
  }

  /**
   * Creates a hash code of this record
   *
   * @return string
   */
  public function hashCode() {
    return 'R'.Objects::hashOf($this->map);
  }

  /**
   * Compares a given value to this record
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? Objects::compare($this->map, $value->map) : 1;
  }
}