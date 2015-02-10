<?php namespace com\maxmind\geoip;

use io\streams\InputStream;
use io\streams\MemoryInputStream;
use lang\FormatException;
use lang\IllegalStateException;

class Reader extends \lang\Object {
  const MAGIC_BYTES   = "\xab\xcd\xefMaxMind.com";
  const MAGIC_LENGTH  = 14;
  const META_SEEK_LEN = 512;
  const META_MAX_ITNS = 10;
  const DATA_SEP_SIZE = 16;

  private $in, $meta;

  public function __construct(InputStream $in) {
    $this->in= $in;

    $magic= self::MAGIC_BYTES;

    // Go to EOF, seek backwards
    $it= 1;
    $bytes= '';
    do {
      $this->in->seek($it * -self::META_SEEK_LEN, SEEK_END);
      $bytes= $this->in->read(self::META_SEEK_LEN).$bytes;
      if (false !== ($p= strpos($bytes, self::MAGIC_BYTES))) {
        $this->meta= (new Data(new MemoryInputStream(substr($bytes, $p + self::MAGIC_LENGTH))))->decode();
        $this->meta['search_tree_size']= ($this->meta['node_count'] * $this->meta['record_size'] / 4);
        //echo \xp::stringOf($this->meta), "\n";
        return;
      }
    } while ($it++ < self::META_MAX_ITNS);

    throw new FormatException(sprintf(
      'Cannot find start of meta data (scanned last %d bytes of input)',
      $it * self::META_SEEK_LEN
    ));
  }

  public function lookup($addr) {
    $bytes= array_values(unpack('C*', inet_pton($addr)));
    $count= sizeof($bytes);
    $node= 0;
    $nodes= $this->meta['node_count'];

    // Skip first 96 nodes if we're looking up an IPv4 address in an IPv6 file.
    if (6 === $this->meta['ip_version'] && 4 === $count) {
      for ($i= 0; $i < 96 && $node < $nodes; $i++) {
        $node= $this->node($node, 0);
      }
    }

    for ($i= 0; $i < $count * 8; $i++) {
      if ($node >= $nodes) break;
      $bit= 1 & ((0xff & $bytes[$i >> 3]) >> 7 - ($i % 8));
      $node= $this->node($node, $bit);
    }

    if ($nodes === $node) {
      return null;
    } else if ($node > $nodes) {
      $resolved= $node - $nodes + $this->meta['search_tree_size'];
      $this->in->seek($resolved, SEEK_SET);
      return (new Data($this->in, $this->meta['search_tree_size'] + self::DATA_SEP_SIZE))->decode();
    }

    throw new IllegalStateException('Should not arrive here');
  }

  private function node($number, $index) {
    switch ($this->meta['record_size']) {
      case 24: {
        $this->in->seek($number * 6 + $index * 3, SEEK_SET);
        return unpack('N', "\x00".$this->in->read(3))[1];
        break;
      }

      case 28: {
        $this->in->seek($number * 7 + 3, SEEK_SET);
        if (0 === $index) {
          $middle= (0xf0 & unpack('C', $this->in->read(1))[1]) >> 4;
        } else {
          $middle= 0x0f & unpack('C', $this->in->read(1))[1];
        }

        $this->in->seek($number * 7 + $index * 4, SEEK_SET);
        return unpack('N', chr($middle).$this->in->read(3))[1];
        break;
      }

      case 32: {
        $this->in->seek($number * 8 + $index * 4, SEEK_SET);
        return unpack('N', $this->in->read(4))[1];
        break;
      }

      default: {
        throw new IllegalStateException('Unhandled record size '.\xp::stringOf($this->meta));
      }
    }
  }
}