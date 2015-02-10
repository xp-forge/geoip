<?php namespace com\maxmind\geoip;

use math\BigInt;
use lang\IllegalStateException;

class Data extends \lang\Object {
  private $in, $base;

  public function __construct($in, $base= 0) {
    $this->in= $in;
    $this->base= $base;
  }

  const T_EXTENDED      = 0x00;
  const T_POINTER       = 0x01;
  const T_UTF8_STRING   = 0x02;
  const T_DOUBLE        = 0x03;
  const T_BYTES         = 0x04;
  const T_UINT16        = 0x05;
  const T_UINT32        = 0x06;
  const T_MAP           = 0x07;
  const T_INT32         = 0x08;
  const T_UINT64        = 0x09;
  const T_UINT128       = 0x0a;
  const T_ARRAY         = 0x0b;
  const T_CONTAINER     = 0x0c;
  const T_END_MARKER    = 0x0d;
  const T_BOOLEAN       = 0x0e;
  const T_FLOAT         = 0x0f;

  private static $READ;

  static function __static() {
    self::$READ= [
      self::T_EXTENDED      => function($data, $size) {
        throw new IllegalStateException('T_EXTENDED not yet implemented');
      },
      self::T_POINTER       => function($data, $size) {
        throw new IllegalStateException('T_POINTER not yet implemented');
      },
      self::T_UTF8_STRING   => function($data, $size) {
        return $data->nextBytes($size);
      },
      self::T_DOUBLE        => function($data, $size) {
        return $data->nextDouble($size);
      },
      self::T_BYTES         => function($data, $size) {
        return $data->nextBytes($size);
      },
      self::T_UINT16        => function($data, $size) {
        return $data->nextUint($size);
      },
      self::T_UINT32        => function($data, $size) {
        return $data->nextUint($size);
      },
      self::T_MAP           => function($data, $size) {
        $map= [];
        for ($i= 0; $i < $size; $i++) {
          $map[$data->decode()]= $data->decode();
        }
        return $map;
      },
      self::T_INT32         => function($data, $size) {
        return $data->nextInt($size);
      },
      self::T_UINT64        => function($data, $size) {
        return $data->nextBigint($size);
      },
      self::T_UINT128       => function($data, $size) {
        return $data->nextBigint($size);
      },
      self::T_ARRAY         => function($data, $size) {
        $array= [];
        for ($i= 0; $i < $size; $i++) {
          $array[]= $data->decode();
        }
        return $array;
      },
      self::T_CONTAINER     => function($data, $size) {
        throw new IllegalStateException('T_CONTAINER not yet implemented');
      },
      self::T_END_MARKER    => function($data, $size) {
        throw new IllegalStateException('T_END_MARKER not yet implemented');
      },
      self::T_BOOLEAN       => function($data, $size) {
        return 0 === $size ? false : true;
      },
      self::T_FLOAT         => function($data, $size) {
        throw new IllegalStateException('T_FLOAT not yet implemented');
      },
    ];
  }

  private function nextBytes($size) {
    return $this->in->read($size);
  }

  private function nextDouble($size) {
    $bytes= $this->in->read($size);
    return unpack('d', $bytes)[1];    // FIXME: Endianess!
  }

  private function nextInt($size) {
    $bytes= $this->in->read($size);
    return unpack('l', str_pad($bytes, 4, "\x00", STR_PAD_LEFT))[1];    // FIXME: Endianess!
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
    $base= $this->base + $offset[$size];

    $buffer= $this->in->read($size);
    if (4 === $size) {
      $pointer= unpack('N', $buffer)[1];
    } else {
      $pointer= unpack('N', str_pad(pack('C', $ctrl & 0x7).$buffer, 4, "\x00", STR_PAD_LEFT))[1];
    }

    return $base + $pointer;
  }

  public function decode() {
    $ctrl= ord($this->in->read(1));

    $type= $ctrl >> 5;
    if (self::T_POINTER === $type) {
      $pointer= $this->nextPointer($ctrl);

      $before= $this->in->tell();

      $this->in->seek($pointer, SEEK_SET);
      $value= $this->decode();
      $this->in->seek($before, SEEK_SET);

      return $value;

    } else if (self::T_EXTENDED === $type) {
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