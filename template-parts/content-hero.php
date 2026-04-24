<?php
$image_id = !empty($args['image_id']) ? $args['image_id'] : '' ;
$title = !empty($args['title']) ? $args['title'] : '' ;
$description = !empty($args['description']) ? $args['description'] : '' ;
$button = !empty($args['button']) ? $args['button'] : '' ;
$template = !empty($args['template']) ? $args['template'] : '' ;
$template_page = get_page_template_slug(get_the_ID());
$current_lang = !empty($args['current_lang']) ? $args['current_lang'] : '' ;
?>

<section class="hero">
  <?php if(!empty($image_id)){?>
    <div class="hero__img">
      <?php echo wp_get_attachment_image($image_id,'full',false,
        [
          'alt'   => esc_attr(get_the_title() . ' ' . $current_lang),
          'title' => esc_attr(__('Фото ', 'braun_2025') . get_the_title() . ' ' . $current_lang)
        ]);?>
    </div>
  <?php } ?>
  <div class="wrapper hero__wrapper <?php echo empty($image_id) ? 'white':'';?>">
    <div class="hero__content <?php echo $template_page == 'template-contacts.php' ? 'contact__content':''?>">
      <?php if(!empty($title)){?>
        <h1><?php echo $title ?></h1>
      <?php } ?>
      <?php if(!empty($description)){?>
        <p><?php echo $description ?></p>
      <?php } 
      if($template == true && !empty($button )){ ?>
      <button type="button" class="btn open-popup"><?php echo $button ?></button>
      <?php } else if($template == false && !empty($button)){
        if(!empty($button['url'])){?>
          <a class="btn" href="<?php echo esc_url($button['url'])?>" <?php echo !empty($button['target']) ? 'target="'.$button['target'].'"' : '';?>>
            <?php echo $button['title']?>
          </a>
        <?php } ?>
    <?php } ?>
    </div>
  </div>
</section>