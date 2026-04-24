<?php

add_action('pre_get_posts', 'custom_woocommerce_filters_by_get');
function custom_woocommerce_filters_by_get($query) {
  if (is_admin() || !$query->is_main_query()) return;

  if (is_post_type_archive('product') || is_tax('product_cat')) {
      $tax_query  = [];
      $meta_query = [];
      $product_filter = isset($_GET['product_filter']) ? sanitize_text_field($_GET['product_filter']) : '';
      $sort_price     = isset($_GET['price']) ? sanitize_text_field($_GET['price']) : '';
      $sort_popular   = isset($_GET['popular']) ? sanitize_text_field($_GET['popular']) : '';
      $sort_novelty   = isset($_GET['novelty']) ? sanitize_text_field($_GET['novelty']) : '';
      $query->set('posts_per_page', 6);
      $query->set('post_status', 'publish');
      $query->set('orderby', 'date');
      $query->set('order', 'DESC');
      if (!empty($product_filter)) {
          $items = explode(',', $product_filter);

          $tax_query[] = [
              'relation' => 'OR',
              'taxonomy' => 'product_cat',
              'field'    => 'slug',
              'terms'    => $items
          ];
      }
      if (!empty($sort_price)) {
          $query->set('orderby', 'meta_value_num');
          $query->set('meta_key', '_price');
          $query->set('order', $sort_price === 'low-to-high' ? 'ASC' : 'DESC');
      } elseif (!empty($sort_popular)) {
        $query->set('orderby', 'meta_value_num');
        $query->set('meta_key', '_product_views');
        $query->set('order', $sort_popular === 'low-to-high' ? 'ASC' : 'DESC');
      } elseif (!empty($sort_novelty)) {
          $query->set('orderby', 'date');
          $query->set('order', $sort_novelty === 'low-to-high' ? 'ASC' : 'DESC');
      }

      if (!empty($tax_query)) {
          $query->set('tax_query', $tax_query);
      }
      if (!empty($meta_query)) {
          $query->set('meta_query', $meta_query);
      }
  }
  if (is_post_type_archive('certificate') || is_tax('category_certificate')) {
    $query->set('posts_per_page', 8);
    $query->set('post_status', 'publish');
    $query->set('orderby', 'date');
    $query->set('order', 'DESC');
  }
  if ( $query->is_home() || is_post_type_archive('news')) {
    $query->set('posts_per_page', 6);
    $query->set('orderby', 'date');
    $query->set('order', 'DESC');
  }
  if ( $query->is_search() ) {
    $query->set('post_type', ['post', 'product']);
    $query->set('posts_per_page', 10000);
  }
}

add_action('pre_get_posts', function($query) {
  if (!is_admin() || !$query->is_main_query()) {
      return;
  }

  if ($orderby = $query->get('orderby')) {
      if ($orderby === 'product_views') {
          $query->set('meta_key', '_product_views');
          $query->set('orderby', 'meta_value_num');
      }
  }
}); 