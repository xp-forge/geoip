<?php namespace com\maxmind\geoip\unittest;

use com\maxmind\geoip\GeoIpDatabase;
use com\maxmind\geoip\Location;
use lang\ClassLoader;
use lang\IllegalArgumentException;

class GeoIpDatabaseTest extends \unittest\TestCase {
  const DATABASE = 'GeoIP2-City-Test.mmdb';
  private $fixture;

  /**
   * Sets up test
   */
  public function setUp() {
    $this->fixture= ClassLoader::getDefault()->getResourceAsStream(self::DATABASE)->in();
  }

  #[@test]
  public function open() {
    GeoIpDatabase::open($this->fixture);
  }

  #[@test]
  public function lookup_v4_localhost() {
    $reader= GeoIpDatabase::open($this->fixture);
    $this->assertEquals(null, $reader->lookup('127.0.0.1'));
  }

  #[@test]
  public function lookup_89_160_20_128_slash_121() {
    $record= GeoIpDatabase::open($this->fixture)->lookup('89.160.20.128');
    $this->assertEquals(
      ['Linköping', 'Sweden', new Location(58.4167, 15.6167, ['time_zone' => 'Europe/Stockholm'])],
      [$record->city()->name(), $record->country()->name(), $record->location()]
    );
  }

  #[@test]
  public function lookup_216_160_83_56_slash_125() {
    $record= GeoIpDatabase::open($this->fixture)->lookup('216.160.83.56');
    $this->assertEquals(
      ['Milton', 'United States', new Location(47.2513, -122.3149, ['time_zone' => 'America/Los_Angeles', 'metro_code' => 819])],
      [$record->city()->name(), $record->country()->name(), $record->location()]
    );
  }

  #[@test]
  public function lookup_v6_localhost() {
    $reader= GeoIpDatabase::open($this->fixture);
    $this->assertEquals(null, $reader->lookup('::1'));
  }

  #[@test]
  public function lookup_2001_256_slash_32() {
    $record= GeoIpDatabase::open($this->fixture)->lookup('2001:256::');
    $this->assertEquals(
      [null, "People's Republic of China", new Location(35, 105, [])],
      [$record->city()->name(), $record->country()->name(), $record->location()]
    );
  }

  #[@test]
  public function lookup_2a02_da80_slash_29() {
    $record= GeoIpDatabase::open($this->fixture)->lookup('2a02:da80::');
    $this->assertEquals(
      [null, 'Austria', new Location(47.33333, 13.33333, ['time_zone' => 'Europe/Vienna'])],
      [$record->city()->name(), $record->country()->name(), $record->location()]
    );
  }

  #[@test, @expect(IllegalArgumentException::class), @values([null, '', 'not.an.ip', '::not-v6'])]
  public function lookup_raises_an_exception_when_input_is_not_an_ip_address($value) {
    $reader= GeoIpDatabase::open($this->fixture);
    $reader->lookup($value);
  }
}