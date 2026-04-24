<?php
defined( 'ABSPATH' ) || exit;
$current_lang = 'default';
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
    $current_lang = apply_filters( 'wpml_current_language', null );
}
global $product;
$title_product = get_the_title();
$product_id = get_the_ID();
$novelty = get_field('novynka');
$best_sale = get_field('best_sale');
$link = get_field('link_shop');
$average =  floatval( $product->get_average_rating() );
$tag =  get_the_terms( get_the_ID(), 'product_tag' );
$sku = $product->get_sku();
$gallery = $product->get_gallery_image_ids();
$attr = get_product_attributes_split($product_id);
$instruction = get_field('instruction');
$technology = get_field('texhnology_list');
$how_use_list = get_field('how_use_list');
$file = get_field('file_imahe_or_video');
$product_cat = get_the_terms($product_id, 'product_cat');

$reviews = get_comments([
    'post_id' => $product_id,
    'status'  => 'approve',
    'type'    => 'review',
    'order'   => 'DESC',
]);


do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); 
	return;
}

$matched_slider_term_ids = [];

foreach ($product_cat as $parent_term) {
  if (get_field('filters_child', 'term_' . $parent_term->term_id)) {
    $child_terms = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => $parent_term->term_id,
        'fields'     => 'ids',
    ]);

    if (!empty($child_terms)) {
      $product_terms = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'ids']);
      $intersect = array_intersect($child_terms, $product_terms);
      if (!empty($intersect)) {
          $matched_slider_term_ids = array_merge($matched_slider_term_ids, $intersect);
      }
    }
  }
}

$args = [
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'post__not_in'   => [$product_id], 
    'tax_query'      => [
      [
        'taxonomy' => 'product_cat',
        'field'    => 'term_id',
        'terms'    => $matched_slider_term_ids,
      ]
    ]
];

$series_product = new WP_Query($args);
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
  <section class="product-card card">
    <div class="wrapper card__wrapper">
      <div class="card__item-top card__item-top-mobile">
        <?php if($novelty || $best_sale || $average !== 0.0){ ?>
          <div class="card__item__labels">
            <?php if($novelty){?>
              <span><?php _e('Новинка','braun_2025')?></span>
            <?php } ?>
            <?php if($best_sale){?>
              <span><?php _e('Краща ціна','braun_2025')?></span>
            <?php } 
            if($average > 0){ ?>
            <a href="<?php echo esc_url(get_the_permalink($product_id) . '#comment-'.$product->get_review_count())?>" class="rating"> 
              <span>
                <?php echo number_format($average, 1) .' / 5.0';?>
                <svg class="icon-star">
                  <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#icon-star"></use>
                </svg> 
              </span>
            </a>
            <?php } ?>
          </div>
        <?php } ?>
        <h1 class="card__item__title"><?php echo get_the_title()?></h1>
        <?php if(!empty($tag)){?>
          <span><?php echo $tag[0]->name?></span>
        <?php } 
        if(!empty($sku)){?> 
          <span><?php echo $sku ?></span>
        <?php } ?>
      </div>
      <?php if(!empty($gallery)){?>
        <div class="card-gallery">
          <div class="gallery__pagination">
            <div class="gallery-button-prev">
              <svg class="slider-icon">
                <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
              </svg>
            </div>
            <div class="swiper-progress card-progress"></div>
            <div class="gallery-button-next">
              <svg class="slider-icon">
                <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
              </svg>
            </div>
          </div>
            <div class="card__slider-multiple swiper">
              <div class="swiper-wrapper">
                <?php
                foreach ($gallery as $image) { ?>
                  <div class="swiper-slide card__slider__item">
                    <?php echo wp_get_attachment_image($image,'actual_image',false,
                      [
                        'alt'   => esc_attr($title_product . ' ' . $current_lang),
                        'title' => esc_attr(__('Фото ', 'braun_2025') . $title_product . ' ' . $current_lang)
                      ]); ?>
                  </div>
                <?php } ?>
              </div>
            </div>
            <div class="card__slider-single swiper">
              <div class="swiper-wrapper">
                <?php
                foreach ($gallery as $image) { ?>
                  <div class="swiper-slide card__slider__item">
                    <?php echo wp_get_attachment_image($image,'product_image',false,
                      [
                        'alt'   => esc_attr($title_product . ' ' . $current_lang),
                        'title' => esc_attr(__('Фото ', 'braun_2025') . $title_product . ' ' . $current_lang)
                      ]); ?>
                  </div>
                <?php } ?>
              </div>
            </div>
        </div>
      <?php } ?>
      <div class="card__item">
        <div class="card__item-top">
          <?php if($novelty || $best_sale || $average !== 0.0){ ?>
            <div class="card__item__labels">
              <?php if($novelty){?>
                <span><?php _e('Новинка','braun_2025')?></span>
              <?php } ?>
              <?php if($best_sale){?>
                <span><?php _e('Краща ціна','braun_2025')?></span>
              <?php } 
              if($average > 0){ ?>
              <a href="<?php echo esc_url(get_the_permalink($product_id) . '#comment-'.$product->get_review_count())?>" class="rating"> 
                <span>
                  <?php echo number_format($average, 1) .' / 5.0';?>
                  <svg class="icon-star">
                    <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#icon-star"></use>
                  </svg> 
                </span>
              </a>
              <?php } ?>
            </div>
          <?php } ?>
          <h1 class="card__item__title"><?php echo get_the_title()?></h1>
          <?php if(!empty($tag)){?>
            <span><?php echo $tag[0]->name?></span>
          <?php } 
          if(!empty($sku)){?> 
            <span><?php echo $sku ?></span>
          <?php } ?>
        </div>
        <?php if($series_product->have_posts()) { ?>
          <div class="card__item-middle model-slider">
            <div class="swiper-wrapper">
              <?php 
                while($series_product->have_posts()) { 
                  $series_product->the_post();
                  $post_id = get_the_ID();
                  $series_title = get_the_title();
                  ?>
                <div class="swiper-slide card__item__model">
                  <a href="<?php echo esc_url(get_permalink($post_id))?>" class="item-link"></a>
                  <div class="card__item__model-img-wrapp">
                    <div class="card__item__model-img">
                      <?php if(has_post_thumbnail()){
                        $image_id = get_post_thumbnail_id($post_id);
                        echo wp_get_attachment_image($image_id,'product_image',false,
                        [
                          'alt'   => esc_attr($series_title . ' ' . $current_lang),
                          'title' => esc_attr(__('Фото ', 'braun_2025') . $series_title . ' ' . $current_lang)
                        ]);
                      } else { ?>
                        <svg class="header__logo-icon">
                          <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
                        </svg>
                      <?php } ?>
                    </div>
                  </div>
                  <span class="card__item__model-name"><?php echo $title_product?></span>
                </div>
              <?php  } wp_reset_postdata(); ?>
            </div>
            <div class="slider-pagination card__pagination">
              <div class="card-button-prev">
                <svg class="slider-icon">
                  <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
                </svg>
              </div>
              <div class="card-button-next">
                <svg class="slider-icon">
                  <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
                </svg>
              </div>
            </div>
          </div>
        <?php } ?>
        <div class="card__item-bottom">
          <?php if(!empty($link)){?>
            <a href="<?php echo esc_url($link)?>" class="btn btn-black" target="_blank"><?php _e('Перейти до магазину','braun_2025')?></a>
          <?php } 
          if(!empty(get_the_excerpt())){
          ?>
          <div class="card__item__excerpt">
            <?php the_excerpt()?>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </section>
<?php if(!empty($attr)) { ?>
  <section class="tabs">
    <div class="wrapper tabs__wrapper">
    <?php 
      if(!empty($attr['characteristics'])) { ?>
        <div class="tabs__item">
          <div class="tabs__item-head">
            <h4><?php _e('Характеристики','braun_2025')?></h4>
            <svg class="tabs-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
            </svg>
          </div>
          <ul class="tabs__item-body">
            <?php 
            foreach ($attr['characteristics'] as $characteristic) { ?>
                <li><span><?php echo $characteristic['label']?></span><span><?php echo implode(', ',$characteristic['values']) ?></span></li>
            <?php } ?>
          </ul>
        </div>
      <?php }
      if(!empty($attr['features'])) { ?>
        <div class="tabs__item">
          <div class="tabs__item-head">
            <h4><?php _e('Особливості','braun_2025')?></h4>
            <svg class="tabs-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
            </svg>
          </div>
          <ul class="tabs__item-body">
          <?php 
            foreach ($attr['features'] as $features) { ?>
                <li><span><?php echo $features['label']?></span><span><?php echo implode(', ',$features['values']) ?></span></li>
            <?php } ?>
          </ul>
        </div>
      <?php } 
      
      if(!empty($instruction)){?>
        <div class="tabs__item">
          <div class="tabs__item-instruction">
            <h4><?php _e('Інструкція','braun_2025')?></h4>
            <a href="<?php echo esc_url($instruction['url'])?>" class="btn btn-black" download><?php _e('Переглянути','braun_2025')?></a>
          </div>
        </div>
      <?php } ?>
    </div>
  </section>
<?php }
if(have_rows('complete_set')) { ?>
  <section id="complete-set" class="sections section card-sections">
    <div class="wrapper sections__wrapper">
      <h2><?php _e('Комплектація','bruan_2025') ?></h2>
      <div class="sections__list">
        <?php 
        while (have_rows('complete_set')) {
          the_row();
          $image_id = get_sub_field('image');
          $name = get_sub_field('name');
        ?>
        <article class="sections__item">
          <div class="sections__item-img">
            <?php if(!empty($image_id)){
              echo wp_get_attachment_image($image_id,'cat_image', false,
                [
                  'alt'   => esc_attr($title_product . ' ' . $current_lang),
                  'title' => esc_attr(__('Фото ', 'braun_2025') . $title_product . ' ' . $current_lang)
                ]);
            } else { ?>
              <svg class="header__logo-icon">
                <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
              </svg>
            <?php } ?>
          </div>
          <?php if(!empty($name)) { ?>
            <h4><?php echo $name ?></h4>
          <?php } ?>
        </article>
        <?php } ?>
      </div>
    </div>
  </section>
<?php }
if(!empty($technology)) { ?>
  <section class="technology section">
    <div class="wrapper technology__wrapper">
      <h2><?php _e('Які технології використано','braun_2025')?></h2>
      <div class="technology__list">
        <?php 
        foreach($technology as $item) { 
          $post_id = $item->ID;
          $post_image_id = get_post_thumbnail_id($post_id);
          $tech_title = $item->post_title;
          ?>
          <article class="technology__item">
            <div class="technology__item-img">
              <?php if(!empty($post_image_id)){
                echo wp_get_attachment_image($post_image_id,'cat_image', false,
                [
                  'alt'   => esc_attr($tech_title . ' ' . $current_lang),
                  'title' => esc_attr(__('Фото ', 'braun_2025') . $tech_title . ' ' . $current_lang)
                ]);
              } else { ?>
                <svg class="header__logo-icon">
                  <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
                </svg>
              <?php } ?>
            </div>
            <h4><?php echo $tech_title?></h4>
            <?php if(!empty($item->post_excerpt)) { ?>
              <div class="technology__item__excerpt">
                <?php echo $item->post_excerpt?>
              </div>
            <?php } ?>
          </article>
        <?php } ?>
      </div>
    </div>
  </section>
<?php } 
if(have_rows('how_use_list')) { ?>
  <section class="use section">
    <div class="wrapper use__wrapper">
      <h2><?php _e('Як використовувати товар','braun_2025')?></h2>
      <div class="use__list use-slider">
        <div class="swiper-wrapper">
          <?php 
          while (have_rows('how_use_list')) {
            the_row();
            $image_id = get_sub_field('image');
            $name = get_sub_field('name');
            $description = get_sub_field('description'); ?>
            <article class="use__item swiper-slide">
              <div class="use__item-img">
                <?php if(!empty($image_id)){
                  echo wp_get_attachment_image($image_id,'cat_image', false,
                  [
                    'alt'   => esc_attr($title_product . ' ' . $current_lang),
                    'title' => esc_attr(__('Фото ', 'braun_2025') . $title_product . ' ' . $current_lang)
                  ]);
                } else { ?>
                  <svg class="header__logo-icon">
                    <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
                  </svg>
                <?php } ?>
              </div>
              <?php if(!empty($name)) { ?>
                <h4><?php echo $name ?></h4>
              <?php } ?>
              <?php if(!empty($description)){?>
                <div class="use__item__excerpt">
                  <?php echo $description; ?>
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
if(!empty($file)){ 
get_template_part('/template-parts/content','file',['file'=>$file,'current_lang'=>$current_lang ]);
} 
if(have_rows('awards_list')) { ?>
  <section class="awards section">
    <div class="wrapper awards__wrapper">
      <h2><?php _e('Нагороди','braun_2025')?></h2>
      <div class="awards__list">
        <?php 
        while(have_rows('awards_list')){
          the_row();
          $image_id = get_sub_field('image');
          $name = get_sub_field('name');
          $description = get_sub_field('description'); 
        ?>
        <article class="awards__item">
          <div class="awards__item-img">
          <?php if(!empty($image_id)){
            echo wp_get_attachment_image($image_id,'cat_image', false,
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
          <div class="awards__item-info">
            <?php if(!empty($name)) { ?>
              <h4><?php echo $name ?></h4>
            <?php } ?>
            <?php if(!empty($description)){?>
              <div class="awards__item__excerpt">
                <?php echo $description; ?>
              </div>
            <?php } ?>
          </div>
        </article>
        <?php } ?>
      </div>
    </div>
  </section>
<?php }
if(!empty($reviews)) { ?>
  <section class="section comments">
    <div class="wrapper comments__wrapper">
      <h2><?php _e('Відгуки','braun_2025')?></h2>
      <div class="comments__list">
        <?php
          foreach($reviews as $key => $review){
            $author_name = $review->comment_author;
            $first_letter = mb_strtoupper(mb_substr($author_name, 0, 1));
            $hash = md5($author_name);
            $r = hexdec(substr($hash, 0, 2));
            $g = hexdec(substr($hash, 2, 2));
            $b = hexdec(substr($hash, 4, 2));
            $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
            $text_color = $brightness > 230 ? '#000' : '#fff';
            $bg_color = "rgb($r, $g, $b)";
            $comment_date = get_comment_date('d.m.Y', $review);
            $review_text = $review->comment_content;
            ?>
            <div id="comment-<?php echo esc_attr($review->comment_ID) ?>" class="comments__item">
              <div class="comments__item-wrapp">
                <?php if(!empty($author_name)){?>
                  <div class="comments__item-avatar" style="background-color:<?php echo esc_attr($bg_color);?>; color:<?php echo esc_attr($text_color)?>">
                    <?php echo $first_letter?>
                  </div>
                  <h4>
                    <?php echo $author_name?>
                  </h4>
                  <div class="comments__item-date">
                    <time datetime="<?php echo esc_attr($comment_date)?>"><?php echo $comment_date ?></time>
                  </div>
                <?php } ?>
                <?php if(!empty($review_text)) { ?>
                <div class="comments__item-text">
                  <?php echo $review_text ?>
                </div>
                <?php } ?>
              </div>
              <?php 
              $child_comments = get_comments([
                  'parent' => $review->comment_ID,
                  'status' => 'approve',
                  'post_id' => $product_id,
              ]);

              if (!empty($child_comments)) {
                  echo '<div class="comments-replies">';
                  foreach ($child_comments as $child) {
                    $author_name = $child->comment_author;
                    $first_letter = mb_strtoupper(mb_substr($author_name, 0, 1));
                    $hash = md5($author_name);
                    $r = hexdec(substr($hash, 0, 2));
                    $g = hexdec(substr($hash, 2, 2));
                    $b = hexdec(substr($hash, 4, 2));
                    $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
                    $text_color = $brightness > 150 ? '#000' : '#fff';
                    $bg_color = "rgb($r, $g, $b)";
                    $comment_date = get_comment_date('d.m.Y', $child);
                    $child_text = $child->comment_content;
                    ?>
                      <div class="comments__item-wrapp">
                        <?php if(!empty($author_name)){?>
                          <div class="comments__item-avatar" style="background-color:<?php echo esc_attr($bg_color);?>; color:<?php echo esc_attr($text_color)?>">
                            <?php echo $first_letter?>
                          </div>
                          <h4>
                            <?php echo $author_name?>
                          </h4>
                          <div class="comments__item-date">
                            <time datetime="<?php echo esc_attr($comment_date)?>"><?php echo $comment_date ?></time>
                          </div>
                        <?php } ?>
                        <?php if(!empty($child_text)) { ?>
                          <div class="comments__item-text">
                            <?php echo $child_text ?>
                          </div>
                        <?php } ?>
                      </div>
                  <?php }
                  echo '</div>';
              } ?>
            </div>
          <?php }
        ?>
      </div>
    </div>
  </section>
<?php } ?>
  <section class="section reply">
    <div class="wrapper reply__wrapper">
      <?php custom_wc_review_form($product_id); ?>
    </div>
  </section>
</div>

<?php 

do_action( 'woocommerce_after_single_product' ); 
