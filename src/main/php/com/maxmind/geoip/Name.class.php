<?php namespace com\maxmind\geoip;

class Name extends \lang\Object {
  private $map;

  public function __construct($map) {
    $this->map= $map;
  }

  public function id() { return $this->map['geoname_id']; }

  public function name($lang= 'en') { return $this->map['names'][$lang]; }

  public function names() { return $this->map['names']; }

  public function attribute($name) { return $this->map[$name]; }

  public function toString() {
    return $this->getClassName().'(#'.$this->map['geoname_id'].': '.$this->map['names']['en'].')';
  }
}