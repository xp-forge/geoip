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

// [
//   city => [
//     geoname_id => 5375480
//     names => [en => "Mountain View"]
//   ]
//   continent => [
//     code => "NA"
//     geoname_id => 6255149
//     names => [en => "North America"]
//   ]
//   country => [
//     geoname_id => 6252001
//     iso_code => "US"
//     names => [en => "United States"]
//   ]
//   location => [
//     latitude => -1.0088030043764E+25
//     longitude => -1.1995528675396E+197
//     metro_code => 807
//     time_zone => "America/Los_Angeles"
//   ]
//   postal => [
//     code => "94035"
//   ]
//   registered_country => [
//     geoname_id => 6252001
//     iso_code => "US"
//     names => [en => "United States"]
//   ]
//   subdivisions => [
//     [
//       geoname_id => 5332921
//       iso_code => "CA"
//       names => [en => "California"]
//     ]
//   ]
// ]
```