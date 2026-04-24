<?php

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
$current_lang = 'default';
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
    $current_lang = apply_filters( 'wpml_current_language', null );
}
global $wp_query;
$max_pages = $wp_query->max_num_pages;

$obj = get_queried_object();
$link = isset($obj->term_id) ? get_term_link($obj->term_id) : get_post_type_archive_link('product');
$post_id = isset($obj->term_id) ? $obj->term_id : wc_get_page_id('shop');
$filter_child = isset($obj->term_id) ? get_field('filters_child', 'product_cat_' . $post_id) :'';
$title = isset($obj->term_id) ? $obj->name : get_the_title($post_id);
$thumbnail_id = isset($obj->term_id) ? get_field('image', 'product_cat_' . $post_id) : get_field('image', $post_id);
$short_descr = isset($obj->term_id) ? get_field('short_descr', 'product_cat_' . $post_id) : get_field('short_descr', $post_id);
$hero_template = isset($obj->term_id) ? get_field('template', 'product_cat_' . $post_id) : get_field('template', $post_id);
$name_btn = isset($obj->term_id) ? get_field('name_btn', 'product_cat_' . $post_id) : get_field('name_btn',$post_id);
$btn = isset($obj->term_id) ? get_field('btn', 'product_cat_' . $post_id) : get_field('btn',$post_id);
$promo = isset($obj->term_id) ? 'product_cat_' . $post_id : wc_get_page_id('shop');
$popular_title = isset($obj->term_id) ? get_field('popular_title', 'product_cat_' . $post_id) : get_field('popular_title',$post_id);
$popular_terms = isset($obj->term_id) ? get_field('popular_terms', 'product_cat_' . $post_id) : get_field('popular_terms',$post_id);

$hero_btn = $hero_template == true ? $name_btn : $btn;
$hero_args = [
  'image_id'=> $thumbnail_id,
  'title' => $title,
  'description'=> $short_descr,
  'template'=> $hero_template,
  'current_lang' => $current_lang,
  'button'=> $hero_btn,
];
if(!empty($thumbnail_id)){
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
if(isset($obj->term_id) && !empty($filter_child)){
  $term_list = get_terms(['taxonomy'=> 'product_cat','hide_empty'=> true,'parent'=> $post_id]);   ?>
  <section class="series">
    <div class="wrapper series-slider slider">
      <div class="swiper-wrapper series__wrapper">
        <?php
        foreach ($term_list as $term) { 
          $term_id = $term->term_id;
          $thumbnail_id = get_term_meta( $term_id, 'thumbnail_id', true );
          $class = '';
          $term_name = $term->name;
          if(!empty($_GET['product_filter'])){
            $filter_item = explode(',', $_GET['product_filter']);
            if(in_array($term->slug, $filter_item)){
              $class = 'active';
            }
          }
          ?>
          <div class="series__item swiper-slide <?php echo esc_attr($class)?>" data-category="<?php echo esc_attr($term->slug)?>">
            <div class="series__item-img">
              <?php 
              if (!empty($thumbnail_id)) {
                echo wp_get_attachment_image(
                  $thumbnail_id,
                  'cat_image',
                  false,
                  [
                    'alt'   => esc_attr($term_name . ' ' . $current_lang),
                    'title' => esc_attr(__('Фото ', 'braun_2025') . $term_name . ' ' . $current_lang)
                  ]
                );
              } else {
                echo wc_placeholder_img('woocommerce_thumbnail');
              } ?>
            </div>
            <span><?php echo $term_name?></span>
          </div>
        <?php } ?>
      </div>
    </div>
  </section>
<?php } ?>
<section id="filters" class="filters" data-url="<?php echo esc_url($link)?>">
  <div class="wrapper filters__wrapper">
    <div class="filters-group">
      <button type="button" class="filters-all" data-id-product="<?php echo isset($obj->term_id) ? $obj->slug: 'all'?>"><?php _e('Усі товари','braun_2025')?></button>
    </div>
    <div class="filters-group">
      <button type="button" class="filters-choice <?php echo isset($_GET['popular']) && !empty($_GET['popular']) ? 'enable':'' ?>" data-text="<?php _e('За популярністю','braun_2025')?>">
        <span class="filters-choice-title">
          <?php _e('За популярністю','braun_2025')?>
        </span>
        <svg class="filters-icon">
          <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#filter-icon"></use>
        </svg>
      </button>
      <div class="filters__list" data-name="popular">
        <ul data-name="popular" class="filters__list-wrap">
          <li class="filters__list__item single
           <?php echo isset($_GET['popular']) && !empty($_GET['popular']) && $_GET['popular'] == 'low-to-high' ? 'selected':'' ?>" 
           data-value="low-to-high">
            <span>
              <?php _e('Від меншого до більшого','braun_2025')?>
            </span>
          </li>
          <li class="filters__list__item single"
          <?php echo isset($_GET['popular']) && !empty($_GET['popular']) && $_GET['popular'] == 'high-to-low' ? 'selected':'' ?>
           data-value="high-to-low">
            <span>
              <?php _e('Від більшого до меншого','braun_2025')?>
            </span>
          </li>
        </ul>
      </div>
    </div>
    <div class="filters-group">
      <button type="button" class="filters-choice <?php echo isset($_GET['price']) && !empty($_GET['price']) ? 'enable':'' ?>" data-text="<?php _e('За ціною','braun_2025')?>">
        <span class="filters-choice-title">
            <?php _e('За ціною','braun_2025')?>
        </span>
        <svg class="filters-icon">
          <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#filter-icon"></use>
        </svg>
      </button>
      <div class="filters__list" data-name="price">
        <ul data-name="price" class="filters__list-wrap">
          <li class="filters__list__item single
           <?php echo isset($_GET['price']) && !empty($_GET['price']) && $_GET['price'] == 'low-to-high' ? 'selected':'' ?>" 
           data-value="low-to-high">
            <span>
              <?php _e('Від меншого до більшого','braun_2025')?>
            </span>
          </li>
          <li class="filters__list__item single"
          <?php echo isset($_GET['price']) && !empty($_GET['price']) && $_GET['price'] == 'high-to-low' ? 'selected':'' ?>
           data-value="high-to-low">
            <span>
              <?php _e('Від більшого до меншого','braun_2025')?>
            </span>
          </li>
        </ul>
      </div>
    </div>
    <div class="filters-group">
      <button type="button" class="filters-choice <?php echo isset($_GET['novelty']) && !empty($_GET['novelty']) ? 'enable':'' ?>" data-text="<?php _e('За новизною','braun_2025')?>">
        <span class="filters-choice-title">
          <?php _e('За новизною','braun_2025')?>
        </span>
        <svg class="filters-icon">
          <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#filter-icon"></use>
        </svg>
      </button>
      <div class="filters__list" data-name="novelty">
        <ul data-name="novelty" class="filters__list-wrap">
          <li class="filters__list__item single
           <?php echo isset($_GET['novelty']) && !empty($_GET['novelty']) && $_GET['novelty'] == 'low-to-high' ? 'selected':'' ?>" 
           data-value="low-to-high">
            <span>
              <?php _e('Від меншого до більшого','braun_2025')?>
            </span>
          </li>
          <li class="filters__list__item single"
          <?php echo isset($_GET['novelty']) && !empty($_GET['novelty']) && $_GET['novelty'] == 'high-to-low' ? 'selected':'' ?>
           data-value="high-to-low">
            <span>
              <?php _e('Від більшого до меншого','braun_2025')?>
            </span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</section>
<?php 
if(have_posts()) { ?>
  <section id="product" class="products">
    <div class="wrapper products__wrapper">
        <div class="products__list">
        <?php
          while(have_posts()) {
            the_post(); 
              get_template_part('template-parts/content','product',['current_lang'=> $current_lang]);
            } wp_reset_postdata(); ?>
        </div>
      <div class="pagination">
      <?php 
        echo paginate_links( array(
          'base' 			=> $link.'%_%#product',
          'prev_next'     => true,
          'prev_text'    	=> 	'<svg><use xlink:href="'. get_template_directory_uri(). '/img/path-icon.svg#slider-arrow"></use></svg>',
          'next_text'    	=> 	'<svg><use xlink:href="'. get_template_directory_uri(). '/img/path-icon.svg#slider-arrow"></use></svg>',
          'mid_size'		=> 1,
          'total'   		=> $max_pages,
        ) );   
        ?>  
      </div>
      <div class="loader-wrapper">
        <div class="loader"></div>
      </div>
    </div>
  </section>
<?php } 
if(have_rows('promo_list', $promo)){?>
  <section class="promo section">
    <div class="wrapper promo__wrapper">
      <?php
      $index = 0;
      while(have_rows('promo_list', $promo )){
        $index++;
        the_row(); 
        $image = get_sub_field('image');
        $title_promo = get_sub_field('title');
        $template = get_sub_field('popaporlink');
        $button = $template == true ? get_sub_field('btn_text') : get_sub_field('button_link');
        ?>
        <div class="promo__item <?php echo $index < 3 ? 'half':'full';  echo empty($title_promo) && $template == true ? ' only-img':'';?>">
          <?php if(!empty($image)){?>
            <div class="promo__item-img">
              <?php echo wp_get_attachment_image($image,'full',false,
                [
                  'alt'   => esc_attr($title . ' ' . $current_lang),
                  'title' => esc_attr(__('Фото ', 'braun_2025') . $title . ' ' . $current_lang)
                ]); ?>
            </div>
          <?php } 
          if(!empty($title_promo)){?>
            <h3><?php echo $title_promo ?></h3>
          <?php } 
          if($template == true && !empty($button )){ ?>
            <button type="button" class="btn open-popup btn-white"><?php echo $button ?></button>
          <?php } else if($template == false && !empty($button)){
            if(!empty($button['url'])){?>
              <a class="btn btn-white" href="<?php echo esc_url($button['url'])?>" <?php echo !empty($button['target']) ? 'target="'.$button['target'].'"' : '';?>>
                <?php echo $button['title']?>
              </a>
            <?php } ?>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  </section>
<?php }
if(!empty($popular_terms)){
?>
<section class="sections section">
  <div class="wrapper sections__wrapper">
    <?php if(!empty($popular_title)){?>
      <h2><?php echo $popular_title ?></h2>
    <?php } ?>
    <div class="sections__list">
      <?php foreach ($popular_terms as $term) { 
        $term_id = $term->term_id;
        $thumbnail_id = get_term_meta( $term_id, 'thumbnail_id', true );
        $name = $term->name;
        ?>
        <article class="sections__item">
          <a href="<?php echo esc_url(get_term_link($term_id))?>" class="item-link"></a>
          <div class="sections__item-img">
            <?php if(!empty($thumbnail_id)){
              echo wp_get_attachment_image($thumbnail_id,'fill',false,
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
          <h4><?php echo $name?></h4>
        </article>
      <?php } ?>
    </div>
  </div>
</section>
<?php } 
if(!empty(category_description())){?>
  <section class="seo section">
    <div class="wrapper seo__wrapper">
      <?php echo category_description(); ?> 
    </div>
  </section>
<?php 
}
get_footer( 'shop' );
