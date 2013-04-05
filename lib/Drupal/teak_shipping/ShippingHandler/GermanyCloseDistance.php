<?php

namespace Drupal\teak_shipping\ShippingHandler;

class GermanyCloseDistance {

  function getShippingCost($order, $shipping, $destination, $amount) {

    $km = $destination->km();
    if ($km <= 50) {
      return ($amount >= 1500 * 100) ? 0 : 50 * 100;
    }
    elseif ($km <= 100) {
      return ($amount >= 1750 * 100) ? 0 : 90 * 100;
    }
    elseif ($km <= 150) {
      return ($amount >= 2000 * 100) ? 0 : 125 * 100;
    }
    else {
      return ($amount >= 2500 * 100) ? 0 : 150 * 100;
    }
  }
}
