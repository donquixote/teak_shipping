<?php

namespace Drupal\teak_shipping;

class ShippingHelper {

  function baseShippingCost($order, $map, $fallback_price) {

    // Shipping cost per product.
    $cost = 0;
    foreach (field_get_items('commerce_order', $order, 'commerce_line_items') as $line_item) {
      $line_item = commerce_line_item_load($line_item['line_item_id']);
      $line_item_cost = 0;
      foreach (field_get_items('commerce_line_item', $line_item, 'commerce_product') as $product) {
        $product = commerce_product_load($product['product_id']);
        $weight_class = $this->productWeightClass($product);
        $line_item_cost += isset($map[$weight_class]) ? $map[$weight_class] : $fallback_price;
      }
      $line_item_cost *= $line_item->quantity;
      $cost += (int)$line_item_cost;
    }
    return $cost;
  }

  protected function productWeightClass($product) {
    foreach (field_get_items('commerce_product', $product, 'taxonomy_vocabulary_1') as $term) {
      $term = taxonomy_term_load($term['tid']);
      if (!empty($term->name)) {
        $candidate = $this->weightClassByTitle($term->name);
        if (!empty($candidate)) {
          return $candidate;
        }
      }
    }
    $candidate = $this->weightClassByTitle($product->title);
    if (!empty($candidate)) {
      return $candidate;
    }
  }

  protected function weightClassByTitle($title) {
    static $map = array(
      'table' => '/tisch|table/i',
      'closet' => '/schrank|schränke|cabinet|closet|wardrobe|armoire|placard|kast|kabinet/i',
      'chair' => '/stuhl|stühle|chair|chaise/i',
      // "banque" (French) is the one about money, we don't want that :)
      'bench' => '/bank|bench|banc/i',
      'tv_meubel' => '/tv|fernseh|televis/i',
      'dresser' => '/dresser/i',
    );
    foreach ($map as $key => $regex) {
      if (preg_match($regex, $title)) {
        return $key;
      }
    }
  }
}
