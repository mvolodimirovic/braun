<?php

/**
 * All functions for customizing and designing the admin area
 */
if ( ! defined( 'ICL_LANGUAGE_CODE' ) ) {
	// Replace the ICL_LANGUAGE_CODE if not defined.
	define( 'ICL_LANGUAGE_CODE', 'uk' );
}
add_action( 'wp_head', 'myplugin_ajaxurl' );
function myplugin_ajaxurl() {
	echo '<script type="text/javascript">
		   var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";
		   var lang = "' . ICL_LANGUAGE_CODE . '";
		 </script>';
}

function login_logo() {
	?>
	<style type="text/css">
		#login h1 a,
		.login h1 a {
			background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/img/logo.svg);
			background-size: contain;
			background-position: center;
			width: 81px;
			height: 80px;
		}
	</style>
	<?php
}
add_action( 'login_enqueue_scripts', 'login_logo' );

function login_logo_url() {
	return home_url();
}
add_filter( 'login_headerurl', 'login_logo_url' );

function login_logo_url_title() {
	return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'login_logo_url_title' );

/**
 * Remove comments and default posts
 */
function my_remove_admin_menus() {
	remove_menu_page( 'edit-comments.php' );
	// remove_menu_page( 'edit.php' );
}
add_action( 'admin_menu', 'my_remove_admin_menus' );
function remove_default_post_tags() {
  unregister_taxonomy_for_object_type('post_tag', 'post');
}
add_action('init', 'remove_default_post_tags');

/**
 * Remove comments and default posts from admin bar
 */
function my_adminbar_new_content() {

	global $wp_admin_bar;

	// get node for New + menu node
	$new_content_node = $wp_admin_bar->get_node( 'new-content' );

	if ($new_content_node) {
		// change URL for node, edit for prefered link
		$new_content_node->href = admin_url( '' );

		// Update New + menu node
		$wp_admin_bar->add_node( $new_content_node );
	}

	// remove items from New Content menu
	$wp_admin_bar->remove_node( 'new-post' );
	$wp_admin_bar->remove_node( 'comments' );
}
add_action( 'wp_before_admin_bar_render', 'my_adminbar_new_content' );

/**
 * Disable WordPress Big Image Size Scaling
 */
add_filter( 'big_image_size_threshold', '__return_false' );

/**
 * Remove default image sizes
 */
function remove_default_image_sizes( $sizes ) {
	unset( $sizes['1536x1536'] );
	unset( $sizes['2048x2048'] );
	return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced', 'remove_default_image_sizes' );

/**
 * Remove empty paragraphs
 */
add_filter( 'the_content', 'remove_empty_p' );
add_filter( 'the_excerpt', 'remove_empty_p' );
function remove_empty_p( $content ) {
	return str_ireplace( array( '<p>&nbsp;</p>', '<p></p>' ), '', $content );
}

/**
 * Remove empty paragraphs ACF
 */
function my_acf_load_value( $value, $post_id, $field ) {
	$content = apply_filters( 'the_content', $value );
	$content = force_balance_tags( $content );
	$content = preg_replace( '#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content );
	$content = preg_replace( '~\s?<p>(\s| )+</p>\s?~', '', $content );

	return $content;
}
add_filter( 'acf/load_value/type=wysiwyg', 'my_acf_load_value', 10, 3 );

/**
 * Allow to load svg
 */
function cc_mime_types( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

/**
 * Remove p tag from img in content
 */
function filter_ptags_on_images( $content ) {
	return preg_replace( '/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content );
}
add_filter( 'the_content', 'filter_ptags_on_images' );

// img unautop
// use the get_field() or the_field() functions
function img_unautop( $pee ) {
	$pee = preg_replace( '/<p>\\s*?(<a .*?><img.*?><\\/a>|<img.*?>)?\\s*<\\/p>/s', '<figure class="wp-block-image">$1</figure>', $pee );
	return $pee;
}
add_filter( 'acf_the_content', 'img_unautop', 30 );

/**
 * Remove <p> Tag From Contact Form 7
 */
add_filter( 'wpcf7_autop_or_not', '__return_false' );

/**
 * Move Yoast to the Bottom
 */
function yoasttobottom() {
	return 'low';
}
add_filter( 'wpseo_metabox_prio', 'yoasttobottom' );

/**
 * Add settings page
 */
if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page(
		array(
			'page_title' => esc_html__( 'Сторінка налаштувань', THEME_WPML_SLUG ),
			'menu_title' => esc_html__( 'Сторінка налаштувань', THEME_WPML_SLUG ),
			'menu_slug'  => 'theme-general-settings',
			'capability' => 'edit_posts',
			'redirect'   => false,
		)
	);
}

function post_relationship_query( $args, $field, $post_id ) {
	// order by newest posts first
	$args['orderby']     = 'date';
	$args['order']       = 'DESC';
	$args['post_status'] = 'publish';

	// return
	return $args;
}
// set order by date filter for every field with post query
add_filter( 'acf/fields/relationship/query', 'post_relationship_query', 10, 3 );
/*
* DISALLOW_FILE_EDIT - turning off edit files from theme editor in admin area
*/
define( 'DISALLOW_FILE_EDIT', true );

function limit_upload_size( $file ) {

	$image_limit = 5 * 1024 * 1024;  // 5 MB
	$file_limit  = 50 * 1024 * 1024;  // 50 MB

	$image_types = array( 'image/jpeg', 'image/png', 'image/gif' );

	// Перевірка чи файл є зображенням
	if ( in_array( $file['type'], $image_types ) ) {
		if ( $file['size'] > $image_limit ) {
			$file['error'] = 'Зображення не може перевищувати 5MB.';
		}
	} elseif ( $file['size'] > $file_limit ) {
		$file['error'] = 'Файл не може перевищувати 50MB.';
	}

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'limit_upload_size' );

function update_admin_color_for_test_environment( $color_scheme ) {
	global $current_user;
	wp_get_current_user();

	if ( is_test_environment() && $current_user->ID == 1 ) {
		$color_scheme = 'coffee';
	}

	return $color_scheme;
}
add_filter( 'get_user_option_admin_color', 'update_admin_color_for_test_environment', 5 );

function disable_default_dashboard_widgets() {
	global $wp_meta_boxes;
	// wp
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_site_health'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts'] );
	// bbpress
	unset( $wp_meta_boxes['dashboard']['normal']['core']['bbp-dashboard-right-now'] );
	// yoast seo
	unset( $wp_meta_boxes['dashboard']['normal']['core']['wpseo-dashboard-overview'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['wpseo-wincher-dashboard-overview'] );
	// gravity forms
	unset( $wp_meta_boxes['dashboard']['normal']['core']['rg_forms_dashboard'] );
}
add_action( 'wp_dashboard_setup', 'disable_default_dashboard_widgets', 999 );

add_filter('use_block_editor_for_post_type', 'disable_gutenberg_for_pages', 10, 2);

function disable_gutenberg_for_pages($use_block_editor, $post_type) {
    if ($post_type === 'page') {
        return false;
    }

    return $use_block_editor;
}

add_action( 'admin_head', 'custom_remove_max_width_on_edittag' );
function custom_remove_max_width_on_edittag() {
    $screen = get_current_screen();

        echo '<style>
            #edittag {
                max-width: none !important;
                width: 100% !important;
            }
        </style>';
}
// Додаємо поле при створенні атрибута
add_action('woocommerce_after_add_attribute_fields', function () {
  ?>
  <tr class="form-field">
      <th scope="row"><label for="is_feature"><?php _e('Характеристика чи Особливість?', 'braun_2025'); ?></label></th>
      <td>
          <input type="checkbox" name="is_feature" id="is_feature" value="1" />
          <p class="description"><?php _e('Відмічений - характеристика, пустий - особливість', 'braun_2025'); ?></p>
      </td>
  </tr>
  <?php
});

add_action('woocommerce_after_edit_attribute_fields', function ($attribute) {
  $attribute_id = isset($_GET['edit']) ? absint($_GET['edit']) : 0;

  if (!$attribute_id) {
      echo '<tr class="form-field"><td colspan="2"><em>Не вдалося визначити ID атрибута.</em></td></tr>';
      return;
  }

  // Отримуємо всі Woo атрибути
  $attribute_taxonomies = wc_get_attribute_taxonomies();

  $attribute_slug = '';
  foreach ($attribute_taxonomies as $attr) {
      if ((int) $attr->attribute_id === $attribute_id) {
          $attribute_slug = $attr->attribute_name;
          break;
      }
  }

  if (!$attribute_slug) {
      echo '<tr class="form-field"><td colspan="2"><em>Не вдалося знайти атрибут за ID.</em></td></tr>';
      return;
  }

  $option_key = 'attribute_is_feature_' . $attribute_slug;
  $value = get_option($option_key, 0);
  ?>
  <tr class="form-field">
      <th scope="row"><label for="is_feature"><?php _e('Характеристика чи Особливість?', 'textdomain'); ?></label></th>
      <td>
          <input type="checkbox" name="is_feature" id="is_feature" value="1" <?php checked($value, 1); ?> />
          <p class="description"><?php _e('Відмічений - характеристика, пустий - особливість', 'textdomain'); ?></p>
      </td>
  </tr>
  <?php
});


// Збереження при створенні атрибута
add_action('woocommerce_attribute_added', function ($attribute_id, $data) {
  $attribute_slug = '';
  if (is_array($data) && isset($data['attribute_name'])) {
      $attribute_slug = $data['attribute_name'];
  } elseif (is_string($data)) {
      $attribute_slug = $data;
  }

  if (!empty($attribute_slug) && isset($_POST['is_feature'])) {
      update_option('attribute_is_feature_' . esc_attr($attribute_slug), 1);
  }
}, 10, 2);

// Збереження при редагуванні атрибута
add_action('woocommerce_attribute_updated', function ($attribute_id, $data) {
  $attribute_slug = '';
  if (is_array($data) && isset($data['attribute_name'])) {
      $attribute_slug = $data['attribute_name'];
  } elseif (is_string($data)) {
      $attribute_slug = $data;
  }

  if (!empty($attribute_slug)) {
      $value = isset($_POST['is_feature']) ? 1 : 0;
      update_option('attribute_is_feature_' . esc_attr($attribute_slug), $value);
  }
}, 10, 2);

add_action('save_post_product', function($post_id, $post, $update) {
  if (wp_is_post_revision($post_id)) return;

  // Якщо поле ще не існує – встановлюємо 0
  if (!metadata_exists('post', $post_id, '_product_views')) {
      update_post_meta($post_id, '_product_views', 0);
  }
}, 10, 3);
