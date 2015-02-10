<?php namespace com\maxmind\geoip;

use util\TimeZone;

class Location extends \lang\Object {
  private $map;
  public static $UNKNOWN;

  static function __static() {
    self::$UNKNOWN= newinstance(__CLASS__, [[]], '{
      static function __static() { }
      public function toString() { return "com.maxmind.geoip.Location(UNKNOWN)"; }
    }');
  }

  /** @param [:var] $map */
  public function __construct($map) { $this->map= $map; }

  /** @return double */
  public function latitude() { return $this->map['latitude']; }

  /** @return double */
  public function longitude() { return $this->map['longitude']; }

  /** @return util.TimeZone */
  public function timeZone() { return isset($this->map['time_zone']) ? new TimeZone($this->map['time_zone']) : null; }

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
    $tz= isset($this->map['time_zone']) ? ', tz= '.$this->map['time_zone'] : '';
    $mc= isset($this->map['metro_code']) ? ', metro= '.$this->map['metro_code'] : '';
    return $this->getClassName().'('.$this->map['latitude'].', '.$this->map['longitude'].$tz.$mc.')';
  }
}