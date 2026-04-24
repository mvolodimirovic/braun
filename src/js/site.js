jQuery( function ( $ ) {
	const Scripts = {
		clickOnHeader() {
			let header = $( '#header' );
			searchShowBlock = header.find( '.header__search-icon' );

			searchShowBlock.on( 'click', function ( event ) {
				event.preventDefault();
				$( '.search-popup' ).addClass( 'search-popup--show' );
				$( '.search-popup__bg' ).addClass( 'show' );
				$( '.search-popup__wrap' ).addClass( 'show' );
				$( 'body' ).addClass( 'no-scroll' );
				header.find( '.search-form__input' ).focus();
				$( '.header__burger' ).removeClass( 'active' );
				$( '#header' ).removeClass( 'white' );
				$( '.header-mobile' ).removeClass( 'header-mobile--show' );
			} );
			$( document ).on( 'click', function ( e ) {
				if (
					! $( '.form-wrap' ).is( e.target ) &&
					$( '.form-wrap' ).has( e.target ).length === 0 &&
					! $( '.header__search-icon' ).is( e.target ) &&
					! $( '.header__burger' ).is( e.target ) &&
					$( '.header__burger' ).has( e.target ).length === 0 &&
					! $( '.header-mobile-wrap' ).is( e.target ) &&
					$( '.header-mobile-wrap' ).has( e.target ).length === 0 &&
					! $( '.open-popup' ).is( e.target ) &&
					$( '#popup-form' ).has( e.target ).length === 0
				) {
					$( 'body' ).removeClass( 'no-scroll' );
					$( '.header__search-block' ).removeClass(
						'search-popup--show'
					);
					setTimeout( function () {
						$( '.search-popup' ).removeClass(
							'search-popup--show'
						);
					}, 300 );
					$( '.search-popup__bg' ).removeClass( 'show' );
					$( '.search-popup__wrap' ).removeClass( 'show' );
				}
			} );
			$( '.header__burger' ).on( 'click', function ( event ) {
				event.preventDefault();
				if ( $( this ).hasClass( 'active' ) ) {
					$( 'body' ).removeClass( 'no-scroll' );
					$( '.header-mobile' ).removeClass( 'header-mobile--show' );
					$( '#header' ).removeClass( 'white' );
					$( this ).removeClass( 'active' );
				} else {
					$( 'body' ).addClass( 'no-scroll' );
					$( '.header-mobile' ).addClass( 'header-mobile--show' );
					$( '#header' ).addClass( 'white' );
					$( this ).addClass( 'active' );
				}
			} );

			$( '.header-mobile__bg' ).on( 'click', function ( event ) {
				event.preventDefault();
				$( '.header-mobile' ).removeClass( 'header-mobile--show' );
				$( 'body' ).removeClass( 'no-scroll' );
				$( '.menu-item-has-children' ).removeClass(
					'active-item-link'
				);
				$( '.header__burger' ).removeClass( 'active' );
				$( '#header' ).removeClass( 'white' );
			} );
		},
		menuCategoryProduct() {
			let level = 1;
			if ( $( window ).width() < 767 ) {
				level = 0;
				$( '.level-0>.menu-item' ).on( 'click', function ( e ) {
					// e.stopPropagation();
          e.preventDefault();
					let clearBlock = $( '.sub-menu-preview' );
					let $this = $( this );
					if ( $( this ).hasClass( 'active' ) ) {
						$( this ).removeClass( 'active' );
						clearBlock.html( '' ).removeAttr( 'data-loaded' );
					} else {
						$( '.level-0>.menu-item' ).removeClass( 'active' );
						$( this ).addClass( 'active' );
						loadAjaxProductMenu( $this );
					}
					$( '.sub-menu.level-1' ).css( {
						'max-height': 0 + 'px',
						opacity: 0,
					} );
					if ( $( this ).hasClass( 'active' ) ) {
						$( this )
							.find( '.sub-menu.level-1' )
							.css( { 'max-height': 370 + 'px', opacity: 1 } );
					}
				} );
				$( '.default-item a' ).on( 'click', function ( e ) {
					e.preventDefault();
					$( this ).parent( 'li' ).hover();
				} );
			} else {
				$( '.level-0>.menu-item' ).on( 'mouseenter', function () {
					$( '.level-0>.menu-item' ).removeClass( 'active' );
					$( this ).addClass( 'active' );
					$( '.sub-menu.level-1' ).css( {
						'max-height': 0 + 'px',
						opacity: 0,
					} );
					$( this )
						.find( '.sub-menu.level-1' )
						.css( { 'max-height': 370 + 'px', opacity: 1 } );
				} );
				const $categoryBtn = $( '.braun__category' );
				const $menuWrapper = $( '.braun__menu-wrapper' );
				let isHovering = false;
				$categoryBtn.on( 'mouseenter', function () {
					isHovering = true;
					$( this ).addClass( 'active' );
					$( '#header' ).addClass( 'white' );
					$menuWrapper.addClass( 'visible' );
				} );
				$categoryBtn.add( $menuWrapper ).on( 'mouseenter', function () {
					isHovering = true;
				} );

				$categoryBtn.add( $menuWrapper ).on( 'mouseleave', function () {
					isHovering = false;
					setTimeout( () => {
						if ( ! isHovering ) {
							$categoryBtn.removeClass( 'active' );
							$( '#header' ).removeClass( 'white' );
							$menuWrapper.removeClass( 'visible' );
						}
					}, 100 );
				} );
				$( '.level-1 .menu-item' ).on( 'mouseenter', function () {
					$this = $( this );
					loadAjaxProductMenu( $this );
				} );
			}
			function loadAjaxProductMenu( $this ) {
				let categoryId = $( $this ).data( 'id' );
				let previewBlock = $( '.sub-menu-preview' );

				previewBlock
					.removeClass( 'loaded' )
					.html( '' )
					.removeAttr( 'data-loaded' );

				$.ajax( {
					type: 'POST',
					url: '/wp-admin/admin-ajax.php',
					data: {
						action: 'get_category_product',
						category_id: categoryId,
					},
					success: function ( response ) {
						if ( response !== '' ) {
							let data = JSON.parse( response );
							previewBlock
								.html( data.product )
								.attr( 'data-loaded', categoryId )
								.fadeIn( 600 );
						} else {
							previewBlock.html( '' ).removeAttr( 'data-loaded' );
						}
					},
					error: function () {
						console.error( 'Помилка завантаження даних.' );
					},
				} );
			}
			$( '.braun__category>button' ).on( 'click', function ( e ) {
				e.preventDefault();
				$( this ).parent().toggleClass( 'active' );
				$( '#header' ).addClass( 'white' );
			} );
			$( '.menu__category-icon' ).on( 'click', function ( event ) {
				event.preventDefault();
				$( '.braun__category' ).removeClass( 'active' );
				$( '#header' ).removeClass( 'white' );
			} );
		},
		catalogSlider() {
			const mySwiper = new Swiper( '.slider-catalog', {
				slidesPerView: 4,
				spaceBetween: 20,
				speed: 1000,
				grid: {
					rows: 2,
					fill: 'row',
				},
				navigation: {
					nextEl: '.catalog-button-next',
					prevEl: '.catalog-button-prev',
				},
				breakpoints: {
					320: {
						slidesPerView: 1.133,
						spaceBetween: 20,
						grid: {
							rows: 1,
							fill: 'row',
						},
					},
					575: {
						slidesPerView: 3,
						spaceBetween: 20,
						grid: {
							rows: 2,
							fill: 'row',
						},
					},
					768: {
						slidesPerView: 4,
						grid: {
							rows: 2,
							fill: 'row',
						},
					},
					1024: {
						slidesPerView: 4,
						grid: {
							rows: 2,
							fill: 'row',
						},
					},
				},
				pagination: {
					el: '.swiper-progress',
					type: 'progressbar',
				},
			} );
		},
		productsSlider() {
			const productsSwiper = new Swiper( '.products-slider', {
				slidesPerView: 3,
				spaceBetween: 20,
				speed: 1000,
				autoHeight: false,
				grid: {
					rows: 2,
					fill: 'row',
				},
				navigation: {
					nextEl: '.products-button-next',
					prevEl: '.products-button-prev',
				},
				breakpoints: {
					320: {
						slidesPerView: 1,
						spaceBetween: 20,
						grid: {
							rows: 1,
							fill: 'row',
						},
					},
					575: {
						slidesPerView: 1.5,
						spaceBetween: 20,
						grid: {
							rows: 2,
							fill: 'row',
						},
					},
					768: {
						slidesPerView: 2,
						grid: {
							rows: 2,
							fill: 'row',
						},
					},
					1024: {
						slidesPerView: 3,
						grid: {
							rows: 2,
							fill: 'row',
						},
					},
				},
				pagination: {
					el: '.swiper-progress',
					type: 'progressbar',
				},
			} );
		},
		updateCategoryItem() {
			let button = $( '.products__categories' ).find( 'button' );
			button.on( 'click', function ( e ) {
				e.preventDefault();
				$( '.products__categories' )
					.find( 'button' )
					.removeClass( 'active' );
				$( this ).addClass( 'active' );
			} );
		},
		mediaSlider() {
			const mediaSwiper = new Swiper( '.media-slider', {
				slidesPerView: 1,
				spaceBetween: 0,
				speed: 1000,
				pagination: {
					el: '.media-slider-pagination',
					type: 'fraction',
					formatFractionCurrent: function ( number ) {
						return number < 10 ? `0${ number }` : number;
					},
					formatFractionTotal: function ( number ) {
						return number < 10 ? `0${ number }` : number;
					},
					renderFraction: ( currentClass, totalClass ) => {
						return `<span class="${ currentClass }"></span><span class="line"></span><span class="${ totalClass }"></span>`;
					},
				},
				loop: true,
				autoplay: {
					delay: 2000,
					disableOnInteraction: false,
				},
			} );
		},
		blogSlider( item ) {
			let count = item == 'gallery' ? 1.5 : 3;
			let countLaptop = item == 'gallery' ? 1 : 3;
			let countlargeMobile = item == 'gallery' ? 1 : 2;
			let countMobile = item == 'gallery' ? 1 : 1.133;
			const mySwiper = new Swiper( '.' + item + '-slider', {
				slidesPerView: count,
				spaceBetween: 20,
				speed: 1500,
				navigation: {
					nextEl: '.' + item + '-button-next',
					prevEl: '.' + item + '-button-prev',
				},
				breakpoints: {
					320: {
						slidesPerView: countMobile,
						spaceBetween: 20,
					},
					575: {
						slidesPerView: countlargeMobile,
						spaceBetween: 20,
					},
					768: {
						slidesPerView: countLaptop,
					},
					1024: {
						slidesPerView: count,
					},
				},
				pagination: {
					el: '.swiper-progress',
					type: 'progressbar',
				},
			} );
		},
		detailsSlow() {
			$( '.faq__details' ).each( function () {
				let detail = $( this ).find( '.faq__summary' );
				let wrap = $( this ).find( '.faq__text' );

				wrap.hide();

				detail.on( 'click', function ( e ) {
					e.preventDefault();

					if ( detail.hasClass( 'open' ) ) {
						detail.removeClass( 'open' );
						wrap.slideUp();
					} else {
						// Закриваємо всі інші відкриті елементи
						$( '.faq__summary.open' )
							.not( detail )
							.removeClass( 'open' );
						$( '.faq__text' ).not( wrap ).slideUp();

						// Відкриваємо поточний
						detail.addClass( 'open' );
						wrap.slideDown();
					}
				} );
			} );
		},
		openPopup() {
			$( '.open-popup' )
				.on( 'click' )
				.on( 'click', function ( e ) {
					e.preventDefault();
					$( 'body' ).addClass( 'no-scroll' );
					$( '#popup-form' ).addClass( 'open' );
				} );
		},
		closePopup() {
			$( '.popup-close' ).on( 'click', function ( e ) {
				e.preventDefault();
				$( 'body' ).removeClass( 'no-scroll' );
				$( '#popup-form' ).removeClass( 'open' );
			} );
		},
		seriesSlider() {
			let swiperInstance = null;
			const sliderContainer = $( '.series__wrapper' );
			const sliderItem = $( '.series__item' );
			function initSwiper() {
				if ( swiperInstance ) {
					swiperInstance.destroy( true, true );
					swiperInstance = null;
					sliderContainer.removeClass( 'swiper-wrapper' );
					sliderItem.removeClass( 'swiper-slide' );
				}

				if ( window.innerWidth >= 768 ) {
					sliderContainer.addClass( 'swiper-wrapper' );
					sliderItem.addClass( 'swiper-slide' );
					swiperInstance = new Swiper( '.series-slider', {
						slidesPerView: 4,
						spaceBetween: 20,
						slidesPerGroup: 1,
						speed: 1000,
            grabCursor: true,
						breakpoints: {
							768: {
								slidesPerView: 4,
							},
						},
					} );
				} else {
					sliderContainer.removeClass( 'swiper-wrapper' );
					sliderItem.removeClass( 'swiper-slide' );
				}
			}

			initSwiper();

			$( window ).on( 'resize', function () {
				initSwiper();
			} );
		},
		filters() {
			const filters = $( '#filters' );

			let selects = filters.find( '.filters__list' );
			let url = filters.data( 'url' );

			function _getFilterParams() {
				let selectChange = [];

				let seriesChange = [];
				$( '.series__item.active' ).each( function () {
					let catVal = $( this ).data( 'category' );
					seriesChange.push( catVal );
				} );

				let seriesRez = '';
				if ( seriesChange.length > 0 ) {
					seriesRez =
						'product_filter=' + seriesChange.join( ',' ) + '&';
				}

				selectChange += seriesRez;

				selects.each( function () {
					let directionsChange = [];
					$(
						'ul[data-name=' +
							$( this ).data( 'name' ) +
							']>li.selected'
					).each( function () {
						let value = $( this ).data( 'value' );
						if ( value != 'select-all' ) {
							directionsChange.push( value );
						}
					} );
					let directionsRez;
					if ( directionsChange.length > 0 ) {
						directionsChange = directionsChange.join( ',' );
						directionsRez =
							$( this ).data( 'name' ) +
							'=' +
							directionsChange +
							'&';
					} else {
						directionsRez = '';
					}
					selectChange += directionsRez;
				} );

				let urlParam =
					selectChange != '' ? '?' + selectChange.slice( 0, -1 ) : '';
				return urlParam;
			}
			function _getFilterPosts( $url ) {
				// console.log( $url );

				let data = {
					action: 'filters',
					'post-type': filters.data( 'url' ),
					'url-param': $url,
					default_cat: $( '.filters-all' ).data( 'id-product' ),
					beforeSend: function () {
						$( '.loader-wrapper' ).css( { display: 'flex' } );
					},
				};
				let params = new URLSearchParams( $url );
				params.forEach( ( value, key ) => {
					data[ key ] = value;
				} );

				$.ajax( {
					url: ajaxurl,
					data: data,
					type: 'POST',
					success: function ( data ) {
						$( '.loader-wrapper' ).css( { display: 'none' } );
						history.pushState( '', '', url + $url );
						let res = JSON.parse( data );

						if ( res.posts !== '' ) {
							$( '.products__list' ).html( res.posts );
						}
						$( '.pagination' ).html( res.pagination );
					},
				} );
			}
			$( '.filters-all' ).on( 'click', function ( e ) {
				e.preventDefault();
				$( this ).parent().addClass( 'hide' );
				$( '.filters-choice' ).removeClass( 'active enable' );
				$( '.filters__list__item:not(.select-all)' ).removeClass(
					'selected'
				);
				$( '.filters__list__item.select-all' ).addClass( 'selected' );
				$( '.series__item' ).removeClass( 'active' );
				_getFilterPosts( _getFilterParams() );
				selects.each( function () {
					let button = $( this ).prev( '.filters-choice' );
					let textAll = $( this )
						.find( '.select-all.single' )
						.text()
						.trim();
					if ( textAll.length ) {
						button.find( '.filters-choice-title' ).text( textAll );
					}
				} );
			} );
			selects.each( function () {
				let filterItem = $( this ).find( '.filters__list__item' );
				let buttonThis = $( this ).prev();
				let button = $( '.filters-choice' );
				filterItem.each( function () {
					$( this ).on( 'click', function () {
						$( '.filters__list__item' ).removeClass( 'selected' );
						$( this ).addClass( 'selected' );
						button.removeClass( 'active' );
						button.removeClass( 'enable' );
						// button.each(function(){
						//   $(this).find('.filters-choice-title').text($(this).data('text'))
						// });
						buttonThis.removeClass( 'active' );
						buttonThis.addClass( 'enable' );
						// buttonThis.find('.filters-choice-title').text($(this).text());
						_getFilterPosts( _getFilterParams() );
					} );
				} );
			} );
			$( '.series__item' ).on( 'click', function ( e ) {
				e.preventDefault();
				$( this ).toggleClass( 'active' );
				_getFilterPosts( _getFilterParams() );
			} );
			$( '.filters-choice' ).each( function () {
				$( this ).on( 'click', function ( e ) {
					$( '.filters-choice' ).not( this ).removeClass( 'active' );
					if ( $( this ).hasClass( 'active' ) ) {
						$( this ).removeClass( 'active' );
					} else {
						$( this ).addClass( 'active' );
					}
				} );
			} );
			$( document ).on( 'click', function ( e ) {
				if (
					! $( '.filters__list' ).is( e.target ) &&
					$( '.filters__list' ).has( e.target ).length === 0 &&
					! $( '.filters-choice' ).is( e.target )
				) {
					$( '.filters-choice.active' ).removeClass( 'active' );
				}
			} );
		},
    instruction() {
			const filters = $( '#term-instruction' );
      let btn = filters.find('.filter-button-cat');
      btn.on('click',function(){
        $this = $(this);
        let data = {
					action: 'term_filters',
					'term_slug': $this.data('cat-id'),
          'page_id' : filters.data('page-id'),
					beforeSend: function () {
						$( '.loader-wrapper' ).css( { display: 'flex', position:'fixed',top:0,left:0 } );
					},
				};

				$.ajax( {
					url: ajaxurl,
					data: data,
					type: 'POST',
					success: function ( data ) {
						$( '.loader-wrapper' ).css( { display: 'none' } );
						let res = JSON.parse( data );
            
						if ( res.posts !== '' ) {
							$( '.instructions' ).html( res.posts );
							Scripts.instruction();
						}
            if ( res.breadcrumbs !== '' ) {
							$( '.breadcrumbs' ).html( res.breadcrumbs );
						}
            if ( res.url !== '' ) {
							history.pushState( '', '', res.url );
						}
            
            if ( res.is_taxonomy == true ) {
							Scripts.seriesSlider();
						}
					},
				} );
      });
		},
		productGallerySlider() {
			$( '.card' ).each( function () {
				const $slider = $( this );
				const $gallerySliderMultiple = $slider.find(
					'.card__slider-multiple'
				);
				const $gallerySliderSingle = $slider.find(
					'.card__slider-single'
				);

				if (
					$gallerySliderMultiple.length &&
					$gallerySliderSingle.length
				) {
					const galleryThumbs = new Swiper(
						$gallerySliderMultiple.get( 0 ),
						{
							direction: 'vertical',
							spaceBetween: 12,
							slidesPerGroup: 1,
							slidesPerView: 4.1,
							touchRatio: 0.2,
							slideToClickedSlide: true,
							autoHeight: false,
							a11y: true,
							lazy: true,
							preloadImages: false,
							keyboard: true,
							breakpoints: {
								320: {
									slidesPerView: 4.1,
									spaceBetween: 8,
								},
								1024: {
									spaceBetween: 12,
								},
							},
						}
					);

					const gallerySingle = new Swiper(
						$gallerySliderSingle.get( 0 ),
						{
							spaceBetween: 0,
							slidesPerView: 1,
							slidesPerGroup: 1,
							spaceBetween: 20,
							navigation: {
								nextEl: '.gallery-button-next',
								prevEl: '.gallery-button-prev',
							},
							pagination: {
								el: '.card-progress',
								type: 'progressbar',
							},
							loop: false,
							a11y: true,
							lazy: true,
							preloadImages: false,
							autoHeight: true,
							keyboard: true,
							thumbs: {
								swiper: galleryThumbs,
								slideThumbActiveClass: 'swiper-slide-active',
							},
						}
					);
				}
			} );
		},
		modelSlider() {
			const modelSwiper = new Swiper( '.model-slider', {
				slidesPerView: 3.65,
				spaceBetween: 16,
				speed: 1000,
				navigation: {
					nextEl: '.card-button-next',
					prevEl: '.card-button-prev',
				},
				breakpoints: {
					320: {
						slidesPerView: 2.65,
						spaceBetween: 16,
					},
					575: {
						slidesPerView: 1.5,
					},
					767: {
						slidesPerView: 3.65,
					},
				},
			} );
		},
		tabsItem() {
			$( '.tabs__item-head' ).on( 'click', function ( e ) {
				e.preventDefault();
				if ( $( this ).hasClass( 'active' ) ) {
					$( this ).removeClass( 'active' );
					$( this ).next().slideUp();
				} else {
					$( this ).addClass( 'active' );
					$( this ).next().slideDown();
				}
			} );
		},
		videoBtn() {
			const $wrapper = $( '.instruction__item' );
			const $video = $wrapper.find( '.custom-video' );
			const $btn = $wrapper.find( '.play-btn' );
			const videoEl = $video.get( 0 );

			// Вмикаємо/вимикаємо відео по кліку на кнопку
			$btn.on( 'click', function ( e ) {
				e.stopPropagation(); // щоб не вплинуло на video timeline
				if ( videoEl.paused ) {
					videoEl.play();
				} else {
					videoEl.pause();
				}
			} );

			// Обробка play
			$video.on( 'play', function () {
				$btn.hide();
				$wrapper.addClass( 'play' );
				$( this ).attr( 'controls', true );
			} );

			// Обробка pause — тільки якщо не перемотка
			$video.on( 'pause', function ( e ) {
				// Перевіримо, чи дійсно користувач поставив на паузу вручну
				// Якщо відео паузиться лише на мілісекунду (через перемотку), ми цього не робимо
				// Трюк: запускаємо короткий таймер
				setTimeout( () => {
					if ( videoEl.paused && ! videoEl.seeking ) {
						$btn.show();
						$wrapper.removeClass( 'play' );
						$( videoEl ).attr( 'controls', false );
					}
				}, 100 ); // затримка, щоб дочекатись, чи дійсно пауза
			} );
		},
		chronologySlider() {
			$( '.chronology' ).each( function () {
				const $slider = $( this );
				const $gallerySliderMultiple =
					$slider.find( '.short-multiple' );
				const $gallerySliderSingle = $slider.find( '.expanded-single' );

				if (
					$gallerySliderMultiple.length &&
					$gallerySliderSingle.length
				) {
					const galleryThumbs = new Swiper(
						$gallerySliderMultiple.get( 0 ),
						{
							spaceBetween: 0,
							slidesPerGroup: 1,
							slidesPerView: 5,
							touchRatio: 0.2,
							slideToClickedSlide: true,
							autoHeight: false,
							a11y: true,
							lazy: true,
							preloadImages: false,
							keyboard: true,
							loop: false,
							breakpoints: {
								320: {
									slidesPerView: 'auto',
									spaceBetween: 0,
								},
								575: {
									slidesPerView: 3,
								},
								768: {
									slidesPerView: 4,
								},
								1024: {
									spaceBetween: 0,
									slidesPerView: 5,
								},
							},
						}
					);

					const gallerySingle = new Swiper(
						$gallerySliderSingle.get( 0 ),
						{
							spaceBetween: 0,
							slidesPerView: 1,
							slidesPerGroup: 1,
							navigation: {
								nextEl: '.chronology-button-next',
								prevEl: '.chronology-button-prev',
							},
							loop: false,
							a11y: true,
							lazy: true,
							preloadImages: false,
							autoHeight: false,
							keyboard: true,
						}
					);

					gallerySingle.on( 'slideChange', function () {
						const ACTIVE_OFFSET =
							$( window ).width() > 767 ? 180 : 158; // Точка на екрані, де завжди повинен з'являтись слайд

						const activeIndex = gallerySingle.realIndex;
						const thumbSlides = galleryThumbs.slides;

						let count = -1;
						let domIndex = null;

						for ( let i = 0; i < thumbSlides.length; i++ ) {
							if (
								thumbSlides[ i ].classList.contains(
									'swiper-slide'
								)
							) {
								count++;
							}

							if ( count === activeIndex ) {
								domIndex = i;
								break;
							}
						}

						if ( domIndex === null ) return;

						const slideEl = thumbSlides[ domIndex ];
						const offsetLeft = slideEl.offsetLeft;

						// Рахуємо, скільки треба проскролити, щоб слайд став у ACTIVE_OFFSET
						let targetScroll = offsetLeft - ACTIVE_OFFSET;

						const containerWidth = galleryThumbs.el.offsetWidth;
						const totalWidth = galleryThumbs.wrapperEl.scrollWidth;

						const maxScroll = totalWidth - containerWidth;

						targetScroll = Math.min( targetScroll, maxScroll );
						targetScroll = Math.max( targetScroll, 0 ); // не виходимо за ліву межу

						galleryThumbs.setTransition( 300 );
						galleryThumbs.setTranslate( -targetScroll );
						galleryThumbs.updateActiveIndex( domIndex );
						galleryThumbs.updateSlidesClasses();
					} );

					const thumbSlides = galleryThumbs.slides;
					$gallerySliderMultiple
						.find( '.swiper-slide' )
						.on( 'click', function () {
							const $clicked = $( this )[ 0 ];
							let realIndex = -1;
							let count = -1;
							let domIndex = -1;

							for ( let i = 0; i < thumbSlides.length; i++ ) {
								if (
									thumbSlides[ i ].classList.contains(
										'swiper-slide'
									)
								) {
									count++;
								}

								if ( thumbSlides[ i ] === $clicked ) {
									domIndex = i;
									realIndex = count;
									break;
								}
							}

							if ( realIndex < 0 || domIndex < 0 ) return;

							gallerySingle.slideTo( realIndex );
							const ACTIVE_OFFSET =
								$( window ).width() > 767 ? 180 : 158;

							let offsetLeft = thumbSlides[ domIndex ].offsetLeft;
							if (
								domIndex > 0 &&
								thumbSlides[ domIndex - 1 ].classList.contains(
									'swiper-year'
								) &&
								realIndex !== 0
							) {
								offsetLeft -=
									thumbSlides[ domIndex - 1 ].offsetWidth;
							}

							const containerWidth = galleryThumbs.el.offsetWidth;
							const totalWidth =
								galleryThumbs.wrapperEl.scrollWidth;
							const maxScroll = totalWidth - containerWidth;

							let targetScroll = Math.min(
								Math.max( offsetLeft - ACTIVE_OFFSET, 0 ),
								maxScroll
							);

							galleryThumbs.setTransition( 300 );
							galleryThumbs.setTranslate( -targetScroll );
						} );
				}
			} );
		},
		braunNow() {
			const now = new Swiper( '.now-slider', {
				slidesPerView: 1,
				spaceBetween: 10,
				speed: 1000,
				autoHeight: false,
				navigation: {
					nextEl: '.now-button-next',
					prevEl: '.now-button-prev',
				},
				breakpoints: {
					320: {
						slidesPerView: 1,
						spaceBetween: 10,
					},
				},
				pagination: {
					el: '.now-progress',
					type: 'progressbar',
				},
			} );
		},
		actualAjax() {
			$( '.catalog__categories__btn' ).on( 'click', function ( event ) {
				event.preventDefault();
				$this = $( this );
				$( '.catalog__categories__btn' ).removeClass( 'active' );
				$this.addClass( 'active' );
				let data = {
					action: 'actuality',
					cat_id: $this.data( 'cat-id' ),
					all_ids: $( '.catalog__categories' ).data( 'all-ids' ),
					type: $( '.catalog__categories' ).data( 'type' ),
          cat: $( '.catalog__categories' ).data( 'cat' ),
					beforeSend: function () {
						$( '.loader-wrapper' ).css( { display: 'flex' } );
					},
				};

				$.ajax( {
					url: ajaxurl,
					data: data,
					type: 'POST',
					success: function ( data ) {
						let res = JSON.parse( data );
						$( '.loader-wrapper' ).css( { display: 'none' } );
						if ( res.post !== '' ) {
							$( '.slider-ajax, .blog__list--show-more' ).html(
								res.post
							);
						}
						if ( $( '.catalog .show-more' ).length ) {
							$( '.catalog .show-more' )
								.next( '.preloder' )
								.remove();
							$( '.catalog .show-more' ).remove();
							$( '.catalog__wrapper' ).append( res.show_more );
							Scripts.loadMore();
						}
					},
				} );
			} );
		},
		commentsValidation() {
			const $form = $( '#commentform' );
			if ( ! $form.length ) return;

			const originalFormHtml = $form.html();

			$form.on( 'click', 'input.submit', function ( e ) {
				e.preventDefault();

				let isValid = true;

				// Очистка попередніх помилок
				$form.find( '.field-error' ).removeClass( 'field-error' );
				$form.find( '.error-message' ).hide();

				// Автор
				const $author = $form.find( '#author' );
				if ( $author.length && ! $author.val().trim() ) {
					$author
						.addClass( 'field-error' )
						.closest( '.comment-form-author' )
						.find( '.error-message' )
						.show();
					isValid = false;
				}

				// Email
				const $email = $form.find( '#email' );
				const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
				if (
					$email.length &&
					( ! $email.val().trim() ||
						! emailRegex.test( $email.val() ) )
				) {
					$email
						.addClass( 'field-error' )
						.closest( '.comment-form-email' )
						.find( '.error-message' )
						.show();
					isValid = false;
				}

				// Коментар
				const $comment = $form.find( '#comment' );
				if ( ! $comment.val().trim() ) {
					$comment
						.addClass( 'field-error' )
						.closest( '.comment-form-comment' )
						.find( '.error-message' )
						.show();
					isValid = false;
				}

				// Рейтинг
				const $ratingInputs = $form.find( 'input[name="rating"]' );
				const ratingChecked = $ratingInputs.is( ':checked' );
				if ( ! ratingChecked ) {
					$form
						.find( '.comment-form-review-top' )
						.addClass( 'field-error' )
						.find( '.error-message' )
						.show();
					isValid = false;
				}

				if ( ! isValid ) return;

				const $loader = $form.find( '.loader-wrapper' );
				$loader.css( { display: 'flex' } ); // Показати лоадер

				const formData = $form.serialize();

				$.ajax( {
					url: $form.attr( 'action' ),
					method: 'POST',
					data: formData,
					success: function () {
						$form.html( `
                <div class="comment-success" style="font-size: 20px; color: green; font-weight: bold; text-align: center;">
                  Ваш коментар буде опубліковано після модерації
                </div>
              ` );
					},
					error: function () {
						$form.html(
							'<div class="comment-error" style="color:red;">Щось пішло не так. Спробуйте пізніше.</div>'
						);
					},
					complete: function () {
						$loader.hide(); // Приховати лоадер

						setTimeout( function () {
							$form.html( originalFormHtml );
							$( document ).trigger( 'ready' );
							Scripts.labelForm();
						}, 5000 );
					},
				} );
			} );
		},
		labelForm() {
			const fields = document.querySelectorAll(
				'.floating-field input, .floating-field textarea'
			);

			fields.forEach( ( field ) => {
				const wrapper = field.closest( '.floating-field' );

				function checkValue() {
					if ( field.value.trim() !== '' ) {
						wrapper.classList.add( 'not-empty' );
					} else {
						wrapper.classList.remove( 'not-empty' );
					}
				}

				field.addEventListener( 'input', checkValue );
				field.addEventListener( 'blur', checkValue );

				checkValue();
			} );
		},
		loadMore() {
			let btnMore = $( '.show-more' );
			let offset = 0;
			btnMore.on( 'click', function ( e ) {
				let currentBtn = $( this );

				e.preventDefault();
				let preloder = currentBtn.next( '.preloder' );
				offset += 1;
				container = $( '.blog__list--show-more' );
				count = currentBtn.data( 'count' );
				taxonomy = currentBtn.data( 'all-ids' );
        post_type = currentBtn.data( 'post-type' );
				$.ajax( {
					type: 'POST',
					url: '/wp-admin/admin-ajax.php',
					data: {
						action: 'load_more',
						offset: offset,
						taxonomy: taxonomy,
            post_type: post_type,
						beforeSend: function () {
							currentBtn.hide();
							preloder.css( { display: 'flex' } );
						},
					},

					success: function ( data ) {
						preloder.hide();
						currentBtn.show();
						container.append( data );
            console.log(data);
            
						limit = count / 6;
						if ( offset + 1 >= limit ) {
							currentBtn.addClass( 'end' );
						}
					},
				} );
			} );
		},
    searchMore() {
			let btnMore = $( '.show-more' );
			let offset = 0;
			btnMore.on( 'click', function ( e ) {
				let currentBtn = $( this );

				e.preventDefault();
				let preloder = currentBtn.next( '.preloder' );
				offset += 1;
        post_type = currentBtn.data( 'post-type' );
        type = post_type == 'post' ? 'blog':'products';
				container = $( '.'+type+'__list--show-more' );
				count = currentBtn.data( 'count' );
				postIds = currentBtn.data( 'all-ids' );
				$.ajax( {
					type: 'POST',
					url: '/wp-admin/admin-ajax.php',
					data: {
						action: 'search_more',
						offset: offset,
						post_ids: postIds,
            post_type: post_type,
						beforeSend: function () {
							currentBtn.hide();
							preloder.css( { display: 'flex' } );
						},
					},

					success: function ( data ) {
						preloder.hide();
						currentBtn.show();
						container.append( data );
            console.log(data);
            
						limit = count / 6;
						if ( offset + 1 >= limit ) {
							currentBtn.addClass( 'end' );
						}
					},
				} );
			} );
		},
    searchServices(){
      $('#search-services').on('input',function(e){
        e.preventDefault();
        
        $this = $(this);
        let data = {
					action: 'post_filters',
					'input_text': $this.val(),
          'page_id': $('.services-wrapper').data('page-id'),
					beforeSend: function () {
						$('.border-wrapp').find('.loader-wrapper' ).css( { display: 'flex', position:'absolute',top:0,left:0 } );
					},
				};
        setTimeout(function(){
          $.ajax( {
            url: ajaxurl,
            data: data,
            type: 'POST',
            success: function ( data ) {
              $('.border-wrapp').find( '.loader-wrapper' ).css( { display: 'none' } );
              let res = JSON.parse( data );
              // console.log(res.posts);
              
              if ( res.posts !== '' ) {
                  $( '.services-wrapper' ).html( res.posts );
                  Scripts.instruction();
              }
            },
          });
        },700);
      });
    },
    searchFilterBtns(){
      $('.search-filter').on('click',function(e){
        e.preventDefault();
        let showBlock = $(this).data('show');
        $('.search-filter').removeClass('active');
        $(this).addClass('active');
        
        if(showBlock == 'all'){
          $('section.section').show();
        } else{
          $('section.section:not(".search-page")').hide();
          $('section.' + showBlock).show();
        }
      });
    },
    formSpinner(){
      let $formWrapper = $('.popup-wrapper');
      let $spinner = $formWrapper.find('.loader-wrapper');
  
      // Вмикаємо спінер до відправки (submit — у фазі capture)
      $(document).on('submit', '.wpcf7 form', function(e) {
        $spinner.css({'display':'flex'});
      });
    
      // Вимикаємо спінер після невдалої валідації
      $(document).on('wpcf7invalid', function() {
        $spinner.fadeOut(200);
      });
    
      // Вимикаємо спінер після будь-якого завершення відправки
      $(document).on('wpcf7submit', function() {
        $spinner.fadeOut(200);
      });
    },
    phoneMask(){
      let $phone = $(".tel");
      let maskOptions = {onKeyPress: function(phone) {
          let masks = ['(+380 (__) ___ __ __', '+380 (__) ___ __ __'];
          mask = phone.match(/^\([0-9]{2}\) 9/g)
              ? masks[1]
              : masks[0];
          $phone.mask(mask, this);
      }}; 
      $phone.mask('+380 (__) ___ __ __', maskOptions);
      $phone.on('focus click', function () {
        let val = $(this).val();
        let firstPlaceholderIndex = val.indexOf('_');
      
        if (firstPlaceholderIndex !== -1) {
          let input = this;
          setTimeout(function () {
            input.setSelectionRange(firstPlaceholderIndex, firstPlaceholderIndex);
          }, 10);
        }
      });
    },
    sectionFilter: $('#search-service'),
    urlArchive: $('#search-service').data('url'),
    pageTrue: $('#search-service').data('page'),

    showLoader() {
      $('.loader-wrapper').css({ display: 'flex' });
    },

    hideLoader() {
      $('.loader-wrapper').css({ display: 'none' });
    },

    runAjax(action, data, onSuccess) {
      this.showLoader();
      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: Object.assign({ action }, data),
        success: (response) => {
          this.hideLoader();
          try {
            const res = JSON.parse(response);
            this.moveMapToProduct(res.lat, res.lng, res.zoom);
            if (onSuccess) onSuccess(res);
          } catch (e) {
            console.error('Invalid JSON:', e);
          }
        }
      });
    },

    updateUIFromResponse(res, options = {}) {
      if (!res.post) return;
      if (options.url) history.pushState('', '', options.url);
      if (res.title && options.inputSelector) $(options.inputSelector).val(res.title).next().hide();
      if (res.child_term && options.childWrapper) $(options.childWrapper).html(res.child_term);
      $('.services__list').html(res.post);
    },

    locationByCompany() {
      const self = this;

      this.sectionFilter.on('click', '.address-ajax', function(e) {
        e.preventDefault();
        const $el = $(this);
        $el.closest('.filters-group').addClass('active');

        self.runAjax('services_search', {
          company_id: $el.data('service'),
          city: $el.data('city'),
          district: $el.data('district'),
          address: $el.data('address')
        }, res => {
          self.updateUIFromResponse(res, {
            url: self.urlArchive + 'point/' + $el.data('slug'),
            inputSelector: '#search-company'
          });
        });
      });

      this.sectionFilter.on('click', '.city-ajax', function(e) {
        e.preventDefault();
        const $el = $(this);
        $el.closest('.filters-group').addClass('active');

        self.runAjax('district_search', {
          city: $el.data('value'),
          label: $el.closest('.filters-group').find('.filters-label').data('parent')
        }, res => {
          const slug = $el.data('value');
          const place = self.pageTrue ? 'srv_place' : 'place';

          self.updateUIFromResponse(res, {
            url: self.urlArchive + place + '/' + slug,
            inputSelector: '#search-city',
            childWrapper: '.search-district .services-wrapper'
          });

          $('.btn-close.city').attr('data-city', slug);
          $('.search-district').removeClass('disabled');
          $('#search-area').removeAttr('disabled');
        });
      });

      this.sectionFilter.on('click', '.district-ajax', function(e) {
        e.preventDefault();
        const $el = $(this);
        $el.closest('.filters-group').addClass('active');

        self.runAjax('final_search', {
          district: $el.data('value')
        }, res => {
          self.updateUIFromResponse(res, {
            url: self.urlArchive + res.url,
            inputSelector: '#search-area',
            childWrapper: '.search-district .services-wrapper'
          });

          $('.btn-close.district').attr('data-district', $el.data('value'));
        });
      });

      $(document).on('click', '.btn-close', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const $group = $btn.closest('.filters-group');
        const labelParent = $group.find('.filters-label').data('parent');
        const isCity = $btn.hasClass('city');
        const isDistrict = $btn.hasClass('district');

        $group.removeClass('active');

        if (isCity || isDistrict) {
          self.runAjax('district_search', {
            city: isCity ? '' : $('.btn-close.city').data('city'),
            label: labelParent
          }, res => {
            const place = self.pageTrue ? 'srv_place' : 'place';
            const url = isCity ? self.urlArchive : self.urlArchive + place + '/' + $('.btn-close.city').data('city') + '/';

            self.updateUIFromResponse(res, {
              url,
              inputSelector: isCity ? '#search-city' : null,
              childWrapper: '.search-district .services-wrapper'
            });

            $('#search-area').val('');
            if (isCity) {
              $('.search-district').addClass('disabled');
              $('#search-area').prop('disabled', true);
              $('.btn-close.city').attr('data-city', '');
              $('.btn-close.district').hide();
              $('#search-area + svg').show();
            }
          });
        } else {
          self.runAjax('services_search', {}, res => {
            self.updateUIFromResponse(res, {
              url: self.urlArchive
            });
          });

          self.runAjax('search_filters', {
            input_text: ''
          }, res => {
            $group.find('.services-wrapper').html(res.posts);
            self.locationByCompany();
          });
        }
      });
    },

    searchCompany() {
      const self = this;

      $('#search-company').on('input', function(e) {
        const $el = $(this);
        const parent = $el.closest('.filters-group');

        self.runAjax('search_filters', {
          input_text: $el.val()
        }, res => {
          parent.find('.services-wrapper').html(res.posts);
          self.locationByCompany();
          parent.addClass('active');
        });
      });

      $('#search-city, #search-area').on('input', function(e) {
        const $el = $(this);
        const parent = $el.closest('.filters-group');

        self.runAjax('city_district', {
          input_text: $el.val(),
          parent: $el.closest('.filters-label').data('parent')
        }, res => {
          parent.find('.services-wrapper').html(res.posts);
          self.locationByCompany();
          parent.addClass('active');
        });
      });
    },

    coordinatesLatLng() {
      if (typeof window.MapData !== 'undefined' && typeof window.map !== 'undefined') {
        const { lat, lng, zoom } = window.MapData;
        if (lat && lng) {
          window.map.setZoom(parseInt(zoom, 10));
          window.map.panTo({ lat: parseFloat(lat), lng: parseFloat(lng) });
        }
      }
    },

    moveMapToProduct(lat, lng, zoom) {
      if (!window.map) {
        console.error('Google Map is not initialized');
        return;
      }
      window.map.setZoom(zoom);
      window.map.panTo({ lat: parseFloat(lat), lng: parseFloat(lng) });
    }, 
		init() {
      if($('.level-0>.menu-item').length){
			  Scripts.menuCategoryProduct();
      }
			Scripts.clickOnHeader();
      if($('.slider-catalog').length){ 
			  Scripts.catalogSlider();
      }
      if($('#search-service').length){
        Scripts.locationByCompany();
        Scripts.searchCompany();
        Scripts.coordinatesLatLng();
      }
			Scripts.productsSlider();
			Scripts.updateCategoryItem();
			Scripts.mediaSlider();
			Scripts.blogSlider( 'blog' );
			Scripts.blogSlider( 'use' );
			Scripts.blogSlider( 'gallery' );
			Scripts.detailsSlow();
			Scripts.openPopup();
			Scripts.closePopup();
      Scripts.seriesSlider();
      if($('#filters').length){
			  Scripts.filters();
      }
			Scripts.productGallerySlider();
			Scripts.modelSlider();
			Scripts.tabsItem();
			Scripts.videoBtn();
			Scripts.chronologySlider();
			Scripts.braunNow();
			Scripts.actualAjax();
			Scripts.commentsValidation();
			Scripts.labelForm();
			Scripts.loadMore();
      Scripts.instruction();
      if($('#product-instruction').length){
        Scripts.searchServices();
      }
      Scripts.searchMore();
      Scripts.searchFilterBtns();
      Scripts.formSpinner();
      Scripts.phoneMask();
		},
	};
	$( document ).ready( function () {
		Scripts.init();
	} );
	$( window ).on( 'resize', function () {
		Scripts.menuCategoryProduct();
		Scripts.catalogSlider();
		Scripts.seriesSlider();
		Scripts.chronologySlider();
	} );
} );
