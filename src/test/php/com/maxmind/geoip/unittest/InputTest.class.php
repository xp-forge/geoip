<?php namespace com\maxmind\geoip\unittest;

use com\maxmind\geoip\Input;
use io\streams\{InputStream, MemoryInputStream};
use lang\{ClassLoader, FormatException, IllegalArgumentException};
use test\{Assert, Expect, Test};

class InputTest {

  /**
   * Returns a class loader resource
   *
   * @param  string $name
   * @return io.streams.InputStream
   */
  private function resourceNamed($name) {
    return ClassLoader::getDefault()->getResourceAsStream($name)->in();
  }

  #[Test]
  public function opens_city_database() {
    $input= new Input($this->resourceNamed('GeoIP2-City-Test.mmdb'));
    $input->close();
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function cannot_open_non_seekable_stream() {
    new Input(new class() implements InputStream {
      public function read($limit= 8192) { }
      public function available() { return -1; }
      public function close() { }
    });
  }

  #[Test, Expect(FormatException::class)]
  public function cannot_open_malformed_stream() {
    new Input(new MemoryInputStream('not.a.mmdb.database'));
  }

  #[Test]
  public function offset_of_existing_ip() {
    $input= new Input($this->resourceNamed('GeoIP2-City-Test.mmdb'));
    Assert::notEquals(-1, $input->offsetOf('89.160.20.128'));
    $input->close();
  }

  #[Test]
  public function offset_of_non_existant_ip() {
    $input= new Input($this->resourceNamed('GeoIP2-City-Test.mmdb'));
    Assert::equals(-1, $input->offsetOf('127.0.0.1'));
    $input->close();
  }
}