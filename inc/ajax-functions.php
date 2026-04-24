<?php
function get_category_product_callback() {
  $category_id = isset($_POST['category_id']) ? intval(sanitize_text_field($_POST['category_id'])) : 0;

  $args = [
      'post_type'      => 'product',
      'posts_per_page' => 1,
      'orderby'        => 'rand',
      'tax_query'      => [
          [
              'taxonomy' => 'product_cat',
              'field'    => 'term_id',
              'terms'    => $category_id,
          ],
      ],
  ];

  $query = new WP_Query($args);
  if ($query->have_posts()) {
      $query->the_post();
      global $product;

      $product_id = get_the_ID();
      $title = get_the_title();
      $link = get_permalink();
      $image_url = get_the_post_thumbnail($product_id, 'medium');
      $description = get_the_excerpt();
      $description = wp_trim_words($description, 10, '...');
      $image = $image_url ? $image_url : wc_placeholder_img();

      $product_html = '<div class="product-card">';
      $product_html .= '<div class="product-card__image">' . $image . '</div>';
      if (!empty($description)) {
        $product_html .= '<div class="product-card__desc">' . $description . '</div>';
      }
      $product_html .=  '<h4 class="product-card__title">' . esc_html($title) . '</h4>';
      $product_html .= '<a href="' . esc_url($link) . '">'.__('Дізнатися більше','braun_2025').'</a></div>';

      $response = ['product' => $product_html];
  } else {
      $response = ['product' => '<p>'.__('Товар не знайдено','braun_2025').'</p>'];
  }

  wp_reset_postdata();
  echo json_encode($response); 
  wp_die();
}

add_action('wp_ajax_get_category_product', 'get_category_product_callback');
add_action('wp_ajax_nopriv_get_category_product', 'get_category_product_callback');


// ajax filters
function filters () {
	ob_start();

	$response = [
    'posts' => '',
    'pagination'=>'',
  ];
	$post_type = sanitize_text_field($_POST['post-type']) ?? '' ;
	$params = sanitize_text_field($_POST['url-param']) ?? '';
	$default_cat = sanitize_text_field($_POST['default_cat']) ?? '';
  $tax_query = [];
  $meta_query = [];
	$args = array(
		'post_type' 		=> 'product',
		'posts_per_page' 	=> 6,
		'post_status'		=> 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
	);
  if($default_cat !== 'all'){
    $tax_query[] = [
      'relation' => 'OR',
      'taxonomy' => 'product_cat',
      'field'    => 'slug',
      'terms'    => $default_cat
    ];
  }
  parse_str(str_replace('?', '', $params), $params_arr);

  if(!empty($params_arr)) {
    foreach ($params_arr as $key => $value) {
      $items = explode(',', $value);
      
      if ($key == 'product_filter') {
        $tax_query[] = [
            'relation' => 'OR',
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $items
        ];
      } else {
          $sort_by = $items[0] == 'low-to-high' ? 'ASC' : 'DESC';
          if($key == 'price'){
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order'] = $sort_by;
          } else if($key == 'popular'){
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_product_views';
            $args['order'] = $sort_by;
          } else if($key == 'novelty'){
            $args['orderby'] = 'date';
            $args['order'] = $sort_by;
          }
      }
    }
  }
  $args['tax_query'] = $tax_query;
	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) : $query->the_post();
			get_template_part('template-parts/content', 'product'); 
      
		endwhile;
  }
	$response['posts'] = ob_get_contents();
	ob_end_clean();

	ob_start();
	$max_num_pages = ceil( $query->found_posts / 6 );

	echo paginate_links( array(
		'base' 			=> $post_type.'%_%'.$params.'#product',
		'prev_next'     => true,
		'prev_text'    	=> 	'<svg><use xlink:href="'. get_template_directory_uri(). '/img/path-icon.svg#slider-arrow"></use></svg>',
		'next_text'    	=> 	'<svg><use xlink:href="'. get_template_directory_uri(). '/img/path-icon.svg#slider-arrow"></use></svg>',
		'mid_size'		=> 1,
		'total'   		=> $max_num_pages,
	) );

	$response['pagination'] = ob_get_contents();
	ob_end_clean();

	wp_reset_postdata();
	echo json_encode($response);
	die();
}

add_action('wp_ajax_filters', 'filters');
add_action('wp_ajax_nopriv_filters', 'filters');


function get_actual_product() {
  ob_start();
  $category_id = $_POST['cat_id'] !== 'all' ? sanitize_text_field($_POST['cat_id']) : explode(', ', sanitize_text_field($_POST['all_ids']));
  $taxonomy = sanitize_text_field($_POST['cat']);
  $post_type = sanitize_text_field($_POST['type']);
  $ppp = sanitize_text_field($_POST['type']) == 'product' ? -1 : 6;
  $product_list = [
    'post_status'  => 'publish',
    'post_type'    => $post_type,
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
    'tax_query' => [
      [
          'taxonomy' => $taxonomy,
          'field'    => 'term_id', 
          'terms'    => $category_id,
          'include_children' => false
      ],
    ]
  ];
  $query = new WP_Query( $product_list );
  if($query->have_posts()) {
    while($query->have_posts()){
      $query->the_post();
      $post_id = get_the_ID();
      if($post_type == 'product'){
        $primary_cat = primary_category($post_id,'product_cat');
        if($_POST['cat_id'] !== 'all'){
          $cat_link = '<a href="'. esc_url(get_term_link((int)$category_id)).'" class="catalog__item__category">'. esc_html(get_term_name($category_id, 'product_cat')). '</a>';
        } else{
          $cat_link = '<a href="'. esc_url($primary_cat['link']).'" class="catalog__item__category">'. esc_html($primary_cat['name']). '</a>';
        }
        ?>
        <article class="catalog__item swiper-slide">
          <a href="<?php echo esc_url(get_permalink($post_id))?>" class="item-link"></a>
          <?php echo $cat_link;?>
          <div class="catalog__item-img">
            <?php if(has_post_thumbnail()){
              the_post_thumbnail('actual_image');
            } else { ?>
              <svg class="header__logo-icon">
                <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
              </svg>
            <?php } ?>
          </div>
          <h5><?php echo get_the_title()?></h5>
        </article>
    <?php 
      } else { 
        $primary_cat = primary_category($post_id,$taxonomy);

        if($_POST['cat_id'] !== 'all'){
          $cat_link = '<span class="blog__item-cat">'. esc_html(get_term_name($category_id, $taxonomy)). '</span>';
        } else{
          $cat_link = '<span class="blog__item-cat">'. esc_html($primary_cat['name']). '</span>';
        }
        ?>
        <article class="blog__item">
          <a href="<?php the_permalink() ?>" class="item-link"></a>
          <div class="blog__item-img">
            <?php if(has_post_thumbnail()){
              the_post_thumbnail('cat_image');
            } else { ?>
              <svg class="header__logo-icon">
                <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
              </svg>
            <?php } ?>
          </div>
          <?php echo $cat_link ?>
          <h4><?php echo get_the_title()?></h4>
          <time datetime="<?php echo esc_attr(get_the_date('Y-m-d'));?>"><?php echo get_the_date('d.m.Y');?></time>
        </article>
      <?php }  
    } wp_reset_postdata();
    }
  $response['post'] = ob_get_contents();
  ob_end_clean();
  if($post_type !== 'product'){
    ob_start(); ?>
      <button type="button" class="show-more" 
        data-all-ids="<?php echo esc_attr($_POST['cat_id'] !== 'all' ? $_POST['cat_id'] : 'all')?>" 
        data-count="<?php echo esc_attr($query->found_posts) ?>" 
        data-post-type="<?php echo esc_attr($post_type)?>">
        <?php _e('Показати більше','braun_2025')?>
      </button>
      <div class="preloder">
        <div class="loder"></div>
      </div>
    <?php 
    $response['show_more'] = ob_get_contents();
    ob_end_clean();
  }
  echo json_encode($response); 
  wp_die();
}

add_action('wp_ajax_actuality', 'get_actual_product');
add_action('wp_ajax_nopriv_actuality', 'get_actual_product');


add_action( 'wp_ajax_load_more', 'loadmore' );
add_action( 'wp_ajax_nopriv_load_more', 'loadmore' );
 
function loadmore() {
    ob_start();

	$offset = sanitize_text_field($_POST['offset']);
	$taxonomy = sanitize_text_field($_POST['taxonomy']);
	$offset = $offset * 6;
  $post_type = sanitize_text_field($_POST['post_type']);
  $taxonomy_name =  $post_type == 'post' ? 'category':'category-news';
  $args = [
    'post_type' => $post_type,
    'posts_per_page' => 6,
    'orderby' => 'date',
    'order'=> 'DESC',
    'suppress_filters' => false,
    'offset' => $offset,
  ];
  if($taxonomy !== 'all'){
    $args['tax_query'] = [[
      'taxonomy' => $taxonomy_name,
      'terms' => $taxonomy,
      'field' => 'term_id',
    ]];
  }
  $post_list = new WP_Query($args);

  if($post_list->have_posts()){
    while ($post_list->have_posts()) {
      $post_list->the_post();
      $post_id = get_the_ID();
      $primary_cat = primary_category($post_id,$taxonomy_name);

        if($taxonomy !== 'all'){
          $cat_link = '<span class="blog__item-cat">'. esc_html(get_term_name($taxonomy, $taxonomy_name)). '</span>';
        } else{
          $cat_link = '<span class="blog__item-cat">'. esc_html($primary_cat['name']). '</span>';
        }
        ?>
        <article class="blog__item">
          <a href="<?php the_permalink() ?>" class="item-link"></a>
          <div class="blog__item-img">
            <?php if(has_post_thumbnail()){
              the_post_thumbnail('cat_image');
            } else { ?>
              <svg class="header__logo-icon">
                <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
              </svg>
            <?php } ?>
          </div>
          <?php echo $cat_link ?>
          <h4><?php echo get_the_title()?></h4>
          <time datetime="<?php echo esc_attr(get_the_date('Y-m-d'));?>"><?php echo get_the_date('d.m.Y');?></time>
        </article> 
<?php } wp_reset_postdata();
  }

	$response = ob_get_contents();
    ob_end_clean();
    echo $response;
    die(1);
}

function term_filters() {
  ob_start();

  $response = [
      'posts' => '',
      'breadcrumbs' => '',
      'url' => '',
      'is_taxonomy' => false,
  ];

  $term_slug = sanitize_text_field($_POST['term_slug']);
  $page_id = sanitize_text_field($_POST['page_id']);
  $taxonomy = 'product_cat';

  $term = get_term_by('slug', $term_slug, $taxonomy);

  if ($term && !is_wp_error($term)) {
      $term_id = $term->term_id;
      $path_parts = [];
      $term_chain = [];
      $current_term = $term;

      $term_chain[] = $current_term;
      while ($current_term && $current_term->parent != 0) {
          $current_term = get_term($current_term->parent, $taxonomy);
          if ($current_term) {
              $term_chain[] = $current_term;
          }
      }

      if (!empty($term_chain)) {
          $term_chain = array_reverse($term_chain);
          foreach ($term_chain as $term_part) {
              $path_parts[] = $term_part->slug;
          }
      }

      $base_url = get_the_permalink($page_id);
      $response['url'] = esc_url($base_url . implode('/', $path_parts) . '/');

      $child_terms = get_terms([
          'taxonomy' => $taxonomy,
          'hide_empty' => true,
          'parent' => $term_id,
      ]);

      if (!empty($child_terms)) {
          $response['is_taxonomy'] = true;

          ob_start();
          ?>
          <div class="wrapper series-slider slider">
              <div class="swiper-wrapper series__wrapper">
                  <?php foreach ($child_terms as $child_term) {
                      $thumbnail_id = get_term_meta($child_term->term_id, 'thumbnail_id', true);
                      ?>
                      <div class="series__item swiper-slide filter-button-cat" data-cat-id="<?php echo esc_attr($child_term->slug) ?>">
                          <div class="series__item-img">
                              <?php
                              if (!empty($thumbnail_id)) {
                                  echo wp_get_attachment_image($thumbnail_id, 'woocommerce_thumbnail');
                              } else {
                                  echo wc_placeholder_img('woocommerce_thumbnail');
                              }
                              ?>
                          </div>
                          <span><?php echo esc_html($child_term->name) ?></span>
                      </div>
                  <?php } ?>
              </div>
          </div>
          <?php
          $response['posts'] = ob_get_clean();
      } else {
          $args = [
              'post_type' => 'product',
              'posts_per_page' => 10000,
              'post_status' => 'publish',
              'orderby' => 'date',
              'order' => 'DESC',
              'tax_query' => [[
                  'taxonomy' => $taxonomy,
                  'field' => 'slug',
                  'terms' => $term_slug,
              ]]
          ];
          $query = new WP_Query($args);

          if ($query->have_posts()) {
              ob_start();
              ?>
              <div class="wrapper file__wrapper">
                  <?php while ($query->have_posts()) : $query->the_post();
                      $item_id = get_the_ID();
                      $instruction = get_field('instruction', $item_id);
                      ?>
                      <div class="file__item">
                          <a href="<?php echo esc_url(get_permalink($item_id)) ?>" class="item-link"></a>
                          <div class="file__item-img">
                              <?php
                              if (has_post_thumbnail()) {
                                  the_post_thumbnail('actual_image');
                              } else { ?>
                                  <svg class="header__logo-icon">
                                      <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#logo"></use>
                                  </svg>
                              <?php } ?>
                          </div>
                          <div class="file__item-info">
                              <h4><?php echo get_the_title() ?></h4>
                              <?php if (!empty($instruction)) { ?>
                                  <span><?php echo esc_html($instruction['title']) ?></span>
                              <?php } ?>
                          </div>
                          <?php if (!empty($instruction)) {
                              $url = get_attached_file($instruction['ID']);
                              $file_size_bytes = filesize($url);
                              $file_size_mb = number_format($file_size_bytes / 1048576, 3);
                              ?>
                              <a href="<?php echo esc_url($instruction['url']) ?>" class="btn btn-black" download>
                                  <?php echo esc_html($instruction['subtype']) . ' (' . $file_size_mb . ' MB)'; ?>
                              </a>
                          <?php } ?>
                      </div>
                  <?php endwhile; ?>
              </div>
              <?php
              $response['posts'] = ob_get_clean();
          }
          wp_reset_postdata();
      }
        ob_start();
        ?>
        <nav class="wrapper woocommerce-breadcrumb breadcrumbs__wrapper" aria-label="Breadcrumb">
          <a href="<?php echo esc_url(site_url()); ?>"><?php _e('Головна','braun_2025')?></a>
          <div class="breadcrumbs__icon">
            <svg>
              <use xlink:href="<?php echo get_template_directory_uri(); ?>/img/path-icon.svg#slider-arrow"></use>
            </svg>
          </div>
          <a href="<?php echo esc_url(get_the_permalink($page_id)); ?>"><?php echo get_the_title($page_id)?></a>
          <div class="breadcrumbs__icon">
            <svg>
              <use xlink:href="<?php echo get_template_directory_uri(); ?>/img/path-icon.svg#slider-arrow"></use>
            </svg>
          </div>
        <?php
        if (!empty($path_parts)) {
            $cumulative_path = [];
            $last_index = count($path_parts) - 1;
            foreach ($path_parts as $index => $slug_part) {
                $cumulative_path[] = $slug_part;
                $url = $base_url . implode('/', $cumulative_path) . '/';

                // Якщо це останній елемент і пост, тоді не лінкаємо
                if ($index === $last_index) {
                    // Це пост, не посилання
                    echo '<span>' . esc_html(get_term_by('slug',$slug_part,'product_cat')->name) . '</span>';
                } else {
                    echo '<a href="' . esc_url($url) . '">' . esc_html(get_term_by('slug',$slug_part,'product_cat')->name) . '</a>';
                    if ($index !== $last_index || ($index === $last_index && $response['is_taxonomy'])) {
                        // SVG іконка для всіх крім останнього
                        ?>
                        <div class="breadcrumbs__icon<?php echo ($index === $last_index) ? ' last' : ''; ?>">
                          <svg>
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/img/path-icon.svg#slider-arrow"></use>
                          </svg>
                        </div>
                    <?php
                    }
                }
            }
        }

        echo '</nav>';
        $response['breadcrumbs'] = ob_get_clean();
  } else {
      // Якщо термін не знайдений
      $response['posts'] = '<p>Категорія не знайдена.</p>';
      $response['breadcrumbs'] = '';
      $response['url'] = '';
  }

  wp_reset_postdata();

  echo json_encode($response);
  die();
}

add_action('wp_ajax_term_filters', 'term_filters');
add_action('wp_ajax_nopriv_term_filters', 'term_filters');

function post_filters() {
  ob_start();

  $response = [
      'posts' => '',
  ];

  $input_text = sanitize_text_field($_POST['input_text']) ?? '';
  $page_id = !empty($_POST['page_id']) ? sanitize_text_field($_POST['page_id']) : '';

  $args = [
    'post_type' => 'product',
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

    $product_item = wc_get_product($item_id);
    $item_name = $product_item->get_name();
    $image_id = $product_item->get_image_id();
    $excerpt = $product_item->get_short_description();
      ?>
      <li class="filters__list__item single services-item">
      <a href="<?php echo esc_url (get_the_permalink($page_id) . get_instruction_url_for_product($item_id))?>" class="item-link"></a>
        <?php if(!empty($image_id)){?>
          <div class="services-item-img">
            <?php echo wp_get_attachment_image($image_id,'thumbnail');?>
          </div>
        <?php } ?>
        <div class="services-item-info">
          <span>
            <?php echo $item_name?>
          </span>
          <?php if(!empty($excerpt)){?>
            <span>
              <?php echo $excerpt?>
            </span>
          <?php } ?>
        </div>
      </li>
    
  <?php 
  endwhile;
    $response['posts'] = ob_get_clean();
  } else { ?>
      <li class="filters__list__item single services-item">
        <div class="services-item-info">
          <span>
            <?php _e( 'Інструкції не знайдено','braun_2025')?>
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

add_action('wp_ajax_post_filters', 'post_filters');
add_action('wp_ajax_nopriv_post_filters', 'post_filters');

add_action( 'wp_ajax_search_more', 'searchmore' );
add_action( 'wp_ajax_nopriv_search_more', 'searchmore' );
 
function searchmore() {
    ob_start();

	$offset = sanitize_text_field($_POST['offset']);
	$post_ids = explode(',',sanitize_text_field($_POST['post_ids']));
	$offset = $offset * 6;
  $post_type = sanitize_text_field($_POST['post_type']);
  $args = [
    'post_type' => $post_type,
    'posts_per_page' => 6,
    'orderby' => 'date',
    'order'=> 'DESC',
    'suppress_filters' => false,
    'offset' => $offset,
    'post__in' => $post_ids,
  ];
  $post_list = new WP_Query($args);
  $get_part = $post_type == 'post' ? 'blog':'product';
  if($post_list->have_posts()){
    while ($post_list->have_posts()) {
      $post_list->the_post();
      get_template_part('template-parts/content',$get_part,['no_slider'=>true]); 
    } wp_reset_postdata();
  }

	$response = ob_get_contents();
    ob_end_clean();
    echo $response;
    die(1);
}