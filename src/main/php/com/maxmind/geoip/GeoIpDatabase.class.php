<?php namespace com\maxmind\geoip;

use io\streams\InputStream;

class GeoIpDatabase extends \lang\Object {

  public static function open(InputStream $in) {
    return new Reader($in);
  }
}