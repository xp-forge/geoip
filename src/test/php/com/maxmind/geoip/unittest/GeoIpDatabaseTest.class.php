<?php namespace com\maxmind\geoip\unittest;

use com\maxmind\geoip\GeoIpDatabase;
use io\streams\FileInputStream;
use lang\ClassLoader;
use lang\IllegalArgumentException;
use unittest\PrerequisitesNotMetError;

class GeoIpDatabaseTest extends \unittest\TestCase {
  const DATABASE = 'GeoLite2-City.mmdb';

  public function setUp() {
    $this->loader= ClassLoader::getDefault();
    if (!$this->loader->providesResource(self::DATABASE)) {
      throw new PrerequisitesNotMetError('Download database first', null, [self::DATABASE]);
    }
  }

  #[@test]
  public function open() {
    GeoIpDatabase::open($this->loader->getResourceAsStream(self::DATABASE)->in());
  }

  #[@test]
  public function lookup_v4_localhost() {
    $reader= GeoIpDatabase::open($this->loader->getResourceAsStream(self::DATABASE)->in());
    $this->assertEquals(null, $reader->lookup('127.0.0.1'));
  }

  #[@test, @values(['92.203.53.176', '8.8.8.8'])]
  public function lookup_v4($addr) {
    $reader= GeoIpDatabase::open($this->loader->getResourceAsStream(self::DATABASE)->in());
    $this->assertNotEquals(null, $reader->lookup($addr));
  }

  #[@test]
  public function lookup_v6_localhost() {
    $reader= GeoIpDatabase::open($this->loader->getResourceAsStream(self::DATABASE)->in());
    $this->assertEquals(null, $reader->lookup('::1'));
  }

  #[@test, @expect(IllegalArgumentException::class), @values([null, '', 'not.an.ip', '::not-v6'])]
  public function lookup_raises_an_exception_when_input_is_not_an_ip_address($value) {
    $reader= GeoIpDatabase::open($this->loader->getResourceAsStream(self::DATABASE)->in());
    $reader->lookup($value);
  }
}