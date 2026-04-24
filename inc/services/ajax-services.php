<?php
function services_search() {
  ob_start();

    $company_id = !empty($_POST['company_id']) ? sanitize_text_field($_POST['company_id']) : '';
    if(!empty($company_id)){
      $address_map = !empty($_POST['address']) ? $_POST['address'] : '';
      $website_link = get_field('website_link',$company_id);
      $city = sanitize_text_field($_POST['city']);
      $district = sanitize_text_field($_POST['district']);
      if(!empty($address_map)){
      ?>
        <div 
          class="services__item" 
          data-service="<?php echo esc_attr($company_id)?>" 
          data-city="<?php echo esc_attr($city)?>" 
          data-district="<?php echo esc_attr($district)?>"
          data-address ="<?php echo esc_attr(json_encode($address_map))?>";
        >
          <h4><?php echo esc_html(get_the_title($company_id))?></h4>
          <ul>
            <li>
              <svg class="services-icon">
                <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#map-icon"></use>
              </svg>
              <?php echo esc_html($address_map['address'])?>
            </li>
            <?php
            if(have_rows('phone_list',$company_id)){
              while(have_rows('phone_list',$company_id)){
                the_row();
                $phone = get_sub_field('number');
                if(!empty($phone)){
            ?>
                <li>
                  <a href="tel:<?php echo esc_attr(preg_replace('/\D/', '', $phone))?>">
                    <svg class="services-icon">
                      <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#phone-icon"></use>
                    </svg>
                    <?php echo esc_html($phone)?>
                  </a>
                </li>
              <?php }
              } 
            } 
            if(have_rows('email_list',$company_id)){
              while(have_rows('email_list',$company_id)){
                the_row();
                $email = get_sub_field('email');
                if(!empty($email)){ ?>
                <li>
                  <a href="mailto:<?php echo esc_attr($email)?>">
                    <svg class="services-icon">
                      <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#email-icon"></use>
                    </svg>
                    <?php echo esc_html($email)?>
                  </a>
                </li>
                <?php }
              } 
            } 
            
            if(have_rows('work_hours',$company_id)){
              while(have_rows('work_hours',$company_id)){
                the_row();
                  $days_week = get_sub_field('days_week');
                  $hours = get_sub_field('hours');
                ?>
                <li>
                  <svg class="services-icon">
                    <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#time-icon"></use>
                  </svg>
                  <?php if(!empty($days_week) && !empty($hours)){?>
                    <span class="days-week"> <?php echo esc_html($days_week);?></span>
                    <span>|</span>
                    <span> <?php echo esc_html($hours);?></span>
                  <?php } ?>
                </li>
            <?php }
            }
            if(!empty($website_link)){?>
              <li>
                <a href="<?php echo esc_url($website_link)?>" target="_blank">
                  <svg class="services-icon">
                    <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#web-icon"></use>
                  </svg>
                  <?php echo rtrim(preg_replace('#^(https?://)?(www\.)?#', '', $website_link), '/'); ?>
                </a>
              </li>
            <?php } ?>
          </ul>
        </div>
      <?php
      }
    } else {
      $services = get_posts([
        'post_type' => 'services',
        'posts_per_page' => 10000,
      ]);
      if(!empty($services)){
        foreach($services as $service){
          $service_id = $service->ID;
          $coordinates = get_field('coordinates',$service_id);
          $website_link = get_field('website_link', $service_id);
          if(!empty($coordinates)){
            foreach ($coordinates as $address) { 
              $address_map = $address['address'];
              $city = $address['location_city_term'];
              $district = $address['location_district_term'];
              $coordinate_slug = $address['search_slug'];?>
              <div 
                class="services__item" 
                data-service="<?php echo esc_attr($service_id)?>" 
                data-city="<?php echo esc_attr($city->slug)?>" 
                data-district="<?php echo esc_attr($district->slug)?>"
                data-address ="<?php echo esc_attr(json_encode($address_map))?>";
              >
                <h4><?php echo esc_html(get_the_title($service_id))?></h4>
                <ul>
                  <li>
                    <svg class="services-icon">
                      <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#map-icon"></use>
                    </svg>
                    <?php echo esc_html($address_map['address'])?>
                  </li>
                  <?php
                  if(have_rows('phone_list',$service_id)){
                    while(have_rows('phone_list',$service_id)){
                      the_row();
                      $phone = get_sub_field('number');
                      if(!empty($phone)){
                  ?>
                      <li>
                        <a href="tel:<?php echo esc_attr(preg_replace('/\D/', '', $phone))?>">
                          <svg class="services-icon">
                            <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#phone-icon"></use>
                          </svg>
                          <?php echo esc_html($phone)?>
                        </a>
                      </li>
                    <?php }
                    } 
                  } 
                  if(have_rows('email_list',$service_id)){
                    while(have_rows('email_list',$service_id)){
                      the_row();
                      $email = get_sub_field('email');
                      if(!empty($email)){ ?>
                      <li>
                        <a href="mailto:<?php echo esc_attr($email)?>">
                          <svg class="services-icon">
                            <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#email-icon"></use>
                          </svg>
                          <?php echo esc_html($email)?>
                        </a>
                      </li>
                      <?php }
                    } 
                  } 
                  
                  if(have_rows('work_hours',$service_id)){
                    while(have_rows('work_hours',$service_id)){
                      the_row();
                        $days_week = get_sub_field('days_week');
                        $hours = get_sub_field('hours');
                      ?>
                      <li>
                        <svg class="services-icon">
                          <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#time-icon"></use>
                        </svg>
                        <?php if(!empty($days_week) && !empty($hours)){?>
                          <span class="days-week"> <?php echo esc_html($days_week);?></span>
                          <span>|</span>
                          <span> <?php echo esc_html($hours);?></span>
                        <?php } ?>
                      </li>
                  <?php }
                  }
                  if(!empty($website_link)){?>
                    <li>
                      <a href="<?php echo esc_url($website_link)?>" target="_blank">
                        <svg class="services-icon">
                          <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#web-icon"></use>
                        </svg>
                        <?php echo rtrim(preg_replace('#^(https?://)?(www\.)?#', '', $website_link), '/'); ?>
                      </a>
                    </li>
                  <?php } ?>
                </ul>
              </div>
              <?php 
            }
          } 
        }
      }
    }


  $response['post'] = ob_get_clean();
  $response['lat'] = !empty($company_id) ? $address_map['lat'] : 48.3794;
  $response['lng'] = !empty($company_id) ? $address_map['lng'] : 31.1656;
  $response['zoom'] = !empty($company_id) ? 14 : 6;
  $response['title'] = $address_map['address'];
  echo json_encode($response);
  die();
}

add_action('wp_ajax_services_search', 'services_search');
add_action('wp_ajax_nopriv_services_search', 'services_search');


function search_filters() {
  ob_start();

  $response = [
      'posts' => '',
  ];

  $input_text = sanitize_text_field($_POST['input_text']) ?? '';

  $args = [
    'post_type' => 'services',
    'posts_per_page' => 10000,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    's' => $input_text,
  ];
  add_filter('posts_search', function($search_sql, $query) use ($input_text) {
    global $wpdb;

    if (!empty($search_sql) && !is_admin()) {
        $search_sql = $wpdb->prepare(" AND {$wpdb->posts}.post_title LIKE %s", '%' . $wpdb->esc_like($input_text) . '%');
    }
    return $search_sql;
  }, 10, 2);
  $query = new WP_Query($args);
  if ($query->have_posts()) {
  while ($query->have_posts()) : $query->the_post();
    $item_id = get_the_ID();
    $coordinates = get_field('coordinates',$item_id);
    if(!empty($coordinates)){
      foreach ($coordinates as $address) { 
        $address_map = $address['address'];
        $city = $address['location_city_term'];
        $district = $address['location_district_term'];
        if(!empty($address_map)){
        ?>
          <li 
            class="filters__list__item single services-item address-ajax" 
            data-service="<?php echo esc_attr($item_id)?>" 
            data-city="<?php echo esc_attr($city->slug)?>" 
            data-district="<?php echo esc_attr($district->slug)?>"
            data-address ="<?php echo esc_attr(json_encode($address_map))?>";
            data-slug="<?php echo esc_attr($address['search_slug'])?>"
          >
            <span>
              <?php echo esc_html(get_the_title())?>
            </span>
            <span>
              <svg class="point-icon">
                <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#point-icon"></use>
              </svg>
              <?php echo esc_html($address_map['address'])?>
            </span>
          </li>
        <?php } 
      }
    } 
    
  endwhile;
    $response['posts'] = ob_get_clean();
  } else { ?>
      <li class="filters__list__item single services-item">
        <div class="services-item-info">
          <span>
            <?php _e('Локації не знайдено','braun_2025')?>
          </span>
        </div>
      </li>
      <?php
      $response['posts'] = ob_get_clean();
  }

  wp_reset_postdata();

  echo json_encode($response);
  die();
}

add_action('wp_ajax_search_filters', 'search_filters');
add_action('wp_ajax_nopriv_search_filters', 'search_filters');


function city_district() {
  ob_start();

  $response = [
      'posts' => '',
  ];

  $input_text = sanitize_text_field($_POST['input_text']) ?? '';
  $parent = (int)sanitize_text_field($_POST['parent']) ?? '';

  $terms = get_terms( [
    'taxonomy'   => 'city',
    'hide_empty' => false,
    'name__like' => $input_text,
    // 'parent' => 0,
  ] );

  $terms = array_filter($terms, function($term) use ($parent) {
    if ($parent == 0) {
        return $term->parent == 0;
    } else {
        return $term->parent != 0;
    }
  });

  if (!empty($terms)) {
    foreach ($terms as $term) {
      // $term_id = $term->term_id;
      ?>
        <li 
          class="filters__list__item single services-item city-ajax" 
          data-value="<?php echo esc_attr($term->slug)?>"
        >
          <span>
            <?php echo esc_html($term->name)?>
          </span>
        </li>
      <?php } 
    $response['posts'] = ob_get_clean();
  } else { ?>
      <li class="filters__list__item single services-item">
        <div class="services-item-info">
          <span>
            <?php _e('Міст не знайдено','braun_2025')?>
          </span>
        </div>
      </li>
      <?php
      $response['posts'] = ob_get_clean();
  }

  wp_reset_postdata();

  echo json_encode($response);
  die();
}

add_action('wp_ajax_city_district', 'city_district');
add_action('wp_ajax_nopriv_city_district', 'city_district');


function district_search() {
  ob_start();

    $slug = !empty($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
    $term = get_term_by('slug', $slug, 'city');
  
    if ($term && !is_wp_error($term)) {
        $term_id = $term->term_id;
    }
    
    $child_term = get_terms( [
    'taxonomy'   => 'city',
    'hide_empty' => false,
    'parent' => $term_id,
    ] );
    if (!empty($child_term)) {
      foreach ($child_term as $term_child) {
        ?>
          <li 
            class="filters__list__item single services-item district-ajax" 
            data-value="<?php echo esc_attr($term_child->slug)?>"
          >
            <span>
              <?php echo esc_html($term_child->name)?>
            </span>
          </li>
        <?php } 
      $response['child_term'] = ob_get_clean();
    }
    global $wpdb;
    $term_id = (int) $term_id;

    $post_ids = $wpdb->get_col("
      SELECT post_id
      FROM {$wpdb->postmeta}
      WHERE meta_key LIKE 'coordinates\_%\_location_city_term'
      AND meta_value LIKE '%i:0;i:{$term_id}%'
    ");

    $services = get_posts([
      'post_type' => 'services',
      'posts_per_page' => 10000,
      'post__in'=> $post_ids,
    ]);
    if(!empty($services)){
      foreach($services as $service){
        $service_id = $service->ID;
        $coordinates = get_field('coordinates',$service_id);
        $website_link = get_field('website_link', $service_id);
        if(!empty($coordinates)){
          foreach ($coordinates as $address) { 
            $address_map = $address['address'];
            $city = $address['location_city_term'];
            $district = $address['location_district_term'];
            $coordinate_slug = $address['search_slug'];?>
            <div 
              class="services__item" 
              data-service="<?php echo esc_attr($service_id)?>" 
              data-city="<?php echo esc_attr($city->slug)?>" 
              data-district="<?php echo esc_attr($district->slug)?>"
              data-address ="<?php echo esc_attr(json_encode($address_map))?>";
            >
              <h4><?php echo esc_html(get_the_title($service_id))?></h4>
              <ul>
                <li>
                  <svg class="services-icon">
                    <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#map-icon"></use>
                  </svg>
                  <?php echo esc_html($address_map['address'])?>
                </li>
                <?php
                if(have_rows('phone_list',$service_id)){
                  while(have_rows('phone_list',$service_id)){
                    the_row();
                    $phone = get_sub_field('number');
                    if(!empty($phone)){
                ?>
                    <li>
                      <a href="tel:<?php echo esc_attr(preg_replace('/\D/', '', $phone))?>">
                        <svg class="services-icon">
                          <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#phone-icon"></use>
                        </svg>
                        <?php echo esc_html($phone)?>
                      </a>
                    </li>
                  <?php }
                  } 
                } 
                if(have_rows('email_list',$service_id)){
                  while(have_rows('email_list',$service_id)){
                    the_row();
                    $email = get_sub_field('email');
                    if(!empty($email)){ ?>
                    <li>
                      <a href="mailto:<?php echo esc_attr($email)?>">
                        <svg class="services-icon">
                          <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#email-icon"></use>
                        </svg>
                        <?php echo esc_html($email)?>
                      </a>
                    </li>
                    <?php }
                  } 
                } 
                
                if(have_rows('work_hours',$service_id)){
                  while(have_rows('work_hours',$service_id)){
                    the_row();
                      $days_week = get_sub_field('days_week');
                      $hours = get_sub_field('hours');
                    ?>
                    <li>
                      <svg class="services-icon">
                        <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#time-icon"></use>
                      </svg>
                      <?php if(!empty($days_week) && !empty($hours)){?>
                        <span class="days-week"> <?php echo esc_html($days_week);?></span>
                        <span>|</span>
                        <span> <?php echo esc_html($hours);?></span>
                      <?php } ?>
                    </li>
                <?php }
                }
                if(!empty($website_link)){?>
                  <li>
                    <a href="<?php echo esc_url($website_link)?>" target="_blank">
                      <svg class="services-icon">
                        <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#web-icon"></use>
                      </svg>
                      <?php echo rtrim(preg_replace('#^(https?://)?(www\.)?#', '', $website_link), '/'); ?>
                    </a>
                  </li>
                <?php } ?>
              </ul>
            </div>
            <?php 
          }
        } 
      }
    }
  $response['post'] = ob_get_clean();

  $coordinates = get_coordinates_by_slug($slug);
  $response['lat'] = !empty($slug) ? $coordinates['lat'] : 48.3794;
  $response['lng'] = !empty($slug) ? $coordinates['lng'] : 31.1656;
  $response['zoom'] = !empty($slug) ? 10 : 6;
  $response['title'] = $term->name;
  echo json_encode($response);
  die();
}

add_action('wp_ajax_district_search', 'district_search');
add_action('wp_ajax_nopriv_district_search', 'district_search');



function final_search() {
  ob_start();

    $slug = !empty($_POST['district']) ? sanitize_text_field($_POST['district']) : '';
    $term = get_term_by('slug', $slug, 'city');
    $lat='';
    $lng='';
    if ($term && !is_wp_error($term)) {
        $term_id = $term->term_id;
        $lat = get_field('lat_term','term_' . $term_id);
        $lng = get_field('lng_term','term_' . $term_id);
        $parent_term = get_term( $term->parent );
        if ( $parent_term && ! is_wp_error( $parent_term ) ) {
            $parent_slug = $parent_term->slug;
        }
    }
    global $wpdb;
    $term_id = (int) $term_id;

    $post_ids = $wpdb->get_col("
      SELECT post_id
      FROM {$wpdb->postmeta}
      WHERE meta_key LIKE 'coordinates\_%\_location_district_term'
      AND meta_value LIKE '%i:0;i:{$term_id}%'
    ");

    $services = get_posts([
      'post_type' => 'services',
      'posts_per_page' => 10000,
      'post__in'=> $post_ids,
    ]);
    if(!empty($services)){
      foreach($services as $service){
        $service_id = $service->ID;
        $coordinates = get_field('coordinates',$service_id);
        $website_link = get_field('website_link', $service_id);
        if(!empty($coordinates)){
          foreach ($coordinates as $address) { 
            $address_map = $address['address'];
            $city = $address['location_city_term'];
            $district = $address['location_district_term'];
            $coordinate_slug = $address['search_slug'];?>
            <div 
              class="services__item" 
              data-service="<?php echo esc_attr($service_id)?>" 
              data-city="<?php echo esc_attr($city->slug)?>" 
              data-district="<?php echo esc_attr($district->slug)?>"
              data-address ="<?php echo esc_attr(json_encode($address_map))?>";
            >
              <h4><?php echo esc_html(get_the_title($service_id))?></h4>
              <ul>
                <li>
                  <svg class="services-icon">
                    <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#map-icon"></use>
                  </svg>
                  <?php echo esc_html($address_map['address'])?>
                </li>
                <?php
                if(have_rows('phone_list',$service_id)){
                  while(have_rows('phone_list',$service_id)){
                    the_row();
                    $phone = get_sub_field('number');
                    if(!empty($phone)){
                ?>
                    <li>
                      <a href="tel:<?php echo esc_attr(preg_replace('/\D/', '', $phone))?>">
                        <svg class="services-icon">
                          <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#phone-icon"></use>
                        </svg>
                        <?php echo esc_html($phone)?>
                      </a>
                    </li>
                  <?php }
                  } 
                } 
                if(have_rows('email_list',$service_id)){
                  while(have_rows('email_list',$service_id)){
                    the_row();
                    $email = get_sub_field('email');
                    if(!empty($email)){ ?>
                    <li>
                      <a href="mailto:<?php echo esc_attr($email)?>">
                        <svg class="services-icon">
                          <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#email-icon"></use>
                        </svg>
                        <?php echo esc_html($email)?>
                      </a>
                    </li>
                    <?php }
                  } 
                } 
                
                if(have_rows('work_hours',$service_id)){
                  while(have_rows('work_hours',$service_id)){
                    the_row();
                      $days_week = get_sub_field('days_week');
                      $hours = get_sub_field('hours');
                    ?>
                    <li>
                      <svg class="services-icon">
                        <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#time-icon"></use>
                      </svg>
                      <?php if(!empty($days_week) && !empty($hours)){?>
                        <span class="days-week"> <?php echo esc_html($days_week);?></span>
                        <span>|</span>
                        <span> <?php echo esc_html($hours);?></span>
                      <?php } ?>
                    </li>
                <?php }
                }
                if(!empty($website_link)){?>
                  <li>
                    <a href="<?php echo esc_url($website_link)?>" target="_blank">
                      <svg class="services-icon">
                        <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#web-icon"></use>
                      </svg>
                      <?php echo rtrim(preg_replace('#^(https?://)?(www\.)?#', '', $website_link), '/'); ?>
                    </a>
                  </li>
                <?php } ?>
              </ul>
            </div>
            <?php 
          }
        } 
      }
    }
  $response['post'] = ob_get_clean();

  $coordinates = get_coordinates_by_slug($slug);
  $response['lat'] = !empty($slug) ? $lat : 48.3794;
  $response['lng'] = !empty($slug) ? $lng : 31.1656;
  $response['zoom'] = !empty($slug) ? 12 : 6;
  $response['url'] = !empty($slug) ? 'place/'.$parent_slug.'/district/'.$slug.'/' : '';
  $response['title'] = $term->name;
  echo json_encode($response);
  die();
}

add_action('wp_ajax_final_search', 'final_search');
add_action('wp_ajax_nopriv_final_search', 'final_search');