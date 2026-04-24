<?php

/**
 * Custom functions which enhance the theme by hooking into WordPress
 */

function is_test_environment() {
	$environment_type = wp_get_environment_type();

	if ( $environment_type === 'production' ) {
		$site_url = get_site_url();

		if (
			strpos( $site_url, 'm1-dev.site' ) !== false) {
			return true;
		}
		return false; 
	}
	return true;
}
class Braun_Category_Nav_Menu extends Walker_Nav_Menu {
  private $preview_added = false;
  private $is_braun_category = false; 

  public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
      $category_id = $item->object_id;
      $classes = implode(' ', $item->classes);
      
      // Визначаємо, чи пункт меню або сам є braun__category, або знаходиться всередині нього
      if ($depth === 0) {
          $this->is_braun_category = in_array('braun__category', $item->classes);
          $this->preview_added = false; 
      }

      $is_category_item = $this->is_braun_category || in_array('braun__category', $item->classes);
      $defaul_item = $this->is_braun_category ? '' : ' default-item';

      $output .= '<li class="' . esc_attr($classes) . esc_attr($defaul_item) .'"';

      if ($depth !== 0) {
          $output .= ' data-id="' . esc_attr($category_id) . '"';
      }

      $output .= '>';

      // Використовуємо <button>, якщо цей пункт сам є `braun__category` або знаходиться всередині нього
      if ($depth < 1 && $is_category_item) {
          $output .= '<button type="button">' . esc_html($item->title) . '</button>';
      } else {
          $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
      }
  }

  public function end_el(&$output, $item, $depth = 0, $args = null) {
      $output .= '</li>';
  }

  public function start_lvl(&$output, $depth = 0, $args = null) {
      if ($depth === 0 && $this->is_braun_category) {
          $output .= '<div class="menu__category-item"><div class="wrapper menu__category-wrapper">';
          $output .= '<h4>' . __('Техніка Braun', 'braun_2025') . '</h4> 
                      <div class="menu__category-icon">
                          <svg width="20px" height="20px" viewBox="0 0 20 21">
                              <path d="M19.7557 0.743989C19.5994 0.587763 19.3875 0.5 19.1665 0.5C18.9455 0.5 18.7336 0.587763 18.5773 0.743989L9.99982 9.32149L1.42232 0.743989C1.26605 0.587763 1.05413 0.5 0.833155 0.5C0.612185 0.5 0.400262 0.587763 0.243989 0.743989C0.0877628 0.900262 0 1.11219 0 1.33316C0 1.55413 0.0877628 1.76605 0.243989 1.92232L8.82149 10.4998L0.243989 19.0773C0.0877628 19.2336 0 19.4455 0 19.6665C0 19.8875 0.0877628 20.0994 0.243989 20.2557C0.400262 20.4119 0.612185 20.4996 0.833155 20.4996C1.05413 20.4996 1.26605 20.4119 1.42232 20.2557L9.99982 11.6782L18.5773 20.2557C18.7336 20.4119 18.9455 20.4996 19.1665 20.4996C19.3875 20.4996 19.5994 20.4119 19.7557 20.2557C19.9119 20.0994 19.9996 19.8875 19.9996 19.6665C19.9996 19.4455 19.9119 19.2336 19.7557 19.0773L11.1782 10.4998L19.7557 1.92232C19.9119 1.76605 19.9996 1.55413 19.9996 1.33316C19.9996 1.11219 19.9119 0.900262 19.7557 0.743989Z" />
                          </svg>
                      </div>';
      }

      $output .= $this->is_braun_category ? '<ul class="sub-menu level-' . esc_attr($depth) . '">' : '<ul class="sub-menu default-submenu">';

      if ($depth === 0 && $this->is_braun_category && !$this->preview_added) {
          $output .= '<li class="sub-menu-preview"></li>';
          $this->preview_added = true; 
      }
  }

  public function end_lvl(&$output, $depth = 0, $args = null) {
      $output .= '</ul>'; 
      if ($depth === 0 && $this->is_braun_category) {
          $output .= '</div></div>';
      }
  }
}

class Braun_Footer_Category_Nav_Menu extends Walker_Nav_Menu {
  public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
      $classes = empty($item->classes) ? [] : (array) $item->classes;
      $class_names = join(' ', array_filter($classes));
      $class_attribute = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

      $output .= '<li' . $class_attribute . '>';

      if ($depth == 0) {
          $output .= '<h5>' . esc_html($item->title) . '</h5>';
      } else {
          $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
      }
  }

  public function end_el(&$output, $item, $depth = 0, $args = null) {
      $output .= '</li>';
  }
}

add_action('after_setup_theme', function() {
  add_theme_support('woocommerce');
});

function track_product_views() {
  if (!is_singular('product')) return;

  global $post;
  $views = get_post_meta($post->ID, '_product_views', true); 
  $views = $views ? $views + 1 : 1;
  update_post_meta($post->ID, '_product_views', $views); 
}
add_action('wp', 'track_product_views');

// Додаємо колонку "Перегляди"
add_filter('manage_edit-product_columns', function($columns) {
  $new_columns = [];
  foreach ($columns as $key => $title) {
      $new_columns[$key] = $title;
      if ($key === 'price') { // Додаємо колонку після ціни
          $new_columns['product_views'] = 'Перегляди 👀';
      }
  }
  return $new_columns;
});

// Заповнюємо колонку даними
add_action('manage_product_posts_custom_column', function($column, $post_id) {
  if ($column === 'product_views') {
      $views = get_post_meta($post_id, '_product_views', true);
      echo $views ? number_format($views) : '<span style="color:#aaa;">—</span>';
  }
}, 10, 2);

// Додаємо підтримку сортування колонки
add_filter('manage_edit-product_sortable_columns', function($sortable_columns) {
  $sortable_columns['product_views'] = 'product_views';
  return $sortable_columns;
});

add_action('admin_head', function() {
  echo '<style>
      .column-product_views { width: 130px; text-align: center; }
  </style>';
});

add_filter('woocommerce_get_breadcrumb', 'custom_post_breadcrumbs_single_category', 10, 2);

function custom_post_breadcrumbs_single_category($crumbs, $breadcrumb) {
    if (is_single() && get_post_type() === 'post') {
        $new_crumbs = [];

        // Головна
        $home_title = __('Головна', 'woocommerce');
        $new_crumbs[] = [$home_title, home_url('/')];

        // Блог (сторінка для записів)
        if ($blog_page_id = get_option('page_for_posts')) {
            $new_crumbs[] = [get_the_title($blog_page_id), get_permalink($blog_page_id)];
        }

        // Одна (перша) категорія
        $categories = get_the_category();
        if (!empty($categories)) {
            $category = $categories[0];
            $new_crumbs[] = [$category->name, get_category_link($category->term_id)];
        }

        // Назва поточного запису (без посилання)
        $new_crumbs[] = [get_the_title(), ''];

        return $new_crumbs;
    }

    return $crumbs;
}

add_filter( 'wc_product_post_type_link_product_cat', function( $term, $terms, $post ) {

  // Get the primary term as saved by Yoast
  $primary_cat_id = (int)get_post_meta( $post->ID, '_yoast_wpseo_primary_product_cat', true );

  // If there is a primary and it's not currently chosen as primary
  if ($primary_cat_id && is_object($term) && $term->term_id != $primary_cat_id) {

      // Find the primary term in the term list
      foreach ( $terms as $term_key => $term_object ) {

          if ( $term_object->term_id == $primary_cat_id ) {
              // Return this as the primary term
              $term = $terms[ $term_key ];
              break;
          }

      }

  }

  return $term;

}, 10, 3 );

function primary_category($id,$taxonomy){
  $product_id = $id;
  $primary_cat_id = (int)get_post_meta($product_id, '_yoast_wpseo_primary_product_cat', true);
  
  if ($primary_cat_id) {
      $primary_category = get_term($primary_cat_id, $taxonomy);
  } else {
      $terms = get_the_terms($product_id, $taxonomy);
      $primary_category = (!empty($terms) && !is_wp_error($terms)) ? $terms[0] : false;
  }

  if ($primary_category && !is_wp_error($primary_category)) {
      $cat_link = get_term_link($primary_category);
      $cat_name = esc_html($primary_category->name);

      return ['name' => $cat_name,'link'=>$cat_link];
  }
}

function get_term_name($term_id, $taxonomy = 'category') {
  $term = get_term($term_id, $taxonomy);

  if (!is_wp_error($term) && !empty($term)) {
      return $term->name;
  }

  return '';
}

add_filter('woocommerce_get_breadcrumb', 'custom_breadcrumb_for_products_and_categories', 20, 2);

function custom_breadcrumb_for_products_and_categories($crumbs, $breadcrumb_obj) {

    // 🔹 Якщо ми на сторінці продукту
    if (is_product()) {
        global $post;

        $terms = get_the_terms($post->ID, 'product_cat');
        if (!$terms || is_wp_error($terms)) return $crumbs;

        foreach ($terms as $term) {
            $ancestors = get_ancestors($term->term_id, 'product_cat');
            $ancestors = array_reverse($ancestors);

            foreach (array_merge($ancestors, [$term->term_id]) as $cat_id) {
                $hide = get_field('filters_child', 'product_cat_' . $cat_id);
                if ($hide) {
                    $new_crumbs = [];

                    foreach ($crumbs as $crumb) {
                        if (strpos($crumb[1], get_term_link($cat_id, 'product_cat')) !== false) {
                            $new_crumbs[] = $crumb;
                            break;
                        }
                        $new_crumbs[] = $crumb;
                    }

                    $new_crumbs[] = end($crumbs); // додай продукт в кінець
                    return $new_crumbs;
                }
            }
        }
    }

    if (is_product_category()) {
      $current_term = get_queried_object();
  
      $hide = get_field('hide_children_from_breadcrumbs', $current_term);
      if ($hide) {
          $parent_ids = get_ancestors($current_term->term_id, 'product_cat');
          $parent_ids = array_reverse($parent_ids);
  
          $new_crumbs = [];
  
          foreach ($crumbs as $crumb) {
              // залишаємо крихти, які не пов’язані з product_cat (наприклад, Головна, Магазин)
              $include = true;
  
              // якщо крихта — одна з дочірніх категорій, ми її пропустимо
              foreach ($parent_ids as $parent_id) {
                  $parent_term = get_term($parent_id, 'product_cat');
                  if (strpos($crumb[1], $parent_term->slug) !== false) {
                      $include = true;
                      break;
                  }
              }
  
              // якщо крихта веде на поточну (дочірню) категорію — не додаємо її
              if (strpos($crumb[1], $current_term->slug) !== false) {
                  continue;
              }
  
              if ($include) {
                  $new_crumbs[] = $crumb;
              }
          }
  
          // додаємо поточну категорію наприкінці
          $new_crumbs[] = end($crumbs);
  
          return $new_crumbs;
      }
  }
  

    return $crumbs;
}

function braun_price($product_id) { 
  
  $product = wc_get_product( $product_id );
  $varialabel = $product->get_children();
  $currency = get_woocommerce_currency_symbol();
  $on_sale = $product->is_on_sale();
  if(!empty($varialabel)){

      $variation_price_min_sale = $product->get_variation_sale_price( 'min' );
      $variation_price_max_sale = $product->get_variation_sale_price( 'max' );

      $variation_price_min_regular = $product->get_variation_regular_price( 'min', true );
      $variation_price_max_regular = $product->get_variation_regular_price( 'max', true ); 
      
      if($on_sale){
          if(!empty($variation_price_min_sale) && !empty($variation_price_max_sale) && $variation_price_min_sale == $variation_price_max_sale) { ?>

              <p class="product_price">
                  <del>
                      <?php 
                          echo $variation_price_min_regular; 
                          echo ' – ';
                          echo $variation_price_max_regular . ' ' . $currency;
                      ?>
                  </del>
                  <bdi>
                      <?php 
                          echo $variation_price_min_sale . ' ' . $currency; 
                      ?>
                  </bdi>
              </p>
  
          <?php } elseif(!empty($variation_price_min_sale) && empty($variation_price_max_sale)) { ?>
  
              <p class="product_price">
                  <del>
                      <?php 
                          echo $variation_price_min_regular; 
                          echo ' – ';
                          echo $variation_price_max_regular . ' ' . $currency;
                      ?>
                  </del>
                  <bdi>
                      <?php 
                          echo $variation_price_min_sale; 
                          echo ' – ';
                          echo $variation_price_max_regular . ' ' . $currency; 
                      ?>
                  </bdi>
              </p>
  
          <?php }  elseif(!empty($variation_price_min_sale) && !empty($variation_price_max_sale)) { ?>
  
              <p class="product_price">
                  <del>
                      <?php 
                          echo $variation_price_min_regular; 
                          echo ' – ';
                          echo $variation_price_max_regular . ' ' . $currency;
                      ?>
                  </del>
                  <bdi>
                      <?php 
                          echo $variation_price_min_sale; 
                          echo ' – ';
                          echo $variation_price_max_sale . ' ' . $currency; 
                      ?>
                  </bdi>
              </p>
  
          <?php } elseif(empty($variation_price_min_sale) && !empty($variation_price_max_sale)) { ?>
  
              <p class="product_price">
                  <del>
                      <?php 
                          echo $variation_price_min_regular; 
                          echo ' – ';
                          echo $variation_price_max_regular . ' ' . $currency;
                      ?>
                  </del>
                  <bdi>
                      <?php 
                          echo $variation_price_min_regular; 
                          echo ' – ';
                          echo $variation_price_max_sale . ' ' . $currency; 
                      ?>
                  </bdi>
              </p>
  
          <?php }
      } else {
          
          if($variation_price_min_regular == $variation_price_max_regular) { ?>
              <p class="product_price">
                  <bdi>
                      <?php 
                          echo $variation_price_max_regular . ' ' . $currency;
                      ?>
                  </bdi>
              </p>
          <?php } else { ?>
              <p class="product_price">
                  <bdi>
                      <?php 
                          echo $variation_price_min_regular; 
                          echo ' – '; 
                          echo $variation_price_max_regular . ' ' . $currency;
                      ?>
                  </bdi>
              </p>
          <?php }
      } 
      
  } else {

      $price = $product->get_regular_price();
      $sale = $product->get_sale_price();

      if (!empty($sale)) { ?>

          <p class="product_price">
              <del><?php echo wc_price( $price ); ?></del>
              <bdi>
                  <?php echo wc_price( $sale ); ?>
              </bdi>
          </p>

      <?php } else { ?>

          <p class="product_price">
              <bdi><?php echo wc_price( $price ); ?></bdi>
          </p>

      <?php } 
  }	
}
add_filter( 'woocommerce_currency_symbol', 'custom_uah_currency_text', 10, 2 );

function custom_uah_currency_text( $currency_symbol, $currency ) {
    if ( $currency === 'UAH' ) {
        return 'грн';
    }
    return $currency_symbol;
}

function get_product_attributes_split($product_id) {
    $product = wc_get_product($product_id);
    $attributes = $product->get_attributes();

    $features = [];
    $characteristics = [];

    foreach ($attributes as $attribute) {
        $label = wc_attribute_label($attribute->get_name());

        // Якщо це глобальний атрибут (таксономія)
        if ($attribute->is_taxonomy()) {
            $taxonomy = $attribute->get_name(); // напр. pa_color
            $slug = str_replace('pa_', '', $taxonomy);

            $terms = wp_get_post_terms($product_id, $taxonomy);
            if (empty($terms)) continue;

            $values = wp_list_pluck($terms, 'name');

            $target_array = get_option('attribute_is_feature_' . $slug) ? 'characteristics' : 'features';
            ${$target_array}[] = [
                'label' => $label,
                'values' => $values
            ];

        } else {
            // Користувацький (локальний) атрибут
            $values = $attribute->get_options();
            if (empty($values)) continue;

            $features[] = [
                'label' => $label,
                'values' => $values
            ];
        }
    }

    return [
        'features' => $features,
        'characteristics' => $characteristics
    ];
}


if (!function_exists('custom_wc_review_form')) {
  function custom_wc_review_form($product_id) {
    if (!comments_open($product_id) || get_post_type($product_id) !== 'product') {
        return;
    }

    ob_start();

    if (!is_user_logged_in()) {
        $commenter = wp_get_current_commenter();
        $consent   = empty($commenter['comment_author_email']) ? '' : ' checked="checked"';
        ?>
        <div class="comment-form-author floating-field">
            <input id="author" name="author" type="text" value="<?php echo esc_attr($commenter['comment_author']); ?>" size="30" required />
            <label for="author"><?php esc_html_e('Ім’я', 'braun_2025'); ?> <span class="required">*</span></label>
            <div class="error-message" style="display:none;color:red;"><?php esc_html_e('Це поле обов’язкове.', 'braun_2025'); ?></div>
        </div>

        <div class="comment-form-email floating-field">
            <input id="email" name="email" type="email" value="<?php echo esc_attr($commenter['comment_author_email']); ?>" size="30" required />
            <label for="email"><?php esc_html_e('Email', 'braun_2025'); ?> <span class="required">*</span></label>
            <div class="error-message" style="display:none;color:red;"><?php esc_html_e('Введіть коректний email.', 'braun_2025'); ?></div>
        </div>
    <?php
    }
    ?>

    <div class="comment-form-review-top" style="display: flex; align-items: center; gap: 1em; margin-bottom: 0.5em;">
        <div class="custom-stars">
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <input type="radio" id="rating-<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" />
                <label for="rating-<?php echo $i; ?>" title="<?php echo sprintf(esc_attr__('%d з 5', 'braun_2025'), $i); ?>">★</label>
            <?php endfor; ?>
        </div>
        <div class="error-message" style="display:none;color:red;"><?php esc_html_e('Будь ласка, оберіть оцінку.', 'braun_2025'); ?></div>
    </div>

    <div class="comment-form-comment floating-field">
        <textarea id="comment" name="comment" cols="45" rows="5" required></textarea>
        <label for="comment"><?php esc_html_e('Відгук*', 'braun_2025'); ?></label>
        <div class="error-message" style="display:none;color:red;"><?php esc_html_e('Напишіть відгук.', 'braun_2025'); ?></div>
    </div>
    <div class="loader-wrapper">
      <div class="loader"></div>
    </div>
    <?php
    $custom_fields = ob_get_clean();

    comment_form([
        'title_reply'          => esc_html__('Залишити відгук', 'braun_2025'),
        'title_reply_to'       => esc_html__('Відповідь на %s', 'braun_2025'),
        'comment_notes_before' => '',
        'comment_notes_after'  => '',
        'fields'               => [],
        'label_submit'         => esc_html__('Надіслати відгук', 'braun_2025'),
        'class_submit' => 'submit btn btn-black',
        'comment_field'        => $custom_fields,
    ], $product_id);
  } 

}

add_filter('comment_text', function ($comment_text, $comment) {
  if (get_post_type($comment->comment_post_ID) === 'product') {
      $rating = intval(get_comment_meta($comment->comment_ID, 'rating', true));
      if ($rating > 0) {
          $stars = '<p class="custom-rating" aria-label="' . sprintf(esc_attr__('Оцінка: %d з 5', 'braun_2025'), $rating) . '">';
          for ($i = 1; $i <= 5; $i++) {
              $stars .= $i <= $rating ? '★' : '☆';
          }
          $stars .= '</p>';
          $comment_text = $stars . $comment_text;
      }
  }
  return $comment_text;
}, 10, 2);

function get_author_full_name($post_id = null) {
  if (!$post_id) {
      $post_id = get_the_ID(); // якщо не передано — беремо поточний пост
  }

  $author_id   = get_post_field('post_author', $post_id);
  $first_name  = get_the_author_meta('first_name', $author_id);
  $last_name   = get_the_author_meta('last_name', $author_id);
  $display_name = get_the_author_meta('display_name', $author_id);

  $full_name = trim($first_name . ' ' . $last_name);

  return $full_name ?: $display_name;
}

function get_time_blog($time){
  $time_format = strtotime($time);
  $hour = date('H',$time_format);
  $minutes = date('i',$time_format);
  $time = '';
  if((int)$hour == 00){
    $time = _e($minutes.' хв.', 'braun_2025'); 
  } else{
    $time = _e($hour .' год. ', 'braun_2025'); _e($minutes .' хв.', 'braun_2025');
  }
  return $time;
}

add_filter( 'wpseo_primary_term_taxonomies', function( $taxonomies ) {
  $taxonomies[] = 'category_news';
  $taxonomies[] = 'product_cat';
  return $taxonomies;
} );


add_action('init', function() {
  add_rewrite_rule('^instrukcziya(?:/(.+))?/?$', 'index.php?pagename=instrukcziya&instruction_path=$matches[1]', 'top');
  add_rewrite_rule('^instrukcyya(?:/(.+))?/?$', 'index.php?pagename=instrukcyya&instruction_path=$matches[1]', 'top');
});

add_filter('query_vars', function($vars) {
  $vars[] = 'instruction_path';
  return $vars;
});

function get_custom_breadcrumbs_from_slug($page_id) {
  $breadcrumbs = [];

  $front_page_id = get_option('page_on_front');
  $home_title = get_the_title($front_page_id);
  

  $breadcrumbs[] = [
      'label' => $home_title,
      'url'   => home_url('/')
  ];

  $breadcrumbs[] = [
      'label' => get_the_title($page_id),
      'url'   => get_the_permalink($page_id)
  ];

  // Отримуємо шлях з URL
  $path = trim(str_replace(get_the_permalink($page_id), '', home_url(add_query_arg([]))));
  $parts = explode('/', trim($path, '/'));

  if (empty($parts)) {
      return $breadcrumbs;
  }

  // Шукаємо категорії
  $term_slugs = $parts;
  $product_slug = array_pop($term_slugs); // останній елемент може бути або терм або товар

  // Перевіряємо: це товар?
  $product = get_page_by_path($product_slug, OBJECT, 'product');

  if ($product) {
      // Якщо товар — залишаємо $term_slugs як є, будуємо терми
      $term_hierarchy_url = '';
      foreach ($term_slugs as $slug) {
          $term = get_term_by('slug', $slug, 'product_cat');
          if ($term) {
              $term_hierarchy_url .= $slug . '/';
              $breadcrumbs[] = [
                  'label' => $term->name,
                  'url'   => get_the_permalink($page_id) . $term_hierarchy_url
              ];
          }
      }

      $breadcrumbs[] = [
          'label' => get_the_title($product),
          'url'   => ''
      ];

  } else {
      $term_slugs[] = $product_slug;
      $term_hierarchy_url = '';
      foreach ($term_slugs as $slug) {
          $term = get_term_by('slug', $slug, 'product_cat');
          if ($term) {
              $term_hierarchy_url .= $slug . '/';
              $breadcrumbs[] = [
                  'label' => $term->name,
                  'url'   => get_the_permalink($page_id) . $term_hierarchy_url
              ];
          }
      }
  }

  return $breadcrumbs;
}

// Додаткова функція: побудова повного шляху до терміну
function get_term_parents_slugs($term) {
  $slugs = [$term->slug];
  while ($term->parent != 0) {
      $term = get_term($term->parent, 'product_cat');
      $slugs[] = $term->slug;
  }
  return implode('/', array_reverse($slugs));
}

function get_instruction_url_for_product($product_id) {
  $terms = get_the_terms($product_id, 'product_cat');

  if (empty($terms) || is_wp_error($terms)) {
      return false;
  }

  // Знаходимо найглибшу категорію
  $main_term = null;
  $max_depth = 0;

  foreach ($terms as $term) {
      $depth = 0;
      $current = $term;
      while ($current->parent != 0) {
          $current = get_term($current->parent, 'product_cat');
          $depth++;
      }

      if ($depth >= $max_depth) {
          $max_depth = $depth;
          $main_term = $term;
      }
  }

  // Формуємо повну ієрархію категорій
  $slugs = [];
  $current = $main_term;

  while ($current && $current->parent != 0) {
      $slugs[] = $current->slug;
      $current = get_term($current->parent, 'product_cat');
  }

  if ($current) {
      $slugs[] = $current->slug;
  }

  $slugs = array_reverse($slugs);

  $product_slug = get_post_field('post_name', $product_id);

  // Створюємо фінальний URL
  $url = implode('/', $slugs) . '/' . $product_slug . '/';

  return $url;
}

add_filter('wp_get_attachment_image_attributes', function($attr) {
  // видаляємо auto з sizes
  if (!empty($attr['sizes'])) {
    $attr['sizes'] = preg_replace('/^auto,\s*/', '', $attr['sizes']);
  }
  return $attr;
});

remove_action( 'wp_body_open', 'wp_enqueue_block_support_styles' );

add_filter('wpseo_opengraph_image', function($image) {
  if (is_archive()) {
    $posts = get_posts(['posts_per_page' => 1]);
    if (!empty($posts)) {
        $thumb = get_the_post_thumbnail_url($posts[0]->ID, 'large');
        if ($thumb) {
            echo '<meta property="og:image" content="' . esc_url($thumb) . '" />';
        }
    }
  }
});

add_filter('woocommerce_taxonomy_args', function($args, $taxonomy) {
    if (strpos($taxonomy, 'pa_') === 0) {
        $args['public'] = true;
        $args['has_archive'] = false;
        $args['rewrite']['slug'] = false;
        $args['rewrite']['with_front'] = false;
        $args['rewrite']['hierarchical'] = false;
    }
    return $args;
}, 10, 2);

add_action('admin_footer', function () {
    global $pagenow;

    if ($pagenow === 'edit-tags.php' && isset($_GET['taxonomy']) && strpos($_GET['taxonomy'], 'pa_') === 0) {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const archiveRow = document.querySelector('.form-field.term-enable-archives-wrap');
                if (archiveRow) archiveRow.style.display = 'none';

                const archiveEditRow = document.querySelector('.form-field.enable-archives-wrap');
                if (archiveEditRow) archiveEditRow.style.display = 'none';
            });
        </script>
        <?php
    }
});
