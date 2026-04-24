<?php
$product_id = get_the_ID();
$novelty = get_field('novynka');
$best_sale = get_field('best_sale');
$product = wc_get_product( $product_id );
$average =  floatval( $product->get_average_rating() );
$funkciyi = explode(',',$product->get_attribute('pa_funkcziyi'));
$power = $product->get_attribute('pa_potuzhnist-vt');
$name = get_the_title();
$current_lang = !empty($args['current_lang']) ? $args['current_lang'] :'';

$comments = get_comments([
  'post_id' => $product_id,
  'number'  => 1,
  'status'  => 'approve',
  'orderby' => 'comment_date_gmt',
  'order'   => 'DESC',
]);
if (!empty($comments)) {
  $last_comment = $comments[0];
  $last_comment_id = $last_comment->comment_ID;
}
?>
<article class="products__item <?php echo $product->is_in_stock() !== false || $product->get_stock_quantity() !== 0 ? '':'disabled'?>">
  <a href="<?php echo esc_url(get_the_permalink($product_id))?>" class="item-link"></a>
  <div class="products__item-top">
    <div class="products__item__img">
      <?php if(has_post_thumbnail()){
        $image_id = get_post_thumbnail_id();
        echo wp_get_attachment_image(
          $image_id,
          'cat_image',
          false,
          [
            'alt'   => esc_attr($name . ' ' . $current_lang),
            'title' => esc_attr(__('Фото ', 'braun_2025') . $name . ' ' . $current_lang)
          ]
        );
      } else { ?>
        <svg class="header__logo-icon">
          <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
        </svg>
      <?php } ?>
    </div>
    <?php if($novelty || $best_sale || $average !== 0.0){?>
    <div class="products__item__labels">
      <?php if($novelty){?>
        <span><?php _e('Новинка','braun_2025')?></span>
      <?php } ?>
      <?php if($best_sale){?>
        <span><?php _e('Краща ціна','braun_2025')?></span>
      <?php } 
      if($average > 0){ ?>
      <a href="<?php echo esc_url(get_the_permalink($product_id)); echo !empty($comments) ? '#comment-' . $comments[0]->comment_ID :''?>" class="rating"> 
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
  </div>
  <div class="products__item-middle">
    <h5 class="products__item__title"><?php echo $name ?></h5>
    <?php if(!empty(get_the_excerpt())){?>
        <p><?php echo get_the_excerpt()?></p>
      <?php } ?>
      <?php if(isset($funkciyi) && !empty($funkciyi[0])){ ?>
      <div class="products__item__attributes">
        <ul>
          <li><?php _e('Функції:','braun_2025')?> </li>
          <?php foreach($funkciyi as $item){?>
            <li><?php echo $item;?></li>
          <?php } ?>
        </ul>
      </div>
      <?php } 
      if(isset($power) && !empty($power)){?>
        <div class="products__item__attributes">
            <ul>
              <li><?php _e('Потужність (Вт):','braun_2025') ?></li>
              <li><?php echo $power ?></li>
            </ul>
        </div>
      <?php } 
      if(have_rows('complete_set')) { ?>
        <div class="products__item__attributes">
            <ul>
              <li><?php _e('Комплектація:','braun_2025')?></li>
              <?php while(have_rows('complete_set')){
                the_row();
                $name = get_sub_field('name');
                ?>
                <li><a href="<?php echo esc_url(get_the_permalink($product_id) . '#complete-set')?>"><?php echo $name?></a></li>
              <?php } ?>
            </ul>
        </div>
      <?php } ?>
  </div>
  <div class="products__item-bottom">
    <?php braun_price($product_id) ?>
    <a href="<?php echo esc_url(get_the_permalink($product_id))?>" class="btn btn-black"><?php _e('Дізнатися більше','braun_2025')?></a>
  </div>
</article>