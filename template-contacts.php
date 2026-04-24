<?php
/**
 * Template Name: Контакти
 */

get_header();
$current_lang = 'default';
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
    $current_lang = apply_filters( 'wpml_current_language', null );
}
$hero_image = get_post_thumbnail_id( get_queried_object_id() );
$hero_title = get_field('title');
$hero_description = get_field('short_descr');
$hero_template = get_field('template');
$hero_btn = $hero_template == true ? get_field('name_btn') : get_field('btn');
$hero_args = [
  'image_id'=> $hero_image,
  'title' => $hero_title,
  'description'=> $hero_description,
  'template'=> $hero_template,
  'button'=> $hero_btn,
  'current_lang' => $current_lang,
 ];
$address = get_field('address');
$lat = $address['lat'];
$lng = $address['lng'];
$phone = get_field('phone');
$email = get_field('email');
$facebook = get_field('facebook');
$instagram = get_field('instagram');
$youtube = get_field('youtube');

$partner_title = get_field('partner_title');
$description = get_field('description');
$bottom_title = get_field('bottom-title');

$cta_title = get_field('cta_title');
$cta_btn = get_field('cta_btn');

get_template_part('/template-parts/content','hero',$hero_args);

if (function_exists('woocommerce_breadcrumb')) {
  woocommerce_breadcrumb([
      'delimiter'   => ' › ',
      'wrap_before' => '<nav class="wrapper woocommerce-breadcrumb breadcrumbs__wrapper">',
      'wrap_after'  => '</nav>',
      'before'      => '',
      'after'       => '',
      'home'        => _x('Home', 'breadcrumb', 'woocommerce'),
  ]);
} ?>
<section class="contacts section">
  <div class="wrapper contacts__wrapper">
    <div class="contacts__list">
      <?php if(!empty($address)) { ?>
        <article class="contacts__item">
          <div class="contacts__item-img">
            <svg class="slider-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#map-icon"></use>
            </svg>
          </div>
          <div class="contacts__item-info">
            <h3><?php _e('Адреса офісу','braun_2025') ?></h3>
            <address>
              <?php echo $address['address']?>
            </address>
          </div>
        </article>
      <?php } 
      if(!empty($phone)) {
        $clean = preg_replace('/\D/', '', $phone); ?>
        <article class="contacts__item">
          <div class="contacts__item-img">
            <svg class="slider-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#phone-icon"></use>
            </svg>
          </div>
          <div class="contacts__item-info">
            <h3> <?php _e('Телефон','braun_2025') ?> </h3>
            <a href="tel:<?php echo $clean ?>">
              <?php echo $phone ?>
            </a>
          </div>
        </article>
      <?php } 
      if(!empty($phone)) { ?>
        <article class="contacts__item">
          <div class="contacts__item-img">
            <svg class="slider-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#email-icon"></use>
            </svg>
          </div>
          <div class="contacts__item-info">
            <h3><?php _e('Електронна пошта','braun_2025') ?></h3>
            <a href="mailto:<?php echo $email ?>">
              <?php echo $email ?>
            </a>
          </div>
        </article>
      <?php } 
      if(!empty($facebook) || !empty($instagram) || !empty($youtube)) { ?>
        <article class="contacts__item social">
          <?php if(!empty($facebook)) { ?>
            <a href="<?php echo esc_url($facebook)?>" class="contacts__item__social" target="_blank">
              <svg class="slider-icon">
                <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#facebook-icon"></use>
              </svg>
            </a>
          <?php } 
          if(!empty($instagram)) { ?>
          <a href="<?php echo esc_url($instagram)?>" class="contacts__item__social" target="_blank">
            <svg class="slider-icon">
              <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#instagram-icon"></use>
            </svg>
          </a>
          <?php } 
          if(!empty($youtube)) { ?>
            <a href="<?php echo esc_url($youtube)?>" class="contacts__item__social" target="_blank">
              <svg class="slider-icon">
                <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#youtube-icon"></use>
              </svg>
            </a>
          <?php } ?>
        </article>
      <?php } ?>
    </div>
  </div>
</section>
<?php if(!empty($lat) && !empty($lng)) { ?>
<section class="map section">
  <iframe src="https://www.google.com/maps?q=<?php echo $lat?>,<?php echo $lng ?>&z=14&output=embed" width="600" height="500"
    style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
  </iframe>
</section>
<?php } 
if(have_rows('advantages')) { ?>
  <section class="partnership section">
    <div class="wrapper partnership__wrapper">
      <?php if(!empty($partner_title)) { ?>
        <h2><?php echo $partner_title ?></h2>
      <?php } 
      if(!empty($description)){?>
        <p><?php echo $description ?></p>
      <?php } ?>
      <div class="partnership__list">
        <?php while(have_rows('advantages')) {
          the_row();
          $img = get_sub_field('image');
          $title = get_sub_field('title');
          $short_descr = get_sub_field('short_descr');
          ?>
          <div class="partnership__item">
            <?php if(!empty($img)){?>
              <div class="partnership__item-img">
                <?php echo wp_get_attachment_image($img,'cat_image',
                false,
                [
                  'alt'   => esc_attr($title . ' ' . $current_lang),
                  'title' => esc_attr(__('Фото ', 'braun_2025') . $title . ' ' . $current_lang)
                ]);?>
              </div>
            <?php } 
            if(!empty( $title)){?>
              <h4><?php echo $title ?></h4>
            <?php } 
            if(!empty($short_descr)){?>
              <p><?php echo $short_descr ?></p>
            <?php } ?>
          </div>
        <?php } ?>
      </div>
      <?php if(!empty($bottom_title)) { ?>
        <h3><?php echo $bottom_title ?></h3>
      <?php } ?>
      <button type="button" class="btn open-popup btn-black"><?php _e('Заповнити форму','braun_2025')?></button>
    </div>
  </section>
<?php } ?>
<?php if(!empty($cta_btn)) { ?>
  <div class="cta section">
    <div class="wrapper cta__wrapper">
      <?php if(!empty($cta_title)) { ?>
        <h3><?php echo $cta_title ?></h3>
      <?php } ?>
      <a href="<?php echo esc_url($cta_btn['url'])?>" class="btn btn-white"><?php echo $cta_btn['title']?></a>
    </div>
  </div>
<?php }

get_footer();