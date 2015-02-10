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

  public function __construct($map) {
    $this->map= $map;
  }

  public function id() { return $this->map['geoname_id']; }

  public function name($lang= 'en') { return isset($this->map['names'][$lang]) ? $this->map['names'][$lang] : null; }

  public function names() { return $this->map['names']; }

  public function attribute($name) { return isset($this->map[$name]) ? $this->map[$name] : null; }

  public function toString() {
    return $this->getClassName().'(#'.$this->map['geoname_id'].': '.$this->map['names']['en'].')';
  }
}