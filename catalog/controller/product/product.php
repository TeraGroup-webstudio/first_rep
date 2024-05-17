<?php
class ControllerProductProduct extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('product/product');
        $this->load->model('tool/image');

        /* setting */
        $data['status_one_click'] = $this->config->get('theme_default_product_one_click_status');
        $data['advantage_status'] = $this->config->get('theme_default_product_advantage_status');
        $data['product_wishlist_status'] = $this->config->get('theme_default_product_wishlist_status');
        $data['product_compare_status'] = $this->config->get('theme_default_product_compare_status');
        $data['product_model_status'] = $this->config->get('theme_default_product_model_status');
        $data['product_manufacturer_status'] = $this->config->get('theme_default_product_manufacturer_status');
        $data['product_stock_status'] = $this->config->get('theme_default_product_stock_status');
        $data['product_shot_description_status'] = $this->config->get('theme_default_product_shot_description_status');


        if($this->config->get('theme_default_product_advantage_status') == 1) {
            if($this->config->get('theme_default_product_advantage_view') == 0){
                $data['advantage_type'] = 'horizontally';
            } else if($this->config->get('theme_default_product_advantage_view') == 1) {
                $data['advantage_type'] = 'vertically';
            } else {
                $data['advantage_type'] = '';
            }

            $results_product_advantage = $this->config->get('theme_default_product_advantage');
            $data['product_advantages'] = array();

            foreach ($results_product_advantage as $result) {

                if (is_file(DIR_IMAGE . $result['image_peace'])) {
                    $image_peace = $result['image_peace'];
                    $thumb_peace = $result['image_peace'];
                } else {
                    $image_peace = '';
                    $thumb_peace = 'no_image.png';
                }

                $data['product_advantages'][] = array(
                    'image_peace' => $image_peace,
                    'thumb_peace' => $this->model_tool_image->resize($thumb_peace, 60, 60),
                    'name'  			=> $result['name'][$this->config->get('config_language_id')],
                    'description'  			=> $result['description'][$this->config->get('config_language_id')],
                    'sort'  			=> $result['sort']
                );
            }
        }
        //print_r($data['product_advantages']);
        $data['product_for_category_status'] = $this->config->get('theme_default_product_for_category_status');
        /* setting */


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$this->load->model('catalog/category');

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path)
					);
				}
			}

			// Set the last category breadcrumb
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$url = '';

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['breadcrumbs'][] = array(
					'text' => $category_info['name'],
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url)
				);
			}
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->get['manufacturer_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_brand'),
				'href' => $this->url->link('product/manufacturer')
			);

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			if ($manufacturer_info) {
				$data['breadcrumbs'][] = array(
					'text' => $manufacturer_info['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url)
				);
			}
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('product/search', $url)
			);
		}

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $product_info['name'],
				'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'])
			);

			$this->document->setTitle($product_info['meta_title']);
			$this->document->setDescription($product_info['meta_description']);
			$this->document->setKeywords($product_info['meta_keyword']);
			$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');
            $this->document->addOGMeta('property="og:url"', $this->url->link('product/product', 'product_id=' . $this->request->get['product_id']) );
			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

			$data['heading_title'] = $product_info['name'];

			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));

			$this->load->model('catalog/review');

			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

			$data['product_id'] = (int)$this->request->get['product_id'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] = $product_info['model'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');

            $data['stickers'] = array();

            $results = $this->model_catalog_product->getProductStickers($this->request->get['product_id']);

            foreach ($results as $result) {
                $data['stickers'][] = array(
                    'image' => $this->model_tool_image->resize($result['sticker'], 53, 25)
                );
            }

			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
				$data['class_stock'] = 'red';
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
                $data['class_stock'] = 'green';
			} else {
				$data['stock'] = $this->language->get('text_instock');
                $data['class_stock'] = 'green';
			}

			if ($product_info['image']) {
				//$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
				$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'), 'product_popup');
			} else {
				$data['popup'] = '';
			}

			if ($product_info['image']) {
				//$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'), 'product_thumb');
			} else {
				$data['thumb'] = '';
			}
            if ($product_info['image']) {
                //$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
                $data['main_thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
            } else {
                $data['main_thumb'] = '';
            }

			$data['images'] = array();

			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

            if ($product_info['image']) {
                $this->document->addOGMeta('property="og:image"', str_replace(' ', '%20', $this->model_tool_image->resize($product_info['image'], 600, 315)) );
                $this->document->addOGMeta('property="og:image:width"', '600');
                $this->document->addOGMeta('property="og:image:height"', '315');
            } else {
                $this->document->addOGMeta( 'property="og:image"', str_replace(' ', '%20', $this->model_tool_image->resize($this->config->get('config_logo'), 300, 300)) );
                $this->document->addOGMeta('property="og:image:width"', '300');
                $this->document->addOGMeta('property="og:image:height"', '300');
            }
            foreach ($results as $result) {
                $this->document->addOGMeta( 'property="og:image"', str_replace(' ', '%20', $this->model_tool_image->resize($result['image'], 600, 315)) );
                $this->document->addOGMeta('property="og:image:width"', '600');
                $this->document->addOGMeta('property="og:image:height"', '315');
            }

			foreach ($results as $result) {
				$data['images'][] = array(
					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'), 'product_popup'),
					'main_thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'), 'product_thumb'),
					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
				);
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

			$data['discounts'] = array();

			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
				);
			}

			/* update price */
            if ($data['price']) {
                $data['price'] = '<span data-value=\'' . $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')) . '\' class=\'autocalc-product-price\'>' . $data['price'] . '</span>';
            }
            if ($data['special']) {
                $data['special'] = '<span data-value=\'' . $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')) . '\' class=\'autocalc-product-special\'>' . $data['special'] . '</span>';
            }
            if ($data['points']) {
                $data['points'] = '<span data-value=\'' . $product_info['points'] . '\' class=\'autocalc-product-points\'>' . $data['points'] . '</span>';
            }
            if ($data['tax']) {
                $data['tax'] = '<span data-value=\'' . (float)($product_info['special'] ? $product_info['special'] : $product_info['price']) . '\' class=\'autocalc-product-tax\'>' . $data['tax'] . '</span>';
            }

            $data['apo_price_value'] = $product_info['price'];
            $data['apo_special_value'] = $product_info['special'];
            $data['apo_tax_value'] = (float)$product_info['special'] ? $product_info['special'] : $product_info['price'];
            $data['apo_points_value'] = $product_info['points'];

            $currency_code = $this->session->data['currency'];
            $data['autocalc_currency'] = array(
                'value'           => (float)$this->currency->getValue($currency_code),
                'symbol_left'     => str_replace("'", "\'", $this->currency->getSymbolLeft($currency_code)),
                'symbol_right'    => str_replace("'", "\'", $this->currency->getSymbolRight($currency_code)),
                'decimals'        => (int)$this->currency->getDecimalPlace($currency_code),
                'decimal_point'   => $this->language->get('decimal_point'),
                'thousand_point'  => $this->language->get('thousand_point'),
            );


            $currency2_code = $this->config->get('theme_default_product_refresh_price_currency2');
            if ($this->currency->has($currency2_code) && $currency2_code != $currency_code) {
                $currency_code = $currency2_code;
                $data['autocalc_currency2'] = array(
                    'value'           => (float)$this->currency->getValue($currency_code),
                    'symbol_left'     => str_replace("'", "\'", $this->currency->getSymbolLeft($currency_code)),
                    'symbol_right'    => str_replace("'", "\'", $this->currency->getSymbolRight($currency_code)),
                    'decimals'        => (int)$this->currency->getDecimalPlace($currency_code),
                    'decimal_point'   => $this->language->get('decimal_point'),
                    'thousand_point'  => $this->language->get('thousand_point'),
                );
            }

            $data['discounts_raw'] = $discounts;

            $data['tax_class_id'] = $product_info['tax_class_id'];
            $data['tax_rates'] = $this->tax->getRates(0, $product_info['tax_class_id']);

            $data['autocalc_option_special'] = $this->config->get('theme_default_product_refresh_price_option_special');
            $data['autocalc_option_discount'] = $this->config->get('theme_default_product_refresh_price_option_discount');
            $data['autocalc_not_mul_qty'] = $this->config->get('theme_default_product_refresh_price_not_mul_qty');
            $data['autocalc_select_first'] = $this->config->get('theme_default_product_refresh_price_select_first');
			/* update price */

			$data['options'] = array();

			foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
				$product_option_value_data = array();

				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = false;
						}

                        if ($price) {
                            switch ($option_value['price_prefix']) {
                                case '%':
                                    $price = ($option_value['price'] > 0 ? '+' : '') . (float)$option_value['price'] . '%';
                                    break;
                                case '*':
                                    $price = '*' . (float)$option_value['price'];
                                    break;
                                case '/':
                                    $price = '/' . (float)$option_value['price'];
                                    break;
                            }
                        }
                        if ($this->config->get('theme_default_product_refresh_price_hide_option_price')) $price = false;

						$product_option_value_data[] = array(
                            'apo_price_value'               => $option_value['price'],
                            'apo_points_value'              => isset($option_value['points_prefix']) && $option_value['points'] ? intval($option_value['points_prefix'].$option_value['points']) : 0,
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						);
					}
				}

				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				);
			}

            $meta_price = ( $data['special'] != false) ? $data['special'] : $data['price'] ;
            $meta_price = trim(trim(($data['special'] != false) ? $data['special'] : $data['price'], $this->currency->getSymbolLeft($this->session->data['currency'])), $this->currency->getSymbolRight($this->session->data['currency']));
            $decimal_point_meta_price = $this->language->get('decimal_point') ? $this->language->get('decimal_point') : '.';
            $thousand_point_meta_price = $this->language->get('thousand_point')? $this->language->get('thousand_point') : ' ';
            $meta_price = str_replace($thousand_point_meta_price, '', $meta_price);
            if ( $decimal_point_meta_price != '.' ) {
                $meta_price = str_replace($decimal_point_meta_price, '.', $meta_price);
            }
            $meta_price = number_format((float)$meta_price, 2, '.', '');

            $this->document->addOGMeta('property="product:price:amount"', $meta_price);
            $this->document->addOGMeta('property="product:price:currency"', $this->session->data['currency']);

			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}

			$data['review_status'] = $this->config->get('config_review_status');

			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$data['review_guest'] = false;
			}

			if ($this->customer->isLogged()) {
				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			} else {
				$data['customer_name'] = '';
			}

			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}

			$data['share'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']);

			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

            $data['products_to_category'] = array();


			if($this->config->get('theme_default_product_for_category_status') == 1) {
                $results = $this->model_catalog_product->getRelatedByCategory($this->request->get['product_id']);

                foreach ($results as $result) {
                    $results_sticker = $this->model_catalog_product->getProductStickers($result['product_id']);

                    $stickers = array();
                    foreach ($results_sticker as $result_sticker) {
                        if ($result_sticker['sticker']) {
                            $sticker_image = $this->model_tool_image->resize($result_sticker['sticker'], 53, 25);
                        } else {
                            $sticker_image = false;
                        }
                        $stickers[] = $sticker_image;
                    }

                    if ($result['image']) {
                        $image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
                    } else {
                        $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
                    }

                    if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                        $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    } else {
                        $price = false;
                    }

                    if ((float)$result['special']) {
                        $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    } else {
                        $special = false;
                    }

                    if ($this->config->get('config_tax')) {
                        $tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
                    } else {
                        $tax = false;
                    }

                    if ($this->config->get('config_review_status')) {
                        $rating = (int)$result['rating'];
                    } else {
                        $rating = false;
                    }

                    $products_to_category = [
                        'product_id'  => $result['product_id'],
                        'thumb'       => $image,
                        'stickers'    => $stickers,
                        'name'        => $result['name'],
                        'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                        'price'       => $price,
                        'special'     => $special,
                        'tax'         => $tax,
                        'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
                        'rating'      => $rating,
                        'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
                        ];
                    $data['products_to_category'][] = $this->load->controller('product/mini_product', $products_to_category);
                }

            }
			$data['products'] = array();

			$results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);

			foreach ($results as $result) {
                $results_sticker = $this->model_catalog_product->getProductStickers($result['product_id']);
                $stickers = array();
                foreach ($results_sticker as $result_sticker) {
                    if ($result_sticker['sticker']) {
                        $sticker_image = $this->model_tool_image->resize($result_sticker['sticker'], 53, 25);
                    } else {
                        $sticker_image = false;
                    }
                    $stickers[] = $sticker_image;
                }

				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

                $product_data = [
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
                    'stickers'    => $stickers,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
                ];
                $data['products'][] = $this->load->controller('product/mini_product', $product_data);
			}

			/* product view*/
            /*if (isset($this->request->get['product_id'])) {
                $this->session->data['product_view']['looked'][] = $this->request->get['product_id'];
            }*/

            if($this->config->get('theme_default_product_view_status') == 1) {
                if($this->config->get('theme_default_product_view_count') != '') {
                    $count_view = $this->config->get('theme_default_product_view_count');
                } else {
                    $count_view = 4;
                }

                if (isset($this->session->data['product_view']['looked'])) {
                    $products_view = $this->session->data['product_view']['looked'];
                } else {
                    $products_view = array();
                }

                if (isset($this->request->get['product_id'])) {
                    $isset = false; // наявність товару в списку
                    foreach($products_view as $key => $product_id){
                        if ($product_id == $this->request->get['product_id']) {
                            $isset = true;
                            unset($products_view[$key]);
                        }
                    }

                    if (!$isset) {
                        $this->session->data['product_view']['looked'][] = $this->request->get['product_id'];
                    }

                    if (count($this->session->data['product_view']['looked']) > (int)$count_view) {
                        $iteration = count($this->session->data['product_view']['looked']) - (int)$count_view;
                        for ($i=0; $i<$iteration; $i++){
                            array_shift($this->session->data['product_view']['looked']);
                        }
                    }
                }

                $data['products_view'] = array();

                foreach(array_reverse($products_view) as $key => $product_id){
                    $product_info = $this->model_catalog_product->getProduct($product_id);
                    if ($product_info) {

                        $results_sticker = $this->model_catalog_product->getProductStickers($product_info['product_id']);
                        $stickers = array();
                        foreach ($results_sticker as $result_sticker) {
                            if ($result_sticker['sticker']) {
                                $sticker_image = $this->model_tool_image->resize($result_sticker['sticker'], 53, 25);
                            } else {
                                $sticker_image = false;
                            }
                            $stickers[] = $sticker_image;
                        }

                        if ($product_info['image']) {
                            $image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
                        } else {
                            $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
                        }

                        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                            $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        } else {
                            $price = false;
                        }

                        if ((float)$product_info['special']) {
                            $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        } else {
                            $special = false;
                        }

                        if ($this->config->get('config_tax')) {
                            $tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
                        } else {
                            $tax = false;
                        }

                        if ($this->config->get('config_review_status')) {
                            $rating = $product_info['rating'];
                        } else {
                            $rating = false;
                        }
                        $product_view_data = [
                            'product_id'  => $product_info['product_id'],
                            'thumb'       => $image,
                            'stickers'    => $stickers,
                            'name'        => $product_info['name'],
                            'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                            'price'       => $price,
                            'special'     => $special,
                            'tax'         => $tax,
                            'rating'      => $rating,
                            'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
                         ];
                        $data['products_view'][] = $this->load->controller('product/mini_product', $product_view_data);
                    }
                }

                $data['count_view'] = count($data['products_view']);

            }
			/* product view*/

			$data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}



			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);

			$this->model_catalog_product->updateViewed($this->request->get['product_id']);
			
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('product/product', $data));
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function review() {
		$this->load->language('product/product');

		$this->load->model('catalog/review');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = array();

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5;
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

		$this->response->setOutput($this->load->view('product/review', $data));
	}

	public function write() {
		$this->load->language('product/product');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error'] = $captcha;
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('catalog/review');

				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getRecurringDescription() {
		$this->load->language('product/product');
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['recurring_id'])) {
			$recurring_id = $this->request->post['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		$recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

		$json = array();

		if ($product_info && $recurring_info) {
			if (!$json) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($recurring_info['trial_status'] == 1) {
					$price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
				} else {
					$trial_text = '';
				}

				$price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				if ($recurring_info['duration']) {
					$text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				} else {
					$text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				}

				$json['success'] = $text;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
