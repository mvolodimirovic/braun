<?php 
$abs_block = isset($block) ? $block : '';
$abs_args  = isset($args) ? $args : '';

$abs_defaults = [
	'show_arrows'         => true,
	'show_pagination'     => true,
	'allowed_innerblocks' => [''],
	'id'                  => (isset($block) && !empty($block['anchor'])) ? $block['anchor'] : '',
	'fields'              => [],
];

[$abs_defaults, $abs_atts] = setup_block_defaults($abs_args, $abs_defaults, $abs_block);

$abs_promo = !empty($abs_defaults['fields']) ? $abs_defaults['fields'] : get_acf_fields(['promo_list'], $block['id']);

if (!empty($abs_block['data']['_is_preview'])) { ?>
<figure>
	<img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/promo.png'); ?>"
		alt="<?php esc_html_e('Preview of the Promo Block', 'braun_2025'); ?>" width="100%">
</figure>
<?php } elseif ( !empty($abs_promo['promo_list'])) { 
  if(have_rows('promo_list', $block['id'])){?>
  <section class="promo section">
    <div class="wrapper promo__wrapper">
      <?php
      $index = 0;
      while(have_rows('promo_list', $block['id'])){
        $index++;
        the_row(); 
        $image = get_sub_field('image');
        $title = get_sub_field('title');
        $template = get_sub_field('popaporlink');
        $button = $template == true ? get_sub_field('btn_text') : get_sub_field('button_link');
        ?>
        <div class="promo__item <?php echo $index < 3 ? 'half':'full';  echo empty($title) && $template == true ? ' only-img':'';?>">
          <?php if(!empty($image)){?>
            <div class="promo__item-img">
              <?php echo wp_get_attachment_image($image,'full'); ?>
            </div>
          <?php } 
          if(!empty($title)){?>
            <h3><?php echo $title ?></h3>
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

} 
