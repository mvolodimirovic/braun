<?php
/**
 * Template Name: Інструкція
 */

get_header();
$current_lang = 'default';
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
    $current_lang = apply_filters( 'wpml_current_language', null );
}
$hero_image = get_post_thumbnail_id( get_queried_object_id() );
$hero_title = get_field('title');
$page_id = get_the_ID();

$hero_args = [
  'image_id'=> $hero_image,
  'title' => $hero_title,
  'description'=> '',
  'template'=> '',
  'button'=> '',
  'current_lang'=> $current_lang,
];

$products_list = wc_get_products([
  'status' => 'publish',
  'limit' => 30,
]);

$get_url_path = get_query_var('instruction_path');

if(!empty($get_url_path)){
  $last_slug = '';
  $parts = explode('/', trim($get_url_path, '/'));
  $last_slug = end($parts);
  $term_last_id = get_term_by('slug', $last_slug, 'product_cat');
  $product_last = get_page_by_path($last_slug, 'OBJECT', 'product');
  if ($product_last) {
    $last_product_id = $product_last->ID;
  } else {
    $product_terms = get_terms(['taxonomy'=>'product_cat','hidden-empty'=> true, 'parent'=>$term_last_id->term_id,'orderby' => 'term_order' ]);
  }
} else {
  $product_terms = get_terms(['taxonomy'=>'product_cat','hidden-empty'=> true, 'parent'=>0,'orderby' => 'term_order' ]);
}

if(!empty($hero_image)) {
  get_template_part('/template-parts/content','hero',$hero_args);
}
$breadcrumbs_custom = get_custom_breadcrumbs_from_slug($page_id);
if(!empty($get_url_path)) { ?>
  <section class="breadcrumbs">
    <nav class="wrapper woocommerce-breadcrumb breadcrumbs__wrapper" aria-label="Breadcrumb">
    <?php if(!empty($breadcrumbs_custom)){
      $last_index = array_key_last($breadcrumbs_custom);
      foreach ($breadcrumbs_custom as $index => $crumb) {
          if ($index === $last_index) {
              echo '<span>' . esc_html($crumb['label']) . '</span>';
          } else {
            echo '<a href="' . esc_url($crumb['url']) . '">' . esc_html($crumb['label']) . '</a>';
            if ($index !== $last_index) {
                ?>
                <div class="breadcrumbs__icon<?php echo ($index === ($last_index - 1)) ? ' last' : ''; ?>">
                  <svg>
                    <use xlink:href="<?php echo get_template_directory_uri(); ?>/img/path-icon.svg#slider-arrow"></use>
                  </svg>
                </div>
            <?php
            }
          }
        } 
      } ?>
    </nav>
  </section>
<?php
} else {
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
}
if(!empty($products_list)) { ?>
  <section id="product-instruction" class="filters filters-services" data-url="<?php echo esc_url(get_the_permalink())?>">
    <div class="wrapper filters__wrapper">
      <div class="filters-group search-shop instruction-search">
        <label for="search-services" class="filters-label">
          <input type="text" name="search-services" id="search-services" placeholder="Пошук">
          <svg class="filters-icon">
            <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#icon-search"></use>
          </svg>
        </label>
        <div class="filters__list" data-name="seacrch-services">
          <div class="border-wrapp">
            <ul class="filters__list-wrap services-wrapper" data-page-id="<?php echo esc_attr($page_id)?>">
              <?php foreach($products_list as $product_item) { 
                  $item_id = $product_item->get_id();
                  $item_name = $product_item->get_name();
                  $image_id = $product_item->get_image_id();
                  $instruction = get_field('instruction', $item_id);
                ?>
                <li class="filters__list__item single services-item">
                  <a href="<?php echo esc_url (get_the_permalink($page_id) . get_instruction_url_for_product($item_id))?>" class="item-link"></a>
                  <?php if(!empty($image_id)){?>
                    <div class="services-item-img">
                      <?php echo wp_get_attachment_image($image_id,'thumbnail',
                      false,
                      [
                        'alt'   => esc_attr($item_name . ' ' . $current_lang),
                        'title' => esc_attr(__('Фото ', 'braun_2025') . $item_name . ' ' . $current_lang)
                      ]);?>
                    </div>
                  <?php } ?>
                  <div class="services-item-info">
                    <span>
                      <?php echo $item_name?>
                    </span>
                    <?php if(!empty($instruction)){?>
                      <span>
                        <?php echo $instruction['title']?>
                      </span>
                    <?php } ?>
                  </div>
                </li>
              <?php } ?> 
            </ul>
            <div class="loader-wrapper">
              <div class="loader"></div>
            </div>
          </div>
        </div>
      </div> 
    </div>
  </section>
<?php } 
if(!empty($product_terms)) { ?>
  <section id="term-instruction" class="instructions section" data-page-id="<?php echo esc_attr(get_the_ID()); ?>">
    <?php if(!empty($get_url_path)) { ?>
      <div class="wrapper series-slider slider">
        <div class="swiper-wrapper series__wrapper">
            <?php foreach ($product_terms as $child_term) {
                $child_term_name = $child_term->name;
                $thumbnail_id = get_term_meta($child_term->term_id, 'thumbnail_id', true); ?>
                <div class="series__item swiper-slide filter-button-cat" data-cat-id="<?php echo esc_attr($child_term->slug) ?>">
                  <div class="series__item-img">
                    <?php
                    if (!empty($thumbnail_id)) {
                        echo wp_get_attachment_image($thumbnail_id, 'woocommerce_thumbnail',
                        false,
                        [
                          'alt'   => esc_attr($child_term_name . ' ' . $current_lang),
                          'title' => esc_attr(__('Фото ', 'braun_2025') . $child_term_name . ' ' . $current_lang)
                        ]);
                    } else {
                        echo wc_placeholder_img('woocommerce_thumbnail');
                    } ?>
                  </div>
                  <span><?php echo esc_html($child_term->name) ?></span>
                </div>
            <?php } ?>
        </div>
      </div>
    <?php } else { ?>
      <div class="wrapper instructions__wrapper">
        <h2><?php _e('Перегляд за категоріями','braun_2025') ?></h2>
        <div class="instructions__list">
          <?php foreach ($product_terms as $term) {
            $term_name = $term->name;
            $term_id = $term->term_id;
            $slug = $term->slug;
            $thumbnail_id = get_term_meta($term_id, 'thumbnail_id', true);
            $child_terms = get_terms(['taxonomy'=>'product_cat','hidden-empty'=> true, 'parent'=>$term_id]);
            ?>
            <div class="instructions__item">
              <button type="button" data-cat-id="<?php echo esc_attr($slug)?>" class="item-link filter-button-cat"></button>
              <div class="instructions__item-img">
                <?php 
                  if (!empty($thumbnail_id)) {
                    echo wp_get_attachment_image( $thumbnail_id,'cat_image',
                    false,
                    [
                      'alt'   => esc_attr($term_name . ' ' . $current_lang),
                      'title' => esc_attr(__('Фото ', 'braun_2025') . $term_name . ' ' . $current_lang)
                    ]);
                  } else {
                    echo wc_placeholder_img('woocommerce_thumbnail');
                  } ?>
              </div>
              <h4><?php echo $term->name?></h4>
              <?php if(!empty($child_terms)){?>
                <ul class="catalog__categories">
                  <?php foreach ($child_terms as $child_term) { ?>
                    <li><button type="button" class="filter-button-cat" data-cat-id="<?php echo esc_attr($child_term->slug)?>"><?php echo $child_term->name?></button></li>
                  <?php } ?>
                </ul>
              <?php } ?>
            </div>
          <?php } ?>
        </div>
      </div>
    <?php } ?>
  </section>
<?php 
} else{
  $args = [
    'post_type' => 'product',
    'posts_per_page' => 10000,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
  ];
  if(isset($last_product_id)){
    $args['post__in'] = [$last_product_id];
  } else {
    $args['tax_query'] = [[
      'taxonomy' => 'product_cat',
      'field' => 'slug',
      'terms' => $last_slug,
    ]];
  }
  $file_download = new WP_Query($args); 
  if ($file_download->have_posts()) {?>
    <section id="term-instruction" class="instructions section">
      <div class="wrapper file__wrapper">
        <?php while ($file_download->have_posts()) : $file_download->the_post();
          $item_id = get_the_ID();
          $instruction = get_field('instruction', $item_id);
          $title = get_the_title();
          ?>
          <div class="file__item">
            <a href="<?php echo esc_url(get_permalink($item_id)) ?>" class="item-link"></a>
            <div class="file__item-img">
                <?php
                if (has_post_thumbnail()) {
                  $image_id = get_post_thumbnail_id($item_id);
                  echo wp_get_attachment_image(
                    $image_id,
                    'actual_image',
                    false,
                    [
                      'alt'   => esc_attr($title . ' ' . $current_lang),
                      'title' => esc_attr(__('Фото ', 'braun_2025') . $title . ' ' . $current_lang)
                    ]
                  );
                } else { ?>
                    <svg class="header__logo-icon">
                        <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#logo"></use>
                    </svg>
                <?php } ?>
            </div>
            <div class="file__item-info">
                <h4><?php echo $title ?></h4>
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
        <?php endwhile; wp_reset_postdata() ?>
      </div>
  </section>
<?php }
}

if(have_rows('faq_list')) {
  get_template_part('template-parts/content','faq',['title'=>get_field('faq_title'),'faq_list' =>'faq_list']);
}
if(!empty(get_field('cta_name_btn') || get_field('cta_link'))){
  get_template_part('template-parts/content','cta',['cta_title'=>get_field('cta_title'),'cta_template' =>get_field('cta_template'),'cta_name_btn'=> get_field('cta_name_btn'),'cta_link'=> get_field('cta_link')]);
}
echo '<div class="loader-wrapper">
  <div class="loader"></div>
</div>';
get_footer();
