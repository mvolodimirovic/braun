<?php
$head_terms_title = get_field('head_terms_title','option');
$head_terms_list = get_field('head_terms_list','option');
$list = $args['sub_cat'];
$current_lang = $args['current_lang'];

if(!empty($head_terms_list)){?>

  <section id="popular" class="instructions section">
    <div class="wrapper instructions__wrapper">
      <?php if(!empty($head_terms_title)){?>
        <h2><?php echo $head_terms_title ?></h2>
      <?php } ?>
      <div class="instructions__list">
        <?php
        foreach($head_terms_list as $term){
          $term_id = $term->term_id;
          $thumbnail_id = get_term_meta( $term_id, 'thumbnail_id', true );
          $term_name = $term->name;
        ?>
          <div class="instructions__item">
            <a href="<?php echo esc_url(get_term_link($term_id))?>" class="item-link"></a>
            <div class="instructions__item-img">
              <?php echo wp_get_attachment_image($thumbnail_id,'cat_image',false,
                [
                  'alt'   => esc_attr($term_name . ' ' . $current_lang),
                  'title' => esc_attr(__('Фото ', 'braun_2025') . $term_name . ' ' . $current_lang)
                ])?>
            </div>
            <h4><?php echo $term->name ?></h4>
            <?php 
              $sub_cat = get_terms([
                'taxonomy'   => 'product_cat',
                'parent'     => $term_id,
                'hide_empty' => true,
              ]);
              if(!empty($sub_cat) && $list !== false) { ?>
                <ul class="catalog__categories">
                  <?php 
                  foreach ($sub_cat as $sub_term) { ?>
                    <li><a href="<?php echo esc_url(get_term_link($sub_term->term_id))?>" class="sub-cat-link"><?php echo $sub_term->name ?></a></li>
                  <?php } ?>
                </ul>
              <?php } ?>
          </div>
        <?php } ?>
      </div>
    </div>
  </section>
<?php } ?>