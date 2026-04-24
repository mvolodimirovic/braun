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

$abs_recipe = !empty($abs_defaults['fields']) ? $abs_defaults['fields'] : get_acf_fields(['group_recipe'], $block['id']);
if (!empty($abs_block['data']['_is_preview'])) { ?>
<figure>
	<img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/img/recipe.png'); ?>"
		alt="<?php esc_html_e('Preview of the recipe Block', 'braun_2025'); ?>" width="100%">
</figure>
<?php } elseif ( !empty($abs_recipe['group_recipe'])) { 
  $title = $abs_recipe['group_recipe']['title'];
  $subtitle = $abs_recipe['group_recipe']['subtitle'];
  $subtitle = $abs_recipe['group_recipe']['subtitle'];
  if(have_rows('group_recipe_description', $block['id'])){?>
    <div class="recipe">
      <?php if(!empty( $title)) { ?>
        <h2><?php echo  $title ?></h2>
      <?php } 
      if(!empty($subtitle)) { ?>
        <span><?php echo $subtitle ?></span>
      <?php } ?>
      <ul class="recipe__list">
        <?php 
          while(have_rows('group_recipe_description', $block['id'])){
            the_row();
            $ing = get_sub_field('ingredient');
            $count = get_sub_field('count');
        ?>
          <li><span><?php echo $ing?></span><span><?php echo $count?></span></li>
        <?php } ?>
      </ul>
    </div>
<?php }
} 
