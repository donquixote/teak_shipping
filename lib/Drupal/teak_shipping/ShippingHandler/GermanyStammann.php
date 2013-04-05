<?php

namespace Drupal\teak_shipping\ShippingHandler;
use Drupal\teak_shipping\Util;

class GermanyStammann {

  protected $helper;

  function __construct($helper) {
    $this->helper = $helper;
  }

  function getShippingCost($order, $shipping, $destination, $amount) {
    $map = array(
      'table' => 150 * 100,
      'closet' => 250 * 100,
      'chair' => 35 * 100,
      'bench' => 50 * 100,
      'tv_meubel' => 125 * 100,
      'dresser' => 200 * 100,
    );
    $cost = $this->helper->baseShippingCost($order, $map, 200 * 100);

    // Lower and upper limit.
    $cost = Util::clipMinMax($cost, 150 * 100, 400 * 100);

    $cost = $this->applyDiscount($cost, $amount);

    return $cost;
  }

  protected function applyDiscount($cost, $amount) {

    if ($amount >= 5000 * 100) {
      $cost = 0;
    }
    elseif ($amount >= 4500 * 100) {
      $cost -= 375 * 100;
    }
    elseif ($amount >= 4000 * 100) {
      $cost -= 325 * 100;
    }
    elseif ($amount >= 3500 * 100) {
      $cost -= 275 * 100;
    }
    elseif ($amount >= 3000 * 100) {
      $cost -= 225 * 100;
    }
    elseif ($amount >= 2500 * 100) {
      $cost -= 175 * 100;
    }
    elseif ($amount >= 2000 * 100) {
      $cost -= 125 * 100;
    }

    // Make sure that transport cost is not below zero.
    if ($cost < 0) {
      $cost = 0;
    }

    return $cost;
  }
}
