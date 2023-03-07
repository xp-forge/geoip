<?php namespace com\maxmind\geoip\unittest;

use com\maxmind\geoip\{GeoIpDatabase, Location};
use lang\{ClassLoader, IllegalArgumentException};
use test\{Assert, Before, Expect, Test, Values};

class GeoIpDatabaseTest {
  const DATABASE= 'GeoIP2-City-Test.mmdb';
  private $fixture;

  #[Before]
  public function fixture() {
    $this->fixture= ClassLoader::getDefault()->getResourceAsStream(self::DATABASE);
  }

  #[Test]
  public function open_file() {
    GeoIpDatabase::open($this->fixture);
  }

  #[Test]
  public function open_uri() {
    GeoIpDatabase::open($this->fixture->getURI());
  }

  #[Test]
  public function open_stream() {
    GeoIpDatabase::open($this->fixture->in());
  }

  #[Test]
  public function lookup_v4_localhost() {
    $reader= GeoIpDatabase::open($this->fixture);
    Assert::equals(null, $reader->lookup('127.0.0.1'));
  }

  #[Test]
  public function lookup_89_160_20_128_slash_121() {
    $record= GeoIpDatabase::open($this->fixture)->lookup('89.160.20.128');
    Assert::equals(
      ['Linköping', 'Sweden', new Location(58.4167, 15.6167, ['time_zone' => 'Europe/Stockholm'])],
      [$record->city()->name(), $record->country()->name(), $record->location()]
    );
  }

  #[Test]
  public function lookup_216_160_83_56_slash_125() {
    $record= GeoIpDatabase::open($this->fixture)->lookup('216.160.83.56');
    Assert::equals(
      ['Milton', 'United States', new Location(47.2513, -122.3149, ['time_zone' => 'America/Los_Angeles', 'metro_code' => 819])],
      [$record->city()->name(), $record->country()->name(), $record->location()]
    );
  }

  #[Test]
  public function lookup_v6_localhost() {
    $reader= GeoIpDatabase::open($this->fixture);
    Assert::equals(null, $reader->lookup('::1'));
  }

  #[Test]
  public function lookup_2001_256_slash_32() {
    $record= GeoIpDatabase::open($this->fixture)->lookup('2001:256::');
    Assert::equals(
      [null, "People's Republic of China", new Location(35, 105, [])],
      [$record->city()->name(), $record->country()->name(), $record->location()]
    );
  }

  #[Test]
  public function lookup_2a02_da80_slash_29() {
    $record= GeoIpDatabase::open($this->fixture)->lookup('2a02:da80::');
    Assert::equals(
      [null, 'Austria', new Location(47.33333, 13.33333, ['time_zone' => 'Europe/Vienna'])],
      [$record->city()->name(), $record->country()->name(), $record->location()]
    );
  }

  #[Test, Expect(IllegalArgumentException::class), Values([null, '', 'not.an.ip', '::not-v6'])]
  public function lookup_raises_an_exception_when_input_is_not_an_ip_address($value) {
    $reader= GeoIpDatabase::open($this->fixture);
    $reader->lookup($value);
  }
}