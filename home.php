<?php
get_header();
$current_lang = 'default';
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
    $current_lang = apply_filters( 'wpml_current_language', null );
}
global $wp_query;
$total_posts = $wp_query->found_posts;
$page_for_posts_id = get_option('page_for_posts');
$head_post = get_field('head_post',$page_for_posts_id);
$categories = get_terms([
  'taxonomy'   => 'category',  
  'hide_empty' => true      
]);
$blog_ids = !empty(get_field('popular_posts',$page_for_posts_id)) ? get_field('popular_posts',$page_for_posts_id) : [] ;

if (count($blog_ids) < 10) {
	$ex_ids = new WP_Query([
		'post_status' 		=> 'publish',
		'post_type' 		=> 'post',
		'posts_per_page' 	=> 10 - count($blog_ids),
		'post__not_in'		=> $blog_ids,
		'fields'			=> 'ids',
	]);

	$blog_ids = array_merge($blog_ids, $ex_ids->posts);
}

$blog_list = new WP_Query([
	'post_status' 		=> 'publish',
	'post_type' 		=> 'post',
	'post__in' 			=> $blog_ids,
	'orderby'			=> 'post__in',
]);

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
if(!empty($head_post)){
  $post_id = $head_post[0]->ID;
  $image_id = get_post_thumbnail_id($post_id);
  $head_title = $head_post[0]->post_title;
  $prymary_cat = primary_category($post_id,'category');
?>
  <section class="hero hero-blog">
    <div class="wrapper hero__wrapper">
      <div class="hero-left">
        <?php if(!empty($primary_cat)){?>
          <span><?php echo $prymary_cat['name']?></span>
        <?php } ?>
        <h1><?php echo $head_title ?></h1>
        <div class="hero__print">
          <div class="hero__print-icon">
            <svg>
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#print-icon"></use>
            </svg>
          </div>
          <?php _e('Друк','braun_2025')?>
        </div>
      </div>
      <div class="hero-right">
        <?php if(!empty($image_id)){
          echo wp_get_attachment_image($image_id,'product_image',false,
          [
            'alt'   => esc_attr($head_title . ' ' . $current_lang),
            'title' => esc_attr(__('Фото ', 'braun_2025') . $head_title . ' ' . $current_lang)
          ]);
        } else { ?>
          <svg class="header__logo-icon">
            <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
          </svg>
        <?php } ?>
      </div>
    </div>
  </section>
<?php }
if(have_posts()){
  $cat_ids = wp_list_pluck($categories, 'term_id' );
?>
  <section class="catalog section">
    <div class="wrapper catalog__wrapper">
      <h2><?php _e('Останні статті','braun_2025')?></h2>
      <?php if(!empty($categories)){?>
        <ul class="catalog__categories" data-all-ids="<?php echo esc_attr(implode(', ', $cat_ids))?>" data-type="post" data-cat="category">
          <li><button type="button" data-cat-id="all" class="catalog__categories__btn active"><?php _e('Усі','braun_2025')?></button></li>
          <?php foreach ($categories as $term) { 
            if($term->count !== 0){?>
              <li><button type="button" class="catalog__categories__btn" data-cat-id="<?php echo esc_attr($term->term_id)?>"><?php echo $term->name ?><sup><?php echo $term->count?></sup></button></li>
            <?php } 
          } ?>
        </ul>
      <?php } ?>
      <div class="blog__list blog__list--show-more">
        <?php
        while(have_posts()){
          the_post();
          $post_id = get_the_ID();
          $prymary_cat = primary_category($post_id,'category');
          $title = get_the_title();
        ?>
        <article class="blog__item">
          <a href="<?php the_permalink() ?>" class="item-link"></a>
          <div class="blog__item-img">
            <?php if(has_post_thumbnail()){
              $image_id = get_post_thumbnail_id();
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
                <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
              </svg>
            <?php } ?>
          </div>
          <?php if(!empty($prymary_cat)){?>
            <span class="blog__item-cat"><?php echo $prymary_cat['name']?></span>
          <?php } ?>
          <h4><?php echo $title?></h4>
          <time datetime="<?php echo esc_attr(get_the_date('Y-m-d'));?>"><?php echo get_the_date('d.m.Y');?></time>
        </article>
        <?php } wp_reset_postdata() ?>
      </div>
      <div class="loader-wrapper">
        <div class="loader"></div>
      </div>
      <button type="button" class="show-more" data-all-ids="all" data-count="<?php echo esc_attr($total_posts) ?>" data-post-type="post">
        <?php _e('Показати більше','braun_2025')?>
      </button>
      <div class="preloder">
        <div class="loder"></div>
      </div>
    </div>
  </section>
<?php } 
if($blog_list->have_posts()) { ?>
  <section class="blog section">
    <div class="wrapper blog__wrapper">
      <h2><?php _e('Популярні статті','braun_2025') ?></h2>
      <div class="blog__list blog-slider">
        <div class="swiper-wrapper">
          <?php 
          while($blog_list->have_posts()) {
            $blog_list->the_post();
            $post_id = get_the_ID();
            $primary_cat = primary_category($post_id,'category');
            get_template_part('template-parts/content','blog', ['primary_cat'=>$primary_cat,'no_slider'=>false,'current_lang'=>$current_lang]);
            } wp_reset_postdata() ?>
        </div>
        <div class="slider-pagination blog__pagination">
          <div class="blog-button-prev">
            <svg class="slider-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
            </svg>
          </div>
          <div class="swiper-progress">
            <div class="swiper-progress-bar"></div>
          </div>
          <div class="blog-button-next">
            <svg class="slider-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php } 
if(!empty(get_field('cta_name_btn',$page_for_posts_id)) || !empty(get_field('cta_link',$page_for_posts_id))){
  get_template_part('template-parts/content','cta',['cta_title'=>get_field('cta_title',$page_for_posts_id),'cta_template' =>get_field('cta_template',$page_for_posts_id),'cta_name_btn'=> get_field('cta_name_btn',$page_for_posts_id),'cta_link'=> get_field('cta_link',$page_for_posts_id)]);
}
get_footer();