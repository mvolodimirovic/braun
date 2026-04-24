<?php
function get_or_create_terms($city_name, $district_name = null) {
    $city_term_id = term_exists($city_name, 'city');
    if (!$city_term_id) {
        $city_term = wp_insert_term($city_name, 'city');
        $city_term_id = $city_term['term_id'] ?? null;
    } else {
        $city_term_id = is_array($city_term_id) ? $city_term_id['term_id'] : $city_term_id;
    }
    $city_term_obj = get_term($city_term_id, 'city');

    $district_term_obj = null;
    if ($district_name && $city_term_id) {
        $districts = get_terms([
            'taxonomy' => 'city',
            'parent' => $city_term_id,
            'hide_empty' => false,
        ]);

        $existing = wp_list_filter($districts, ['name' => $district_name]);

        if (!empty($existing)) {
            $district_term_obj = array_shift($existing);
        } else {
            $district_term = wp_insert_term($district_name, 'city', ['parent' => $city_term_id]);
            if (!is_wp_error($district_term)) {
                $district_term_obj = get_term($district_term['term_id'], 'city');
            }
        }
    }

    return [$city_term_obj, $district_term_obj];
}

function get_coordinates_by_address($address) {
    $api_key = 'AIzaSyDgpsA7omeC1OtlpzsDaJaEmBvA9x06f7M';
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=' . $api_key . '&language=uk';

    $response = wp_remote_get($url);
    if (is_wp_error($response)) return [null, null];

    $data = json_decode(wp_remote_retrieve_body($response), true);
    if ($data['status'] === 'OK') {
        $location = $data['results'][0]['geometry']['location'];
        return [$location['lat'], $location['lng']];
    }

    return [null, null];
}

add_action('acf/save_post', 'auto_update_location_terms_on_save', 20);
function auto_update_location_terms_on_save($post_id) {
    if (get_post_type($post_id) !== 'services') return;

    $coordinates = get_field('coordinates', $post_id);

    if ($coordinates && is_array($coordinates)) {
        foreach ($coordinates as $index => &$row) {
            $location = $row['address'];

            if (!empty($location['lat']) && !empty($location['lng'])) {
                // Отримуємо назви міста та району з координат
                $reverse_data = get_location_names_by_coordinates($location['lat'], $location['lng']);
                $city_name = $reverse_data['city'];
                $district_name = $reverse_data['district'];

                if ($city_name) {
                    [$city_term, $district_term] = get_or_create_terms($city_name, $district_name);

                    if ($city_term) {
                        $row['location_city_term'] = [$city_term->term_id];
                        [$lat, $lng] = get_coordinates_by_address($city_term->name);
                        if ($lat && $lng) {
                            update_field('lat_term', $lat, 'term_' . $city_term->term_id);
                            update_field('lng_term', $lng, 'term_' . $city_term->term_id);
                        }
                    }

                    if ($district_term) {
                        $row['location_district_term'] = [$district_term->term_id];
                        $address = $city_term->name . ', ' . $district_term->name;
                        [$lat, $lng] = get_coordinates_by_address($address);
                        if ($lat && $lng) {
                            update_field('lat_term', $lat, 'term_' . $district_term->term_id);
                            update_field('lng_term', $lng, 'term_' . $district_term->term_id);
                        }
                    }
                }
            }
        }

        update_field('coordinates', $coordinates, $post_id);
    }
}

function get_location_names_by_coordinates($lat, $lng) {
    $api_key = 'AIzaSyDgpsA7omeC1OtlpzsDaJaEmBvA9x06f7M';
    $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$api_key}&language=uk";

    $response = wp_remote_get($url);
    if (is_wp_error($response)) return ['city' => null, 'district' => null];

    $data = json_decode(wp_remote_retrieve_body($response), true);
    $city = $district = null;

    if ($data['status'] === 'OK') {
        foreach ($data['results'][0]['address_components'] as $component) {
            if (in_array('locality', $component['types'])) {
                $city = $component['long_name'];
            }
            if (in_array('sublocality_level_1', $component['types']) || in_array('sublocality', $component['types'])) {
                $district = $component['long_name'];
            }
        }
    }

    return ['city' => $city, 'district' => $district];
}

add_filter('acf/prepare_field/name=location_city_term', function($field) {
    $field['disabled'] = true;
    return $field;
});
add_filter('acf/prepare_field/name=location_district_term', function($field) {
    $field['disabled'] = true;
    return $field;
});

add_action('wp_enqueue_scripts', 'enqueue_services_map_script');
function enqueue_services_map_script() {
    if (is_archive('services') || is_page_template( 'warranty-template.php' )) {

      $services = get_posts([
          'post_type' => 'services',
          'posts_per_page' => -1,
          'post_status' => 'publish',
      ]);

      $locations = [];

      foreach ($services as $service) {
        $service_id = $service->ID;
          if (have_rows('coordinates', $service_id)) {
              while (have_rows('coordinates', $service_id)) {
                  the_row();
                  $address = get_sub_field('address');
                  $desc = get_sub_field('description'); // за потреби

                  if (!empty($address['lat']) && !empty($address['lng'])) {
                      $locations[] = [
                          'lat' => (float) $address['lat'],
                          'lng' => (float) $address['lng'],
                          'content' => sprintf(
                              '<div class="map-popup"><h4>%s</h4><p>%s</p></div>',
                              esc_html(get_the_title($service_id)),
                              esc_html($desc ?: $address['address'])
                          )
                      ];
                  }
              }
          }
      }

      wp_register_script('custom-google-map-init', get_template_directory_uri() . '/js/custom-map.js', [], null, true);
      wp_script_add_data('custom-google-map-init', 'defer', true);
      wp_enqueue_script('custom-google-map-init');

      wp_register_script('google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDgpsA7omeC1OtlpzsDaJaEmBvA9x06f7M&callback=initMap', [], null, true);
      wp_script_add_data('google-maps-api', 'async', true);
      wp_enqueue_script('google-maps-api');
      wp_localize_script('custom-google-map-init', 'acfMapData', [
        'locations' => $locations
      ]);
    }
}

function generate_address_slug($address) {
    $address = mb_strtolower($address, 'UTF-8');

    // Ручна транслітерація (можеш доповнити)
    $replace = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'h','ґ'=>'g','д'=>'d','е'=>'e','є'=>'ye','ж'=>'zh','з'=>'z','и'=>'y','і'=>'i','ї'=>'yi','й'=>'y',
        'к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'kh','ц'=>'ts','ч'=>'ch',
        'ш'=>'sh','щ'=>'shch','ь'=>'','ю'=>'yu','я'=>'ya',
        'ъ'=>'','ы'=>'y','э'=>'e','ё'=>'yo','œ'=>'oe','æ'=>'ae','č'=>'ch','š'=>'sh','ž'=>'zh'
    ];
    $address = strtr($address, $replace);

    // Заміна дробів (наприклад: 1/2 → 1-2)
    $address = preg_replace('/(\d+)\/(\d+)/u', '$1-$2', $address);

    // Видалення непотрібних символів
    $address = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $address);

    // Заміна пробілів на дефіси
    $address = trim($address);
    $address = preg_replace('/\s+/u', '-', $address);

    return sanitize_title($address);
}


add_action('acf/save_post', 'save_repeater_address_slugs', 20);
function save_repeater_address_slugs($post_id) {
    if (get_post_type($post_id) !== 'services') return;

    if (have_rows('coordinates', $post_id)) {
        $i = 0;
        while (have_rows('coordinates', $post_id)) {
            the_row();
            $address = get_sub_field('address');
            if ($address) {
                $slug = generate_address_slug($address['address']);
                update_post_meta($post_id, "coordinates_{$i}_search_slug", $slug);
            }
            $i++;
        }
    }
}

add_filter('acf/prepare_field/name=search_slug', function ($field) {
    return false;
});

add_rewrite_rule(
    '^services/point/([^/]+)/?$',
    'index.php?post_type=services&point=$matches[1]',
    'top'
);

add_rewrite_rule(
    '^services/place/([^/]+)/?$',
    'index.php?post_type=services&place=$matches[1]&taxonomy=&term=',
    'top'
);
// 1. place + district
add_rewrite_rule(
    '^services/place/([^/]+)/district/([^/]+)/?$',
    'index.php?post_type=services&place=$matches[1]&district=$matches[2]&taxonomy=&term=',
    'top'
);

add_action('init', function () {
    add_rewrite_tag('%srv_place%', '([^&]+)');

    add_rewrite_rule(
        '^garantiya-ta-obslugovuvannya/srv_place/([^/]+)/?$',
        'index.php?pagename=garantiya-ta-obslugovuvannya&srv_place=$matches[1]',
        'top'
    );
});

add_filter('query_vars', function($vars) {
    $vars[] = 'point';
    $vars[] = 'place';
    $vars[] = 'district';
    $vars[] = 'srv_place';
    return $vars;
});

function get_coordinates_by_slug($address) {
    $api_key = 'AIzaSyDgpsA7omeC1OtlpzsDaJaEmBvA9x06f7M';
    $endpoint = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=' . $api_key;

    $response = wp_remote_get($endpoint);
    if (is_wp_error($response)) return false;

    $data = json_decode(wp_remote_retrieve_body($response), true);

    if ($data['status'] === 'OK' && isset($data['results'][0]['geometry']['location'])) {
        return $data['results'][0]['geometry']['location'];
    }

    return false;
}
