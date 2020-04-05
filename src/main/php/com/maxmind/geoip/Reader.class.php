<?php namespace com\maxmind\geoip;

use io\streams\InputStream;
use io\streams\MemoryInputStream;
use io\streams\Seekable;
use lang\Closeable;
use lang\FormatException;
use lang\IllegalStateException;

class Reader implements Closeable {
  private $in;

  /**
   * Creates a new reader instance
   *
   * @param  io.streams.InputStream $in
   */
  public function __construct(InputStream $in) {
    $this->in= new Input($in);
  }

  /**
   * Looks up an IP address
   *
   * @param  string $addr
   * @return com.maxmind.geoip.Record or NULL if nothing is found.
   */
  public function lookup($addr) {
    $offset= $this->in->offsetOf($addr);
    if (-1 === $offset) {
      return null;
    } else {
      return new Record($this->in->valueAt($offset));
    }
  }

  /**
   * Closes underlying input
   *
   * @return void
   */
  public function close() {
    $this->in->close();
  }
}