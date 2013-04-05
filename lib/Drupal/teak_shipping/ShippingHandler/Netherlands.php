<?php

namespace Drupal\teak_shipping\ShippingHandler;
use Drupal\teak_shipping\Util;

class Netherlands {

  protected $helper;

  function __construct($helper) {
    $this->helper = $helper;
  }

  function getShippingCost($order, $shipping, $destination, $amount) {
    if (!empty($shipping->field_provincie)) {
      return $shipping->field_provincie->field_bezorgkosten->value();
    }
    else {
      // Shipping not possible.
      return FALSE;
    }
  }
}
