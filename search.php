<?php
get_header();
$current_lang = 'default';
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
    $current_lang = apply_filters( 'wpml_current_language', null );
}
global $wp_query;
$total_results = $wp_query->found_posts;

$post_types_in_results = [];
$post_ids = [];
$product_ids = [];
if (have_posts() ) {
  $index = 0;
  while (have_posts()) {
    the_post();
      $post_types_in_results[] = get_post_type($post);
      // var_dump(get_post_type());
      if(get_post_type( $post ) == "post") {
        $post_ids[] = get_the_ID();
      }
      if(get_post_type( $post ) == "product") {
        $product_ids[] = get_the_ID();
      }
    }
    $post_types_in_results = array_unique( $post_types_in_results );
} wp_reset_postdata();
?>
<section class="search-page section">
  <div class="wrapper search__wrapper">
    <h2><?php _e('Пошук по сайту','braun_2025')?></h2>
    <form method="GET" class="search-popup__form search-form" action="<?php echo home_url(); ?>">
      <input type="search" 
            name="s" 
            class="search-form__input search-input"
            required
            placeholder="<?php _e('Пошук','braun_2025')?>"
            value="<?php echo esc_attr(get_search_query())?>"
      />
      <button class="search-form__btn" type="submit">
        <svg class="search-form__btn__icon search-icon">
          <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#icon-search"></use>
        </svg>
      </button>
    </form>
    <div class="search-results">
      <?php printf(esc_html__( '%s Результати пошуку для: %s', 'braun_2025' ),'<strong>' . $total_results . '</strong>', '<strong>' . get_search_query() . '</strong>' ); ?>
    </div>
  </div>
</section>
<?php 
if(!empty($post_types_in_results) && count($post_types_in_results) > 1) { ?>
<section class="filter">
  <div class="wrapper">
    <div class="search__buttons">
      <button class="btn search-filter active" type="button" data-show="all"><?php _e('Всі','braun_2025')?></button>
      <button class="btn search-filter" type="button" data-show="products"><?php _e('Товари','braun_2025')?></button>
      <button class="btn search-filter" type="button" data-show="blog"><?php _e('Статті','braun_2025')?></button>
    </div>
  </div>
  </section>
<?php } 
if(have_posts()) {
  $post_types = [
    'product' => 'Товари',
    'post'    => 'Блог',
  ];

  foreach ( $post_types as $type => $label ) {
    $args = [
      'post_type'      => $type,
      's'              => get_search_query(),
      'posts_per_page' => 6,
    ];
    $query = new WP_Query( $args );

    if ($query->have_posts()){
      $post_type = $query->query['post_type'];
      $section_title = $post_type == 'post' ? 'Статті':'Техніка';
      $section_class = $post_type == 'post'?'blog':'products';
      $get_part = $post_type == 'post' ? 'blog':'product';
      $count_search_ids = $post_type == 'post' ? $post_ids : $product_ids;
    ?>
      <section class="<?php echo esc_attr($section_class)?> section">
        <div class="wrapper <?php echo esc_attr($section_class)?>__wrapper">
          <h3><?php _e($section_title,'braun_2025')?></h3>
          <div class="<?php echo esc_attr($section_class)?>__list <?php echo esc_attr($section_class)?>__list--show-more">
            <?php
              while ( $query->have_posts() ) {
                $query->the_post();
                  get_template_part('template-parts/content',$get_part,['no_slider'=>true,'current_lang'=>$current_lang]);
              } wp_reset_postdata();
            ?>
          </div>
          <button type="button" class="show-more" 
            data-all-ids="<?php echo esc_attr(implode(',',$count_search_ids))?>" 
            data-count="<?php echo esc_attr(count($count_search_ids)) ?>" 
            data-post-type="<?php echo esc_attr($post_type)?>">
            <?php _e('Показати більше','braun_2025')?>
          </button>
          <div class="preloder">
            <div class="loder"></div>
          </div>
        </div>
      </section>
  <?php } 
  } ?>
<?php
} else {	?>
  <div class="error__message">
    <h2><?php _e( 'На жаль, за вашим запитом нічого не знайдено. Спробуйте ввести інший запит','braun_2025'); ?></h2>
  </div>
<?php } 
get_footer();
