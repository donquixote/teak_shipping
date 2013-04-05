<?php

namespace Drupal\teak_shipping;
use Drupal\teak_shipping\ShippingHandler as sh;

class Dispatcher {

  function getShippingCost($order) {

    // More about the order.
    $order_wrapper = entity_metadata_wrapper('commerce_order', $order);
    $shipping = $order_wrapper->commerce_customer_shipping;

    if (!empty($shipping->field_afhalen_of_leveren)) {
      $ship_collect = $shipping->field_afhalen_of_leveren->value();
      if ('afhalen' === $ship_collect) {
        // Shipping is free.
        dpm('Free shipping');
        return 0;
      }
    }

    $destination = $this->getDestination($order);
    $amount = $order->commerce_order_total['und'][0]['amount'];

    $handler = $this->getShippingHandler($order, $destination, $amount);
    if (FALSE === $handler) {
      // Cannot ship (probably because it's < 600 EUR in Germany)
      return FALSE;
    }
    elseif (empty($handler)) {
      // Free shipping
      return 0;
    }
    return $handler->getShippingCost($order, $shipping, $destination, $amount);
  }

  protected function getShippingHandler($order, $destination, $amount) {

    $km = $destination->km();
    $helper = new ShippingHelper();

    switch ($destination->countryCode()) {

      case 'DE':
        if ($amount < 600 * 100) {
          // Shipping not possible.
          return FALSE;
        }
        if ($km > 275) {
          return new sh\GermanyStammann($helper);
        }
        else {
          return new sh\GermanyCloseDistance($helper);
        }
        break;

      case 'NL':
        if ($amount >= 1500 * 100) {
          // Shipping is free.
          return;
        }
        else {
          return new sh\Netherlands($helper);
        }
        break;

      default:
        // Country not supported.
        return FALSE;
    }
  }

  protected function getDestination($order) {

    // Get the address
    $address = $this->addressFromOrder($order);
    if (empty($address)) {
      dpm("No address found.");
      return;
    }

    // Geocode address
    $parsed = geocoder_widget_parse_addressfield($address);
    $point = geocoder('google', $parsed);
    if (empty($point)) {
      dpm("Unable to geocode.");
      return;
    }

    // Coordinates of Nuth, NL
    $home = array(5.8820282, 50.9217535);
    $distance = Util::geoDistance($home, $point->coords);

    return new Destination($address, $point, $distance);
  }

  protected function addressFromOrder($order) {

    // field_get_items() feels awkward.
    foreach (field_get_items('commerce_order', $order, 'commerce_customer_shipping') as $profile_item) {
      $profile = commerce_customer_profile_load($profile_item['profile_id']);
      if ($profile) {
        foreach (field_get_items('commerce_customer_profile', $profile, 'commerce_customer_address') as $address) {
          return $address;
        }
      }
    }
  }
}
