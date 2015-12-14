<?php namespace com\maxmind\geoip\unittest;

use io\streams\InputStream;
use com\maxmind\geoip\Input;
use lang\ClassLoader;
use lang\IllegalArgumentException;
use lang\FormatException;
use io\streams\MemoryInputStream;

class InputTest extends \unittest\TestCase {

  /**
   * Returns a class loader resource
   *
   * @param  string $name
   * @return io.streams.InputStream
   */
  private function resourceNamed($name) {
    return ClassLoader::getDefault()->getResourceAsStream($name)->in();
  }

  #[@test]
  public function opens_city_database() {
    $input= new Input($this->resourceNamed('GeoIP2-City-Test.mmdb'));
    $input->close();
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function cannot_open_non_seekable_stream() {
    new Input(newinstance(InputStream::class, [], [
      'read'      => function($limit= 8192) { },
      'available' => function() { return -1; },
      'close'     => function() { }
    ]));
  }

  #[@test, @expect(FormatException::class)]
  public function cannot_open_malformed_stream() {
    new Input(new MemoryInputStream('not.a.mmdb.database'));
  }

  #[@test]
  public function offset_of_existing_ip() {
    $input= new Input($this->resourceNamed('GeoIP2-City-Test.mmdb'));
    $this->assertNotEquals(-1, $input->offsetOf('89.160.20.128'));
    $input->close();
  }

  #[@test]
  public function offset_of_non_existant_ip() {
    $input= new Input($this->resourceNamed('GeoIP2-City-Test.mmdb'));
    $this->assertEquals(-1, $input->offsetOf('127.0.0.1'));
    $input->close();
  }
}