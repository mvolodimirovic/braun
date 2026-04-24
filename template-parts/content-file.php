<?php $file = $args['file']; 
$current_lang = $args['current_lang'];
?>

<section class="instruction section">
    <div class="instruction__item <?php echo $file['type'] == 'video' ? 'video-wrapper':'';?> ">
      <?php 
      if($file['type'] == 'video'){ ?>
        <video class="custom-video" preload="auto">
          <source src="<?php echo esc_url($file['url'])?>" type="video/mp4">
        </video>
        <button class="play-btn" aria-label="Play video">
          <svg viewBox="0 0 56 56">
            <rect width="56" height="56" rx="28" fill="white" />
            <path
              d="M32.7929 27.2929L26.2071 20.7071C25.5771 20.0771 24.5 20.5233 24.5 21.4142V34.5858C24.5 35.4767 25.5771 35.9229 26.2071 35.2929L32.7929 28.7071C33.1834 28.3166 33.1834 27.6834 32.7929 27.2929Z"
              fill="black" fill-opacity="0.98" />
          </svg>
        </button>
      <?php } else if($file['type'] == 'image') {
        echo wp_get_attachment_image($file['ID'],'full',false,
        [
          'alt'   => esc_attr(get_the_title() . ' ' . $current_lang),
          'title' => esc_attr(__('Фото ', 'braun_2025') . get_the_title() . ' ' . $current_lang)
        ]);
      }?>
    </div>
  </section>