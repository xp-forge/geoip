GeoIP database
==============

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/geoip.svg)](http://travis-ci.org/xp-forge/geoip)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_4plus.png)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/geoip/version.png)](https://packagist.org/packages/xp-forge/geoip)

This API allows working with data geoips of different kinds in a functional style, e.g. map/reduce.

Examples
--------

```php
use com\maxmind\geoip\GeoIpDatabase;
use io\streams\FileInputStream;

$database= GeoIpDatabase::open(new FileInputStream('GeoLite2-City.mmdb'));
$results= $database->lookup('8.8.8.8');
$database->close();

// com.maxmind.geoip.Record@{
//   [city        ] com.maxmind.geoip.Name(#5375480: Mountain View)
//   [country     ] com.maxmind.geoip.Name(#6252001: United States)
//   [continent   ] com.maxmind.geoip.Name(#6255149: North America)
//   [postal      ] [
//     code => "94035"
//   ]
//   [location    ] com.maxmind.geoip.Location(37.386, -122.0838, tz= America/Los_Angeles)
//   [subdivisions] [com.maxmind.geoip.Name(#5332921: California)]
// }
```

See also
--------
http://dev.maxmind.com/geoip/geoip2/geolite2/ GeoLite2 Free Downloadable Databases
http://maxmind.github.io/MaxMind-DB/ MaxMind DB File Format Specification