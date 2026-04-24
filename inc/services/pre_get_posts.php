<?php

add_action('pre_get_posts', function($query) {
  if (is_admin() || !$query->is_main_query()) return;
  if (is_post_type_archive('services') || is_page_template( 'warranty-template.php' )){
      global $wpdb;

    $slug_point = get_query_var('point');
    $slug_city = get_query_var('place');
    $slug_district = get_query_var('district');
      if (!empty($slug_point)){

        $post_ids = $wpdb->get_col($wpdb->prepare("
            SELECT post_id FROM {$wpdb->prefix}postmeta
            WHERE meta_key LIKE 'coordinates_%_search_slug'
            AND meta_value = %s
        ", $slug_point)); 

        if ($post_ids) {
            $query->set('post__in', array_unique($post_ids));
        } else {
            $query->set('post__in', [0]); // нічого не знайшли
        }
      }
      if (!empty($slug_city)) {
        $city_term = get_term_by('slug', $slug_city, 'city');
        $district_term = !empty($slug_district) ? get_term_by('slug', $slug_district, 'city') : false;

        if ($city_term && !is_wp_error($city_term)) {
            $city_id = (int) $city_term->term_id;
            $post_ids_city = $wpdb->get_col("
                SELECT post_id
                FROM {$wpdb->postmeta}
                WHERE meta_key LIKE 'coordinates\_%\_location_city_term'
                AND meta_value LIKE '%i:0;i:{$city_id}%'
            ");
        } else {
            $post_ids_city = [];
        }

        if ($district_term && !is_wp_error($district_term)) {
            $district_id = (int) $district_term->term_id;
            $post_ids_district = $wpdb->get_col("
                SELECT post_id
                FROM {$wpdb->postmeta}
                WHERE meta_key LIKE 'coordinates\_%\_location_district_term'
                AND meta_value LIKE '%i:0;i:{$district_id}%'
            ");
        } else {
            $post_ids_district = [];
        }

        // 🔁 Обʼєднання або перетин залежно від того, що є
        if (!empty($post_ids_city) && !empty($post_ids_district)) {
            $post_ids = array_intersect($post_ids_city, $post_ids_district);
        } elseif (!empty($post_ids_city)) {
            $post_ids = $post_ids_city;
        } else {
            $post_ids = [];
        }

        if (!empty($post_ids)) {
            $query->set('post__in', array_map('intval', array_unique($post_ids)));
        } else {
            $query->set('post__in', [0]);
        }
    }
  }

});
