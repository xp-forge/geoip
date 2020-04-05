<?php namespace com\maxmind\geoip;

use io\streams\FileInputStream;
use io\streams\InputStream;

class GeoIpDatabase {

  /**
   * Opens a database
   *
   * @param  io.streams.InputStream|io.File|io.Path|string $in
   */
  public static function open($in) {
    if ($in instanceof InputStream) {
      return new Reader($in);
    } else {
      return new Reader(new FileInputStream($in));
    }
  }
}