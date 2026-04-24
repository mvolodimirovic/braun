<?php
$cta_title = $args['cta_title'];
$cta_template = $args['cta_template'];
$cta_name_btn = $args['cta_name_btn'];
$cta_link = $args['cta_link'];
?>

<div class="cta section">
  <div class="wrapper cta__wrapper">
    <?php if(!empty($cta_title )) { ?>
      <h3><?php echo $cta_title ?></h3>
    <?php }
    if($cta_template == true && !empty($cta_name_btn )){ ?>
      <button type="button" class="btn open-popup btn-white"><?php echo $cta_name_btn ?></button>
    <?php } else if($cta_template == false && !empty($cta_link)){
      if(!empty($cta_link['url'])){?>
        <a class="btn btn-white" href="<?php echo esc_url($cta_link['url'])?>" <?php echo !empty($cta_link['target']) ? 'target="'.$cta_link['target'].'"' : '';?>>
          <?php echo $cta_link['title']?>
        </a>
      <?php } ?>
    <?php } ?>
  </div>
</div>