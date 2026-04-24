<?php
/**
 * Template Name: Гарантія
 */

get_header();
$current_lang = 'default';
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
    $current_lang = apply_filters( 'wpml_current_language', null );
}
$hero_title = get_the_title();
$hero_description = get_field('subtitle');

$warranty_content = get_field('content_template');
$seo_text = get_field('seo_text');

$place = get_query_var('place');
$args_list = new WP_Query(
  [
    'post_type' => 'services',
    'posts_per_page' => 10000,
  ]
);


if (function_exists('woocommerce_breadcrumb')) {
  woocommerce_breadcrumb([
      'delimiter'   => ' › ',
      'wrap_before' => '<nav class="wrapper woocommerce-breadcrumb breadcrumbs__wrapper">',
      'wrap_after'  => '</nav>',
      'before'      => '',
      'after'       => '',
      'home'        => _x('Home', 'breadcrumb', 'woocommerce'),
  ]);
}

if(!empty($hero_title) || !empty($hero_description)){?>


  <section class="section mission white">
    <div class="wrapper mission__wrapper">
      <?php if(!empty($hero_title)) { ?>
        <h1><?php echo $hero_title ?></h1>
      <?php } 
      if(!empty($hero_description)) { ?>
        <p><?php echo $hero_description ?></p>
      <?php } ?>
    </div>
  </section>
<?php } 
if(!empty($warranty_content)) { ?>
   <section class="seo section">
    <div class="wrapper seo__wrapper">
      <?php echo $warranty_content ; ?> 
    </div>
  </section>
<?php } ?>
<section id="search-service" class="filters filters-services" data-url="<?php echo esc_url(get_the_permalink())?>" data-page="true">
  <div class="wrapper filters__wrapper filters__wrapper--warranty">
    <?php  
 
    $city = get_terms(['taxonomy' => 'city','parent'=> 0,'hide_empty' => false]);
 
    if(!empty($place)){
      $place_term = get_term_by('slug', $place, 'city');
    
      if ($place_term && !is_wp_error($place_term)) {
          $place_term_id = $place_term->term_id;
      }
    } 
    if(!empty($city )){      
      ?>
      <h2><?php _e('Сервісні центри Braun','braun_2025')?></h2>
      <div class="filters-group search-area warranty-page <?php echo !empty($place) ? 'active':'' ?>">
        <label for="search-city" class="filters-label" data-parent="0">
          <input type="text" name="search-city" id="search-city" placeholder="<?php _e('Оберіть ваше місто','braun_2025')?>" value="<?php echo !empty($place)? $place_term->name:''?>"> 
          <svg class="filters-icon arrow-icon">
            <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
          </svg>
        </label>
        <button type="button" class="btn btn-close">            
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
    <?php }  ?>

  </div>
</section>
<?php  
  if($args_list->have_posts()) { 
    ?>
    <section class="services">
      <div class="wrapper services__wrapper">
          <div class="services__list">
            <?php
              while($args_list->have_posts()) {
                $args_list->the_post(); 
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
if(!empty($seo_text)) { ?>
   <section class="seo section">
    <div class="wrapper seo__wrapper">
      <?php echo $seo_text; ?> 
    </div>
  </section>
<?php }
get_footer();