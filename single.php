<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 */

get_header();
$current_lang = 'default';
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
    $current_lang = apply_filters( 'wpml_current_language', null );
}
$level = get_field('strong_hard');
$time = get_field('time_create');
$realted_post = get_field('relation_posts');
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
$title = get_the_title();
?>
<section class="hero hero-single">
  <div class="wrapper hero__wrapper">
    <h1><?php echo $title ?></h1>
    <div class="hero__print">
      <div class="hero__print-icon">
        <svg>
          <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#print-icon"></use>
        </svg>
      </div>
      <?php _e('Друк','braun_2025')?>
    </div>
  </div>
  <div class="hero__info">
    <?php if(has_post_thumbnail()) { ?>
      <div class="hero__info-img">
        <?php $image_id = get_post_thumbnail_id();
          echo wp_get_attachment_image(
            $image_id,
            'full',
            false,
            [
              'alt'   => esc_attr($title . ' ' . $current_lang),
              'title' => esc_attr(__('Фото ', 'braun_2025') . $title . ' ' . $current_lang)
            ]
          ); ?>
      </div>
    <?php } ?>
    <div class="wrapper">
      <div class="hero__author">
        <svg>
          <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#author-icon"></use>
        </svg>
        <h5><?php _e('Автор:','braun_2025') ?></h5>
        <span><?php echo get_author_full_name() ?></span>
      </div>
      <?php if(!empty($level)) { ?>
        <div class="hero__complexity">
          <svg>
            <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#complexity-icon"></use>
          </svg>
          <h5><?php _e('Складність:','braun_2025') ?></h5>
          <span><?php echo $level?></span>
        </div>
      <?php } 
      if(!empty($time)){?>
        <div class="hero__time">
          <svg>
            <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#time-icon"></use>
          </svg>
          <h5><?php _e('Час:','braun_2025') ?></h5>
          <time datetime="<?php echo esc_attr($time);?>">
            <?php echo get_time_blog($time); ?>  
          </time>
        </div>
      <?php } ?>
      <time class="hero__created" datetime="<?php echo esc_attr(get_the_date('Y-m-d'));?>"><?php echo get_the_date('d.m.Y');?></time>
    </div>
  </div>
</section>
<?php if(!empty(get_the_content())){?>
<section class="article section">
  <div class="wrapper">
    <article class="article-content">
      <?php the_content() ?>
    </article>
  </div>
</section>
<?php } ?>
<?php if(!empty($realted_post)) { ?>
  <section class="blog section">
    <div class="wrapper blog__wrapper">
      <h2><?php _e('Популярні статті','braun_2025') ?></h2>
      <a href="<?php echo esc_url(get_post_type_archive_link('post'))?>" class="show-all"><?php _e('Перейти до усіх статтей','brsun_2025')?></a>

      <div class="blog__list blog-slider">
        <div class="swiper-wrapper">
          <?php 
            if ( $realted_post ) {
              foreach ( $realted_post as $post ) {
                setup_postdata( $post );

                $post_id = get_the_ID();
                $primary_cat = primary_category($post_id, 'category');

                get_template_part('template-parts/content', 'blog', ['primary_cat' => $primary_cat,'no_slider' => false,'current_lang'=> $current_lang]);
              }

              wp_reset_postdata();
            }
            ?>
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
get_footer();