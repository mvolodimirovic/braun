<?php 
$title = !empty($args['title']) ? $args['title'] : '' ;
$description = !empty($args['description']) ? $args['description'] : '' ;
$mission_list = !empty($args['list']) ? $args['list'] : '' ;
$current_lang = !empty($args['current_lang']) ? $args['current_lang'] :'';
?>

<section class="section mission">
  <div class="wrapper mission__wrapper">
    <?php if(!empty($title)){?>
      <h2><?php echo $title ?></h2>
    <?php } 
    if(!empty($description)) { ?>
      <p><?php echo $description ?></p>
    <?php } 
    if(have_rows($mission_list)){
    ?>
      <div class="mission__list">
        <?php 
        while(have_rows($mission_list)){
          the_row();
          $image = get_sub_field('image');
          $title = get_sub_field('title');
          $short_descr = get_sub_field('short_descr');
        ?>
          <div class="mission__item">
            <?php if(!empty($image)) { ?>
              <div class="mission__item-svg">
                  <?php echo wp_get_attachment_image($image,'thumbnail',false,
                [
                  'alt'   => esc_attr($title . ' ' . $current_lang),
                  'title' => esc_attr(__('Фото ', 'braun_2025') . $title . ' ' . $current_lang)
                ])?>
              </div>
            <?php } ?>
            <div class="mission__item-info">
              <?php if(!empty($title)){?>
                <h4><?php echo $title ?></h4>
              <?php } 
              if(!empty($short_descr)){?>
                <span><?php echo $short_descr ?></span>
              <?php } ?>
            </div>
          </div>
        <?php } ?>
      </div>
    <?php } ?>
  </div>
</section>