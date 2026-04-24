<?php
get_header();
global $wp_query;

$current_lang = 'default';
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
    $current_lang = apply_filters( 'wpml_current_language', null );
}
// hero 
$hero_image = get_field('image','services_opt');
$hero_title = get_field('title','services_opt');
$hero_description = get_field('short_descr','services_opt');
$hero_args = [
  'image_id'=> $hero_image,
  'title' => $hero_title,
  'description'=> $hero_description,
  'template'=> '',
  'button'=> '',
  'current_lang' => $current_lang
];

$slug = get_query_var('point');
$place = get_query_var('place');
$district_place = get_query_var('district');

$args_list = new WP_Query(
  [
    'post_type' => 'services',
    'posts_per_page' => 10000,
  ]
);

$search_value = '';
$lat_search = '';
$lng_search = '';
if(have_posts()) { 
  while(have_posts()) {
    the_post(); 
    $service_id = get_the_ID();
    $coordinates = get_field('coordinates',$service_id);
    if(!empty($coordinates)){
      foreach ($coordinates as $address) { 
          $value = $address['search_slug'];
          if(!empty($slug) && $value == $slug){
            $search_value = $address['address']['address'];
            $lat_search = $address['address']['lat'];
            $lng_search = $address['address']['lng'];
            break;
          }
      }
    }
  } wp_reset_postdata();
}

get_template_part('/template-parts/content','hero',$hero_args);

if (function_exists('woocommerce_breadcrumb')) {
  woocommerce_breadcrumb([
      'delimiter'   => ' › ',
      'wrap_before' => '<nav class="wrapper woocommerce-breadcrumb breadcrumbs__wrapper">',
      'wrap_after'  => '</nav>',
      'before'      => '',
      'after'       => '',
      'home'        => _x('Home', 'breadcrumb', 'woocommerce'),
  ]);
} ?>
<section id="search-service" class="filters filters-services" data-url="<?php echo esc_url(get_post_type_archive_link('services'))?>">
  <div class="wrapper filters__wrapper">
    <?php  
      if($args_list->have_posts()) { ?>
        <div class="filters-group search-shop <?php echo !empty($slug) ? 'active':'' ?>">
          <label for="search-company" class="filters-label">
            <input type="text" name="search-services" id="search-company" value="<?php echo !empty($slug) ? $search_value:''?>" placeholder="<?php _e('Пошук за назвою магазину','braun_2025')?>">
            <svg class="filters-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#icon-search"></use>
            </svg>
          </label>
          <button type="button" class="btn btn-close">            
            <svg class="icon-close">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#icon-close"></use>
            </svg>
          </button>
          <div class="filters__list" data-name="seacrch-services">
            <div class="border-wrapp">
              <ul class="filters__list-wrap services-wrapper">
                <?php
                while($args_list->have_posts()) {
                  $args_list->the_post(); 
                  $service_id = get_the_ID();
                  $coordinates = get_field('coordinates',$service_id);
                  if(!empty($coordinates)){
                    foreach ($coordinates as $address) { 
                      $address_map = $address['address'];
                      $city = $address['location_city_term'];
                      $district = $address['location_district_term'];
                      $value = $address['search_slug'];
                      $coordinate_slug = $address['search_slug'];
                      if(!empty($address_map)){
                        ?>
                          <li 
                            class="filters__list__item single services-item address-ajax" 
                            data-service="<?php echo esc_attr($service_id)?>" 
                            data-city="<?php echo esc_attr($city->slug)?>" 
                            data-district="<?php echo esc_attr($district->slug)?>"
                            data-slug="<?php echo esc_attr($address['search_slug'])?>"
                            data-address ="<?php echo esc_attr(json_encode($address_map))?>";
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
                      <?php 
                      } 
                    }
                  } 
                } wp_reset_postdata(); ?>
              </ul>
            </div>
          </div>
        </div> 
    <?php } 
    $city = get_terms(['taxonomy' => 'city','parent'=> 0,'hide_empty' => false]);
 
    if(!empty($place)){
      $place_term = get_term_by('slug', $place, 'city');
    
      if ($place_term && !is_wp_error($place_term)) {
          $place_term_id = $place_term->term_id;
      }
      $child_terms = get_terms([
        'taxonomy'   => 'city',
        'hide_empty' => false,
        'parent'=> $place_term_id
      ]);
    } 
    if(!empty($city )){      
      ?>
      <div class="filters-group search-area <?php echo !empty($place) ? 'active':'' ?>">
        <label for="search-city" class="filters-label" data-parent="0">
          <input type="text" name="search-city" id="search-city" placeholder="<?php _e('Оберіть ваше місто','braun_2025')?>" value="<?php echo !empty($place)? $place_term->name:''?>"> 
          <svg class="filters-icon arrow-icon">
            <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
          </svg>
        </label>

        <button type="button" class="btn btn-close city" data-city="<?php echo !empty($place ) ? esc_attr($place) : '';?>">            
          <svg class="icon-close">
            <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#icon-close"></use>
          </svg>
        </button>
        <div class="filters__list" data-name="place">
          <div class="border-wrapp">
            <ul class="filters__list-wrap services-wrapper">
            <?php
              foreach ($city as $city_name) {
            ?>
                <li class="filters__list__item single services-item city-ajax" data-value="<?php echo esc_attr($city_name->slug)?>">
                  <span>
                    <?php echo esc_html($city_name->name);?>
                  </span>
                </li>
                <?php } ?>
            </ul>
          </div>
        </div>
      </div>
    <?php } 

    $district_place_term = get_term_by('slug', $district_place, 'city');
    $district_place_term_name = '';

    if ( $district_place_term instanceof WP_Term && term_exists( $district_place_term->term_id, 'city' ) ) {
        $district_place_term_name = esc_attr( $district_place_term->name );
    }
    ?>
      <div class="filters-group search-district <?php echo empty($place) ? 'disabled': 'active';?>">
        <label for="search-area" class="filters-label" data-parent="1">
          <input type="text" name="search-area" id="search-area" value="<?php echo $district_place_term_name; ?>" placeholder="<?php _e('Оберіть ваш район','braun_2025')?>" <?php echo empty($place) ? 'disabled': '';?>> 
          <svg class="filters-icon arrow-icon">
            <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
          </svg>
        </label>
        <button type="button" class="btn btn-close district" data-district="<?php echo !empty($district_place ) ? esc_attr($district_place) : '';?>">            
          <svg class="icon-close">
            <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#icon-close"></use>
          </svg>
        </button>
        <div class="filters__list" data-name="area">
          <div class="border-wrapp">
            <ul data-name="area"  class="filters__list-wrap services-wrapper">
              <?php
                if(!empty($child_terms)) { 
                  foreach ($child_terms as $child_term) { ?>
                    <li 
                      class="filters__list__item single services-item district-ajax" 
                      data-value="<?php echo esc_attr($child_term->slug)?>"
                    >
                      <span>
                        <?php echo esc_html($child_term->name)?>
                      </span>
                    </li>
                  <?php } 
                } ?>
            </ul>
          </div>
        </div>
      </div>
  </div>
</section>
<?php  
  if(have_posts()) { 
    ?>
    <section class="services">
      <div class="wrapper services__wrapper">
          <div class="services__list">
            <?php
              while(have_posts()) {
                the_post(); 
                $service_id = get_the_ID();
                $coordinates = get_field('coordinates',$service_id);
                $website_link = get_field('website_link');
                if(!empty($coordinates)){
                  foreach ($coordinates as $address) { 
                    $address_map = $address['address'];
                    $city = $address['location_city_term'];
                    $district = $address['location_district_term'];
                    $coordinate_slug = $address['search_slug'];
                     
                    if(!empty($address_map)) { 
                      if(!empty($slug) && $slug !== $coordinate_slug) {
                        continue;
                      }
                      ?>
                      <div 
                        class="services__item" 
                        data-service="<?php echo esc_attr($service_id)?>" 
                        data-city="<?php echo esc_attr($city->slug)?>" 
                        data-district="<?php echo esc_attr($district->slug)?>"
                      >
                        <h4><?php echo esc_html(get_the_title())?></h4>
                        <ul>
                          <li>
                            <svg class="services-icon">
                              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#map-icon"></use>
                            </svg>
                            <?php echo esc_html($address_map['address'])?>
                          </li>
                          <?php
                          if(have_rows('phone_list')){
                            while(have_rows('phone_list')){
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
                          if(have_rows('email_list')){
                            while(have_rows('email_list')){
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
                          
                          if(have_rows('work_hours')){
                            while(have_rows('work_hours')){
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
              } wp_reset_postdata(); ?>
          </div>
          <div class="services__map" id="map"></div>
        <div class="loader-wrapper">
          <div class="loader"></div>
        </div>
      </div>
    </section>
<?php 
}

get_footer();
