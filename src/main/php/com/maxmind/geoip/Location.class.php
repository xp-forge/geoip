<?php namespace com\maxmind\geoip;

use util\TimeZone;
use util\Objects;

class Location extends \lang\Object {
  private $lat, $long, $attr;
  public static $UNKNOWN;

  static function __static() {
    self::$UNKNOWN= newinstance(__CLASS__, [0.0, 0.0, []], '{
      static function __static() { }
      public function toString() { return "com.maxmind.geoip.Location(UNKNOWN)"; }
    }');
  }

  /**
   * Creates a new location
   *
   * @param  double $lat Latitude
   * @param  double $long Longitude
   * @param  [:var] $attr Further attributes
   */
  public function __construct($lat, $long, $attr) {
    unset($attr['latitude'], $attr['longitude']);

    $this->lat= $lat;
    $this->long= $long;
    $this->attr= $attr;
  }

  /** @return double */
  public function latitude() { return $this->lat; }

  /** @return double */
  public function longitude() { return $this->long; }

  /** @return util.TimeZone */
  public function timeZone() { return isset($this->attr['time_zone']) ? new TimeZone($this->attr['time_zone']) : null; }

  /**
   * Gets a specific attribute, or NULL if the attribute does not exist
   *
   * @param  string $name
   * @return string
   */
  public function attribute($name) {
    return isset($this->attr[$name]) ? $this->attr[$name] : null;
  }

  /**
   * Creates a string representation of this name
   *
   * @return string
   */
  public function toString() {
    $tz= isset($this->attr['time_zone']) ? '; tz= '.$this->attr['time_zone'] : '';
    return $this->getClassName().'('.$this->lat.','.$this->long.$tz.')';
  }

  /**
   * Test whether a given value is equal to this location instance.
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && (
      abs($this->lat - $cmp->lat) < 0.00001 &&
      abs($this->long - $cmp->long) < 0.00001 &&
      Objects::equal($this->attr, $cmp->attr)
    );
  }
}