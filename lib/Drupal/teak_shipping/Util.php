<?php

namespace Drupal\teak_shipping;

/**
 * This class contains ONLY methods that are "pure", and having
 * nothing to do with Drupal or Teak.
 */
class Util {

  static function clipMinMax($value, $min, $max) {
    if ($value < $min) {
      return $min;
    }
    elseif ($value > $max) {
      return $max;
    }
    else {
      return $value;
    }
  }

  /**
   * Calculate distance between two geographical points.
   * Formula used: Haversine.
   * See http://rosettacode.org/wiki/Category:PHP
   *
   * In theory, geoPHP should be able to do the distance calculation, but it is
   * not as easy as expected. Somehow didn't work. So we roll our own.
   *
   * Note: geoPHP has LNG first, LAT second.
   *
   * @param array $p0
   *   LNG and LAT of the first point, in degree.
   * @param array $p1
   *   LNG and LAT of the second point, in degree.
   *
   * @return float
   *   Distance in meters
   */
  static function geoDistance($p0, $p1) {

    // Earth's radius in meters.
    $radiusOfEarth = 6371 * 1000;

    // Conversion to rad
    $p0[0] = deg2rad($p0[0]);
    $p0[1] = deg2rad($p0[1]);
    $p1[0] = deg2rad($p1[0]);
    $p1[1] = deg2rad($p1[1]);

    $diffLongitude = $p1[0] - $p0[0];
    $diffLatitude = $p1[1] - $p0[1];

    $a = 0
      + 1
        * sin($diffLatitude / 2)
        * sin($diffLatitude / 2)
      + 1
        * cos($p0[1])
        * cos($p1[1])
        * sin($diffLongitude / 2)
        * sin($diffLongitude / 2)
    ;

    $b = 2 * asin(sqrt($a));

    return $radiusOfEarth * $b;
  }
}
