<?php namespace com\maxmind\geoip;

use io\streams\InputStream;
use io\streams\Seekable;
use lang\FormatException;
use lang\IllegalStateException;
use lang\IllegalArgumentException;
use math\BigInt;
use io\ByteOrder;

class Input extends \lang\Object {
  const MAGIC_BYTES        = "\xab\xcd\xefMaxMind.com";
  const MAGIC_LENGTH       = 14;
  const META_SEEK_LEN      = 512;
  const META_MAX_ITNS      = 10;
  const DATA_SEP_SIZE      = 16;

  const TYPE_EXTENDED      = 0x00;
  const TYPE_POINTER       = 0x01;
  const TYPE_UTF8_STRING   = 0x02;
  const TYPE_DOUBLE        = 0x03;
  const TYPE_BYTES         = 0x04;
  const TYPE_UINT16        = 0x05;
  const TYPE_UINT32        = 0x06;
  const TYPE_MAP           = 0x07;
  const TYPE_INT32         = 0x08;
  const TYPE_UINT64        = 0x09;
  const TYPE_UINT128       = 0x0a;
  const TYPE_ARRAY         = 0x0b;
  const TYPE_CONTAINER     = 0x0c;
  const TYPE_END_MARKER    = 0x0d;
  const TYPE_BOOLEAN       = 0x0e;
  const TYPE_FLOAT         = 0x0f;

  private $in, $meta;
  private static $READ, $REORDER;

  static function __static() {
    self::$READ= [
      self::TYPE_EXTENDED      => function($data, $size) {
        throw new IllegalStateException('TYPE_EXTENDED not yet implemented');
      },
      self::TYPE_POINTER       => function($data, $size) {
        throw new IllegalStateException('TYPE_POINTER not yet implemented');
      },
      self::TYPE_UTF8_STRING   => function($data, $size) {
        return $data->nextBytes($size);
      },
      self::TYPE_DOUBLE        => function($data, $size) {
        return $data->nextDouble($size);
      },
      self::TYPE_BYTES         => function($data, $size) {
        return $data->nextBytes($size);
      },
      self::TYPE_UINT16        => function($data, $size) {
        return $data->nextUint($size);
      },
      self::TYPE_UINT32        => function($data, $size) {
        return $data->nextUint($size);
      },
      self::TYPE_MAP           => function($data, $size) {
        $map= [];
        for ($i= 0; $i < $size; $i++) {
          $map[$data->nextValue()]= $data->nextValue();
        }
        return $map;
      },
      self::TYPE_INT32         => function($data, $size) {
        return $data->nextInt($size);
      },
      self::TYPE_UINT64        => function($data, $size) {
        return $data->nextBigint($size);
      },
      self::TYPE_UINT128       => function($data, $size) {
        return $data->nextBigint($size);
      },
      self::TYPE_ARRAY         => function($data, $size) {
        $array= [];
        for ($i= 0; $i < $size; $i++) {
          $array[]= $data->nextValue();
        }
        return $array;
      },
      self::TYPE_CONTAINER     => function($data, $size) {
        throw new IllegalStateException('TYPE_CONTAINER not yet implemented');
      },
      self::TYPE_END_MARKER    => function($data, $size) {
        throw new IllegalStateException('TYPE_END_MARKER not yet implemented');
      },
      self::TYPE_BOOLEAN       => function($data, $size) {
        return 0 === $size ? false : true;
      },
      self::TYPE_FLOAT         => function($data, $size) {
        throw new IllegalStateException('TYPE_FLOAT not yet implemented');
      },
    ];

    self::$REORDER= (ByteOrder::nativeOrder() === LITTLE_ENDIAN);
  }

  /**
   * Creates a new input instance
   *
   * @param  io.streams.InputStream $in
   * @throws lang.IllegalArgumentException if stream is not seekable
   * @throws lang.FormatException if the meta data cannot be found or is malformed
   */
  public function __construct(InputStream $in) {
    if (!$in instanceof Seekable) {
      throw new IllegalArgumentException('Must pass a seekable stream');
    }

    $this->in= $in;
    $magic= self::MAGIC_BYTES;

    // Go to EOF, seek backwards
    $it= 1;
    $bytes= '';
    do {
      $this->in->seek($it * -self::META_SEEK_LEN, SEEK_END);
      $bytes= $this->in->read(self::META_SEEK_LEN).$bytes;
      if (false !== ($p= strpos($bytes, self::MAGIC_BYTES))) {
        $this->in->seek(-self::META_SEEK_LEN + $p + self::MAGIC_LENGTH, SEEK_CUR);
        $this->initialize($this->nextValue());
        return;
      }
    } while ($it++ < self::META_MAX_ITNS);

    throw new FormatException(sprintf(
      'Cannot find start of meta data (scanned last %d bytes of input)',
      $it * self::META_SEEK_LEN
    ));
  }

  /**
   * Initialize
   *
   * @param  [:var] $meta
   */
  private function initialize($meta) {
    $this->meta= $meta;

    $this->meta['search_tree_size']= ($this->meta['node_count'] * $this->meta['record_size'] / 4);
    $this->meta['base']= $this->meta['search_tree_size'] + self::DATA_SEP_SIZE;

    switch ($this->meta['record_size']) {
      case 24: {
        $this->meta['read_node']= function($number, $index) {
          $this->in->seek($number * 6 + $index * 3, SEEK_SET);
          return unpack('N', "\x00".$this->in->read(3))[1];
        };
        break;
      }

      case 28: {
        $this->meta['read_node']= function($number, $index) {
          $this->in->seek($number * 7 + 3, SEEK_SET);
          if (0 === $index) {
            $middle= (0xf0 & unpack('C', $this->in->read(1))[1]) >> 4;
          } else {
            $middle= 0x0f & unpack('C', $this->in->read(1))[1];
          }

          $this->in->seek($number * 7 + $index * 4, SEEK_SET);
          return unpack('N', chr($middle).$this->in->read(3))[1];
        };
        break;
      }

      case 32: {
        $this->meta['read_node']= function($number, $index) {
          $this->in->seek($number * 8 + $index * 4, SEEK_SET);
          return unpack('N', $this->in->read(4))[1];
        };
        break;
      }

      default: {
        throw new FormatException('Unsupported record size '.$this->meta['record_size']);
      }
    }
  }

  /**
   * Returns the offset of an address, or -1 if nothing is found.
   *
   * @see    php://inet_pton
   * @param  string $addr
   * @return int
   * @throws lang.IllegalArgumentException if given address is not a valid IP
   */
  public function offsetOf($addr) {
    if (false === ($packed= inet_pton($addr))) {
      $e= new IllegalArgumentException('Cannot convert "'.$addr.'" to packed in_addr representation');
      \xp::gc(__FILE__);
      throw $e;
    }

    $bytes= unpack('C*', $packed);
    $count= sizeof($bytes) * 8;
    $node= 0;
    $nodes= $this->meta['node_count'];
    $read= $this->meta['read_node'];

    // Skip first 96 nodes if we're looking up an IPv4 address in an IPv6 file.
    if (6 === $this->meta['ip_version'] && 32 === $count) {
      for ($i= 0; $i < 96 && $node < $nodes; $i++) {
        $node= $read($node, 0);
      }
    }

    for ($i= 0; $i < $count && $node < $nodes; $i++) {
      $node= $read($node, 1 & ((0xff & $bytes[1 + ($i >> 3)]) >> 7 - ($i % 8)));
    }

    if ($nodes === $node) {
      return -1;
    } else if ($node > $nodes) {
      return $node - $nodes + $this->meta['search_tree_size'];
    }

    throw new IllegalStateException('Should not arrive here');
  }

  /**
   * Reads and decodes a value at a given position
   *
   * @param  int $offset
   * @return var
   */
  public function valueAt($offset) {
    $this->in->seek($offset, SEEK_SET);
    return $this->nextValue();
  }

  /**
   * Closes underlying input stream
   *
   * @return void
   */
  public function close() {
    $this->in->close();
  }

  private function nextBytes($size) {
    return $this->in->read($size);
  }

  private function nextDouble($size) {
    $bytes= $this->in->read($size);
    return unpack('d', self::$REORDER ? strrev($bytes) : $bytes)[1];
  }

  private function nextInt($size) {
    $bytes= $this->in->read($size);
    return unpack('l', str_pad(self::$REORDER ? strrev($bytes) : $bytes, 4, "\x00", STR_PAD_LEFT))[1];
  }

  private function nextUint($size) {
    $bytes= $this->in->read($size);
    return unpack('N', str_pad($bytes, 4, "\x00", STR_PAD_LEFT))[1];
  }

  private function nextBigint($size) {
    $bytes= $this->in->read($size);
    $mul= new BigInt('4294967296');
    $int= new BigInt('0');
    for ($i= 0; $i < $size; $i+= 4) {
      $int= $int->multiply($mul)->add(unpack('N', substr($bytes, $i, 4))[1]);
    }
    return $int;
  }

  private function nextPointer($ctrl) {
    static $offset= [1 => 0, 2 => 2048, 3 => 526336, 4 => 0];

    $size= (($ctrl >> 3) & 0x3) + 1;
    $base= $this->meta['base'] + $offset[$size];

    $buffer= $this->in->read($size);
    if (4 === $size) {
      $pointer= unpack('N', $buffer)[1];
    } else {
      $pointer= unpack('N', str_pad(pack('C', $ctrl & 0x7).$buffer, 4, "\x00", STR_PAD_LEFT))[1];
    }

    return $base + $pointer;
  }

  private function nextValue() {
    $ctrl= ord($this->in->read(1));

    $type= $ctrl >> 5;
    if (self::TYPE_POINTER === $type) {
      $pointer= $this->nextPointer($ctrl);

      $before= $this->in->tell();
      $value= $this->valueAt($pointer);
      $this->in->seek($before, SEEK_SET);

      return $value;

    } else if (self::TYPE_EXTENDED === $type) {
      $type= ord($this->in->read(1)) + 7;
    }

    $size= $ctrl & 0x1f;

    $bytesToRead= $size < 29 ? 0 : $size - 28;
    $decoded= $this->nextUint($bytesToRead);
    if (29 === $size) {
      $size= 29 + $decoded;
    } else if (30 === $size) {
      $size= 285 + $decoded;
    } else if ($size > 30) {
      $size= ($decoded & (0x0FFFFFFF >> (32 - (8 * $bytesToRead)))) + 65821;
    }

    if (isset(self::$READ[$type])) {
      $decode= self::$READ[$type];
      return $decode($this, $size);
    }

    throw new IllegalStateException('Unexpected type #'.$type);
  }
}