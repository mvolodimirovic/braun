<?php
/**
 * Template Name: Про Braun
 */

get_header();
$current_lang = 'default';
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
    $current_lang = apply_filters( 'wpml_current_language', null );
}
$hero_image = get_post_thumbnail_id( get_queried_object_id() );
$hero_title = get_field('title');
$hero_description = get_field('short_descr');

$hero_args = [
  'image_id'=> $hero_image,
  'title' => $hero_title,
  'description'=> $hero_description,
  'template'=> '',
  'button'=> '',
  'current_lang' => $current_lang
];
$mission_title = get_field('mission_title');
$mission_description = get_field('mission_description');
$mission_args = [
  'title' => $mission_title,
  'description'=> $mission_description,
  'list'=> 'mission_list',
  'current_lang' => $current_lang
];
$file_first = get_field('first_image_or_video');

$products_list = get_field('product_gallery');
$design_title = get_field('design_title');
$design_description = get_field('design_description');

$second_image_or_video = get_field('second_image_or_video');
$technology_list = get_field('technology_list');

$last_title = get_field('last_title');
$last_description = get_field('last_description');

$page_title = get_the_title();

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
} 
get_template_part('/template-parts/content','mission',$mission_args);
if(!empty($file_first)){ 
  get_template_part('/template-parts/content','file',['file'=>$file_first,'current_lang'=>$current_lang ]);
} 

if(!empty($products_list)){?>
  <section class="gallery section">
    <div class="wrapper gallery__wrapper">
      <h2><?php _e('Галерея продуктів','braun_2025') ?></h2>
      
      <div class="gallery__list gallery-slider">
        <div class="swiper-wrapper">
          <?php foreach ($products_list as $item) { 
            $post_id = $item->ID;
            $product = wc_get_product( $post_id );
            $image_id = $product->get_image_id();
            $primary_cat = primary_category($post_id,'product_cat');
            $product_title = $product->get_name();
            ?>
            <article class="gallery__item swiper-slide">
              <div class="gallery__item-img">
                <?php if(!empty($image_id)){
                  echo wp_get_attachment_image($image_id, 'cat_image',
                  false,
                  [
                    'alt'   => esc_attr($product_title . ' ' . $current_lang),
                    'title' => esc_attr(__('Фото ', 'braun_2025') . $product_title . ' ' . $current_lang)
                  ]);
                } else { ?>
                  <svg class="header__logo-icon">
                    <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
                  </svg>
                <?php } ?>
              </div>
              <div class="gallery__item-info">
                <?php if(!empty( $primary_cat)){?>
                  <a href="<?php echo esc_url($primary_cat['link'])?>" class="gallery__item__cat"><?php echo $primary_cat['name']?></a>
                <?php } ?>
                <h4><?php echo $product_title?></h4>
                <?php if(!empty($product->get_short_description())){?>
                <p><?php echo $product->get_short_description()?></p>
                <?php } ?>
                <a href="<?php echo esc_url(get_the_permalink($post_id))?>" class="gallery__item__link"><?php _e('Дізнатися більше','braun_2025') ?></a>
              </div>
            </article>
          <?php } ?>
        </div>
        <div class="slider-pagination gallery__pagination">
          <div class="gallery-button-prev">
            <svg class="slider-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
            </svg>
          </div>
          <div class="swiper-progress">
            <div class="swiper-progress-bar"></div>
          </div>
          <div class="gallery-button-next">
            <svg class="slider-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php } 
if(have_rows('design_list')){?>
  <section class="technology section">
    <div class="wrapper technology__wrapper">
      <?php if(!empty($design_title)){?>
        <h2><?php echo $design_title ?></h2>
      <?php }
      if(!empty($design_description)){ ?>
        <p><?php echo $design_description ?> </p>
      <?php } ?>
      <div class="technology__list">
        <?php
        while(have_rows('design_list')){
          the_row();
          $image = get_sub_field('image');
          $title =  get_sub_field('title');
          $short_descr = get_sub_field('short_descr');
          $subtext = get_sub_field('subtext');
        ?>
          <article class="technology__item">
            <!-- <a href="#" class="item-link"></a> -->
            <div class="technology__item-img">
              <?php if($image){
                echo wp_get_attachment_image($image,'cat_image',
                false,
                [
                  'alt'   => esc_attr($title . ' ' . $current_lang),
                  'title' => esc_attr(__('Фото ', 'braun_2025') . $title . ' ' . $current_lang)
                ]);
              } else { ?>
                <svg class="header__logo-icon">
                  <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
                </svg>
              <?php } ?>
            </div>
            <?php if(!empty($title)){?>
              <h4><?php echo $title ?></h4>
            <?php } 
            if(!empty($short_descr)){?>
            <div class="technology__item__excerpt">
              <?php echo $short_descr;
                if(!empty($subtext)){
                  echo '<span>'.$subtext.'</span>';
                }?>
            </div>
            <?php } ?>
          </article>
        <?php } ?>
      </div>
    </div>
  </section>
<?php } 
if(!empty($second_image_or_video)){ 
  get_template_part('/template-parts/content','file',['file'=>$second_image_or_video,'current_lang'=>$current_lang ]);
} 
if(!empty($technology_list)){?>
  <section class="technology section">
    <div class="wrapper technology__wrapper">
      <h2><?php _e('Інновації та технології','braun_2025') ?></h2>
      <div class="technology__list">
        <?php
        foreach($technology_list as $technology){
          $post_id = $technology->ID;
          $title =  $technology->post_title;
          $short_descr = $technology->post_excerpt;
        ?>
          <article class="technology__item">
            <!-- <a href="#" class="item-link"></a> -->
            <div class="technology__item-img">
              <?php if(has_post_thumbnail($post_id)){
                $image_id = get_post_thumbnail_id($post_id);
                echo wp_get_attachment_image(
                  $image_id,
                  'cat_image',
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
            <?php if(!empty($title)){?>
              <h4><?php echo $title ?></h4>
            <?php } 
            if(!empty($short_descr)){?>
            <div class="technology__item__excerpt">
              <?php echo $short_descr;?>
            </div>
            <?php } ?>
          </article>
        <?php } ?>
      </div>
    </div>
  </section>
<?php }

if(!empty($last_title) || !empty($last_description)){?>
  <section class="section mission">
    <div class="wrapper mission__wrapper">
      <?php if(!empty($last_title)) { ?>
        <h2><?php echo $last_title ?></h2>
      <?php } 
      if(!empty($last_description)) { ?>
        <p><?php echo $last_description ?></p>
      <?php } ?>
    </div>
  </section>
<?php } 
if(!empty(get_field('cta_link')) || !empty(get_field('cta_name_btn'))){
  get_template_part('template-parts/content','cta',['cta_title'=>get_field('cta_title'),'cta_template' =>get_field('cta_template'),'cta_name_btn'=> get_field('cta_name_btn'),'cta_link'=> get_field('cta_link')]);
}
get_footer();