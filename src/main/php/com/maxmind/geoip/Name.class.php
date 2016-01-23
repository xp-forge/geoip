<?php namespace com\maxmind\geoip;

class Name extends \lang\Object {
  private $id, $names, $code;
  public static $UNKNOWN;

  static function __static() {
    self::$UNKNOWN= newinstance(__CLASS__, [null, [], null], '{
      static function __static() { }
      public function toString() { return "com.maxmind.geoip.Name(UNKNOWN)"; }
    }');
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
    return isset($this->names[$lang]) ? $this->names[$lang] : null;
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
}