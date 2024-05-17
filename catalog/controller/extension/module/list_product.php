<?php
class ControllerExtensionModuleListProduct extends Controller {
    public function index($setting) {
        static $module = 0;

        $this->load->model('localisation/language');


        $data['product_wishlist_status'] =  $this->config->get('theme_default_all_product_wishlist_status');
        $data['product_compare_status'] =  $this->config->get('theme_default_all_product_compare_status');
        $languages = $this->model_localisation_language->getLanguages();
        $language_id = $this->config->get('config_language_id');

        $this->load->language('extension/module/list_product');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        //$this->load->model('setting/setting');
        $data['type_slide'] =  $setting['type_slide'];

        if($setting['type_slide'] == 0){
            $this->document->addStyle('catalog/view/javascript/owl-carousel/owl.carousel.min.css');
            $this->document->addScript('catalog/view/javascript/owl-carousel/owl.carousel.min.js');
        } elseif($setting['type_slide'] == 1){
            $this->document->addStyle('catalog/view/javascript/jquery/swiper-bundle/swiper-bundle.min.css');
            $this->document->addScript('catalog/view/javascript/jquery/swiper-bundle/swiper-bundle.min.js');
        }


        $data['products'] = array();

        if (!$setting['limit']) {
            $setting['limit'] = 4;
        }

        if ($setting['title']) {
            $data['heading_title'] = html_entity_decode($setting['title'][$language_id], ENT_QUOTES, 'UTF-8');
        }

        $data['controls'] = isset($setting['controls']) ? $setting['controls'] : array();
        $data['module_type'] = $setting['module_type'];
        $data['autoplay'] = $setting['autoplay'];
        $data['autoplay_speed'] = $setting['autoplay_speed'];
        $data['hide_out_of_stock_products'] = $setting['hide_out_of_stock_products'];
        $data['page_status'] = $setting['page_status'];
        if($setting['page_status'] == 1){
            $data['page_title'] = $setting['page_title'][$language_id];
            $data['page_url'] = $setting['page_url'][$language_id];
        }


        $data['items'] = $setting['items'] ? $setting['items'] : 1;
        $data['responsive_items'] = array();

        $responsive_items = isset($setting['responsive_items']) ? $setting['responsive_items'] : array();
        foreach ($responsive_items as $item) {
            if ($item['breakpoint'] && $item['amount']) {
                $data['responsive_items'][] = array(
                    'breakpoint' => $item['breakpoint'],
                    'amount' => $item['amount']
                );
            }
        }

        if (isset($setting['hide_noimage_products'])) {
            $hide_noimage_products = $setting['hide_noimage_products'];
        } else {
            $hide_noimage_products = false;
        }

        $results = array();

        if ($setting['module_type'] == 'latest') {
            $filter_data = array(
                'sort'  => 'p.date_added',
                'order' => 'DESC',
                'start' => 0,
                'limit' => $setting['limit']
            );

            $results = $this->model_catalog_product->getProducts($filter_data);
        }

        if ($setting['module_type'] == 'special') {
            $filter_data = array(
                'sort'  => 'pd.name',
                'order' => 'ASC',
                'start' => 0,
                'limit' => $setting['limit']
            );

            $results = $this->model_catalog_product->getProductSpecials($filter_data);
        }

        if ($setting['module_type'] == 'bestseller') {
            $results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);
        }

        if ($results) {
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
                if($setting['height']){
                    $data['img_height'] = $setting['height'];
                }
                if ($result['image'] && is_file(DIR_IMAGE . $result['image'])) {
                    $image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
                } else {
                    $image = $this->model_tool_image->resize('noimage.png', $setting['width'], $setting['height']);
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
                    $rating = $result['rating'];
                } else {
                    $rating = false;
                }

                $stickers = array();


                if (!($hide_noimage_products & !$result['image'])) {
                    $product_data = [
                        'product_id'  => $result['product_id'],
                        'thumb'       => $image,
                        'img_width'   =>  $setting['width'] . 'px',
                        'img_height'  =>  $setting['height'] . 'px',
                        'stickers'  			=> $stickers,
                        'type_stikers' => $setting['module_type'],
                        'name'        => $result['name'],
                        'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                        'quantity'    => $result['quantity'],
                        'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
                        'price'       => $price,
                        'special'     => $special,
                        'tax'         => $tax,
                        'rating'      => $rating,
                        'slide'       => 'swiper-slide',
                        'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
                    ];
                    $data['products'][] = $this->load->controller('product/mini_product', $product_data);
                }

            }

        }

        if ($setting['module_type'] == 'featured' || $setting['module_type'] == 'viewed') {

            $products = array();

            if (!empty($setting['product']) && $setting['module_type'] == 'featured') {
                $products = array_slice($setting['product'], 0, (int)$setting['limit']);
            }

            if ($setting['module_type'] == 'viewed') {

                if (isset($this->session->data['product_view']['looked'])) {
                    $products = $this->session->data['product_view']['looked'];
                }

                if (!empty($products)) {
                    $products = array_unique($products);
                    krsort($products);
                    $products = array_slice($products, 0, (int)$setting['limit']);
                }

            }

            if (!empty($products)) {
                foreach ($products as $product_id) {
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
                        if($setting['height']){
                            $data['img_height'] = $setting['height'];
                        }

                        if ($product_info['image']) {
                            $image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
                        } else {
                            $image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
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

                        /* $stickers = array();

                         if ($product_info['price'] && $product_info['special']) {
                             $stickers['special'] = array(
                                 'text'  => round(100 - ($product_info['special'] / $product_info['price']) * 100) * (-1) . '%',
                                 'class' => 'badge stiker-special'
                             );
                         }*/


                        if (!($hide_noimage_products & !$product_info['image'])) {
                            $product_data1 = [
                                'product_id'  => $product_info['product_id'],
                                'thumb'       => $image,
                                'img_width'   =>  $setting['width'] . 'px',
                                'img_height'  =>  $setting['height'] . 'px',
                                'thumb_holder'    => $this->model_tool_image->resize('catalog/default/src_holder.png', $setting['width'], $setting['height']),
                                'stickers'  			=> $stickers,
                                'type_stikers' => $setting['module_type'],
                                'name'        => $product_info['name'],
                                'description' => utf8_substr(trim(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                                'quantity'    => $product_info['quantity'],
                                'minimum'     => $product_info['minimum'] > 0 ? $product_info['minimum'] : 1,
                                'price'       => $price,
                                'special'     => $special,
                                'tax'         => $tax,
                                'rating'      => $rating,
                                'slide'       => 'swiper-slide',
                                'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
                            ];
                            $data['products'][] = $this->load->controller('product/mini_product', $product_data1);
                        }

                    }
                }
            }
        }

        if ($setting['shufle_products']) {
            shuffle($data['products']);
        }

        $data['module'] = $module++;

        if ($data['products']) {
            return $this->load->view('extension/module/list_product', $data);
        }
    }
}
