<?php
$phone_1 = get_field('phone_1','option');
$phone_2 = get_field('phone_2','option');
$email = get_field('email','option');
$address = get_field('address','option');
$twitter = get_field('twitter','option');
$facebook = get_field('facebook','option');
$youtube = get_field('youtube','option');
?>
  </main>
	<footer id="footer" class="footer">
    <div class="wrapper footer__wrapper">
      <a href="<?php echo esc_url(site_url())?>" class="footer__logo">
        <svg class="footer__logo-icon">
          <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#logo"></use>
        </svg>
      </a>
      <div class="footer__nav">
        <div class="footer-quarter">
          <h5><?php _e('Навігація','braun_2025')?></h5>
          <?php if(has_nav_menu('footer_menu')){?>
                <nav class="footer__menu">
                  <?php
                    wp_nav_menu(array(
                      'theme_location' => 'footer_menu',
                      'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                      'menu_class' => 'menu footer__menu-list',
                      'container'  => false,
                    ));
                  ?>
                </nav>
            <?php } ?>
        </div>
        <div class="footer-quarter large">
          <h5><?php _e('Категорії','braun_2025')?></h5>
          <?php if(has_nav_menu('footer_category')){?>
                <nav class="footer__menu">
                  <?php
                    wp_nav_menu(array(
                      'theme_location' => 'footer_category',
                      'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                      'menu_class' => 'menu footer__menu-list',
                      'container'  => false,
                      'walker'        => new Braun_Footer_Category_Nav_menu(),
                    ));
                  ?>
                </nav>
            <?php } ?>
        </div>
        <div class="footer-quarter">
          <h5><?php _e('Контакти','braun_2025')?></h5>
          <ul class="footer__contacts">
            <?php if(!empty($phone_1)){?>
              <li><a href="tel:<?php echo preg_replace('/\D/', '', $phone_1)?>"><?php echo $phone_1 ?></a></li>
            <?php } ?>
            <?php if(!empty($phone_2)){?>
              <li><a href="tel:<?php echo preg_replace('/\D/', '', $phone_2)?>"><?php echo $phone_2 ?></a></li>
            <?php } ?>
            <?php if(!empty($email)){?>
              <li><a href="mailto:<?php echo $email?>"><?php echo $email ?></a></li>
            <?php } 
            if(!empty($address)){ ?>
              <li><address><?php echo $address ?></address></li>
            <?php } ?>
          </ul>
        </div>
      </div>
      <div class="footer__controls">
          <div class="footer__lang">
            <a href="/">Ua</a>
            <span>Ukraine</span>
          </div>
          <div class="footer__social">
            <?php if(!empty($twitter)){?>
              <a href="<?php echo esc_url($twitter)?>" class="footer__social__item">
                <svg width="24" height="24" viewBox="0 0 24 24">
                  <g clip-path="url(#clip0_454_18555)">
                  <path d="M12 0C5.37281 0 0 5.37281 0 12C0 18.6272 5.37281 24 12 24C18.6272 24 24 18.6272 24 12C24 5.37281 18.6272 0 12 0Z" />
                  <path d="M13.312 10.914L18.449 4.94263H17.2317L12.7712 10.1275L9.20864 4.94263H5.09961L10.4869 12.7831L5.09961 19.045H6.317L11.0274 13.5696L14.7898 19.045H18.8988L13.3117 10.914H13.312ZM6.75563 5.85907H8.62546L17.2323 18.1703H15.3625L6.75563 5.85907Z" fill="white"/>
                  </g>
                  <defs>
                  <clipPath id="clip0_454_18555">
                  <rect width="24" height="24"/>
                  </clipPath>
                  </defs>
                </svg>
              </a>
            <?php } 
            if(!empty($facebook)){?>
              <a href="<?php echo esc_url($facebook)?>" class="footer__social__item">
                <svg width="24" height="24" viewBox="0 0 24 24">
                  <g clip-path="url(#clip0_454_18635)">
                  <path d="M12 0C5.38323 0 0 5.38323 0 12C0 18.6163 5.38323 24 12 24C18.6163 24 24 18.6163 24 12C24 5.38323 18.6173 0 12 0ZM14.9843 12.4225H13.032V19.381H10.139C10.139 19.381 10.139 15.5788 10.139 12.4225H8.76388V9.9631H10.139V8.37235C10.139 7.23306 10.6804 5.45283 13.0586 5.45283L15.2023 5.46105V7.84838C15.2023 7.84838 13.8996 7.84838 13.6463 7.84838C13.3931 7.84838 13.0329 7.97503 13.0329 8.51833V9.96359H15.2371L14.9843 12.4225Z" />
                  </g>
                  <defs>
                  <clipPath id="clip0_454_18635">
                  <rect width="24" height="24"/>
                  </clipPath>
                  </defs>
                </svg>
              </a>
            <?php } 
            if(!empty($youtube)) { ?>
              <a href="<?php echo esc_url($youtube)?>" class="footer__social__item">
                <svg width="24" height="24" viewBox="0 0 24 24">
                  <g clip-path="url(#clip0_454_18587)">
                  <path d="M10.3359 13.9358L13.6644 12.0047L10.3359 10.0642V13.9358Z" />
                  <path d="M12 0C9.62663 0 7.30655 0.703788 5.33316 2.02236C3.35977 3.34094 1.8217 5.21509 0.913451 7.4078C0.00519938 9.60051 -0.232441 12.0133 0.230582 14.3411C0.693605 16.6689 1.83649 18.8071 3.51472 20.4853C5.19295 22.1635 7.33115 23.3064 9.65892 23.7694C11.9867 24.2324 14.3995 23.9948 16.5922 23.0866C18.7849 22.1783 20.6591 20.6402 21.9776 18.6668C23.2962 16.6935 24 14.3734 24 12C24 8.8174 22.7357 5.76516 20.4853 3.51472C18.2348 1.26428 15.1826 0 12 0ZM18.3158 14.1237C18.316 14.4375 18.2544 14.7484 18.1344 15.0384C18.0144 15.3284 17.8385 15.5919 17.6166 15.8139C17.3948 16.0359 17.1314 16.2121 16.8414 16.3322C16.5515 16.4524 16.2407 16.5142 15.9268 16.5142H8.07316C7.75931 16.5142 7.44853 16.4524 7.15859 16.3322C6.86864 16.2121 6.60522 16.0359 6.38336 15.8139C6.16151 15.5919 5.98557 15.3284 5.86561 15.0384C5.74565 14.7484 5.68401 14.4375 5.68422 14.1237V9.87632C5.68401 9.56246 5.74565 9.25164 5.86561 8.96162C5.98557 8.6716 6.16151 8.40806 6.38336 8.18605C6.60522 7.96405 6.86864 7.78794 7.15859 7.66779C7.44853 7.54763 7.75931 7.48579 8.07316 7.48579H15.9268C16.2407 7.48579 16.5515 7.54763 16.8414 7.66779C17.1314 7.78794 17.3948 7.96405 17.6166 8.18605C17.8385 8.40806 18.0144 8.6716 18.1344 8.96162C18.2544 9.25164 18.316 9.56246 18.3158 9.87632V14.1237Z" />
                  </g>
                  <defs>
                  <clipPath id="clip0_454_18587">
                  <rect width="24" height="24"/>
                  </clipPath>
                  </defs>
                </svg>
              </a>
              <?php } ?>
          </div>
      </div>
      <div class="footer__bottom">
        <p>
          <svg width="15" height="14" viewBox="0 0 15 14">
            <g clip-path="url(#clip0_441_3422)">
            <path d="M7.5 0C6.11553 0 4.76216 0.410543 3.61101 1.17971C2.45987 1.94888 1.56266 3.04213 1.03285 4.32122C0.503033 5.6003 0.36441 7.00777 0.634506 8.36563C0.904603 9.7235 1.57129 10.9708 2.55026 11.9497C3.52922 12.9287 4.7765 13.5954 6.13437 13.8655C7.49224 14.1356 8.8997 13.997 10.1788 13.4672C11.4579 12.9373 12.5511 12.0401 13.3203 10.889C14.0895 9.73785 14.5 8.38447 14.5 7C14.498 5.1441 13.7599 3.36479 12.4475 2.05247C11.1352 0.74015 9.3559 0.0020073 7.5 0ZM7.5 12.8333C6.34628 12.8333 5.21846 12.4912 4.25918 11.8502C3.29989 11.2093 2.55222 10.2982 2.11071 9.23232C1.66919 8.16642 1.55368 6.99353 1.77876 5.86197C2.00384 4.73042 2.55941 3.69102 3.37521 2.87521C4.19102 2.0594 5.23042 1.50383 6.36198 1.27875C7.49353 1.05367 8.66642 1.16919 9.73232 1.6107C10.7982 2.05221 11.7093 2.79989 12.3502 3.75917C12.9912 4.71846 13.3333 5.84628 13.3333 7C13.3316 8.54658 12.7165 10.0293 11.6229 11.1229C10.5293 12.2165 9.04658 12.8316 7.5 12.8333Z"  fill-opacity="0.98"/>
            <path d="M9.56367 4.93766C9.67369 5.04392 9.82104 5.10272 9.97399 5.10139C10.1269 5.10006 10.2732 5.03871 10.3814 4.93055C10.4896 4.8224 10.5509 4.67609 10.5522 4.52314C10.5536 4.3702 10.4948 4.22285 10.3885 4.11283C9.81743 3.5418 9.08985 3.15295 8.29777 2.99542C7.50569 2.8379 6.68469 2.91879 5.93858 3.22786C5.19247 3.53693 4.55477 4.0603 4.10611 4.73179C3.65744 5.40328 3.41797 6.19274 3.41797 7.00033C3.41797 7.80792 3.65744 8.59737 4.10611 9.26886C4.55477 9.94035 5.19247 10.4637 5.93858 10.7728C6.68469 11.0819 7.50569 11.1628 8.29777 11.0052C9.08985 10.8477 9.81743 10.4589 10.3885 9.88783C10.4948 9.77781 10.5536 9.63046 10.5522 9.47751C10.5509 9.32456 10.4896 9.17826 10.3814 9.0701C10.2732 8.96195 10.1269 8.9006 9.97399 8.89927C9.82104 8.89794 9.67369 8.95674 9.56367 9.06299C9.15574 9.47082 8.63604 9.74853 8.07028 9.86101C7.50452 9.97348 6.91812 9.91568 6.38521 9.6949C5.85231 9.47412 5.39683 9.10029 5.07638 8.62066C4.75592 8.14103 4.58488 7.57716 4.58488 7.00033C4.58488 6.4235 4.75592 5.85962 5.07638 5.37999C5.39683 4.90037 5.85231 4.52653 6.38521 4.30575C6.91812 4.08497 7.50452 4.02717 8.07028 4.13965C8.63604 4.25213 9.15574 4.52983 9.56367 4.93766Z"  fill-opacity="0.98"/>
            </g>
            <defs>
            <clipPath id="clip0_441_3422">
            <rect width="14" height="14" fill="white" transform="translate(0.5)"/>
            </clipPath>
            </defs>
          </svg>
          Braun. <?php echo date('Y')?>, Усі права захищені.
        </p>
        <?php if(has_nav_menu('footer_bottom')){?>
          <nav class="footer_bottom">
            <?php
              wp_nav_menu(array(
                'theme_location' => 'footer_bottom',
                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'menu_class' => 'footer__privacy-policy__group',
                'container'  => false,
              ));
            ?>
          </nav>
      <?php } ?>
        <!-- <ul class="footer__privacy-policy__group">
          <li><a href="#">Політика конфіденційності</a></li>
          <li><a href="#">Cookies</a></li>
          <li><a href="#">Про Braun</a></li>
          <li><a href="#">De’Longhi Group</a></li>
        </ul> -->
      </div>
    </div>

	</footer>
</div>
<div id="popup-form" class="popup">
    <div class="popup-content">
      <div class="popup-wrapper">
        <h3>
          <?php _e('Заповніть форму, щоб залишатися на зв’язку із нами!','braun_2025')?>
        </h3>
        <button type="button" class="popup-close link-hover">
          <span class="link-hover-wrapper">
            <svg>
              <use xlink:href="<?php echo get_template_directory_uri()?>/img/path-icon.svg#icon-close"></use>
            </svg>
          </span>
        </button>
        <div class="popup-form">
          <?php echo do_shortcode('[contact-form-7 id="ce5f6f2" title="CTA Form"]');?>
        </div>
        <div class="loader-wrapper">
          <div class="loader"></div>
        </div>
      </div>
    </div>
  </div>
<?php wp_footer(); ?>

</body>
</html>
