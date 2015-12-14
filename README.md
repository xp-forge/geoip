GeoIP database
==============

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/geoip.svg)](http://travis-ci.org/xp-forge/geoip)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.5+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_5plus.png)](http://php.net/)
[![Required PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)
[![Required HHVM 3.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/hhvm-3_4plus.png)](http://hhvm.com/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/geoip/version.png)](https://packagist.org/packages/xp-forge/geoip)

This API allows working with data geoips of different kinds in a functional style, e.g. map/reduce.

Examples
--------

```php
use com\maxmind\geoip\GeoIpDatabase;
use io\streams\FileInputStream;

$database= GeoIpDatabase::open(new FileInputStream('GeoLite2-City.mmdb'));
$record= $database->lookup('8.8.8.8');
$database->close();

// $record= com.maxmind.geoip.Record@{
//   [city        ] com.maxmind.geoip.Name(#5375480: Mountain View)
//   [country     ] com.maxmind.geoip.Name(#6252001: United States; code= US)
//   [continent   ] com.maxmind.geoip.Name(#6255149: North America; code= NA)
//   [postalCode  ] "94035"
//   [location    ] com.maxmind.geoip.Location(37.386,-122.0838; tz= America/Los_Angeles)
//   [subdivisions] [com.maxmind.geoip.Name(#5332921: California; code= CA)]
// }
```

See also
--------
http://dev.maxmind.com/geoip/geoip2/geolite2/ GeoLite2 Free Downloadable Databases
http://maxmind.github.io/MaxMind-DB/ MaxMind DB File Format Specification