<?php namespace com\maxmind\geoip\unittest;

use com\maxmind\geoip\GeoIpDatabase;
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

  #[@test, @values(['89.160.20.128', '216.160.83.56'])]
  public function lookup_v4($addr) {
    $reader= GeoIpDatabase::open($this->fixture);
    $this->assertNotEquals(null, $reader->lookup($addr));
  }

  #[@test]
  public function lookup_v6_localhost() {
    $reader= GeoIpDatabase::open($this->fixture);
    $this->assertEquals(null, $reader->lookup('::1'));
  }

  #[@test, @values(['2001:256::', '2a02:da80::'])]
  public function lookup_v6($addr) {
    $reader= GeoIpDatabase::open($this->fixture);
    $this->assertNotEquals(null, $reader->lookup($addr));
  }

  #[@test, @expect(IllegalArgumentException::class), @values([null, '', 'not.an.ip', '::not-v6'])]
  public function lookup_raises_an_exception_when_input_is_not_an_ip_address($value) {
    $reader= GeoIpDatabase::open($this->fixture);
    $reader->lookup($value);
  }
}