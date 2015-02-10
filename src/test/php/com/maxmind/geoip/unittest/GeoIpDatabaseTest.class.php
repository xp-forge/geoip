<?php namespace com\maxmind\geoip\unittest;

use com\maxmind\geoip\GeoIpDatabase;
use io\streams\FileInputStream;
use lang\ClassLoader;
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
}