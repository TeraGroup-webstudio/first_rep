jQuery(function($){
	$(document).mouseup( function(e){ // событие клика по веб-документу
		var div = $(".main-menu-content, .main-menu"); // тут указываем ID элемента
		if ( !div.is(e.target) // если клик был не по нашему блоку
			&& div.has(e.target).length === 0 ) { // и не по его дочерним элементам
			//div.hide(); // скрываем его
			$(".main-menu-content").removeClass('in');
		}
	});
	$(document).mouseup( function(e){ // событие клика по веб-документу
		var div = $(".categories_listWraperBox, .catalog"); // тут указываем ID элемента
		console.log(div.has(e.target).length);
		if ( !div.is(e.target) // если клик был не по нашему блоку
			&& div.has(e.target).length === 0 ) { // и не по его дочерним элементам
			//div.hide(); // скрываем его
			$(".categories_listWraper").removeClass('open');
			$("body").removeClass('lock');
		}
	});
	$(document).mouseup( function(e){ // событие клика по веб-документу
		var div = $("#cart, .container-cart"); // тут указываем ID элемента
		if ( !div.is(e.target) // если клик был не по нашему блоку
			&& div.has(e.target).length === 0 ) { // и не по его дочерним элементам
			//div.hide(); // скрываем его
			$(".container-cart").removeClass('in');
		}
	});



});
function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	} else { 			// Изменения для seo_url от Русской сборки OpenCart 3x
		var query = String(document.location.pathname).split('/');
		if (query[query.length - 1] == 'cart') value['route'] = 'checkout/cart';
		if (query[query.length - 1] == 'checkout') value['route'] = 'checkout/checkout';

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}
// Top Fixed Menu
function headerfix() {
	let width = $(window).width();
	let height_header = 0;
	console.log(width);
	if(width < 1024){
		height_header = '53.36px';
	} else {
		height_header = '100px';
	}
	if ($(this).scrollTop() > 150) {
		$("header").addClass('fixed');
		$("body").css('margin-top',height_header);
	}else{
		$("header").removeClass('fixed');
		$("body").css('margin-top','0');
	}
}

jQuery(window).resize(function() {headerfix();});
jQuery(document).ready(function() {headerfix();});
jQuery(document).scroll(function() {headerfix();});
$(document).ready(function() {
	// Highlight any found errors
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();

		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});

	$('body').on('click', '.btn-exit', function(){
		$('.container-cart').removeClass('in');
	});

	$('.main-menu').on('click', function(){
		$('.main-menu-content').toggleClass('in');
		$('.categories_listWraper').removeClass('in');
		$('body').removeClass('fixbody');
	});

	$('#categories_listButtonClose').on('click', function () {
		$('.categories_listWraper').removeClass('in');
		$('body').removeClass('fixbody');
		$('body').removeClass('lock');
	});

	$('#cart').on('click', function(){
		$('.container-cart').toggleClass('in');
	});

	$('.section-wishlist > div').on('click', function(){
		$(this).parent().toggleClass('add-wishlist');
	});

	$('.item-catalog-panel, .mobile-catalog').on('click', function () {
		console.log('click test');
		$('.categories_listWraper').toggleClass('in');
		$('body').toggleClass('fixbody');
		$('body').toggleClass('lock');
	});

	$('.main-title-category').on('click', function(e){
		$('.categoriesList li').removeClass('open');
	});

	$('.categories_lvl1Box .open').on('click', function () {
		$(this).parent().parent().addClass('open');
	});


	// Currency
	$('#form-currency .currency-select').on('click', function(e) {
		e.preventDefault();

		$('#form-currency input[name=\'code\']').val($(this).attr('name'));

		$('#form-currency').submit();
	});

	// Language
	$('#form-language .language-select').on('click', function(e) {
		e.preventDefault();

		$('#form-language input[name=\'code\']').val($(this).attr('name'));

		$('#form-language').submit();
	});

	/* Search */
	$('#search input[name=\'search\']').parent().find('button').on('click', function() {
		var url = $('base').attr('href') + 'index.php?route=product/search';

		var value = $('header #search input[name=\'search\']').val();

		if (value) {
			url += '&search=' + encodeURIComponent(value);
		}

		location = url;
	});

	$('#search input[name=\'search\']').on('keydown', function(e) {
		if (e.keyCode == 13) {
			$('header #search input[name=\'search\']').parent().find('button').trigger('click');
		}
	});

	// Menu
	$('#menu .dropdown-menu').each(function() {
		var menu = $('#menu').offset();
		var dropdown = $(this).parent().offset();

		var i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());

		if (i > 0) {
			$(this).css('margin-left', '-' + (i + 10) + 'px');
		}
	});

	// Product List
	$('#list-view').click(function() {
		$('#content .product-grid > .clearfix').remove();

		$('#content .row > .product-grid').attr('class', 'product-layout product-list col-xs-12');
		$('#grid-view').removeClass('active');
		$('#list-view').addClass('active');

		localStorage.setItem('display', 'list');
	});

	// Product Grid
	$('#grid-view').click(function() {
		// What a shame bootstrap does not take into account dynamically loaded columns
		var cols = $('#column-right, #column-left').length;

		if (cols == 2) {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
		} else if (cols == 1) {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
		} else {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
		}

		$('#list-view').removeClass('active');
		$('#grid-view').addClass('active');

		localStorage.setItem('display', 'grid');
	});

	if (localStorage.getItem('display') == 'list') {
		$('#list-view').trigger('click');
		$('#list-view').addClass('active');
	} else {
		$('#grid-view').trigger('click');
		$('#grid-view').addClass('active');
	}

	// Checkout
	$(document).on('keydown', '#collapse-checkout-option input[name=\'email\'], #collapse-checkout-option input[name=\'password\']', function(e) {
		if (e.keyCode == 13) {
			$('#collapse-checkout-option #button-login').trigger('click');
		}
	});

	// tooltips on hover
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});

	// Makes tooltips work on ajax generated content
	$(document).ajaxStop(function() {
		$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
	});
});

// Cart add remove functions
var cart = {
	'add': function(product_id, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			success: function(json) {
				$('.alert-dismissible, .text-danger').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					//$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					// Need to set timeout otherwise it wont update the total
					setTimeout(function () {
						$('#cart-total').html('' + json['total'] + '');
					}, 100);

					//$('html, body').animate({ scrollTop: 0 }, 'slow');

					$('.mini-cart .container-cart > ul').load('index.php?route=common/cart/info ul');
					$('.container-cart').addClass('in');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'update': function(key, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/edit',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart-total').html('' + json['total'] + '');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('.mini-cart .container-cart > ul').load('index.php?route=common/cart/info ul');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart-total').html('' + json['total'] + '');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('.mini-cart .container-cart > ul').load('index.php?route=common/cart/info ul');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var voucher = {
	'add': function() {

	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart-total').html('' + json['total'] + '');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('.mini-cart .container-cart > ul').load('index.php?route=common/cart/info ul');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var wishlist = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=account/wishlist/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert-dismissible').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					if(!json['error_product']){
						$('#content').parent().before('<div class="alert alert-success"><svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 56 56" fill="none"><rect x="4" y="4" width="48" height="48" rx="24" fill="#fdbbbd"/><path fill-rule="evenodd" clip-rule="evenodd" d="M19.4639 22.5264C20.4016 21.5891 21.6731 21.0625 22.9989 21.0625C24.3248 21.0625 25.5963 21.5891 26.5339 22.5264L27.9989 23.9902L29.4639 22.5264C29.9252 22.0489 30.4769 21.668 31.0869 21.4059C31.6969 21.1439 32.353 21.006 33.0169 21.0002C33.6808 20.9944 34.3392 21.1209 34.9537 21.3723C35.5682 21.6237 36.1264 21.995 36.5959 22.4645C37.0654 22.9339 37.4366 23.4922 37.688 24.1067C37.9394 24.7212 38.066 25.3796 38.0602 26.0434C38.0544 26.7073 37.9165 27.3634 37.6544 27.9735C37.3924 28.5835 37.0115 29.1352 36.5339 29.5964L27.9989 38.1327L19.4639 29.5964C18.5266 28.6588 18 27.3873 18 26.0614C18 24.7356 18.5266 23.4641 19.4639 22.5264V22.5264Z" fill="#ffdfe0"/><rect x="4" y="4" width="48" height="48" rx="24" stroke="#ffdfe0" stroke-width="8"/></svg> ' + json['success'] + ' <div class="alert_buttons"><button type="button" class="close_alert" data-dismiss="alert">' + json['text_back'] + '</button><a href="' + json['basket'] + '" class="to_basket">' + json['text_go'] + '</a></div></div>');
					} else {
						$('#content').parent().before('<div class="alert alert-success"><svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 56 56" fill="none"><rect x="4" y="4" width="48" height="48" rx="24" fill="#fdbbbd"/><path fill-rule="evenodd" clip-rule="evenodd" d="M19.4639 22.5264C20.4016 21.5891 21.6731 21.0625 22.9989 21.0625C24.3248 21.0625 25.5963 21.5891 26.5339 22.5264L27.9989 23.9902L29.4639 22.5264C29.9252 22.0489 30.4769 21.668 31.0869 21.4059C31.6969 21.1439 32.353 21.006 33.0169 21.0002C33.6808 20.9944 34.3392 21.1209 34.9537 21.3723C35.5682 21.6237 36.1264 21.995 36.5959 22.4645C37.0654 22.9339 37.4366 23.4922 37.688 24.1067C37.9394 24.7212 38.066 25.3796 38.0602 26.0434C38.0544 26.7073 37.9165 27.3634 37.6544 27.9735C37.3924 28.5835 37.0115 29.1352 36.5339 29.5964L27.9989 38.1327L19.4639 29.5964C18.5266 28.6588 18 27.3873 18 26.0614C18 24.7356 18.5266 23.4641 19.4639 22.5264V22.5264Z" fill="#ffdfe0"/><rect x="4" y="4" width="48" height="48" rx="24" stroke="#ffdfe0" stroke-width="8"/></svg> ' + json['error_product'] + ' <div class="alert_buttons"><button type="button" class="close_alert" data-dismiss="alert">' + json['text_back'] + '</button><a href="' + json['basket'] + '" class="to_basket">' + json['text_go'] + '</a></div></div>');
					}

				}
				$('#wishlist-total span.whishlist-total').css('display', 'flex');
				$('#wishlist-total span.whishlist-total').html(json['total']);
				$('#wishlist-total').attr('title', json['total']);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
}

var compare = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=product/compare/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert-dismissible').remove();

				if (json['success']) {
					$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					$('#compare-total').html(json['total']);

					$('html, body').animate({ scrollTop: 0 }, 'slow');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
}

/* Agree to Terms */
$(document).delegate('.agree', 'click', function(e) {
	e.preventDefault();

	$('#modal-agree').remove();

	var element = this;

	$.ajax({
		url: $(element).attr('href'),
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-agree" class="modal">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
			html += '      </div>';
			html += '      <div class="modal-body">' + data + '</div>';
			html += '    </div>';
			html += '  </div>';
			html += '</div>';

			$('body').append(html);

			$('#modal-agree').modal('show');
		}
	});
});

// Autocomplete */
(function($) {
	$.fn.autocomplete = function(option) {
		return this.each(function() {
			this.timer = null;
			this.items = new Array();

			$.extend(this, option);

			$(this).attr('autocomplete', 'off');

			// Focus
			$(this).on('focus', function() {
				this.request();
			});

			// Blur
			$(this).on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);
			});

			// Keydown
			$(this).on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}
			});

			// Click
			this.click = function(event) {
				event.preventDefault();

				value = $(event.target).parent().attr('data-value');

				if (value && this.items[value]) {
					this.select(this.items[value]);
				}
			}

			// Show
			this.show = function() {
				var pos = $(this).position();

				$(this).siblings('ul.dropdown-menu').css({
					top: pos.top + $(this).outerHeight(),
					left: pos.left
				});

				$(this).siblings('ul.dropdown-menu').show();
			}

			// Hide
			this.hide = function() {
				$(this).siblings('ul.dropdown-menu').hide();
			}

			// Request
			this.request = function() {
				clearTimeout(this.timer);

				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			}

			// Response
			this.response = function(json) {
				html = '';

				if (json.length) {
					for (i = 0; i < json.length; i++) {
						this.items[json[i]['value']] = json[i];
					}

					for (i = 0; i < json.length; i++) {
						if (!json[i]['category']) {
							html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
						}
					}

					// Get all the ones with a categories
					var category = new Array();

					for (i = 0; i < json.length; i++) {
						if (json[i]['category']) {
							if (!category[json[i]['category']]) {
								category[json[i]['category']] = new Array();
								category[json[i]['category']]['name'] = json[i]['category'];
								category[json[i]['category']]['item'] = new Array();
							}

							category[json[i]['category']]['item'].push(json[i]);
						}
					}

					for (i in category) {
						html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

						for (j = 0; j < category[i]['item'].length; j++) {
							html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
						}
					}
				}

				if (html) {
					this.show();
				} else {
					this.hide();
				}

				$(this).siblings('ul.dropdown-menu').html(html);
			}

			$(this).after('<ul class="dropdown-menu"></ul>');
			$(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));

		});
	}
})(window.jQuery);
