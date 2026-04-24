<?php
/**
 * braun_2025 functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package braun_2025
 */

define("THEME_WPML_SLUG", "braun_2025");
define("THEME_SLUG", "braun_2025"); 

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '3.0.0' );
}

if ( ! function_exists( 'theme_setup__setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function theme_setup__setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on braun_2025, use a find and replace
		 * to change THEME_WPML_SLUG to the name of your theme in all the template files.
		 */
		load_theme_textdomain( THEME_WPML_SLUG, get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		/** ==============================================
		 *  register menus ===============================
		 * ===============================================
		 */
		register_nav_menus(
			array(
				'top_menu' => esc_html__('Top menu', THEME_WPML_SLUG),
				'footer_menu' => esc_html__('Footer menu', THEME_WPML_SLUG),
				'footer_category' => esc_html__('Footer category menu', THEME_WPML_SLUG),
				'footer_bottom' => esc_html__('Footer bottom', THEME_WPML_SLUG),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		/** ==============================================
		 *  Add image sizes ==============================
		 * ===============================================
		 */
		add_image_size('cat_image', 380, 380, false);
		add_image_size('cat_image_large', 780, 380, false);
		add_image_size('actual_image', 240, 230, false);
		add_image_size('product_image', 580, 580, false);
		add_image_size('chronology_image', 680, 450, false);
	}
endif;
add_action( 'after_setup_theme', 'theme_setup__setup' );

/**
 * Enqueue scripts and styles.
 */
function braun_enqueue_scripts() {
  // СТИЛІ
  wp_enqueue_style('braun-style', get_template_directory_uri() . '/style.min.css', [], filemtime(get_template_directory() . '/style.min.css'));
  wp_enqueue_style('swiper-style', get_template_directory_uri() . '/swiper.min.css', [], filemtime(get_template_directory() . '/swiper.min.css'));

  // JQUERY
  if (!is_admin()) {
    wp_deregister_script('jquery');
    wp_register_script('jquery', includes_url('/js/jquery/jquery.js'), false, null, true);
    wp_enqueue_script('jquery');
  }

  // SWIPER (defer)
  wp_register_script('swiper-scripts', get_template_directory_uri() . '/js/swiper.min.js', ['jquery'], filemtime(get_template_directory() . '/js/swiper.min.js'), true);
  wp_script_add_data('swiper-scripts', 'defer', true);
  wp_enqueue_script('swiper-scripts');

  // MASK (defer)
  wp_register_script('mask-scripts', get_template_directory_uri() . '/js/mask.min.js', ['jquery'], filemtime(get_template_directory() . '/js/mask.min.js'), true);
  wp_script_add_data('mask-scripts', 'defer', true);
  wp_enqueue_script('mask-scripts');

  // GOOGLE MAP (async), INIT (defer)
  if (is_archive('services') || is_page_template( 'warranty-template.php' )) {
    wp_register_script('custom-google-map-init', get_template_directory_uri() . '/js/custom-map.js', [], null, true);
    wp_script_add_data('custom-google-map-init', 'defer', true);
    wp_enqueue_script('custom-google-map-init');

    wp_register_script('google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDgpsA7omeC1OtlpzsDaJaEmBvA9x06f7M&callback=initMap', [], null, true);
    wp_script_add_data('google-maps-api', 'async', true);
    wp_enqueue_script('google-maps-api');
  }
  $lat = null;
  $lng = null;
  $zoom = null;
  if (is_post_type_archive('services') || is_page_template( 'warranty-template.php' )) {
      // Це твоя логіка пошуку координат з archive-services.php
      $slug_point = get_query_var('point'); 
      $slug_city = get_query_var('place'); 
      $slug_district = get_query_var('district'); 
      if(!empty($slug_point)){
        $search_value = '';
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                $coordinates = get_field('coordinates');
                if (!empty($coordinates)) {
                    foreach ($coordinates as $address) {
                        $value = $address['search_slug'];
                        if (!empty($slug_point) && $value == $slug_point) {
                            $lat = $address['address']['lat'];
                            $lng = $address['address']['lng'];
                            $zoom = 14;
                            break 2;
                        } 
                    }
                }
            }
            wp_reset_postdata();
        }
      }
      if(!empty($slug_city)){
        $city_term = get_term_by('slug', $slug_city,'city');

        if (!$city_term || is_wp_error($city_term)) {
            return null;
        }

        $lat = get_field('lat_term', 'term_' . $city_term->term_id);
        $lng = get_field('lng_term', 'term_' . $city_term->term_id);
        $zoom = 11;
      }
      if (!empty($slug_district)) {
        $district_term = get_term_by('slug', $slug_district,'city');

        if (!$district_term || is_wp_error($district_term)) {
            return null;
        }

        $lat = get_field('lat_term', 'term_' . $district_term->term_id);
        $lng = get_field('lng_term', 'term_' . $district_term->term_id);
        $zoom = 12;
      }
    }
  

  // Вимикаємо WooCommerce сингл-скрипт
  if (is_singular('product')) {
    wp_dequeue_script('wc-single-product');
  }
  // SITE JS (defer)
  wp_register_script('braun-scripts', get_template_directory_uri() . '/src/js/site.js', ['jquery'], filemtime(get_template_directory() . '/src/js/site.js'), true);
  wp_script_add_data('braun-scripts', 'defer', true);
  wp_enqueue_script('braun-scripts');
  // AJAX
  wp_localize_script('jquery', 'themeAjax', [
    'ajaxurl'    => admin_url('admin-ajax.php'),
    'nextNonce'  => wp_create_nonce('themeAjaxNonce')
  ]);

  if ($lat && $lng) {
      wp_localize_script('braun-scripts', 'MapData', [
          'lat' => $lat,
          'lng' => $lng,
          'zoom' => $zoom,
      ]);
  }
}
add_action('wp_enqueue_scripts', 'braun_enqueue_scripts');


add_action('admin_enqueue_scripts', 'enqueue_google_maps_admin');

function enqueue_google_maps_admin($hook) {

    global $post;

    if (!$post) return;
    if (!in_array($hook, ['post.php', 'post-new.php'])) {
        return;
    }    
    if (!is_admin() || !in_array($hook, ['post.php', 'post-new.php'])) return;

    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;
    $post_type = $post_id ? get_post_type($post_id) : ($_GET['post_type'] ?? '');
    $template = get_page_template_slug($post->ID);
    if ($template === 'template-contacts.php' || $post_type === 'services') {
        wp_enqueue_script(
        'google-maps-api',
        'https://maps.googleapis.com/maps/api/js?key=AIzaSyDgpsA7omeC1OtlpzsDaJaEmBvA9x06f7M',
        array(),
        null,
        true
      );
    }
}

add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Additions to the basic Wordpress customizer
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * All functions for customizing and designing the admin area
 */
require get_template_directory() . '/inc/admin-customize.php';

/**
 * All pre_get_posts functions from the theme
 */
require get_template_directory() . '/inc/pre-get-post.php';

/**
 * All redirects which created from theme files
 */
require get_template_directory() . '/inc/redirections.php';

/**
 * Custom functions which enhance the theme by hooking into WordPress
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Rest api disable
 */
require get_template_directory() . '/inc/rest-api-disable.php';

/**
 * Ajax 
 */
require get_template_directory() . '/inc/ajax-functions.php';

/**
 * Custom Blocks 
 */
require get_template_directory() . '/inc/custom-blocks.php';

/**
 * ClaenUp
 */
require get_template_directory() . '/inc/cleanup.php';

/**
 * Inc Services
 */
require get_template_directory() . '/inc/services/inc-services.php';