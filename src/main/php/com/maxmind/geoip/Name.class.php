<?php namespace com\maxmind\geoip;

class Name extends \lang\Object {
  private $map;
  public static $UNKNOWN;

  static function __static() {
    self::$UNKNOWN= newinstance(__CLASS__, [['geoname_id' => null, 'names' => ['en' => null]]], '{
      static function __static() { }
      public function toString() { return "com.maxmind.geoip.Name(UNKNOWN)"; }
    }');
  }

  /** @param [:var] $map */
  public function __construct($map) { $this->map= $map; }

  /** @return int */
  public function id() { return $this->map['geoname_id']; }

  /** @return [:string] */
  public function names() { return $this->map['names']; }

  /**
   * Gets a specific name, or NULL if the name does not exist
   *
   * @param  string $lang
   * @return string
   */
  public function name($lang= 'en') {
    return isset($this->map['names'][$lang]) ? $this->map['names'][$lang] : null;
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
    return $this->getClassName().'(#'.$this->map['geoname_id'].': '.$this->map['names']['en'].')';
  }
}