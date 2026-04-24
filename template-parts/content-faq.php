<?php

$faq_title = $args['title'];
$faq_list = $args['faq_list'];

if(have_rows($faq_list)) {
?>
<section class="faq section">
  <div class="wrapper faq__wrapper">
    <?php if(!empty($faq_title)) { ?>
      <h2><?php echo $faq_title?></h2>
    <?php } ?>
    <div class="faq__list">
      <?php 
      while(have_rows($faq_list)) {
        the_row();
        $ask = get_sub_field('faq_ask');
        $answere = get_sub_field('faq_answere');
      ?>
        <div class="faq__details">
          <?php if(!empty($ask)) { ?>
            <div class="faq__summary">
                <p><?php echo $ask ?></p>
              <div class="faq__icon">
                <svg class="slider-icon">
                  <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#slider-arrow"></use>
                </svg>
              </div>
            </div>
          <?php } 
          if(!empty($answere)) { ?>
            <div class="faq__text" style="display: none;">
              <p class="hide-content"><?php echo $answere?></p>
            </div>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  </div>
</section>
<?php }