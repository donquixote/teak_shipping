<?php

namespace Drupal\teak_shipping;

class Destination {

  protected $address;
  protected $point;
  protected $distance;

  function __construct($address, $point, $distance) {
    $this->address = $address;
    $this->point = $point;
    $this->distance = $distance;
  }

  function countryCode() {
    return $this->address['country'];
  }

  function shippingDistance() {
    return $this->distance;
  }

  function km() {
    return $this->distance * 0.001;
  }
}
