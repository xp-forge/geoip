<?php namespace com\maxmind\geoip;

use lang\Value;

class Name implements Value {
  private $id, $names, $code;
  public static $UNKNOWN;

  static function __static() {
    self::$UNKNOWN= new class(null, [], null) extends Name {
      static function __static() { }
      public function toString() { return 'com.maxmind.geoip.Name(UNKNOWN)'; }
    };
  }

  /**
   * Creates a new name
   *
   * @param  int $id
   * @param  [:string] $names
   * @param  string $code
   */
  public function __construct($id, $names, $code= null) {
    $this->id= $id;
    $this->names= $names;
    $this->code= $code;
  }

  /** @return int */
  public function id() { return $this->id; }

  /** @return [:string] */
  public function names() { return $this->names; }

  /** @return string */
  public function code() { return $this->code; }

  /**
   * Gets a specific name, or NULL if the name does not exist
   *
   * @param  string $lang
   * @return string
   */
  public function name($lang= 'en') {
    return $this->names[$lang] ?? null;
  }

  /**
   * Creates a string representation of this name
   *
   * @return string
   */
  public function toString() {
    $code= null === $this->code ? '' : '; code= '.$this->code;
    return nameof($this).'(#'.$this->id.': '.$this->name('en').$code.')';
  }

  /**
   * Creates a hash code of this record
   *
   * @return string
   */
  public function hashCode() {
    return 'N'.$this->id.$this->code;
  }

  /**
   * Compares a given value to this record
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? strcmp($this->id.$this->code, $value->id.$value->code) : 1;
  }
}