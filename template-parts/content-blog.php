<?php 
$no_slider = $args['no_slider'];
$name = get_the_title();
$current_lang = $args['current_lang'];

?>

<article class="blog__item <?php echo esc_attr($no_slider == true ? '':'swiper-slide' )?>">
  <a href="<?php the_permalink() ?>" class="item-link"></a>
  <div class="blog__item-img">
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
  <?php if(!empty($args['primary_cat']['name'])) { ?>
    <span class="blog__item-cat"><?php echo $args['primary_cat']['name']?></span>
  <?php } ?> 
  <h4><?php echo $name?></h4>
  <time datetime="<?php echo esc_attr(get_the_date('Y-m-d'));?>"><?php echo get_the_date('d.m.Y');?></time>
</article>