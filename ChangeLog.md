GeoIP database ChangeLog
========================

## ?.?.? / ????-??-??

## 5.0.0 / 2024-02-04

* Implemented xp-framework/rfc#341: Drop XP <= 9 compatibility - @thekid
* Added PHP 8.4 to the test matrix - @thekid
* Merged PR #3: Migrate to new testing library - @thekid

## 4.0.1 / 2021-10-24

* Made compatible with XP 11 - @thekid

## 4.0.0 / 2020-04-10

* Implemented xp-framework/rfc#334: Drop PHP 5.6:
  . **Heads up:** Minimum required PHP version now is PHP 7.0.0
  . Rewrote code base, grouping use statements
  . Converted `newinstance` to anonymous classes
  . Rewrote `isset(X) ? X : default` to `X ?? default`
  (@thekid)

## 3.1.0 / 2020-04-05

* Extended `GeoIpDatabase::open()` to also accept `File` and `Path` instances
  as well as strings containing file system URIs.
  (@thekid)
* Made compatible with XP 10 - @thekid

## 3.0.0 / 2017-10-14

* Added version compatibility with XP 9 - @thekid
* **Heads up: Dropped PHP 5.5 support**. Minimum PHP version is now PHP 5.6.0
  (@thekid)

## 2.1.0 / 2016-08-29

* Added version compatibility with XP 8 - @thekid

## 2.0.0 / 2016-02-21

* Added version compatibility with XP 7 - @thekid

## 1.1.0 / 2016-01-23

* Forward compatibility with XP7: Added dependencies on math and unittest
  libraries, rewrote getClassName() to `nameof()`.
  (@thekid)

## 1.0.0 / 2015-12-14

* **Heads up**: Changed minimum XP version to XP 6.5.0, and with it the
  minimum PHP version to PHP 5.5.
  (@thekid)
* Added official PHP 7 support
  (@thekid)

## 0.2.0 / 2015-02-11

* Verified HHVM 3.4+ works - @thekid
* Fixed issue #2: Unittests should use fixture DB - @thekid
* Fixed issue #1: Error message when input is not an IP address - @thekid

## 0.1.0 / 2015-02-10

* First public release - @thekid
