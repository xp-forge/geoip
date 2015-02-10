<?php namespace com\maxmind\geoip;

use io\streams\InputStream;
use io\streams\Seekable;
use io\streams\MemoryInputStream;
use lang\FormatException;
use lang\IllegalStateException;

class Reader extends \lang\Object implements \lang\Closeable {
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
   * @return var or NULL if nothing is found.
   */
  public function lookup($addr) {
    $offset= $this->in->offsetOf($addr);
    if (-1 === $offset) {
      return null;
    } else {
      return $this->in->valueAt($offset);
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