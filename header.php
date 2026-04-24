<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
<header id="header" class="header">
  <div class="wrapper header__wrapper">
    <div class="header__logo">
      <a href="<?php echo esc_url(site_url())?>">
        <svg class="header__logo-icon">
          <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
        </svg>
      </a>
    </div>
    
    <?php if(has_nav_menu('top_menu')){?>
      <div class="header__nav">
        <nav class="header__menu">
          <?php
            wp_nav_menu(array(
              'theme_location' => 'top_menu',
              'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
              'menu_class' => 'menu header__menu-list',
              'container'  => false,
              'walker'        => new Braun_Category_Nav_menu(),
            ));
          ?>
        </nav>
      </div>
    <?php } ?>
    
    <div class="header__controls">
      <div class="header__lang">
        <a class="header__lang-item" href="#">Ua</a>
        <a class="header__lang-item" href="#">En</a>
      </div>
      <div class="header__search">
        <svg class="header__search-icon">
          <use xlink:href="<?php echo get_template_directory_uri() ?>/img/path-icon.svg#icon-search"></use>
        </svg>
        <div class="header__search-block search-popup">
          <div class="search-popup__wrap">   
            <div class="form-wrap">
              <h3><?php _e('Пошук по сайту','braun_2025')?></h3>  
              <form method="GET" class="search-popup__form search-form" action="<?php echo home_url(); ?>">
                <input type="search" 
                      name="s" 
                      class="search-form__input search-input"
                      required
                      placeholder="<?php _e('Пошук','braun_2025')?>"
                      value=""
                />
                <button type="reset" class="search-form__reset hide">
                  <svg class="icon-close">
                    <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#icon-close"></use>
                  </svg>
                </button>
                <button class="search-form__btn" type="submit">
                  <svg class="search-form__btn__icon search-icon">
                    <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#icon-search"></use>
                  </svg>
                </button>
              </form>
            </div> 
            <button class="search-popup__btn-close" type="button">
              <svg class="search-popup__btn-close__icon icon-close">
                <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#icon-close"></use>
              </svg>
            </button>
          </div>
          <div class="search-popup__bg"></div>
        </div>
      </div>
      <div class="header__burger">
        <div class="header__burger-wrap">
          <span></span>
          <span></span>
          <span></span>
        </div>
        <?php _e('Меню','braun_2025')?>
      </div>
      <div class="header__mobile header-mobile">
        <div class="header-mobile-wrap">
          <?php if(has_nav_menu('top_menu')) { ?>
            <div class="header__nav">
              <nav class="header__menu">
                <?php
                  wp_nav_menu(array(
                    'theme_location' => 'top_menu',
                    'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'menu_class' => 'menu header__menu-list',
                    'container'  => false,
                    'walker'        => new Braun_Category_Nav_menu(),
                  ));
                ?>
              </nav>
            </div>
          <?php } ?>
          <div class="header__lang">
            <a class="header__lang-item" href="#">Ua</a>
            <a class="header__lang-item" href="#">Ru</a>
          </div>
        </div>
        <div class="header-mobile__bg"></div>
      </div>
    </div>
  </div>
</header>

  <main id="main" class="main">