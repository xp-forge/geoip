<?php namespace com\maxmind\geoip;

use lang\Value;
use util\Objects;

class Record implements Value {
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
    return $this->map['postal']['code'] ?? null;
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
    return $this->map[$name] ?? null;
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
      Objects::stringOf($this->city()),
      Objects::stringOf($this->country()),
      Objects::stringOf($this->continent()),
      str_replace("\n", "\n  ", Objects::stringOf($this->postalCode())),
      str_replace("\n", "\n  ", Objects::stringOf($this->location())),
      isset($this->map['subdivisions']) ? Objects::stringOf($this->subdivisions()) : '[]'
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