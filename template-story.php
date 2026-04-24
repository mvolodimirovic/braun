<?php
/**
 * Template Name: Історія бренду
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
  'current_lang' => $current_lang,
];
$first_title = get_field('first_title');
$first_description = get_field('first_description');

$first_product_title = get_field('product_title');

$chronology_title = get_field('chronology_title');

$today_title = get_field('today_title');

$certeficate = get_field('certeficate');
$awards = get_field('awards');

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

if(have_rows('product_list')){ ?>
  <section class="use section">
    <div class="wrapper use__wrapper">
      <?php if(!empty($first_product_title)){?>
        <h2><?php echo $first_product_title ?></h2>
      <?php } ?>
      <div class="use__list use-slider">
        <div class="swiper-wrapper">
          <?php 
          while(have_rows('product_list')){
            the_row();
            $image_id = get_sub_field('image');
            $model = get_sub_field('model');
            $year = get_sub_field('year');
            $title = get_sub_field('title');
            $short_descr = get_sub_field('short_descr');
          ?>
            <article class="use__item swiper-slide">
              <div class="use__item-img">
                <?php if(!empty($image_id)){
                  echo wp_get_attachment_image($image_id, 'cat_image',
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
              <?php if(!empty($model) || !empty($year)){?>
                <div class="use__item-old">
                  <?php if(!empty($year)){?>
                    <span><?php echo $year ?></span>
                  <?php } ?>
                  <?php if(!empty($model)){?>
                    <span><?php echo $model ?></span>
                  <?php } ?>
                </div>
              <?php } 
              if(!empty($title)) { ?>
                <h4><?php echo $title ?></h4>
              <?php } 
              if(!empty($short_descr)) { ?>
                <div class="use__item__excerpt">
                 <?php echo $short_descr ?>
                </div>
              <?php } ?>
            </article>
          <?php } ?>
        </div>
        <div class="slider-pagination use__pagination">
          <div class="use-button-prev">
            <svg class="slider-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
            </svg>
          </div>
          <div class="swiper-progress">
          </div>
          <div class="use-button-next">
            <svg class="slider-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php } 
if(have_rows('chronology_list')) { ?>
  <section class="chronology section">
    <div class="wrapper chronology__wrapper">
      <?php if(!empty($chronology_title)) { ?>
        <h2><?php echo $chronology_title ?></h2>
      <?php } ?>
      <div class="chronology__short short-multiple">
        <div class="swiper-wrapper">
          <?php while (have_rows('chronology_list')) { 
            the_row();
            $main_year = get_sub_field('main_year');
            if(!empty($main_year)){
            ?>
              <div class="chronology__short__item swiper-year">
                <h3><?php echo $main_year ?></h3>
                <div class="chronology__short__item-img">
                  <svg class="slider-icon">
                    <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#story-slider-icon"></use>
                  </svg>
                </div>
              </div>
            <?php } 
            if(have_rows('detal_chronology')) {
              while (have_rows('detal_chronology')) {
                the_row();
                $image_id = get_sub_field('image');
                $name = get_sub_field('name');
                $year = get_sub_field('year');
                $description  = get_sub_field('description');
                ?>
                <div class="chronology__short__item swiper-slide">
                  <div class="chronology__short__item-img">
                    <?php if(!empty($image_id)){
                      echo wp_get_attachment_image($image_id, 'thumbnail',
                      false,
                      [
                        'alt'   => esc_attr($name . ' ' . $current_lang),
                        'title' => esc_attr(__('Фото ', 'braun_2025') . $name . ' ' . $current_lang)
                      ]);
                    } else { ?>
                      <svg class="header__logo-icon">
                        <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
                      </svg>
                    <?php } ?>
                  </div>
                  <?php if(!empty($name)){?>
                    <div class="chronology__short__item-name">
                      <?php echo $name ?>
                    </div>
                  <?php } 
                  if(!empty($year)) { ?>
                    <div class="chronology__short__item-explanation">
                      <?php echo $year?>
                    </div>
                  <?php } ?>
                </div>
              <?php } 
            }
          } ?>
        </div>
      </div>
      <div class="chronology__expanded swiper expanded-single">
        <div class="swiper-wrapper">
        <?php while (have_rows('chronology_list')) { 
            the_row();
            if(have_rows('detal_chronology')) {
              while (have_rows('detal_chronology')) {
                the_row();
                $image_id = get_sub_field('image');
                $name = get_sub_field('name');
                $year = get_sub_field('year');
                $description  = get_sub_field('description');
            ?>
              <div class="chronology__expanded__item swiper-slide">
                <div class="chronology__expanded__item-img">
                  <?php if(!empty($image_id)){
                    echo wp_get_attachment_image($image_id, 'chronology_image',
                    false,
                    [
                      'alt'   => esc_attr($name . ' ' . $current_lang),
                      'title' => esc_attr(__('Фото ', 'braun_2025') . $name . ' ' . $current_lang)
                    ]);
                  } else { ?>
                    <svg class="header__logo-icon">
                      <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
                    </svg>
                  <?php } ?>
                </div>
                <div class="chronology__expanded__item-info">
                  <?php if(!empty($name)){?>
                    <h4><?php echo $name?></h4>
                  <?php } 
                  if(!empty($year)) { ?>
                    <h5><?php echo $year ?></h5>
                  <?php } 
                  if(!empty($description)){?>
                    <p><?php echo $description ?></p>
                  <?php } ?>
                </div>
              </div>
              <?php } 
              }
            } ?>
        </div>
      </div>
      <div class="chronology__pagination">
        <div class="chronology-button-prev">
          <svg class="slider-icon">
            <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
          </svg>
        </div>
        <div class="chronology-button-next">
          <svg class="slider-icon">
            <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
          </svg>
        </div>
      </div>
    </div>
  </section>
<?php } 
if(have_rows('slider_list')) { ?>
  <section class="now section">
    <div class="wrapper now__wrapper">
      <?php if(!empty($today_title)) { ?>
        <h2><?php echo $today_title ?></h2>
      <?php } ?>
      <div class="now__list now-slider">
        <div class="swiper-wrapper">
          <?php while (have_rows('slider_list')) {
            the_row(); 
            $image_id = get_sub_field('image');
            $title = get_sub_field('title');
            $short_descr = get_sub_field('short_descr');
            ?>
          <div class="swiper-slide now__item">
            <div class="now__item-img">
              <?php if(!empty($image_id)){
                echo wp_get_attachment_image($image_id, 'full',
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
            <?php if(!empty($title)) { ?>
              <h3><?php echo $title ?></h3>
            <?php } 
            if(!empty($short_descr)) { ?>
              <?php echo $short_descr ?>
            <?php } ?>
          </div>
          <?php } ?>
        </div>
      </div>
      <div class="now__pagination">
        <div class="now-button-prev">
          <svg class="slider-icon">
            <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
          </svg>
        </div>
        <div class="swiper-progress now-progress"></div>
        <div class="now-button-next">
          <svg class="slider-icon">
            <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
          </svg>
        </div>
      </div>
    </div>
  </section>
<?php }
if(!empty($certeficate) || !empty($awards)) {  ?>
  <section class="inter section">
    <div class="wrapper inter__wrapper">
      <?php if(!empty($certeficate)) { ?>
        <article class="inter__item">
          <a href="<?php echo esc_url($certeficate)?>" class="item-link"></a>
          <div class="inter__item-img">
            <svg class="slider-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#certeficate-icon"></use>
            </svg>
          </div>
          <h4><?php _e('Сертифікати','braun_2025') ?></h4>
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