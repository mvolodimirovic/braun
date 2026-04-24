<?php
get_header();
$current_lang = 'default';
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
    $current_lang = apply_filters( 'wpml_current_language', null );
}
global $wp_query;
$max_pages = $wp_query->max_num_pages;

$link = get_post_type_archive_link('certificate');

$hero_image = get_field('image','certificate_opt');
$hero_title = get_field('title','certificate_opt');
$hero_description = get_field('short_descr','certificate_opt');

$hero_args = [
  'image_id'=> $hero_image,
  'title' => $hero_title,
  'description'=> $hero_description,
  'template'=> '',
  'button'=> '',
  'current_lang' => $current_lang
];
$first_title = get_field('first_title','certificate_opt');
$first_description = get_field('first_description','certificate_opt');

$history = get_field('history','certificate_opt');
$awards = get_field('awards','certificate_opt');

$certificate_terms = get_terms([
  'taxonomy' => 'category_certificate',
  'hide_empty' => true, 
]);

if(!empty($hero_image)){
get_template_part('/template-parts/content','hero',$hero_args);
}

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
if(!empty($first_title) || !empty($first_description)){?>
  <section class="section mission">
    <div class="wrapper mission__wrapper">
      <?php if(!empty($first_title)) { ?>
        <h2><?php echo $first_title ?></h2>
      <?php } 
      if(!empty($first_description)) { ?>
        <p><?php echo $first_description ?></p>
      <?php } ?>
    </div>
  </section>
<?php } 
if(have_posts()) { ?>
  <section class="certificate" id="certificate">
    <div class="wrapper certificate__wrapper">
      <h2><?php _e('Список сертифікатів','braun_2025')?></h2>
      <ul class="catalog__categories">
        <li><button type="button" class="active"><?php _e('Усі','braun_2025')?></button></li>
        <?php if(!empty($certificate_terms)) { 
          foreach ($certificate_terms as $term) { ?>
            <li><a href="<?php echo esc_url(get_term_link($term->term_id).'#certificate')?>"><?php echo $term->name?></a></li>
          <?php } ?>
        <?php } ?>
      </ul>
      <div class="certificate__list">
          <?php
          while(have_posts()) {
            the_post();
            ?>
          <div class="certificate__item">
            <div class="certificate__item-img">
              <?php if(has_post_thumbnail()){
                $image_id = get_post_thumbnail_id();
                echo wp_get_attachment_image(
                  $image_id,
                  'cat_image_large',
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
            <h4><?php echo get_the_title() ?></h4>
            <?php if(!empty(get_the_excerpt())) { ?>
              <p><?php echo get_the_excerpt() ?></p>
            <?php } ?>
          </div>
        <?php } wp_reset_postdata(); ?>
      </div>
      <div class="pagination">
        <?php 
          echo paginate_links( array(
            'base' 			=> $link.'%_%#certificate',
            'prev_next'     => true,
            'prev_text'    	=> 	'<svg><use xlink:href="'. get_template_directory_uri(). '/img/path-icon.svg#slider-arrow"></use></svg>',
            'next_text'    	=> 	'<svg><use xlink:href="'. get_template_directory_uri(). '/img/path-icon.svg#slider-arrow"></use></svg>',
            'mid_size'		=> 1,
            'total'   		=> $max_pages,
          ) );   
          ?>  
        </div>
    </div>
  </section>
<?php 
}
if(!empty($history) || !empty($awards)) {  ?>
  <section class="inter section">
    <div class="wrapper inter__wrapper">
      <?php if(!empty($history)) { ?>
        <article class="inter__item">
          <a href="<?php echo esc_url($history)?>" class="item-link"></a>
          <div class="inter__item-img">
            <svg class="slider-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#logo"></use>
            </svg>
          </div>
          <h4><?php _e('Історія бренду','braun_2025') ?></h4>
        </article>
      <?php }  
      if(!empty($awards)) { ?>
        <article class="inter__item">
          <a href="<?php echo esc_url($awards)?>" class="item-link"></a>
          <div class="inter__item-img">
            <svg class="slider-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#awards-icon"></use>
            </svg>
          </div>
          <h4><?php _e('Нагороди','braun_2025') ?></h4>
        </article>
      <?php } ?>
    </div>
  </section>
<?php
}

get_footer();