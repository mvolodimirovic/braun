<?php
/**
 * Template Name: Головна
 */

get_header();
$current_lang = 'default';
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
    $current_lang = apply_filters( 'wpml_current_language', null );
}
// hero 
$hero_image = get_post_thumbnail_id( get_queried_object_id() );
$hero_title = get_field('hero_title');
$hero_description = get_field('hero_description');
$hero_template = get_field('hero_template');
$hero_btn = $hero_template == true ? get_field('hero_name_btn') : get_field('hero_btn');
$hero_args = [
  'image_id'=> $hero_image,
  'title' => $hero_title,
  'description'=> $hero_description,
  'template'=> $hero_template,
  'button'=> $hero_btn,
  'current_lang' => $current_lang
];

$child_terms_title = get_field('child_terms_title');
$child_terms_list = get_field('child_terms_list');

$actual_terms_title = get_field('actual_terms_title');
$actual_terms_list = get_field('actual_terms_list');

$blog_title = get_field('blog_title');
$blog_ids = !empty(get_field('blog_list')) ? get_field('blog_list') : [] ;
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

get_template_part('/template-parts/content','hero',$hero_args);
get_template_part('/template-parts/content','instruction',['sub_cat' => false,'current_lang' => $current_lang]);

?>
<?php if(!empty($child_terms_list)){?>
  <section class="categories section">
    <div class="wrapper categories__wrapper">
      <?php if(!empty($child_terms_title)){?>
        <h2><?php echo $child_terms_title?></h2>
      <?php } ?>
      <a href="<?php echo esc_url(wc_get_page_permalink( 'shop' ))?>"><?php _e('Перейти до усіх категорій','braun_2025')?></a>
      <div class="categories__list">
        <?php foreach ($child_terms_list as $term) { 
          $term_id = $term->term_id;
          $term_name = $term->name;
          $thumbnail_id = !empty(get_field('home_image', 'product_cat_' . $term_id)) ? get_field('home_image', 'product_cat_' . $term_id) : get_term_meta( $term_id, 'thumbnail_id', true );;
          ?>
          <article class="categories__item">
            <a href="<?php echo esc_url(get_term_link($term_id))?>" class="item-link"></a>
            <div class="categories__item-img">
              <?php echo wp_get_attachment_image($thumbnail_id,'cat_image_large',false, ['alt'=> esc_attr($term_name .' '. $current_lang),'title'=> __('Фото ','braun_2025') . esc_attr($term_name .' '. $current_lang)]); ?>
            </div>
            <div class="categories__item__bottom">
              <h4><?php echo $term_name?></h4>
              <span>
                <svg class="slider-icon">
                  <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
                </svg>
              </span>
            </div>
          </article>
        <?php } ?>
      </div>
    </div>
  </section>
<?php } ?>
<?php if(!empty($actual_terms_list)){
  $cat_ids =  wp_list_pluck($actual_terms_list, 'term_id' );
  $product_list = new WP_Query([
      'post_status'  => 'publish',
      'post_type'    => 'product',
      'posts_per_page' => -1,
      'suppress_filters' => true,
      'tax_query'    => [
          [
              'taxonomy' => 'product_cat',
              'field'    => 'id',
              'terms'    => $cat_ids,
              'operator' => 'IN',
          ],
      ],
  ]);
  ?>
  <section class="catalog section">
    <div class="wrapper catalog__wrapper">
      <?php if(!empty($actual_terms_title)){?>
        <h2><?php echo $actual_terms_title ?></h2>
      <?php } ?>
      <ul class="catalog__categories" data-all-ids="<?php echo esc_attr(implode(', ', $cat_ids))?>" data-type="product" data-cat="product_cat">
        <li><button type="button" data-cat-id="all" class="catalog__categories__btn active"><?php _e('Усі','braun_2025')?></button></li>
        <?php foreach ($actual_terms_list as $term) { 
          if($term->count !== 0){?>
            <li><button type="button" class="catalog__categories__btn" data-cat-id="<?php echo esc_attr($term->term_id)?>"><?php echo $term->name ?><sup><?php echo $term->count?></sup></button></li>
          <?php } 
        } ?>
      </ul>
      <?php if($product_list->have_posts()){?>
        <div class="catalog__list swiper slider-catalog">
          <div class="swiper-wrapper slider-ajax">
            <?php while($product_list->have_posts()){
              $product_list->the_post();
              $post_id = get_the_ID();
              $primary_cat = primary_category($post_id,'product_cat');
              $title = get_the_title();
              ?>
              <article class="catalog__item swiper-slide">
                <a href="<?php echo esc_url(get_permalink($post_id))?>" class="item-link"></a>
                <a href="<?php echo esc_url($primary_cat['link'])?>" class="catalog__item__category"><?php echo $primary_cat['name']?></a>
                  <div class="catalog__item-img">
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
                <h5><?php echo $title ?></h5>
              </article>
            <?php } wp_reset_postdata() ?>
          </div>
          <div class="slider-pagination catalog__pagination">
            <div class="catalog-button-prev">
              <svg class="slider-icon">
                <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
              </svg>
            </div>
            <div class="swiper-progress">
            </div>
            <div class="catalog-button-next">
              <svg class="slider-icon">
                <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
              </svg>
            </div>
          </div>
          <div class="loader-wrapper">
            <div class="loader"></div>
          </div>
        </div>
      <?php } ?>
    </div>
  </section>
<?php } 
if(have_rows('media_slider')){?>
  <section class="media-slider section">
    <div class="swiper-wrapper">
      <?php while(have_rows('media_slider')) { 
        the_row(); 
        $image_id = get_sub_field('media_slider_image');
        $name = get_sub_field('media_slider_name');
        $button = get_sub_field('media_slider_btn');
        if(!empty($image_id)) { ?>
          <div class="swiper-slide media-slider__item">
            <div class="media-slider__item-img">
              <?php echo wp_get_attachment_image($image_id,'full',false, ['alt'=> esc_attr($name .' '. $current_lang),'title'=> __('Фото ','braun_2025') . esc_attr($name .' '. $current_lang)]);?>
            </div>
            <div class="wrapper media-slider__wrapper">
              <?php if(!empty($name)){?>
                <h3><?php echo $name?></h3>
              <?php }
              if(!empty($button['url'])){?>
                <a class="btn" href="<?php echo esc_url($button['url'])?>" <?php echo !empty($button['target']) ? 'target="'.$button['target'].'"' : '';?>>
                  <?php echo $button['title']?>
                </a>
              <?php } ?>
            </div>
          </div>
        <?php }
      } ?>
    </div>
    <div class="media-slider-pagination wrapper"></div>
  </section>
<?php }
if($blog_list->have_posts()) { ?>
  <section class="blog section">
    <div class="wrapper blog__wrapper">
      <?php if(!empty($blog_title)){?>
        <h2><?php echo $blog_title ?></h2>
      <?php } ?>
      <a href="<?php echo esc_url(get_permalink( get_option( 'page_for_posts' ) ))?>" class="show-all"><?php _e('Перейти до усіх статтей','braun_2025') ?></a>
      <div class="blog__list blog-slider">
        <div class="swiper-wrapper">
          <?php 
          while($blog_list->have_posts()) {
            $blog_list->the_post();
            $post_id = get_the_ID();
            $primary_cat = primary_category($post_id,'category');
            get_template_part('template-parts/content','blog', ['primary_cat'=>$primary_cat,'no_slider'=>false,'current_lang'=> $current_lang]);
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
if(have_rows('faq_list')){
  get_template_part('template-parts/content','faq',['title'=>get_field('faq_title'),'faq_list' =>'faq_list']);
}

if(!empty(get_field('cta_name_btn') || get_field('cta_link'))){
  get_template_part('template-parts/content','cta',['cta_title'=>get_field('cta_title'),'cta_template' =>get_field('cta_template'),'cta_name_btn'=> get_field('cta_name_btn'),'cta_link'=> get_field('cta_link')]);
}
get_footer();