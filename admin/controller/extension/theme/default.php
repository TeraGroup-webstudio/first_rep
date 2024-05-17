<?php
class ControllerExtensionThemeDefault extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/theme/default');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['user_token'] = $this->session->data['user_token'];

        $data['text'] = '';

        $file = str_replace("system/", "", DIR_SYSTEM) . 'robots.txt';

        if (file_exists($file)) {
            $data['text'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
        }

        $start_setting = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `store_id` = '0' AND `code`='start' AND `key`='start_settings' AND `value` = '1'");
        if($start_setting){
            $data['start_setting'] = true;
        } else {
            $data['start_setting'] = false;
        }
        $this->load->model('tool/image');
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if(isset($this->request->post['theme_default_robots'])){
                $file = str_replace("system/", "", DIR_SYSTEM) . 'robots.txt';

                $handles = fopen($file, 'w+');

                $robots = str_replace("&amp;", "&", $this->request->post['theme_default_robots']);

                fwrite($handles, $robots);

                fclose($handles);

            }
            $this->model_setting_setting->editSetting('theme_default', $this->request->post, $this->request->get['store_id']);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['product_limit'])) {
            $data['error_product_limit'] = $this->error['product_limit'];
        } else {
            $data['error_product_limit'] = '';
        }

        if (isset($this->error['product_description_length'])) {
            $data['error_product_description_length'] = $this->error['product_description_length'];
        } else {
            $data['error_product_description_length'] = '';
        }

        if (isset($this->error['image_category'])) {
            $data['error_image_category'] = $this->error['image_category'];
        } else {
            $data['error_image_category'] = '';
        }

        if (isset($this->error['image_thumb'])) {
            $data['error_image_thumb'] = $this->error['image_thumb'];
        } else {
            $data['error_image_thumb'] = '';
        }

        if (isset($this->error['image_popup'])) {
            $data['error_image_popup'] = $this->error['image_popup'];
        } else {
            $data['error_image_popup'] = '';
        }

        if (isset($this->error['image_product'])) {
            $data['error_image_product'] = $this->error['image_product'];
        } else {
            $data['error_image_product'] = '';
        }

        if (isset($this->error['image_additional'])) {
            $data['error_image_additional'] = $this->error['image_additional'];
        } else {
            $data['error_image_additional'] = '';
        }

        if (isset($this->error['image_related'])) {
            $data['error_image_related'] = $this->error['image_related'];
        } else {
            $data['error_image_related'] = '';
        }

        if (isset($this->error['image_compare'])) {
            $data['error_image_compare'] = $this->error['image_compare'];
        } else {
            $data['error_image_compare'] = '';
        }

        if (isset($this->error['image_wishlist'])) {
            $data['error_image_wishlist'] = $this->error['image_wishlist'];
        } else {
            $data['error_image_wishlist'] = '';
        }

        if (isset($this->error['image_cart'])) {
            $data['error_image_cart'] = $this->error['image_cart'];
        } else {
            $data['error_image_cart'] = '';
        }

        if (isset($this->error['image_location'])) {
            $data['error_image_location'] = $this->error['image_location'];
        } else {
            $data['error_image_location'] = '';
        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/theme/default', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true)
        );

        $data['action'] = $this->url->link('extension/theme/default', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme', true);


        if (isset($this->request->get['store_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $setting_info = $this->model_setting_setting->getSetting('theme_default', $this->request->get['store_id']);
        }

        if (isset($this->request->post['theme_default_directory'])) {
            $data['theme_default_directory'] = $this->request->post['theme_default_directory'];
        } elseif (isset($setting_info['theme_default_directory'])) {
            $data['theme_default_directory'] = $setting_info['theme_default_directory'];
        } else {
            $data['theme_default_directory'] = 'default';
        }

        $data['directories'] = array();

        $directories = glob(DIR_CATALOG . 'view/theme/*', GLOB_ONLYDIR);

        foreach ($directories as $directory) {
            $data['directories'][] = basename($directory);
        }

        if (isset($this->request->post['theme_default_product_limit'])) {
            $data['theme_default_product_limit'] = $this->request->post['theme_default_product_limit'];
        } elseif (isset($setting_info['theme_default_product_limit'])) {
            $data['theme_default_product_limit'] = $setting_info['theme_default_product_limit'];
        } else {
            $data['theme_default_product_limit'] = 15;
        }

        if (isset($this->request->post['theme_default_status'])) {
            $data['theme_default_status'] = $this->request->post['theme_default_status'];
        } elseif (isset($setting_info['theme_default_status'])) {
            $data['theme_default_status'] = $setting_info['theme_default_status'];
        } else {
            $data['theme_default_status'] = '';
        }

        if (isset($this->request->post['theme_default_no_index_status'])) {
            $data['theme_default_no_index_status'] = $this->request->post['theme_default_no_index_status'];
        } elseif (isset($setting_info['theme_default_no_index_status'])) {
            $data['theme_default_no_index_status'] = $setting_info['theme_default_no_index_status'];
        } else {
            $data['theme_default_no_index_status'] = '';
        }

        if (isset($this->request->post['theme_default_version_bootstrap'])) {
            $data['theme_default_version_bootstrap'] = $this->request->post['theme_default_version_bootstrap'];
        } elseif (isset($setting_info['theme_default_version_bootstrap'])) {
            $data['theme_default_version_bootstrap'] = $setting_info['theme_default_version_bootstrap'];
        } else {
            $data['theme_default_version_bootstrap'] = 1;
        }

        if (isset($this->request->post['theme_default_webfont_status'])) {
            $data['theme_default_webfont_status'] = $this->request->post['theme_default_webfont_status'];
        } elseif (isset($setting_info['theme_default_webfont_status'])) {
            $data['theme_default_webfont_status'] = $setting_info['theme_default_webfont_status'];
        } else {
            $data['theme_default_webfont_status'] = false;
        }

        if (isset($this->request->post['theme_default_webfont_style'])) {
            $data['theme_default_webfont_style'] = $this->request->post['theme_default_webfont_style'];
        } elseif (isset($setting_info['theme_default_webfont_style'])) {
            $data['theme_default_webfont_style'] = $setting_info['theme_default_webfont_style'];
        } else {
            $data['theme_default_webfont_style'] = false;
        }

        if (isset($this->request->post['theme_default_webfont_link'])) {
            $data['theme_default_webfont_link'] = $this->request->post['theme_default_webfont_link'];
        } elseif (isset($setting_info['theme_default_webfont_link'])) {
            $data['theme_default_webfont_link'] = $setting_info['theme_default_webfont_link'];
        } else {
            $data['theme_default_webfont_link'] = false;
        }

        /* header */
        if (isset($this->request->post['theme_default_header_box_info_status'])) {
            $data['theme_default_header_box_info_status'] = $this->request->post['theme_default_header_box_info_status'];
        } elseif (isset($setting_info['theme_default_header_box_info_status'])) {
            $data['theme_default_header_box_info_status'] = $setting_info['theme_default_header_box_info_status'];
        } else {
            $data['theme_default_header_box_info_status'] = false;
        }

        if (isset($this->request->post['theme_default_header_box_info_text'])) {
            $data['theme_default_header_box_info_text'] = $this->request->post['theme_default_header_box_info_text'];
        } elseif (isset($setting_info['theme_default_header_box_info_text'])) {
            $data['theme_default_header_box_info_text'] = $setting_info['theme_default_header_box_info_text'];
        } else {
            $data['theme_default_header_box_info_text'] = '';
        }

        if (isset($this->request->post['theme_default_header_box_info_price'])) {
            $data['theme_default_header_box_info_price'] = $this->request->post['theme_default_header_box_info_price'];
        } elseif (isset($setting_info['theme_default_header_box_info_price'])) {
            $data['theme_default_header_box_info_price'] = $setting_info['theme_default_header_box_info_price'];
        } else {
            $data['theme_default_header_box_info_price'] = '';
        }

        if (isset($this->request->post['theme_default_header_box_info_price_ot'])) {
            $data['theme_default_header_box_info_price_ot'] = $this->request->post['theme_default_header_box_info_price_ot'];
        } elseif (isset($setting_info['theme_default_header_box_info_price_ot'])) {
            $data['theme_default_header_box_info_price_ot'] = $setting_info['theme_default_header_box_info_price_ot'];
        } else {
            $data['theme_default_header_box_info_price_ot'] = '';
        }

        if (isset($this->request->post['theme_default_header_box_info_price_symbol'])) {
            $data['theme_default_header_box_info_price_symbol'] = $this->request->post['theme_default_header_box_info_price_symbol'];
        } elseif (isset($setting_info['theme_default_header_box_info_price_symbol'])) {
            $data['theme_default_header_box_info_price_symbol'] = $setting_info['theme_default_header_box_info_price_symbol'];
        } else {
            $data['theme_default_header_box_info_price_symbol'] = '';
        }

        if (isset($this->request->post['theme_default_header_box_info_url'])) {
            $data['theme_default_header_box_info_url'] = $this->request->post['theme_default_header_box_info_url'];
        } elseif (isset($setting_info['theme_default_header_box_info_url'])) {
            $data['theme_default_header_box_info_url'] = $setting_info['theme_default_header_box_info_url'];
        } else {
            $data['theme_default_header_box_info_url'] = '';
        }

        if (isset($this->request->post['theme_default_header_box_info_image'])) {
            $data['theme_default_header_box_info_image'] = $this->request->post['theme_default_header_box_info_image'];
        } elseif (isset($setting_info['theme_default_header_box_info_image'])) {
            $data['theme_default_header_box_info_image'] = $setting_info['theme_default_header_box_info_image'];
        } else {
            $data['theme_default_header_box_info_image'] = '';
        }

        if (isset($this->request->post['theme_default_header_box_info_image']) && is_file(DIR_IMAGE . $this->request->post['theme_default_header_box_info_image'])) {
            $data['theme_default_header_box_info_thumb'] = $this->model_tool_image->resize($this->request->post['theme_default_header_box_info_image'], 100, 100);
        } elseif (isset($setting_info['theme_default_header_box_info_image']) && is_file(DIR_IMAGE . $setting_info['theme_default_header_box_info_image'])) {
            $data['theme_default_header_box_info_thumb'] = $this->model_tool_image->resize($setting_info['theme_default_header_box_info_image'], 100, 100);
            $size_water = getimagesize(DIR_IMAGE . $setting_info['theme_default_header_box_info_image']);
        } else {
            $data['theme_default_header_box_info_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);;
        }

        /* footer */

        if (isset($this->request->post['theme_default_footer_seti_status'])) {
            $data['theme_default_footer_seti_status'] = $this->request->post['theme_default_footer_seti_status'];
        } elseif (isset($setting_info['theme_default_footer_seti_status'])) {
            $data['theme_default_footer_seti_status'] = $setting_info['theme_default_footer_seti_status'];
        } else {
            $data['theme_default_footer_seti_status'] = '';
        }

        $data['theme_default_footer_seties'] = array();

        if (isset($this->request->post['theme_default_footer_seti'])) {
            $results_seti = $this->request->post['theme_default_footer_seti'];
        } elseif (isset($setting_info['theme_default_footer_seti'])) {
            $results_seti = $setting_info['theme_default_footer_seti'];
        } else {
            $results_seti = array();
        }

        foreach ($results_seti as $result) {

            if (is_file(DIR_IMAGE . $result['image_peace'])) {
                $image_peace = $result['image_peace'];
                $thumb_peace = $result['image_peace'];
            } else {
                $image_peace = '';
                $thumb_peace = 'no_image.png';
            }

            $data['theme_default_footer_seties'][] = array(
                'image_peace' => $image_peace,
                'thumb_peace' => $this->model_tool_image->resize($thumb_peace, 60, 60),
                'link'  			=> $result['link'],
                'sort'  			=> $result['sort']
            );
        }
        /* footer */

        /*Решта налаштувань  */
        if (isset($this->request->post['theme_default_all_product_wishlist_status'])) {
            $data['theme_default_all_product_wishlist_status'] = $this->request->post['theme_default_all_product_wishlist_status'];
        } elseif (isset($setting_info['theme_default_all_product_wishlist_status'])) {
            $data['theme_default_all_product_wishlist_status'] = $setting_info['theme_default_all_product_wishlist_status'];
        } else {
            $data['theme_default_all_product_wishlist_status'] = false;
        }

        if (isset($this->request->post['theme_default_all_product_compare_status'])) {
            $data['theme_default_all_product_compare_status'] = $this->request->post['theme_default_all_product_compare_status'];
        } elseif (isset($setting_info['theme_default_all_product_compare_status'])) {
            $data['theme_default_all_product_compare_status'] = $setting_info['theme_default_all_product_compare_status'];
        } else {
            $data['theme_default_all_product_compare_status'] = false;
        }

        /*Решта налаштувань  */

        /* product */
        if (isset($this->request->post['theme_default_product_wishlist_status'])) {
            $data['theme_default_product_wishlist_status'] = $this->request->post['theme_default_product_wishlist_status'];
        } elseif (isset($setting_info['theme_default_product_wishlist_status'])) {
            $data['theme_default_product_wishlist_status'] = $setting_info['theme_default_product_wishlist_status'];
        } else {
            $data['theme_default_product_wishlist_status'] = false;
        }

        if (isset($this->request->post['theme_default_product_compare_status'])) {
            $data['theme_default_product_compare_status'] = $this->request->post['theme_default_product_compare_status'];
        } elseif (isset($setting_info['theme_default_product_compare_status'])) {
            $data['theme_default_product_compare_status'] = $setting_info['theme_default_product_compare_status'];
        } else {
            $data['theme_default_product_compare_status'] = false;
        }

        if (isset($this->request->post['theme_default_product_model_status'])) {
            $data['theme_default_product_model_status'] = $this->request->post['theme_default_product_model_status'];
        } elseif (isset($setting_info['theme_default_product_model_status'])) {
            $data['theme_default_product_model_status'] = $setting_info['theme_default_product_model_status'];
        } else {
            $data['theme_default_product_model_status'] = false;
        }

        if (isset($this->request->post['theme_default_product_manufacturer_status'])) {
            $data['theme_default_product_manufacturer_status'] = $this->request->post['theme_default_product_manufacturer_status'];
        } elseif (isset($setting_info['theme_default_product_manufacturer_status'])) {
            $data['theme_default_product_manufacturer_status'] = $setting_info['theme_default_product_manufacturer_status'];
        } else {
            $data['theme_default_product_manufacturer_status'] = false;
        }

        if (isset($this->request->post['theme_default_product_stock_status'])) {
            $data['theme_default_product_stock_status'] = $this->request->post['theme_default_product_stock_status'];
        } elseif (isset($setting_info['theme_default_product_stock_status'])) {
            $data['theme_default_product_stock_status'] = $setting_info['theme_default_product_stock_status'];
        } else {
            $data['theme_default_product_stock_status'] = false;
        }

        if (isset($this->request->post['theme_default_product_one_click_status'])) {
            $data['theme_default_product_one_click_status'] = $this->request->post['theme_default_product_one_click_status'];
        } elseif (isset($setting_info['theme_default_product_one_click_status'])) {
            $data['theme_default_product_one_click_status'] = $setting_info['theme_default_product_one_click_status'];
        } else {
            $data['theme_default_product_one_click_status'] = false;
        }

        if (isset($this->request->post['theme_default_product_one_click_setting'])) {
            $data['theme_default_product_one_click_setting'] = $this->request->post['theme_default_product_one_click_setting'];
        } elseif (isset($setting_info['theme_default_product_one_click_setting'])) {
            $data['theme_default_product_one_click_setting'] = $setting_info['theme_default_product_one_click_setting'];
        } else {
            $data['theme_default_product_one_click_setting'] = array();
        }

        $data['options'] = array();
        $this->load->model('catalog/option');
        foreach ($this->model_catalog_option->getOptions() as $product_option) {
            $data['options'][] = array(
                'option_id'  => $product_option['option_id'],
                'name'       => $product_option['name']
            );
        }

        /* Product Онвлення цін */
        $this->load->model('localisation/currency');

        $data['currencies'] = $this->model_localisation_currency->getCurrencies();

        if (isset($this->request->post['theme_default_product_refresh_price_status'])) {
            $data['theme_default_product_refresh_price_status'] = $this->request->post['theme_default_product_refresh_price_status'];
        } elseif (isset($setting_info['theme_default_product_refresh_price_status'])) {
            $data['theme_default_product_refresh_price_status'] = $setting_info['theme_default_product_refresh_price_status'];
        } else {
            $data['theme_default_product_refresh_price_status'] = false;
        }

        if (isset($this->request->post['theme_default_product_refresh_price_currency2'])) {
            $data['theme_default_product_refresh_price_currency2'] = $this->request->post['theme_default_product_refresh_price_currency2'];
        } elseif (isset($setting_info['theme_default_product_refresh_price_currency2'])) {
            $data['theme_default_product_refresh_price_currency2'] = $setting_info['theme_default_product_refresh_price_currency2'];
        } else {
            $data['theme_default_product_refresh_price_currency2'] = '';
        }

        if (isset($this->request->post['theme_default_product_refresh_price_option_special'])) {
            $data['theme_default_product_refresh_price_option_special'] = $this->request->post['theme_default_product_refresh_price_option_special'];
        } elseif (isset($setting_info['theme_default_product_refresh_price_option_special'])) {
            $data['theme_default_product_refresh_price_option_special'] = $setting_info['theme_default_product_refresh_price_option_special'];
        } else {
            $data['theme_default_product_refresh_price_option_special'] = false;
        }

        if (isset($this->request->post['theme_default_product_refresh_price_option_discount'])) {
            $data['theme_default_product_refresh_price_option_discount'] = $this->request->post['theme_default_product_refresh_price_option_discount'];
        } elseif (isset($setting_info['theme_default_product_refresh_price_option_discount'])) {
            $data['theme_default_product_refresh_price_option_discount'] = $setting_info['theme_default_product_refresh_price_option_discount'];
        } else {
            $data['theme_default_product_refresh_price_option_discount'] = false;
        }

        if (isset($this->request->post['theme_default_product_refresh_price_not_mul_qty'])) {
            $data['theme_default_product_refresh_price_not_mul_qty'] = $this->request->post['theme_default_product_refresh_price_not_mul_qty'];
        } elseif (isset($setting_info['theme_default_product_refresh_price_not_mul_qty'])) {
            $data['theme_default_product_refresh_price_not_mul_qty'] = $setting_info['theme_default_product_refresh_price_not_mul_qty'];
        } else {
            $data['theme_default_product_refresh_price_not_mul_qty'] = false;
        }

        if (isset($this->request->post['theme_default_product_refresh_price_select_first'])) {
            $data['theme_default_product_refresh_price_select_first'] = $this->request->post['theme_default_product_refresh_price_select_first'];
        } elseif (isset($setting_info['theme_default_product_refresh_price_select_first'])) {
            $data['theme_default_product_refresh_price_select_first'] = $setting_info['theme_default_product_refresh_price_select_first'];
        } else {
            $data['theme_default_product_refresh_price_select_first'] = false;
        }

        if (isset($this->request->post['theme_default_product_refresh_price_hide_option_price'])) {
            $data['theme_default_product_refresh_price_hide_option_price'] = $this->request->post['theme_default_product_refresh_price_hide_option_price'];
        } elseif (isset($setting_info['theme_default_product_refresh_price_hide_option_price'])) {
            $data['theme_default_product_refresh_price_hide_option_price'] = $setting_info['theme_default_product_refresh_price_hide_option_price'];
        } else {
            $data['theme_default_product_refresh_price_hide_option_price'] = false;
        }

        /* Product Онвлення цін */


        /* Опис */
        if (isset($this->request->post['theme_default_product_shot_description_status'])) {
            $data['theme_default_product_shot_description_status'] = $this->request->post['theme_default_product_shot_description_status'];
        } elseif (isset($setting_info['theme_default_product_shot_description_status'])) {
            $data['theme_default_product_shot_description_status'] = $setting_info['theme_default_product_shot_description_status'];
        } else {
            $data['theme_default_product_shot_description_status'] = false;
        }
        /* Опис */

        /* Product Переваги */

        if (isset($this->request->post['theme_default_product_advantage_status'])) {
            $data['theme_default_product_advantage_status'] = $this->request->post['theme_default_product_advantage_status'];
        } elseif (isset($setting_info['theme_default_product_advantage_status'])) {
            $data['theme_default_product_advantage_status'] = $setting_info['theme_default_product_advantage_status'];
        } else {
            $data['theme_default_product_advantage_status'] = false;
        }

        if (isset($this->request->post['theme_default_product_advantage_view'])) {
            $data['theme_default_product_advantage_view'] = $this->request->post['theme_default_product_advantage_view'];
        } elseif (isset($setting_info['theme_default_product_advantage_view'])) {
            $data['theme_default_product_advantage_view'] = $setting_info['theme_default_product_advantage_view'];
        } else {
            $data['theme_default_product_advantage_view'] = false;
        }

        if (isset($this->request->post['theme_default_product_advantage'])) {
            $results_product_advantage = $this->request->post['theme_default_product_advantage'];
        } elseif (isset($setting_info['theme_default_product_advantage'])) {
            $results_product_advantage = $setting_info['theme_default_product_advantage'];
        } else {
            $results_product_advantage = array();
        }

        $data['theme_default_product_advantages'] = array();

        foreach ($results_product_advantage as $result) {

            if (is_file(DIR_IMAGE . $result['image_peace'])) {
                $image_peace = $result['image_peace'];
                $thumb_peace = $result['image_peace'];
            } else {
                $image_peace = '';
                $thumb_peace = 'no_image.png';
            }



            $data['theme_default_product_advantages'][] = array(
                'image_peace' => $image_peace,
                'thumb_peace' => $this->model_tool_image->resize($thumb_peace, 60, 60),
                'name'  			=> $result['name'],
                'description'  			=> $result['description'],
                'sort'  			=> $result['sort']
            );
        }
        /* product */

        /* Супутні товари з категорії */
        if (isset($this->request->post['theme_default_product_for_category_status'])) {
            $data['theme_default_product_for_category_status'] = $this->request->post['theme_default_product_for_category_status'];
        } elseif (isset($setting_info['theme_default_product_for_category_status'])) {
            $data['theme_default_product_for_category_status'] = $setting_info['theme_default_product_for_category_status'];
        } else {
            $data['theme_default_product_for_category_status'] = false;
        }

        if (isset($this->request->post['theme_default_product_for_category_count'])) {
            $data['theme_default_product_for_category_count'] = $this->request->post['theme_default_product_for_category_count'];
        } elseif (isset($setting_info['theme_default_product_for_category_count'])) {
            $data['theme_default_product_for_category_count'] = $setting_info['theme_default_product_for_category_count'];
        } else {
            $data['theme_default_product_for_category_count'] = 0;
        }

        if (isset($this->request->post['theme_default_product_for_category_slide'])) {
            $data['theme_default_product_for_category_slide'] = $this->request->post['theme_default_product_for_category_slide'];
        } elseif (isset($setting_info['theme_default_product_for_category_slide'])) {
            $data['theme_default_product_for_category_slide'] = $setting_info['theme_default_product_for_category_slide'];
        } else {
            $data['theme_default_product_for_category_slide'] = 0;
        }

        /* product view */
        if (isset($this->request->post['theme_default_product_view_status'])) {
            $data['theme_default_product_view_status'] = $this->request->post['theme_default_product_view_status'];
        } elseif (isset($setting_info['theme_default_product_view_status'])) {
            $data['theme_default_product_view_status'] = $setting_info['theme_default_product_view_status'];
        } else {
            $data['theme_default_product_view_status'] = false;
        }

        if (isset($this->request->post['theme_default_product_view_count'])) {
            $data['theme_default_product_view_count'] = $this->request->post['theme_default_product_view_count'];
        } elseif (isset($setting_info['theme_default_product_view_count'])) {
            $data['theme_default_product_view_count'] = $setting_info['theme_default_product_view_count'];
        } else {
            $data['theme_default_product_view_count'] = 0;
        }

        if (isset($this->request->post['theme_default_product_view_slide'])) {
            $data['theme_default_product_view_slide'] = $this->request->post['theme_default_product_view_slide'];
        } elseif (isset($setting_info['theme_default_product_view_slide'])) {
            $data['theme_default_product_view_slide'] = $setting_info['theme_default_product_view_slide'];
        } else {
            $data['theme_default_product_view_slide'] = 0;
        }
        /* product view */



        /* lazy load */
        if (isset($this->request->post['theme_default_image_lazy_load_status'])) {
            $data['theme_default_image_lazy_load_status'] = $this->request->post['theme_default_image_lazy_load_status'];
        } elseif (isset($setting_info['theme_default_image_lazy_load_status'])) {
            $data['theme_default_image_lazy_load_status'] = $setting_info['theme_default_image_lazy_load_status'];
        } else {
            $data['theme_default_image_lazy_load_status'] = false;
        }

        if (isset($this->request->post['theme_default_lazy_load_image'])) {
            $data['theme_default_lazy_load_image'] = $this->request->post['theme_default_lazy_load_image'];
        } elseif (isset($setting_info['theme_default_lazy_load_image'])) {
            $data['theme_default_lazy_load_image'] = $setting_info['theme_default_lazy_load_image'];
        } else {
            $data['theme_default_lazy_load_image'] = '';
        }

        if (isset($this->request->post['theme_default_lazy_load_image']) && is_file(DIR_IMAGE . $this->request->post['theme_default_lazy_load_image'])) {
            $data['theme_default_lazy_load_thumb'] = $this->model_tool_image->resize($this->request->post['theme_default_lazy_load_image'], 100, 100);
        } elseif (isset($setting_info['theme_default_lazy_load_image']) && is_file(DIR_IMAGE . $setting_info['theme_default_lazy_load_image'])) {
            $data['theme_default_lazy_load_thumb'] = $this->model_tool_image->resize($setting_info['theme_default_lazy_load_image'], 100, 100);
            $size_water = getimagesize(DIR_IMAGE . $setting_info['theme_default_lazy_load_image']);
        } else {
            $data['theme_default_lazy_load_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);;
        }
        /* lazy load */

        /* webp */
        if (isset($this->request->post['theme_default_webp_status'])) {
            $data['theme_default_webp_status'] = $this->request->post['theme_default_webp_status'];
        } elseif (isset($setting_info['theme_default_webp_status'])) {
            $data['theme_default_webp_status'] = $setting_info['theme_default_webp_status'];
        } else {
            $data['theme_default_webp_status'] = false;
        }

        if (isset($this->request->post['theme_default_webp_webp_quality'])) {
            $data['theme_default_webp_webp_quality'] = $this->request->post['theme_default_webp_webp_quality'];
        } elseif (isset($setting_info['theme_default_webp_webp_quality'])) {
            $data['theme_default_webp_webp_quality'] = $setting_info['theme_default_webp_webp_quality'];
        } else {
            $data['theme_default_webp_webp_quality'] = '80';
        }
        /* webp */

        /* watermark */
        if (isset($this->request->post['theme_default_watermark_status'])) {
            $data['theme_default_watermark_status'] = $this->request->post['theme_default_watermark_status'];
        } elseif (isset($setting_info['theme_default_watermark_status'])) {
            $data['theme_default_watermark_status'] = $setting_info['theme_default_watermark_status'];
        } else {
            $data['theme_default_watermark_status'] = false;
        }

        if (isset($this->request->post['theme_default_watermark_image'])) {
            $data['theme_default_watermark_image'] = $this->request->post['theme_default_watermark_image'];
        } else if (isset($setting_info['theme_default_watermark_image'])){
            $data['theme_default_watermark_image'] = $setting_info['theme_default_watermark_image'];
        } else {
            $data['theme_default_watermark_image'] = '';
        }

        if (isset($this->request->post['theme_default_watermark_image']) && is_file(DIR_IMAGE . $this->request->post['theme_default_watermark_image'])) {
            $data['theme_default_watermark_thumb'] = $this->model_tool_image->resize($this->request->post['theme_default_watermark_image'], 100, 100);
        } elseif ($setting_info['theme_default_watermark_image'] && is_file(DIR_IMAGE . $setting_info['theme_default_watermark_image'])) {
            $data['theme_default_watermark_thumb'] = $this->model_tool_image->resize($setting_info['theme_default_watermark_image'], 100, 100);
            $size_water = getimagesize(DIR_IMAGE . $setting_info['theme_default_watermark_image']);
            //print_r($size_water);
            $data['watermark_image_width'] = $size_water[1];
            $data['watermark_image_height'] = $size_water[0];
        } else {
            $data['theme_default_watermark_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
            $data['watermark_image_width'] = 0;
            $data['watermark_image_height'] = 0;
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);


        if (isset($this->request->post['theme_default_watermark_zoom'])) {
            $data['theme_default_watermark_zoom'] = $this->request->post['theme_default_watermark_zoom'];
        } elseif (isset($setting_info['theme_default_watermark_zoom'])) {
            $data['theme_default_watermark_zoom'] = $setting_info['theme_default_watermark_zoom'];
        } else {
            $data['theme_default_watermark_zoom'] = '';
        }

        if (isset($this->request->post['theme_default_watermark_opacity'])) {
            $data['theme_default_watermark_opacity'] = $this->request->post['theme_default_watermark_opacity'];
        } elseif (isset($setting_info['theme_default_watermark_opacity'])) {
            $data['theme_default_watermark_opacity'] = $setting_info['theme_default_watermark_opacity'];
        } else {
            $data['theme_default_watermark_opacity'] = '';
        }

        if (isset($this->request->post['theme_default_watermark_pos_x_center'])) {
            $data['theme_default_watermark_pos_x_center'] = $this->request->post['theme_default_watermark_pos_x_center'];
        } elseif (isset($setting_info['theme_default_watermark_pos_x_center'])) {
            $data['theme_default_watermark_pos_x_center'] = $setting_info['theme_default_watermark_pos_x_center'];
        } else {
            $data['theme_default_watermark_pos_x_center'] = false;
        }

        if (isset($this->request->post['theme_default_watermark_pos_y_center'])) {
            $data['theme_default_watermark_pos_y_center'] = $this->request->post['theme_default_watermark_pos_y_center'];
        } elseif (isset($setting_info['theme_default_watermark_pos_y_center'])) {
            $data['theme_default_watermark_pos_y_center'] = $setting_info['theme_default_watermark_pos_y_center'];
        } else {
            $data['theme_default_watermark_pos_y_center'] = false;
        }

        if (isset($this->request->post['theme_default_watermark_pos_x'])) {
            $data['theme_default_watermark_pos_x'] = $this->request->post['theme_default_watermark_pos_x'];
        } elseif (isset($setting_info['theme_default_watermark_pos_x'])) {
            $data['theme_default_watermark_pos_x'] = $setting_info['theme_default_watermark_pos_x'];
        } else {
            $data['theme_default_watermark_pos_x'] = '';
        }

        if (isset($this->request->post['theme_default_watermark_pos_y'])) {
            $data['theme_default_watermark_pos_y'] = $this->request->post['theme_default_watermark_pos_y'];
        } elseif (isset($setting_info['theme_default_watermark_pos_y'])) {
            $data['theme_default_watermark_pos_y'] = $setting_info['theme_default_watermark_pos_y'];
        } else {
            $data['theme_default_watermark_pos_y'] = '';
        }

        if (isset($this->request->post['theme_default_watermark_category_image'])) {
            $data['theme_default_watermark_category_image'] = $this->request->post['theme_default_watermark_category_image'];
        } elseif (isset($setting_info['theme_default_watermark_category_image'])) {
            $data['theme_default_watermark_category_image'] = $setting_info['theme_default_watermark_category_image'];
        } else {
            $data['theme_default_watermark_category_image'] = false;
        }

        if (isset($this->request->post['theme_default_watermark_product_thumb'])) {
            $data['theme_default_watermark_product_thumb'] = $this->request->post['theme_default_watermark_product_thumb'];
        } elseif (isset($setting_info['theme_default_watermark_product_thumb'])) {
            $data['theme_default_watermark_product_thumb'] = $setting_info['theme_default_watermark_product_thumb'];
        } else {
            $data['theme_default_watermark_product_thumb'] = false;
        }

        if (isset($this->request->post['theme_default_watermark_product_popup'])) {
            $data['theme_default_watermark_product_popup'] = $this->request->post['theme_default_watermark_product_popup'];
        } elseif (isset($setting_info['theme_default_watermark_product_popup'])) {
            $data['theme_default_watermark_product_popup'] = $setting_info['theme_default_watermark_product_popup'];
        } else {
            $data['theme_default_watermark_product_popup'] = false;
        }

        if (isset($this->request->post['theme_default_watermark_product_list'])) {
            $data['theme_default_watermark_product_list'] = $this->request->post['theme_default_watermark_product_list'];
        } elseif (isset($setting_info['theme_default_watermark_product_list'])) {
            $data['theme_default_watermark_product_list'] = $setting_info['theme_default_watermark_product_list'];
        } else {
            $data['theme_default_watermark_product_list'] = false;
        }

        if (isset($this->request->post['theme_default_watermark_product_additional'])) {
            $data['theme_default_watermark_product_additional'] = $this->request->post['theme_default_watermark_product_additional'];
        } elseif (isset($setting_info['theme_default_watermark_product_additional'])) {
            $data['theme_default_watermark_product_additional'] = $setting_info['theme_default_watermark_product_additional'];
        } else {
            $data['theme_default_watermark_product_additional'] = false;
        }

        if (isset($this->request->post['theme_default_watermark_product_related'])) {
            $data['theme_default_watermark_product_related'] = $this->request->post['theme_default_watermark_product_related'];
        } elseif (isset($setting_info['theme_default_watermark_product_related'])) {
            $data['theme_default_watermark_product_related'] = $setting_info['theme_default_watermark_product_related'];
        } else {
            $data['theme_default_watermark_product_related'] = false;
        }

        if (isset($this->request->post['theme_default_watermark_product_in_compare'])) {
            $data['theme_default_watermark_product_in_compare'] = $this->request->post['theme_default_watermark_product_in_compare'];
        } elseif (isset($setting_info['theme_default_watermark_product_in_compare'])) {
            $data['theme_default_watermark_product_in_compare'] = $setting_info['theme_default_watermark_product_in_compare'];
        } else {
            $data['theme_default_watermark_product_in_compare'] = false;
        }

        if (isset($this->request->post['theme_default_watermark_product_in_wish_list'])) {
            $data['theme_default_watermark_product_in_wish_list'] = $this->request->post['theme_default_watermark_product_in_wish_list'];
        } elseif (isset($setting_info['theme_default_watermark_product_in_wish_list'])) {
            $data['theme_default_watermark_product_in_wish_list'] = $setting_info['theme_default_watermark_product_in_wish_list'];
        } else {
            $data['theme_default_watermark_product_in_wish_list'] = false;
        }

        if (isset($this->request->post['theme_default_watermark_product_in_cart'])) {
            $data['theme_default_watermark_product_in_cart'] = $this->request->post['theme_default_watermark_product_in_cart'];
        } elseif (isset($setting_info['theme_default_watermark_product_in_cart'])) {
            $data['theme_default_watermark_product_in_cart'] = $setting_info['theme_default_watermark_product_in_cart'];
        } else {
            $data['theme_default_watermark_product_in_cart'] = false;
        }

        $data['theme_default_watermark_thumb'] = $this->model_tool_image->resize($data['theme_default_watermark_image'], 100, 100);
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        if (!$data['theme_default_watermark_thumb']){
            $data['theme_default_watermark_thumb'] = $data['placeholder'];
        }



        /* watermark */

        /* translit image */
        if (isset($this->request->post['theme_default_image_translate_download'])) {
            $data['theme_default_image_translate_download'] = $this->request->post['theme_default_image_translate_download'];
        } elseif (isset($setting_info['theme_default_image_translate_download'])) {
            $data['theme_default_image_translate_download'] = $setting_info['theme_default_image_translate_download'];
        } else {
            $data['theme_default_image_translate_download'] = false;
        }

        /* translit image */

        /* delete image product */
        if (isset($this->request->post['theme_default_image_delete_product'])) {
            $data['theme_default_image_delete_product'] = $this->request->post['theme_default_image_delete_product'];
        } elseif (isset($setting_info['theme_default_image_delete_product'])) {
            $data['theme_default_image_delete_product'] = $setting_info['theme_default_image_delete_product'];
        } else {
            $data['theme_default_image_delete_product'] = false;
        }
        /* delete image product */

        /* SEO prefix lang */
        if (isset($this->request->post['theme_default_prefix_lang_status'])) {
            $data['theme_default_prefix_lang_status'] = $this->request->post['theme_default_prefix_lang_status'];
        } elseif (isset($setting_info['theme_default_prefix_lang_status'])) {
            $data['theme_default_prefix_lang_status'] = $setting_info['theme_default_prefix_lang_status'];
        } else {
            $data['theme_default_prefix_lang_status'] = false;
        }

        if (isset($this->request->post['theme_default_prefix_lang'])) {
            $data['theme_default_prefix_lang'] = $this->request->post['theme_default_prefix_lang'];
        } elseif (isset($setting_info['theme_default_prefix_lang'])) {
            $data['theme_default_prefix_lang'] = $setting_info['theme_default_prefix_lang'];
        } else {
            $data['theme_default_prefix_lang'] = false;
        }

        if (isset($this->request->post['theme_default_hreflang'])) {
            $data['theme_default_hreflang'] = $this->request->post['theme_default_hreflang'];
        } elseif (isset($setting_info['theme_default_hreflang'])) {
            $data['theme_default_hreflang'] = $setting_info['theme_default_hreflang'];
        } else {
            $data['theme_default_prefix_lang'] = false;
        }

        if (isset($this->request->post['theme_default_lang_cookie'])) {
            $data['theme_default_lang_cookie'] = $this->request->post['theme_default_lang_cookie'];
        } elseif (isset($setting_info['theme_default_lang_cookie'])) {
            $data['theme_default_lang_cookie'] = $setting_info['theme_default_lang_cookie'];
        } else {
            $data['theme_default_lang_cookie'] = false;
        }

        /* SEO prefix lang */

        /* SEO microdata */
        if (isset($this->request->post['theme_default_microdata_status'])) {
            $data['theme_default_microdata_status'] = $this->request->post['theme_default_microdata_status'];
        } elseif (isset($setting_info['theme_default_microdata_status'])) {
            $data['theme_default_microdata_status'] = $setting_info['theme_default_microdata_status'];
        } else {
            $data['theme_default_microdata_status'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_status'])) {
            $data['theme_default_microdata_opengraph_status'] = $this->request->post['theme_default_microdata_opengraph_status'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_status'])) {
            $data['theme_default_microdata_opengraph_status'] = $setting_info['theme_default_microdata_opengraph_status'];
        } else {
            $data['theme_default_microdata_opengraph_status'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_show_company'])) {
            $data['theme_default_microdata_opengraph_show_company'] = $this->request->post['theme_default_microdata_opengraph_show_company'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_show_company'])) {
            $data['theme_default_microdata_opengraph_show_company'] = $setting_info['theme_default_microdata_opengraph_show_company'];
        } else {
            $data['theme_default_microdata_opengraph_show_company'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_company_syntax'])) {
            $data['theme_default_microdata_opengraph_company_syntax'] = $this->request->post['theme_default_microdata_opengraph_company_syntax'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_company_syntax'])) {
            $data['theme_default_microdata_opengraph_company_syntax'] = $setting_info['theme_default_microdata_opengraph_company_syntax'];
        } else {
            $data['theme_default_microdata_opengraph_company_syntax'] = '';
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_company_phones'])) {
            $data['theme_default_microdata_opengraph_company_phones'] = $this->request->post['theme_default_microdata_opengraph_company_phones'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_company_phones'])) {
            $data['theme_default_microdata_opengraph_company_phones'] = $setting_info['theme_default_microdata_opengraph_company_phones'];
        } else {
            $data['theme_default_microdata_opengraph_company_phones'] = '';
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_company_seti'])) {
            $data['theme_default_microdata_opengraph_company_seti'] = $this->request->post['theme_default_microdata_opengraph_company_seti'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_company_seti'])) {
            $data['theme_default_microdata_opengraph_company_seti'] = $setting_info['theme_default_microdata_opengraph_company_seti'];
        } else {
            $data['theme_default_microdata_opengraph_company_seti'] = '';
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_company_city'])) {
            $data['theme_default_microdata_opengraph_company_city'] = $this->request->post['theme_default_microdata_opengraph_company_city'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_company_city'])) {
            $data['theme_default_microdata_opengraph_company_city'] = $setting_info['theme_default_microdata_opengraph_company_city'];
        } else {
            $data['theme_default_microdata_opengraph_company_city'] = '';
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_company_post_code'])) {
            $data['theme_default_microdata_opengraph_company_post_code'] = $this->request->post['theme_default_microdata_opengraph_company_post_code'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_company_post_code'])) {
            $data['theme_default_microdata_opengraph_company_post_code'] = $setting_info['theme_default_microdata_opengraph_company_post_code'];
        } else {
            $data['theme_default_microdata_opengraph_company_post_code'] = '';
        }


        if (isset($this->request->post['theme_default_microdata_opengraph_company_street'])) {
            $data['theme_default_microdata_opengraph_company_street'] = $this->request->post['theme_default_microdata_opengraph_company_street'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_company_street'])) {
            $data['theme_default_microdata_opengraph_company_street'] = $setting_info['theme_default_microdata_opengraph_company_street'];
        } else {
            $data['theme_default_microdata_opengraph_company_street'] = '';
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_company_email'])) {
            $data['theme_default_microdata_opengraph_company_email'] = $this->request->post['theme_default_microdata_opengraph_company_email'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_company_email'])) {
            $data['theme_default_microdata_opengraph_company_email'] = $setting_info['theme_default_microdata_opengraph_company_email'];
        } else {
            $data['theme_default_microdata_opengraph_company_email'] = '';
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_product_status'])) {
            $data['theme_default_microdata_opengraph_product_status'] = $this->request->post['theme_default_microdata_opengraph_product_status'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_product_status'])) {
            $data['theme_default_microdata_opengraph_product_status'] = $setting_info['theme_default_microdata_opengraph_product_status'];
        } else {
            $data['theme_default_microdata_opengraph_product_status'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_product_breadcrumb'])) {
            $data['theme_default_microdata_opengraph_product_breadcrumb'] = $this->request->post['theme_default_microdata_opengraph_product_breadcrumb'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_product_breadcrumb'])) {
            $data['theme_default_microdata_opengraph_product_breadcrumb'] = $setting_info['theme_default_microdata_opengraph_product_breadcrumb'];
        } else {
            $data['theme_default_microdata_opengraph_product_breadcrumb'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_product_reviews'])) {
            $data['theme_default_microdata_opengraph_product_reviews'] = $this->request->post['theme_default_microdata_opengraph_product_reviews'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_product_reviews'])) {
            $data['theme_default_microdata_opengraph_product_reviews'] = $setting_info['theme_default_microdata_opengraph_product_reviews'];
        } else {
            $data['theme_default_microdata_opengraph_product_reviews'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_product_related'])) {
            $data['theme_default_microdata_opengraph_product_related'] = $this->request->post['theme_default_microdata_opengraph_product_related'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_product_related'])) {
            $data['theme_default_microdata_opengraph_product_related'] = $setting_info['theme_default_microdata_opengraph_product_related'];
        } else {
            $data['theme_default_microdata_opengraph_product_related'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_product_attribute'])) {
            $data['theme_default_microdata_opengraph_product_attribute'] = $this->request->post['theme_default_microdata_opengraph_product_attribute'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_product_attribute'])) {
            $data['theme_default_microdata_opengraph_product_attribute'] = $setting_info['theme_default_microdata_opengraph_product_attribute'];
        } else {
            $data['theme_default_microdata_opengraph_product_attribute'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_product_in_stock'])) {
            $data['theme_default_microdata_opengraph_product_in_stock'] = $this->request->post['theme_default_microdata_opengraph_product_in_stock'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_product_in_stock'])) {
            $data['theme_default_microdata_opengraph_product_in_stock'] = $setting_info['theme_default_microdata_opengraph_product_in_stock'];
        } else {
            $data['theme_default_microdata_opengraph_product_in_stock'] = false;
        }

        //stock_statuses
        $this->load->model('localisation/stock_status');
        $data['stock_statuses'] =  $this->model_localisation_stock_status->getStockStatuses();

        if (isset($this->request->post['theme_default_microdata_opengraph_product_in_stock_status_id'])) {
            $data['theme_default_microdata_opengraph_product_in_stock_status_id'] = $this->request->post['theme_default_microdata_opengraph_product_in_stock_status_id'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_product_in_stock_status_id'])) {
            $data['theme_default_microdata_opengraph_product_in_stock_status_id'] = $setting_info['theme_default_microdata_opengraph_product_in_stock_status_id'];
        } else {
            $data['theme_default_microdata_opengraph_product_in_stock_status_id'] = '';
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_product_in_stock_status_id'])) {
            $data['theme_default_microdata_opengraph_product_in_stock_status_id'] = $this->request->post['theme_default_microdata_opengraph_product_in_stock_status_id'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_product_in_stock_status_id'])) {
            $data['theme_default_microdata_opengraph_product_in_stock_status_id'] = $setting_info['theme_default_microdata_opengraph_product_in_stock_status_id'];
        } else {
            $data['theme_default_microdata_opengraph_product_in_stock_status_id'] = '';
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_product_description'])) {
            $data['theme_default_microdata_opengraph_product_description'] = $this->request->post['theme_default_microdata_opengraph_product_description'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_product_description'])) {
            $data['theme_default_microdata_opengraph_product_description'] = $setting_info['theme_default_microdata_opengraph_product_description'];
        } else {
            $data['theme_default_microdata_opengraph_product_description'] = '';
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_category_status'])) {
            $data['theme_default_microdata_opengraph_category_status'] = $this->request->post['theme_default_microdata_opengraph_category_status'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_category_status'])) {
            $data['theme_default_microdata_opengraph_category_status'] = $setting_info['theme_default_microdata_opengraph_category_status'];
        } else {
            $data['theme_default_microdata_opengraph_category_status'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_category_breadcrumb'])) {
            $data['theme_default_microdata_opengraph_category_breadcrumb'] = $this->request->post['theme_default_microdata_opengraph_category_breadcrumb'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_category_breadcrumb'])) {
            $data['theme_default_microdata_opengraph_category_breadcrumb'] = $setting_info['theme_default_microdata_opengraph_category_breadcrumb'];
        } else {
            $data['theme_default_microdata_opengraph_category_breadcrumb'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_category_syntax'])) {
            $data['theme_default_microdata_opengraph_category_syntax'] = $this->request->post['theme_default_microdata_opengraph_category_syntax'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_category_syntax'])) {
            $data['theme_default_microdata_opengraph_category_syntax'] = $setting_info['theme_default_microdata_opengraph_category_syntax'];
        } else {
            $data['theme_default_microdata_opengraph_category_syntax'] = '';
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_category_description'])) {
            $data['theme_default_microdata_opengraph_category_description'] = $this->request->post['theme_default_microdata_opengraph_category_description'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_category_description'])) {
            $data['theme_default_microdata_opengraph_category_description'] = $setting_info['theme_default_microdata_opengraph_category_description'];
        } else {
            $data['theme_default_microdata_opengraph_category_description'] = '';
        }

        //***//
        if (isset($this->request->post['theme_default_microdata_opengraph_manufacturer_status'])) {
            $data['theme_default_microdata_opengraph_manufacturer_status'] = $this->request->post['theme_default_microdata_opengraph_manufacturer_status'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_manufacturer_status'])) {
            $data['theme_default_microdata_opengraph_manufacturer_status'] = $setting_info['theme_default_microdata_opengraph_manufacturer_status'];
        } else {
            $data['theme_default_microdata_opengraph_manufacturer_status'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_manufacturer_breadcrumb'])) {
            $data['theme_default_microdata_opengraph_manufacturer_breadcrumb'] = $this->request->post['theme_default_microdata_opengraph_manufacturer_breadcrumb'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_manufacturer_breadcrumb'])) {
            $data['theme_default_microdata_opengraph_manufacturer_breadcrumb'] = $setting_info['theme_default_microdata_opengraph_manufacturer_breadcrumb'];
        } else {
            $data['theme_default_microdata_opengraph_manufacturer_breadcrumb'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_manufacturer_syntax'])) {
            $data['theme_default_microdata_opengraph_manufacturer_syntax'] = $this->request->post['theme_default_microdata_opengraph_manufacturer_syntax'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_manufacturer_syntax'])) {
            $data['theme_default_microdata_opengraph_manufacturer_syntax'] = $setting_info['theme_default_microdata_opengraph_manufacturer_syntax'];
        } else {
            $data['theme_default_microdata_opengraph_manufacturer_syntax'] = '';
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_manufacturer_description'])) {
            $data['theme_default_microdata_opengraph_manufacturer_description'] = $this->request->post['theme_default_microdata_opengraph_manufacturer_description'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_manufacturer_description'])) {
            $data['theme_default_microdata_opengraph_manufacturer_description'] = $setting_info['theme_default_microdata_opengraph_manufacturer_description'];
        } else {
            $data['theme_default_microdata_opengraph_manufacturer_description'] = '';
        }
        //***//
        if (isset($this->request->post['theme_default_microdata_opengraph_special_status'])) {
            $data['theme_default_microdata_opengraph_special_status'] = $this->request->post['theme_default_microdata_opengraph_special_status'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_special_status'])) {
            $data['theme_default_microdata_opengraph_special_status'] = $setting_info['theme_default_microdata_opengraph_special_status'];
        } else {
            $data['theme_default_microdata_opengraph_special_status'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_special_breadcrumb'])) {
            $data['theme_default_microdata_opengraph_special_breadcrumb'] = $this->request->post['theme_default_microdata_opengraph_special_breadcrumb'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_special_breadcrumb'])) {
            $data['theme_default_microdata_opengraph_special_breadcrumb'] = $setting_info['theme_default_microdata_opengraph_special_breadcrumb'];
        } else {
            $data['theme_default_microdata_opengraph_special_breadcrumb'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_special_syntax'])) {
            $data['theme_default_microdata_opengraph_special_syntax'] = $this->request->post['theme_default_microdata_opengraph_special_syntax'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_special_syntax'])) {
            $data['theme_default_microdata_opengraph_special_syntax'] = $setting_info['theme_default_microdata_opengraph_special_syntax'];
        } else {
            $data['theme_default_microdata_opengraph_special_syntax'] = '';
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_special_description'])) {
            $data['theme_default_microdata_opengraph_special_description'] = $this->request->post['theme_default_microdata_opengraph_special_description'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_special_description'])) {
            $data['theme_default_microdata_opengraph_special_description'] = $setting_info['theme_default_microdata_opengraph_special_description'];
        } else {
            $data['theme_default_microdata_opengraph_special_description'] = '';
        }

        //***//
        if (isset($this->request->post['theme_default_microdata_opengraph_information_status'])) {
            $data['theme_default_microdata_opengraph_information_status'] = $this->request->post['theme_default_microdata_opengraph_information_status'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_information_status'])) {
            $data['theme_default_microdata_opengraph_information_status'] = $setting_info['theme_default_microdata_opengraph_information_status'];
        } else {
            $data['theme_default_microdata_opengraph_information_status'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_information_breadcrumb'])) {
            $data['theme_default_microdata_opengraph_information_breadcrumb'] = $this->request->post['theme_default_microdata_opengraph_information_breadcrumb'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_information_breadcrumb'])) {
            $data['theme_default_microdata_opengraph_information_breadcrumb'] = $setting_info['theme_default_microdata_opengraph_information_breadcrumb'];
        } else {
            $data['theme_default_microdata_opengraph_information_breadcrumb'] = false;
        }

        if (isset($this->request->post['theme_default_microdata_opengraph_information_syntax'])) {
            $data['theme_default_microdata_opengraph_information_syntax'] = $this->request->post['theme_default_microdata_opengraph_information_syntax'];
        } elseif (isset($setting_info['theme_default_microdata_opengraph_information_syntax'])) {
            $data['theme_default_microdata_opengraph_information_syntax'] = $setting_info['theme_default_microdata_opengraph_information_syntax'];
        } else {
            $data['theme_default_microdata_opengraph_information_syntax'] = '';
        }


        //
        /* SEO microdata */

        /* catalog */
        if (isset($this->request->post['theme_default_product_empty_end'])) {
            $data['theme_default_product_empty_end'] = $this->request->post['theme_default_product_empty_end'];
        } elseif (isset($setting_info['theme_default_product_empty_end'])) {
            $data['theme_default_product_empty_end'] = $setting_info['theme_default_product_empty_end'];
        } else {
            $data['theme_default_product_empty_end'] = false;
        }

        if (isset($this->request->post['theme_default_product_more_status'])) {
            $data['theme_default_product_more_status'] = $this->request->post['theme_default_product_more_status'];
        } elseif (isset($setting_info['theme_default_product_more_status'])) {
            $data['theme_default_product_more_status'] = $setting_info['theme_default_product_more_status'];
        } else {
            $data['theme_default_product_more_status'] = false;
        }
        /* catalog */

        if (isset($this->request->post['theme_default_product_description_length'])) {
            $data['theme_default_product_description_length'] = $this->request->post['theme_default_product_description_length'];
        } elseif (isset($setting_info['theme_default_product_description_length'])) {
            $data['theme_default_product_description_length'] = $setting_info['theme_default_product_description_length'];
        } else {
            $data['theme_default_product_description_length'] = 100;
        }

        if (isset($this->request->post['theme_default_image_category_width'])) {
            $data['theme_default_image_category_width'] = $this->request->post['theme_default_image_category_width'];
        } elseif (isset($setting_info['theme_default_image_category_width'])) {
            $data['theme_default_image_category_width'] = $setting_info['theme_default_image_category_width'];
        } else {
            $data['theme_default_image_category_width'] = 80;
        }

        if (isset($this->request->post['theme_default_image_category_height'])) {
            $data['theme_default_image_category_height'] = $this->request->post['theme_default_image_category_height'];
        } elseif (isset($setting_info['theme_default_image_category_height'])) {
            $data['theme_default_image_category_height'] = $setting_info['theme_default_image_category_height'];
        } else {
            $data['theme_default_image_category_height'] = 80;
        }

        if (isset($this->request->post['theme_default_image_thumb_width'])) {
            $data['theme_default_image_thumb_width'] = $this->request->post['theme_default_image_thumb_width'];
        } elseif (isset($setting_info['theme_default_image_thumb_width'])) {
            $data['theme_default_image_thumb_width'] = $setting_info['theme_default_image_thumb_width'];
        } else {
            $data['theme_default_image_thumb_width'] = 228;
        }

        if (isset($this->request->post['theme_default_image_thumb_height'])) {
            $data['theme_default_image_thumb_height'] = $this->request->post['theme_default_image_thumb_height'];
        } elseif (isset($setting_info['theme_default_image_thumb_height'])) {
            $data['theme_default_image_thumb_height'] = $setting_info['theme_default_image_thumb_height'];
        } else {
            $data['theme_default_image_thumb_height'] = 228;
        }

        if (isset($this->request->post['theme_default_image_popup_width'])) {
            $data['theme_default_image_popup_width'] = $this->request->post['theme_default_image_popup_width'];
        } elseif (isset($setting_info['theme_default_image_popup_width'])) {
            $data['theme_default_image_popup_width'] = $setting_info['theme_default_image_popup_width'];
        } else {
            $data['theme_default_image_popup_width'] = 500;
        }

        if (isset($this->request->post['theme_default_image_popup_height'])) {
            $data['theme_default_image_popup_height'] = $this->request->post['theme_default_image_popup_height'];
        } elseif (isset($setting_info['theme_default_image_popup_height'])) {
            $data['theme_default_image_popup_height'] = $setting_info['theme_default_image_popup_height'];
        } else {
            $data['theme_default_image_popup_height'] = 500;
        }

        if (isset($this->request->post['theme_default_image_product_width'])) {
            $data['theme_default_image_product_width'] = $this->request->post['theme_default_image_product_width'];
        } elseif (isset($setting_info['theme_default_image_product_width'])) {
            $data['theme_default_image_product_width'] = $setting_info['theme_default_image_product_width'];
        } else {
            $data['theme_default_image_product_width'] = 228;
        }

        if (isset($this->request->post['theme_default_image_product_height'])) {
            $data['theme_default_image_product_height'] = $this->request->post['theme_default_image_product_height'];
        } elseif (isset($setting_info['theme_default_image_product_height'])) {
            $data['theme_default_image_product_height'] = $setting_info['theme_default_image_product_height'];
        } else {
            $data['theme_default_image_product_height'] = 228;
        }

        if (isset($this->request->post['theme_default_image_additional_width'])) {
            $data['theme_default_image_additional_width'] = $this->request->post['theme_default_image_additional_width'];
        } elseif (isset($setting_info['theme_default_image_additional_width'])) {
            $data['theme_default_image_additional_width'] = $setting_info['theme_default_image_additional_width'];
        } else {
            $data['theme_default_image_additional_width'] = 74;
        }

        if (isset($this->request->post['theme_default_image_additional_height'])) {
            $data['theme_default_image_additional_height'] = $this->request->post['theme_default_image_additional_height'];
        } elseif (isset($setting_info['theme_default_image_additional_height'])) {
            $data['theme_default_image_additional_height'] = $setting_info['theme_default_image_additional_height'];
        } else {
            $data['theme_default_image_additional_height'] = 74;
        }

        if (isset($this->request->post['theme_default_image_related_width'])) {
            $data['theme_default_image_related_width'] = $this->request->post['theme_default_image_related_width'];
        } elseif (isset($setting_info['theme_default_image_related_width'])) {
            $data['theme_default_image_related_width'] = $setting_info['theme_default_image_related_width'];
        } else {
            $data['theme_default_image_related_width'] = 80;
        }

        if (isset($this->request->post['theme_default_image_related_height'])) {
            $data['theme_default_image_related_height'] = $this->request->post['theme_default_image_related_height'];
        } elseif (isset($setting_info['theme_default_image_related_height'])) {
            $data['theme_default_image_related_height'] = $setting_info['theme_default_image_related_height'];
        } else {
            $data['theme_default_image_related_height'] = 80;
        }

        if (isset($this->request->post['theme_default_image_compare_width'])) {
            $data['theme_default_image_compare_width'] = $this->request->post['theme_default_image_compare_width'];
        } elseif (isset($setting_info['theme_default_image_compare_width'])) {
            $data['theme_default_image_compare_width'] = $setting_info['theme_default_image_compare_width'];
        } else {
            $data['theme_default_image_compare_width'] = 90;
        }

        if (isset($this->request->post['theme_default_image_compare_height'])) {
            $data['theme_default_image_compare_height'] = $this->request->post['theme_default_image_compare_height'];
        } elseif (isset($setting_info['theme_default_image_compare_height'])) {
            $data['theme_default_image_compare_height'] = $setting_info['theme_default_image_compare_height'];
        } else {
            $data['theme_default_image_compare_height'] = 90;
        }

        if (isset($this->request->post['theme_default_image_wishlist_width'])) {
            $data['theme_default_image_wishlist_width'] = $this->request->post['theme_default_image_wishlist_width'];
        } elseif (isset($setting_info['theme_default_image_wishlist_width'])) {
            $data['theme_default_image_wishlist_width'] = $setting_info['theme_default_image_wishlist_width'];
        } else {
            $data['theme_default_image_wishlist_width'] = 47;
        }

        if (isset($this->request->post['theme_default_image_wishlist_height'])) {
            $data['theme_default_image_wishlist_height'] = $this->request->post['theme_default_image_wishlist_height'];
        } elseif (isset($setting_info['theme_default_image_wishlist_height'])) {
            $data['theme_default_image_wishlist_height'] = $setting_info['theme_default_image_wishlist_height'];
        } else {
            $data['theme_default_image_wishlist_height'] = 47;
        }

        if (isset($this->request->post['theme_default_image_cart_width'])) {
            $data['theme_default_image_cart_width'] = $this->request->post['theme_default_image_cart_width'];
        } elseif (isset($setting_info['theme_default_image_cart_width'])) {
            $data['theme_default_image_cart_width'] = $setting_info['theme_default_image_cart_width'];
        } else {
            $data['theme_default_image_cart_width'] = 47;
        }

        if (isset($this->request->post['theme_default_image_cart_height'])) {
            $data['theme_default_image_cart_height'] = $this->request->post['theme_default_image_cart_height'];
        } elseif (isset($setting_info['theme_default_image_cart_height'])) {
            $data['theme_default_image_cart_height'] = $setting_info['theme_default_image_cart_height'];
        } else {
            $data['theme_default_image_cart_height'] = 47;
        }

        if (isset($this->request->post['theme_default_image_location_width'])) {
            $data['theme_default_image_location_width'] = $this->request->post['theme_default_image_location_width'];
        } elseif (isset($setting_info['theme_default_image_location_width'])) {
            $data['theme_default_image_location_width'] = $setting_info['theme_default_image_location_width'];
        } else {
            $data['theme_default_image_location_width'] = 268;
        }

        if (isset($this->request->post['theme_default_image_location_height'])) {
            $data['theme_default_image_location_height'] = $this->request->post['theme_default_image_location_height'];
        } elseif (isset($setting_info['theme_default_image_location_height'])) {
            $data['theme_default_image_location_height'] = $setting_info['theme_default_image_location_height'];
        } else {
            $data['theme_default_image_location_height'] = 50;
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/theme/default', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/theme/default')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['theme_default_product_limit']) {
            $this->error['product_limit'] = $this->language->get('error_limit');
        }

        if (!$this->request->post['theme_default_product_description_length']) {
            $this->error['product_description_length'] = $this->language->get('error_limit');
        }

        if (!$this->request->post['theme_default_image_category_width'] || !$this->request->post['theme_default_image_category_height']) {
            $this->error['image_category'] = $this->language->get('error_image_category');
        }

        if (!$this->request->post['theme_default_image_thumb_width'] || !$this->request->post['theme_default_image_thumb_height']) {
            $this->error['image_thumb'] = $this->language->get('error_image_thumb');
        }

        if (!$this->request->post['theme_default_image_popup_width'] || !$this->request->post['theme_default_image_popup_height']) {
            $this->error['image_popup'] = $this->language->get('error_image_popup');
        }

        if (!$this->request->post['theme_default_image_product_width'] || !$this->request->post['theme_default_image_product_height']) {
            $this->error['image_product'] = $this->language->get('error_image_product');
        }

        if (!$this->request->post['theme_default_image_additional_width'] || !$this->request->post['theme_default_image_additional_height']) {
            $this->error['image_additional'] = $this->language->get('error_image_additional');
        }

        if (!$this->request->post['theme_default_image_related_width'] || !$this->request->post['theme_default_image_related_height']) {
            $this->error['image_related'] = $this->language->get('error_image_related');
        }

        if (!$this->request->post['theme_default_image_compare_width'] || !$this->request->post['theme_default_image_compare_height']) {
            $this->error['image_compare'] = $this->language->get('error_image_compare');
        }

        if (!$this->request->post['theme_default_image_wishlist_width'] || !$this->request->post['theme_default_image_wishlist_height']) {
            $this->error['image_wishlist'] = $this->language->get('error_image_wishlist');
        }

        if (!$this->request->post['theme_default_image_cart_width'] || !$this->request->post['theme_default_image_cart_height']) {
            $this->error['image_cart'] = $this->language->get('error_image_cart');
        }

        if (!$this->request->post['theme_default_image_location_width'] || !$this->request->post['theme_default_image_location_height']) {
            $this->error['image_location'] = $this->language->get('error_image_location');
        }

        return !$this->error;
    }

    //public function refresh($directory = DIR_IMAGE . 'cache/webp') {
    public function refresh_image($directory = DIR_IMAGE . 'cache') {

        foreach(glob($directory."/*") as $file)
        {
            if(is_dir($file)) {
                $this->refresh($file);
            } else {
                unlink($file);
            }
        }
        rmdir($directory);
    }


    public function start(){
        $setting_simple = '{"address":{"geoIpMode":1,"rows":{"default":[{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"zone_id","masterField":"","requireWhen":[],"required":"1","sortOrder":"0","type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"city","masterField":"","requireWhen":[],"required":"1","sortOrder":"1","type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"default","masterField":"","requireWhen":[],"required":0,"sortOrder":"5","type":"field"}]},"scrollToError":true},"addressFormat":"{firstname} {lastname}, {city}, {address_1}","checkout":[{"agreement":{"displayHeader":true},"agreementId":0,"asapForGuests":false,"asapForLogged":false,"cart":{"displayHeader":true,"displayModel":false,"maxAmount":[],"maxQuantity":[],"maxWeight":[],"minAmount":[],"minQuantity":[],"minWeight":[],"hideForGuest":false,"hideForLogged":false,"minicartText":{"uk_ua":"","ru_ru":""},"quantityStepAsMinimum":false},"comment":{"displayHeader":false,"label":{"en_gb":"Comment","ru_ru":"\u041a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0439"},"placeholder":{"en_gb":"Comment","ru_ru":"\u041a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0439","uk_ua":"\u041a\u043e\u043c\u0435\u043d\u0442\u0430\u0440"}},"customer":{"addressSameInit":true,"addressSelectionFormat":"{firstname} {lastname}, {city}, {address_1}","displayAddressSame":false,"displayAddressSelection":true,"displayHeader":true,"displayLogin":false,"displayYouWillRegistered":false,"rows":{"default":[{"id":"email","type":"field","displayWhen":[],"hideForGuest":false,"hideForLogged":true,"masterField":"","requireWhen":[],"required":1,"sortOrder":"2"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"telephone","masterField":"","requireWhen":[],"required":"1","sortOrder":"3","type":"field"},{"type":"field","id":"lastname","displayWhen":[],"hideForGuest":false,"hideForLogged":false,"masterField":"","requireWhen":[],"required":"1","sortOrder":"1"},{"type":"field","id":"firstname","displayWhen":[],"hideForGuest":false,"hideForLogged":false,"masterField":"","requireWhen":[],"required":"1","sortOrder":"0"}]}},"displayProceedText":false,"displayWeight":false,"geoIpMode":1,"help":{"displayHeader":true},"helpId":0,"leftColumnWidth":"","loginType":"popup","payment":{"displayHeader":false,"hideForGuest":false,"methods":[],"rows":[],"selectFirst":false,"displayType":"1"},"paymentAddress":{"addressSameInit":true,"displayAddressSame":true,"displayHeader":true,"rows":{"default":[{"displayWhen":[],"hideForGuest":true,"hideForLogged":false,"id":"address_id","masterField":"","requireWhen":[],"required":"0","sortOrder":1,"type":"field"},{"displayWhen":{"1":false},"hideForGuest":false,"hideForLogged":false,"id":"firstname","masterField":"","requireWhen":[],"required":"1","sortOrder":2,"type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"lastname","masterField":"","requireWhen":[],"required":"1","sortOrder":3,"type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"country_id","masterField":"","requireWhen":[],"required":"1","sortOrder":4,"type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"zone_id","masterField":"","requireWhen":[],"required":"1","sortOrder":6,"type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"city","masterField":"","requireWhen":[],"required":"1","sortOrder":7,"type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"postcode","masterField":"","requireWhen":[],"required":"1","sortOrder":8,"type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"address_1","masterField":"","requireWhen":[],"required":"1","sortOrder":9,"type":"field"}]}},"rightColumnWidth":"","scrollToError":true,"scrollToPaymentForm":false,"settingsId":0,"shipping":{"displayHeader":false,"hideForGuest":false,"displayTitles":false,"methods":{"novaposhta":{"code":"novaposhta","title":{"uk_ua":""},"wait":false,"sortOrder":"","useTitle":false,"methods":[]}},"rows":{"filterit0.filterit1|":[]},"selectFirst":true,"displayType":"1","hideCost":false},"shippingAddress":{"addressSelectionFormat":"{firstname} {lastname}, {city}, {address_1}","displayAddressSelection":true,"displayHeader":false,"hideForGuest":false,"hideForLogged":false,"rows":{"default":[],"filterit0.filterit1|":[],"filterit0.filterit0|":[{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"city","masterField":"","requireWhen":[],"required":"1","sortOrder":"1","type":"field"},{"type":"field","id":"address_2","displayWhen":[],"hideForGuest":false,"hideForLogged":false,"masterField":"","requireWhen":[],"required":"1","sortOrder":"2"},{"type":"field","id":"zone_id","displayWhen":[],"hideForGuest":false,"hideForLogged":false,"masterField":"","requireWhen":[],"required":"1","sortOrder":"0"}],"novaposhta.department|":[{"type":"field","id":"zone_id","displayWhen":[],"hideForGuest":false,"hideForLogged":false,"masterField":"","requireWhen":[],"required":"1","sortOrder":1},{"type":"field","id":"city","displayWhen":[],"hideForGuest":false,"hideForLogged":false,"masterField":"","requireWhen":[],"required":"1","sortOrder":2},{"type":"field","id":"address_1","displayWhen":[],"hideForGuest":false,"hideForLogged":false,"masterField":"","requireWhen":[],"required":"1","sortOrder":3}],"novaposhta.doors|":[{"type":"field","id":"zone_id","displayWhen":[],"hideForGuest":false,"hideForLogged":false,"masterField":"","requireWhen":[],"required":"1","sortOrder":1},{"type":"field","id":"city","displayWhen":[],"hideForGuest":false,"hideForLogged":false,"masterField":"","requireWhen":[],"required":"1","sortOrder":2},{"type":"field","id":"address_2","displayWhen":[],"hideForGuest":false,"hideForLogged":false,"masterField":"","requireWhen":[],"required":"1","sortOrder":3}],"novaposhta.poshtomat|":[{"type":"field","id":"zone_id","displayWhen":[],"hideForGuest":false,"hideForLogged":false,"masterField":"","requireWhen":[],"required":"1","sortOrder":1},{"type":"field","id":"city","displayWhen":[],"hideForGuest":false,"hideForLogged":false,"masterField":"","requireWhen":[],"required":"1","sortOrder":2},{"type":"field","id":"field24","displayWhen":[],"hideForGuest":false,"hideForLogged":false,"masterField":"","requireWhen":[],"required":"1","sortOrder":3}]},"hideForMethods":[]},"steps":[{"id":"step_0","label":{"en_gb":"Buyer","uk_ua":"\u041f\u043e\u043a\u0443\u043f\u0435\u0446\u044c","ru_ru":"\u041f\u043e\u043a\u0443\u043f\u0430\u0442\u0435\u043b\u044c"},"manual":0,"template":"{customer}{comment}","buttonNext":{"ru_ru":"\u0412\u043f\u0435\u0440\u0435\u0434","uk_ua":"\u0412\u043f\u0435\u0440\u0435\u0434","en_gb":"Next"}},{"id":"step_1","label":{"ru_ru":"\u0421\u043f\u043e\u0441\u043e\u0431 \u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0438","en_gb":"Method of delivery","uk_ua":"\u0421\u043f\u043e\u0441\u0456\u0431 \u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0438"},"template":"{shipping}{shipping_address}","buttonNext":{"ru_ru":"\u0412\u043f\u0435\u0440\u0435\u0434","uk_ua":"\u0412\u043f\u0435\u0440\u0435\u0434","en_gb":"Next"}},{"id":"step_2","label":{"ru_ru":"\u0421\u043f\u043e\u0441\u043e\u0431 \u043e\u043f\u043b\u0430\u0442\u044b","en_gb":"Payment method","uk_ua":"\u0421\u043f\u043e\u0441\u0456\u0431 \u043e\u043f\u043b\u0430\u0442\u0438"},"template":"{payment}{payment_form}"}],"summary":{"displayHeader":true},"agreementCheckboxStep":0,"menuType":2}],"colorbox":true,"edit":{"rows":{"default":[{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"email","masterField":"","requireWhen":[],"required":"1","sortOrder":2,"type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"firstname","masterField":"","requireWhen":[],"required":"1","sortOrder":3,"type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"lastname","masterField":"","requireWhen":[],"required":"1","sortOrder":4,"type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"telephone","masterField":"","requireWhen":[],"required":"1","sortOrder":5,"type":"field"}]},"scrollToError":true},"fields":[{"autoreload":false,"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"0","source":"saved"},"description":[],"id":"register","label":{"en_gb":"Register account","ru_ru":"\u0417\u0430\u0440\u0435\u0433\u0438\u0441\u0442\u0440\u0438\u0440\u043e\u0432\u0430\u0442\u044c\u0441\u044f","pl_pl":"Rejestr","uk_ua":"\u0417\u0430\u0440\u0435\u0454\u0441\u0442\u0440\u0443\u0432\u0430\u0442\u0438\u0441\u044c"},"mask":{"source":"saved"},"objects":{"address":false,"customer":true,"order":false},"placeholder":[],"rules":{"api":[],"byLength":[],"equal":[],"notEmpty":[],"regexp":[]},"saveToComment":false,"type":"radio","values":{"method":"getYesNo","saved":[],"source":"model"}},{"autoreload":false,"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":{"en_gb":""},"id":"email","label":{"en_gb":"Email","ru_ru":"Email","pl_pl":"Email","uk_ua":"Email"},"mask":{"source":"saved"},"masterField":"register","objects":{"customer":true},"placeholder":{"ru_ru":"Email","en_gb":"Email","uk_ua":"Email"},"rules":{"api":{"enabled":true,"errorText":{"en_gb":"E-Mail Address is already registered!","ru_ru":"\u0410\u0434\u0440\u0435\u0441 \u0443\u0436\u0435 \u0437\u0430\u0440\u0435\u0433\u0438\u0441\u0442\u0440\u0438\u0440\u043e\u0432\u0430\u043d!","uk_ua":"\u0410\u0434\u0440\u0435\u0441\u0430 \u0432\u0436\u0435 \u0437\u0430\u0440\u0435\u0454\u0441\u0442\u0440\u043e\u0432\u0430\u043d\u0430!"},"filter":"register","method":"checkEmailForUniqueness"},"byLength":[],"equal":[],"notEmpty":[],"regexp":{"enabled":true,"errorText":{"en_gb":"E-Mail Address does not appear to be valid!","ru_ru":"\u041d\u0435\u043a\u043e\u0440\u0440\u0435\u043a\u0442\u043d\u044b\u0439 \u0430\u0434\u0440\u0435\u0441 \u044d\u043b\u0435\u043a\u0442\u0440\u043e\u043d\u043d\u043e\u0439 \u043f\u043e\u0447\u0442\u044b!","uk_ua":"\u041d\u0435\u043a\u043e\u0440\u0435\u043a\u0442\u043d\u0430 \u0430\u0434\u0440\u0435\u0441\u0430 \u0435\u043b\u0435\u043a\u0442\u0440\u043e\u043d\u043d\u043e\u0457 \u043f\u043e\u0448\u0442\u0438!"},"value":".+@.+"}},"saveToComment":false,"type":"email"},{"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"method":"getRandomPassword","saved":"","source":"saved"},"description":[],"id":"password","label":{"en_gb":"Password","ru_ru":"\u041f\u0430\u0440\u043e\u043b\u044c","pl_pl":"Has\u0142o","uk_ua":"\u041f\u0430\u0440\u043e\u043b\u044c"},"mask":{"source":"saved"},"masterField":"register","objects":{"customer":true},"placeholder":[],"rules":{"api":[],"byLength":{"enabled":true,"errorText":{"en_gb":"Password must be between 4 and 20 characters!","ru_ru":"\u041f\u0430\u0440\u043e\u043b\u044c \u0434\u043e\u043b\u0436\u0435\u043d \u0431\u044b\u0442\u044c \u043e\u0442 4 \u0434\u043e 20 \u0441\u0438\u043c\u0432\u043e\u043b\u043e\u0432!","uk_ua":"\u041f\u0430\u0440\u043e\u043b\u044c \u043c\u0430\u0454 \u0431\u0443\u0442\u0438 \u0432\u0456\u0434 4 \u0434\u043e 20 \u0441\u0438\u043c\u0432\u043e\u043b\u0456\u0432!"},"max":"20","min":"4"},"equal":[],"notEmpty":[],"regexp":[]},"saveToComment":false,"type":"password"},{"autoreload":false,"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"method":"getRandomPassword","saved":"","source":"saved"},"description":[],"id":"confirm_password","label":{"en_gb":"Confirm password","ru_ru":"\u041f\u043e\u0434\u0442\u0432\u0435\u0440\u0434\u0438\u0442\u0435 \u043f\u0430\u0440\u043e\u043b\u044c","uk_ua":"\u041f\u0456\u0434\u0442\u0432\u0435\u0440\u0434\u0456\u0442\u044c \u043f\u0430\u0440\u043e\u043b\u044c","pl_pl":"Potwierd\u017a has\u0142o"},"mask":{"source":"saved"},"objects":{"customer":true},"placeholder":[],"rules":{"api":{"enabled":false,"errorText":[]},"byLength":[],"equal":{"enabled":true,"errorText":{"en_gb":"Password confirmation does not match password!","ru_ru":"\u041f\u043e\u0434\u0442\u0432\u0435\u0440\u0436\u0434\u0435\u043d\u0438\u0435 \u043f\u0430\u0440\u043e\u043b\u044f \u043d\u0435 \u0441\u043e\u043e\u0442\u0432\u0435\u0442\u0441\u0442\u0432\u0443\u0435\u0442 \u043f\u0430\u0440\u043e\u043b\u044e!","uk_ua":"\u041f\u0456\u0434\u0442\u0432\u0435\u0440\u0434\u0436\u0435\u043d\u043d\u044f \u043f\u0430\u0440\u043e\u043b\u044f \u043d\u0435 \u0432\u0456\u0434\u043f\u043e\u0432\u0456\u0434\u0430\u0454 \u043f\u0430\u0440\u043e\u043b\u044e!"},"fieldId":"password"},"notEmpty":[],"regexp":[]},"saveToComment":false,"type":"password"},{"autoreload":false,"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"1","source":"saved"},"description":[],"id":"newsletter","label":{"en_gb":"Subscribe","ru_ru":"\u041f\u043e\u0434\u043f\u0438\u0441\u0430\u0442\u044c\u0441\u044f \u043d\u0430 \u043d\u043e\u0432\u043e\u0441\u0442\u0438","pl_pl":"Subskrybuj wiadomo\u015bci","uk_ua":"\u041f\u0456\u0434\u043f\u0438\u0441\u0430\u0442\u0438\u0441\u044c \u043d\u0430 \u043d\u043e\u0432\u0438\u043d\u0438"},"mask":{"source":"saved"},"objects":{"customer":true},"placeholder":[],"rules":{"api":[],"byLength":[],"equal":[],"notEmpty":[],"regexp":[]},"saveToComment":false,"type":"radio","values":{"method":"getYesNo","saved":[],"source":"model"}},{"autoreload":true,"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"method":"getDefaultGroup","saved":"","source":"model"},"description":[],"id":"customer_group_id","label":{"en_gb":"Customer group","ru_ru":"\u0413\u0440\u0443\u043f\u043f\u0430 \u043f\u043e\u043a\u0443\u043f\u0430\u0442\u0435\u043b\u044f","pl_pl":"Grupa kupuj\u0105cych","uk_ua":"\u0413\u0440\u0443\u043f\u0430 \u043f\u043e\u043a\u0443\u043f\u0446\u044f"},"mask":{"source":"saved"},"objects":{"customer":true},"placeholder":[],"rules":{"api":[],"byLength":[],"equal":[],"notEmpty":[],"regexp":[]},"saveToComment":false,"type":"radio","values":{"method":"getCustomerGroups","saved":[],"source":"model"}},{"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":[],"id":"firstname","label":{"en_gb":"Firstname","ru_ru":"\u0418\u043c\u044f","uk_ua":"\u0406\u043c\'\u044f ","pl_pl":"Imi\u0119"},"mask":{"source":"saved"},"objects":{"address":true,"customer":true},"placeholder":{"uk_ua":"\u0406\u043c\'\u044f ","en_gb":"Firstname","ru_ru":"\u0418\u043c\u044f"},"rules":{"api":[],"byLength":{"enabled":true,"errorText":{"en_gb":"First Name must be between 1 and 32 characters!","ru_ru":"\u0418\u043c\u044f \u0434\u043e\u043b\u0436\u043d\u043e \u0431\u044b\u0442\u044c \u043e\u0442 1 \u0434\u043e 32 \u0441\u0438\u043c\u0432\u043e\u043b\u043e\u0432!","uk_ua":"\u0406\u043c\'\u044f \u043c\u0430\u0454 \u0431\u0443\u0442\u0438 \u0432\u0456\u0434 1 \u0434\u043e 32 \u0441\u0438\u043c\u0432\u043e\u043b\u0456\u0432!"},"max":"32","min":"1"},"equal":[],"notEmpty":[],"regexp":[]},"saveToComment":false,"type":"text"},{"autoreload":false,"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":[],"id":"lastname","label":{"en_gb":"Lastname","ru_ru":"\u0424\u0430\u043c\u0438\u043b\u0438\u044f","pl_pl":"Nazwisko","uk_ua":"\u041f\u0440\u0456\u0437\u0432\u0438\u0449\u0435"},"mask":{"source":"saved"},"objects":{"address":true,"customer":true},"placeholder":{"en_gb":"Lastname","ru_ru":"\u0424\u0430\u043c\u0438\u043b\u0438\u044f","uk_ua":"\u041f\u0440\u0456\u0437\u0432\u0438\u0449\u0435"},"rules":{"api":[],"byLength":{"enabled":true,"errorText":{"en_gb":"Last Name must be between 1 and 32 characters!","ru_ru":"\u0424\u0430\u043c\u0438\u043b\u0438\u044f \u0434\u043e\u043b\u0436\u043d\u0430 \u0431\u044b\u0442\u044c \u043e\u0442 1 \u0434\u043e 32 \u0441\u0438\u043c\u0432\u043e\u043b\u043e\u0432!","uk_ua":"\u041f\u0440\u0456\u0437\u0432\u0438\u0449\u0435 \u043c\u0430\u0454 \u0431\u0443\u0442\u0438 \u0432\u0456\u0434 1 \u0434\u043e 32 \u0441\u0438\u043c\u0432\u043e\u043b\u0456\u0432!"},"max":"32","min":"1"},"equal":[],"notEmpty":[],"regexp":[]},"saveToComment":false,"type":"text"},{"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":[],"id":"telephone","label":{"en_gb":"Telephone","ru_ru":"\u0422\u0435\u043b\u0435\u0444\u043e\u043d","uk_ua":"\u0422\u0435\u043b\u0435\u0444\u043e\u043d","pl_pl":"Telefon"},"mask":{"filter":"country_id","method":"getTelephoneMask","saved":"39 (999) 99 99 999","source":"saved"},"masterField":"","objects":{"customer":true},"placeholder":{"en_gb":"Telephone","uk_ua":"\u0422\u0435\u043b\u0435\u0444\u043e\u043d","ru_ru":"\u0422\u0435\u043b\u0435\u0444\u043e\u043d"},"rules":{"api":[],"byLength":{"enabled":true,"errorText":{"en_gb":"Telephone must be between 3 and 32 characters!","ru_ru":"\u0422\u0435\u043b\u0435\u0444\u043e\u043d \u0434\u043e\u043b\u0436\u0435\u043d \u0431\u044b\u0442\u044c \u043e\u0442 10 \u0434\u043e 32 \u0441\u0438\u043c\u0432\u043e\u043b\u043e\u0432!","uk_ua":"\u0422\u0435\u043b\u0435\u0444\u043e\u043d \u043c\u0430\u0454 \u0431\u0443\u0442\u0438 \u0432\u0456\u0434 10 \u0434\u043e 32 \u0441\u0438\u043c\u0432\u043e\u043b\u0456\u0432!"},"max":"32","min":"10"},"equal":[],"notEmpty":[],"regexp":[]},"saveToComment":false,"type":"tel"},{"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":[],"id":"fax","label":{"en_gb":"Fax","ru_ru":"\u0424\u0430\u043a\u0441","pl_pl":"Faks","uk_ua":"\u0424\u0430\u043a\u0441"},"mask":{"source":"saved"},"objects":{"customer":true},"placeholder":[],"rules":{"api":[],"byLength":[],"equal":[],"notEmpty":[],"regexp":[]},"saveToComment":false,"type":"text"},{"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":[],"id":"company","label":{"en_gb":"Company","ru_ru":"\u041a\u043e\u043c\u043f\u0430\u043d\u0438\u044f","uk_ua":"\u041a\u043e\u043c\u043f\u0430\u043d\u0456\u044f","pl_pl":"Sp\u00f3\u0142ka"},"mask":{"source":"saved"},"objects":{"address":true},"placeholder":[],"rules":{"api":[],"byLength":[],"equal":[],"notEmpty":{"enabled":false,"errorText":{"en_gb":""}},"regexp":[]},"saveToComment":false,"type":"text"},{"autoreload":false,"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":[],"id":"address_1","label":{"en_gb":"Address 1","ru_ru":"\u0410\u0434\u0440\u0435\u0441","pl_pl":"Adres","uk_ua":"\u0410\u0434\u0440\u0435\u0441\u0430\/\u0412\u0456\u0434\u0434\u0456\u043b\u0435\u043d\u043d\u044f"},"mask":{"source":"saved"},"objects":{"address":true},"placeholder":{"uk_ua":"\u0412\u0432\u0435\u0434\u0456\u0442\u044c \u043d\u0430\u0437\u0432\u0443 \u043e\u0431\u043b\u0430\u0441\u0442\u0456, \u043d\u0430\u0441\u0435\u043b\u0435\u043d\u043e\u0433\u043e \u043f\u0443\u043d\u043a\u0442\u0443, \u0432\u0443\u043b\u0438\u0446\u044e, \u2116 \u0431\u0443\u0434."},"rules":{"api":[],"byLength":{"enabled":false,"errorText":{"en_gb":"Address 1 must be between 3 and 128 characters!","ru_ru":"\u0410\u0434\u0440\u0435\u0441 \u0434\u043e\u043b\u0436\u0435\u043d \u0431\u044b\u0442\u044c \u043e\u0442 3 \u0434\u043e 128 \u0441\u0438\u043c\u0432\u043e\u043b\u043e\u0432!","uk_ua":"\u0410\u0434\u0440\u0435\u0441\u0430 \u043c\u0430\u0454 \u0431\u0443\u0442\u0438 \u0432\u0456\u0434 3 \u0434\u043e 128 \u0441\u0438\u043c\u0432\u043e\u043b\u0456\u0432!"},"max":"128","min":"3"},"equal":[],"notEmpty":{"enabled":true,"errorText":{"uk_ua":"\u0417\u0430\u043f\u043e\u0432\u043d\u0456\u0442\u044c \u043f\u043e\u043b\u0435!"}},"regexp":{"enabled":false}},"saveToComment":false,"type":"select2","values":{"source":"model","method":"getShippingDepartments","filter":"city"}},{"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":{"uk_ua":"\u0412\u043a\u0430\u0436\u0456\u0442\u044c \u0430\u0434\u0440\u0435\u0441\u0443"},"id":"address_2","label":{"en_gb":"Address 2","ru_ru":"\u041f\u0440\u043e\u0434\u043e\u043b\u0436\u0435\u043d\u0438\u0435 \u0430\u0434\u0440\u0435\u0441\u0430","pl_pl":"Kontynuacja adresu","uk_ua":"\u0410\u0434\u0440\u0435\u0441\u0430"},"mask":{"source":"saved"},"objects":{"address":true},"placeholder":{"uk_ua":"\u0412\u043a\u0430\u0436\u0456\u0442\u044c \u0430\u0434\u0440\u0435\u0441\u0443"},"rules":{"api":[],"byLength":[],"equal":[],"notEmpty":{"enabled":true,"errorText":{"uk_ua":"\u0412\u0432\u0435\u0434\u0456\u0442\u044c \u0430\u0434\u0440\u0435\u0441\u0443"}},"regexp":[]},"saveToComment":false,"type":"text","autoreload":true},{"autoreload":true,"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":[],"id":"city","label":{"en_gb":"New post office","ru_ru":"\u041e\u0442\u0434\u0435\u043b\u0435\u043d\u0438\u0435 \u043d\u043e\u0432\u043e\u0439 \u043f\u043e\u0447\u0442\u044b","uk_ua":"\u041c\u0456\u0441\u0442\u043e","pl_pl":"Nowa poczta"},"mask":{"source":"saved"},"objects":{"address":true},"placeholder":[],"rules":{"api":[],"byLength":{"enabled":false,"errorText":{"en_gb":"City must be between 2 and 128 characters!","ru_ru":"\u0413\u043e\u0440\u043e\u0434 \u0434\u043e\u043b\u0436\u0435\u043d \u0431\u044b\u0442\u044c \u043e\u0442 2 \u0434\u043e 128 \u0441\u0438\u043c\u0432\u043e\u043b\u043e\u0432!"},"max":"128","min":"2"},"equal":[],"notEmpty":{"enabled":true,"errorText":{"ru_ru":"\u0412\u044b\u0431\u0435\u0440\u0438\u0442\u0435 \u043e\u0442\u0434\u0435\u043b\u0435\u043d\u0438\u0435!","en_gb":"Choose a branch!","pl_pl":"Wybierz oddzia\u0142!","uk_ua":"\u0417\u0430\u043f\u043e\u0432\u043d\u0456\u0442\u044c \u043f\u043e\u043b\u0435!"}},"regexp":{"enabled":false}},"saveToComment":false,"type":"select2","values":{"method":"getShippingCities","saved":"","source":"model","filter":"zone_id"}},{"autoreload":true,"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":[],"id":"postcode","label":{"en_gb":"Postcode","ru_ru":"\u0418\u043d\u0434\u0435\u043a\u0441","pl_pl":"Indeks","uk_ua":"\u0406\u043d\u0434\u0435\u043a\u0441"},"mask":{"source":"saved"},"objects":{"address":true},"placeholder":[],"rules":{"api":[],"byLength":{"enabled":true,"errorText":{"en_gb":"Postcode must be between 2 and 10 characters!","ru_ru":"\u0418\u043d\u0434\u0435\u043a\u0441 \u0434\u043e\u043b\u0436\u0435\u043d \u0431\u044b\u0442\u044c \u043e\u0442 2 \u0434\u043e 10 \u0441\u0438\u043c\u0432\u043e\u043b\u043e\u0432!","uk_ua":"\u0406\u043d\u0434\u0435\u043a\u0441 \u043c\u0430\u0454 \u0431\u0443\u0442\u0438 \u0432\u0456\u0434 2 \u0434\u043e 10 \u0441\u0438\u043c\u0432\u043e\u043b\u0456\u0432!"},"max":"10","min":"2"},"equal":[],"notEmpty":[],"regexp":[]},"saveToComment":false,"type":"text"},{"autoreload":false,"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"220","source":"saved"},"description":[],"id":"country_id","label":{"en_gb":"Region\/State","ru_ru":"\u041e\u0431\u043b\u0430\u0441\u0442\u044c","uk_ua":"\u041a\u0440\u0430\u0457\u043d\u0430","pl_pl":"Region"},"mask":{"source":"saved"},"objects":{"address":true},"placeholder":[],"rules":{"api":[],"byLength":{"enabled":false},"equal":[],"notEmpty":{"enabled":true,"errorText":{"en_gb":"Please select a Region\/State!","ru_ru":"\u0412\u044b\u0431\u0435\u0440\u0438\u0442\u0435 \u043e\u0431\u043b\u0430\u0441\u0442\u044c!","pl_pl":"Wybierz obszar!","uk_ua":"\u0412\u0438\u0431\u0435\u0440\u0456\u0442\u044c \u043a\u0440\u0430\u0457\u043d\u0443!"}},"regexp":{"enabled":false}},"saveToComment":false,"type":"select","values":{"method":"getCountries","saved":[],"source":"model"}},{"autoreload":true,"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"3502","source":"saved"},"description":[],"id":"zone_id","label":{"en_gb":"Town","ru_ru":"\u0413\u043e\u0440\u043e\u0434","pl_pl":"Miasto","uk_ua":"\u041e\u0431\u043b\u0430\u0441\u0442\u044c"},"mask":{"source":"saved"},"objects":{"address":true},"placeholder":[],"rules":{"api":[],"byLength":{"enabled":false},"equal":[],"notEmpty":{"enabled":true,"errorText":{"en_gb":"Choose a City!","ru_ru":"\u0412\u044b\u0431\u0435\u0440\u0438\u0442\u0435 \u0413\u043e\u0440\u043e\u0434!","pl_pl":"Wybierz miasto!","uk_ua":"\u0412\u0438\u0431\u0435\u0440\u0456\u0442\u044c \u043c\u0456\u0441\u0442\u043e!"}},"regexp":{"enabled":false}},"saveToComment":false,"type":"select2","values":{"filter":"country_id","method":"getZones","saved":[],"source":"model"}},{"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":[],"id":"captcha","label":{"en_gb":"Verification code","ru_ru":"\u0417\u0430\u0449\u0438\u0442\u043d\u044b\u0439 \u043a\u043e\u0434","uk_ua":"\u0417\u0430\u0445\u0438\u0441\u043d\u0438\u0439 \u043a\u043e\u0434","pl_pl":"kod bezpiecze\u0144stwa"},"mask":{"source":"saved"},"objects":{"customer":true,"order":false},"placeholder":[],"rules":{"api":{"enabled":true,"errorText":{"en_gb":"Verification code does not match the image!","ru_ru":"\u0417\u0430\u0449\u0438\u0442\u043d\u044b\u0439 \u043a\u043e\u0434 \u043d\u0435 \u0441\u043e\u043e\u0442\u0432\u0435\u0442\u0441\u0442\u0432\u0443\u0435\u0442 \u0438\u0437\u043e\u0431\u0440\u0430\u0436\u0435\u043d\u0438\u044e!","uk_ua":"\u041a\u043e\u0434 \u0437\u0430\u0445\u0438\u0441\u0442\u0443 \u043d\u0435 \u0432\u0456\u0434\u043f\u043e\u0432\u0456\u0434\u0430\u0454 \u0437\u043e\u0431\u0440\u0430\u0436\u0435\u043d\u043d\u044e!"},"method":"checkCaptcha"},"byLength":[],"equal":[],"notEmpty":[],"regexp":[]},"saveToComment":false,"type":"captcha"},{"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"filter":"address_id","method":"isDefaultAddress","saved":"","source":"model"},"description":[],"id":"default","label":{"en_gb":"Default address","ru_ru":"\u041e\u0441\u043d\u043e\u0432\u043d\u043e\u0439 \u0430\u0434\u0440\u0435\u0441","pl_pl":"G\u0142\u00f3wny adres","uk_ua":"\u041e\u0441\u043d\u043e\u0432\u043d\u0430 \u0430\u0434\u0440\u0435\u0441\u0430"},"mask":{"source":"saved"},"objects":{"address":true},"placeholder":[],"rules":{"api":[],"byLength":[],"equal":[],"notEmpty":[],"regexp":[]},"saveToComment":false,"type":"radio","values":{"method":"getYesNo","saved":[],"source":"model"}},{"autoreload":true,"custom":false,"dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"method":"getDefaultAddressId","saved":"","source":"model"},"description":[],"id":"address_id","label":{"en_gb":"Select address","ru_ru":"\u0412\u044b\u0431\u0435\u0440\u0438\u0442\u0435 \u0430\u0434\u0440\u0435\u0441","uk_ua":"\u0412\u0438\u0431\u0435\u0440\u0456\u0442\u044c \u0430\u0434\u0440\u0435\u0441\u0443","pl_pl":"Wybierz adres"},"mask":{"source":"saved"},"objects":{"address":true},"placeholder":[],"rules":{"api":[],"byLength":[],"equal":[],"notEmpty":[],"regexp":[]},"saveToComment":false,"type":"select","values":{"method":"getAddresses","saved":[],"source":"model"}},{"id":"delivery_warehouses","label":{"ru_ru":"\u041e\u0442\u0434\u0435\u043b\u0435\u043d\u0438\u0435 Delivery","en_gb":"Department of Delivery","uk_ua":"\u0412\u0456\u0434\u0434\u0456\u043b\u0435\u043d\u043d\u044f Delivery"},"custom":true,"type":"text","object":"address","dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"values":{"saved":"","source":"saved"},"description":{"ru_ru":"","en_gb":"","uk_ua":""},"mask":{"saved":"","source":"saved"},"placeholder":{"ru_ru":"\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u043d\u0430\u0437\u0432\u0430\u043d\u0438\u0435 \u0433\u043e\u0440\u043e\u0434\u0430","en_gb":"Enter the name of the city","uk_ua":"\u0412\u0432\u0435\u0434\u0456\u0442\u044c \u043d\u0430\u0437\u0432\u0443 \u043c\u0456\u0441\u0442\u0430"},"rules":{"api":[],"byLength":[],"equal":[],"notEmpty":[],"regexp":[]},"saveToComment":true},{"id":"delivery_hidden_warehouse_id","label":{"ru_ru":"\u0421\u043a\u043b\u0430\u0434 Delivery","en_gb":"Warehouse Delivery","uk_ua":"\u0421\u043a\u043b\u0430\u0434 Delivery"},"custom":true,"type":"text","object":"address","dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"values":{"saved":"","source":"saved"},"description":{"ru_ru":"","en_gb":"","uk_ua":""},"mask":{"saved":"","source":"saved"},"placeholder":{"ru_ru":"","en_gb":"","uk_ua":""},"rules":{"api":[],"byLength":[],"equal":[],"notEmpty":{"enabled":true,"errorText":{"ru_ru":"\u041d\u0435 \u0432\u044b\u0431\u0440\u0430\u043d \u0441\u043a\u043b\u0430\u0434 Delivery!","en_gb":"Delivery warehouse not selected!","uk_ua":"\u041d\u0435 \u0432\u0438\u0431\u0440\u0430\u043d\u043e \u0441\u043a\u043b\u0430\u0434 Delivery!"}},"regexp":[]},"saveToComment":false},{"id":"delivery_hidden_city_id","label":{"ru_ru":"\u0413\u043e\u0440\u043e\u0434","en_gb":"City","uk_ua":"\u041c\u0456\u0441\u0442\u043e"},"custom":true,"type":"text","object":"address","dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"values":{"saved":"","source":"saved"},"description":{"ru_ru":"","en_gb":"","uk_ua":""},"mask":{"saved":"","source":"saved"},"placeholder":{"ru_ru":"","en_gb":"","uk_ua":""},"rules":{"api":[],"byLength":[],"equal":[],"notEmpty":[],"regexp":[]},"saveToComment":false},{"id":"field23","label":{"uk_ua":"\u041d\u0430\u0437\u0432\u0430 \u0422\u041e\u0412 \u0430\u0431\u043e \u0424\u041e\u041f"},"custom":true,"object":"order","dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":{"uk_ua":""},"mask":{"saved":"","source":"saved"},"placeholder":{"uk_ua":"\u0412\u0432\u0435\u0434\u0456\u0442\u044c \u043d\u0430\u0437\u0432\u0443 \u043a\u043e\u043c\u043f\u0430\u043d\u0456"},"rules":{"api":[],"byLength":[],"equal":[],"notEmpty":{"enabled":true,"errorText":{"uk_ua":"\u0412\u0432\u0435\u0434\u0456\u0442\u044c \u043d\u0430\u0437\u0432\u0443 \u043a\u043e\u043c\u043f\u0430\u043d\u0456\u0457"}},"regexp":[]},"saveToComment":false,"type":"text","autoreload":false},{"id":"field24","label":{"uk_ua":"\u041f\u043e\u0448\u0442\u043e\u043c\u0430\u0442 \u041d\u043e\u0432\u0430 \u041f\u043e\u0448\u0442\u0430"},"custom":true,"object":"address","dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":{"uk_ua":""},"mask":{"saved":"","source":"saved"},"placeholder":{"uk_ua":""},"rules":{"api":[],"byLength":{"enabled":false},"equal":[],"notEmpty":{"enabled":true,"errorText":{"uk_ua":"\u0412\u043a\u0430\u0436\u0456\u0442\u044c \u043f\u043e\u0448\u0442\u043e\u043c\u0430\u0442"}},"regexp":{"enabled":false}},"saveToComment":false,"type":"select2","values":{"method":"getShippingPoshtomats","saved":"","source":"model","filter":"city"}},{"id":"field25","label":{"uk_ua":"\u042f - \u043c\u0430\u0439\u0441\u0442\u0435\u0440"},"custom":true,"object":"customer","dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":{"uk_ua":"\u042f\u043a\u0449\u043e \u0412\u0438 \u043c\u0430\u0439\u0441\u0442\u0435\u0440 - \u0432\u043a\u0430\u0436\u0456\u0442\u044c \u043c\u0456\u0441\u0442\u043e \u0442\u0430 \u0430\u0434\u0440\u0435\u0441\u0443 \u0441\u0442\u0443\u0434\u0456\u0457"},"mask":{"saved":"","source":"saved"},"placeholder":{"uk_ua":"\u0412\u043a\u0430\u0436\u0456\u0442\u044c \u043c\u0456\u0441\u0442\u043e \u0442\u0430 \u0430\u0434\u0440\u0435\u0441\u0443 \u0441\u0442\u0443\u0434\u0456\u0457"},"rules":{"api":[],"byLength":{"enabled":false},"equal":[],"notEmpty":[],"regexp":{"enabled":false}},"saveToComment":false,"type":"text","values":{"method":"","saved":{"uk_ua":[{"id":"2","text":""}]},"source":"saved"}},{"id":"field26","label":{"uk_ua":" "},"custom":true,"object":"customer","dateEndType":"calculated","dateSelected":[],"dateStartType":"calculated","default":{"saved":"","source":"saved"},"description":{"uk_ua":""},"mask":{"saved":"","source":"saved"},"placeholder":{"uk_ua":""},"rules":{"api":[],"byLength":{"enabled":false},"equal":[],"notEmpty":[],"regexp":{"enabled":false}},"saveToComment":false,"type":"radio","values":{"method":"","saved":{"uk_ua":[{"id":"1","text":"\u042f- \u043a\u043b\u0456\u0454\u043d\u0442"},{"id":"2","text":"\u042f- \u043c\u0430\u0439\u0441\u0442\u0435\u0440"}]},"source":"saved"}}],"headers":[{"custom":true,"id":"main","tag":"legend","label":{"en_gb":"Your Personal Details","ru_ru":"\u041e\u0441\u043d\u043e\u0432\u043d\u0430\u044f \u0438\u043d\u0444\u043e\u0440\u043c\u0430\u0446\u0438\u044f","pl_pl":"Podstawowe informacje","uk_ua":"\u041e\u0441\u043d\u043e\u0432\u043d\u0430 \u0456\u043d\u0444\u043e\u0440\u043c\u0430\u0446\u0456\u044f"}},{"custom":true,"id":"address","tag":"legend","label":{"en_gb":"Your Address","ru_ru":"\u0412\u0430\u0448 \u0430\u0434\u0440\u0435\u0441","uk_ua":"\u0412\u0430\u0448\u0430 \u0430\u0434\u0440\u0435\u0441\u0430","pl_pl":"Tw\u00f3j adres"}}],"modules":[],"register":{"agreementCheckboxInit":false,"agreementId":0,"displayAgreementCheckbox":false,"geoIpMode":1,"rows":{"default":[{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"email","masterField":"","requireWhen":[],"required":"1","sortOrder":"4","type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"password","masterField":"","requireWhen":[],"required":"1","sortOrder":"5","type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"confirm_password","masterField":"","requireWhen":[],"required":"1","sortOrder":"6","type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"firstname","masterField":"","requireWhen":[],"required":"1","sortOrder":"0","type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"lastname","masterField":"","requireWhen":[],"required":"1","sortOrder":"1","type":"field"},{"displayWhen":[],"hideForGuest":false,"hideForLogged":false,"id":"telephone","masterField":"","requireWhen":[],"required":"1","sortOrder":"3","type":"field"},{"type":"field","id":"field25","displayWhen":[],"hideForGuest":false,"hideForLogged":false,"masterField":"","requireWhen":[],"required":0,"sortOrder":"2"}]},"scrollToError":true,"useGeoIp":false,"useGoogleApi":false},"replaceAddress":true,"replaceCart":true,"replaceCheckout":true,"replaceEdit":true,"replaceRegister":true,"javascriptCallback":"$(\'#cart > ul\').load(\'index.php?route=common\/cart\/info ul li\');\n\/*$(\'nav#top\').load(\'index.php?route=common\/simple_connector\/header #top > div\');*\/\n\/* Delivery shipping script start *\/\n\/* \u0414\u043b\u044f \u0441\u043a\u0440\u044b\u0442\u044b\u0445 \u044f\u0447\u0435\u0435\u043a - \u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0430 Delivery *\/\n$(\'#shipping_address_delivery_hidden_warehouse_id, #shipping_address_delivery_hidden_city_id\').hide()\n\nvar div_to_hide_warehouse_id = $(\"#shipping_address_delivery_hidden_warehouse_id\").parent().parent(),\n    div_to_hide_city_id = $(\"#shipping_address_delivery_hidden_city_id\").parent().parent(),\n    div_text_error_to_hide = div_to_hide_warehouse_id.find(\".simplecheckout-rule-group\");\n\ndiv_to_hide_warehouse_id.find(\'label\').remove();\ndiv_to_hide_warehouse_id.find(\'td\').first().remove();\n\ndiv_to_hide_city_id.find(\'label\').remove();\ndiv_to_hide_city_id.find(\'td\').first().remove();\n\ndiv_to_hide_city_id.hide();\n\ndiv_text_error_to_hide.find(\'div\').css(\"text-align\", \"right\");\n\n$(\"#shipping_address_delivery_warehouses\").on(\'change\', function() {\n    $(\"#shipping_address_delivery_hidden_warehouse_id\").val(\'\');\n    $(\"#shipping_address_delivery_hidden_city_id\").val(\'\');\n});\n\n$(\"#shipping_address_delivery_warehouses\")\n    .after(\'<div id=\"autocomplete-city-for-delivery\" style=\"position:relative;\"><\/div>\');\n\n$( \"#shipping_address_delivery_warehouses\" ).autocomplete({\n    minLength: 2,\n    appendTo: \"#autocomplete-city-for-delivery\",\n    source: function (request, response) {\n      $.ajax({\n          type: \"GET\",\n          url: \'index.php?route=checkout\/shipping_delivery\/cities\/&city_name=\' +  encodeURIComponent(request),\n          dataType: \'json\',\n          success: function(json) {\n            response($.map( json, function( item ) {\n                return {\n                    label: item.name + \" (\" + item.address + \")\",\n                    value: item.warehouse_id,\n                    city_id: item.city_id\n                }\n            }));\n          }\n      });\n    },\n    select: function(event) {\n        $(\'#shipping_address_delivery_warehouses\').val(event.label);\n        $(\'#shipping_address_delivery_hidden_warehouse_id\').val(event.value);\n        $(\'#shipping_address_delivery_hidden_city_id\').val(event.city_id);\n        \n        $( \"#simplecheckout_button_cart\" ).trigger( \"click\" );\n        return false; \/\/ Prevent the widget from inserting the value.\n    },\n   focus: function(event, ui) {\n        event.preventDefault();\n   }\n});\n\/* Delivery shipping script end *\/\n\nvar o = $(\'.simple-content .radio\');\nif (o.length) {\n    var input;\n    var arrVal = [];\n    o.each(function (i) {\n        input = $(this).find(\'input[type=\"radio\"]\');\n        if ($.inArray(input.attr(\'name\') + input.attr(\'value\'), arrVal) == -1) {\n            input.attr(\'id\', input.attr(\'name\') + input.attr(\'value\'))\n            input.insertBefore($(this).find(\'label\').attr(\'for\', input.attr(\'name\') + input.attr(\'value\')));\n            arrVal.push(input.attr(\'name\') + input.attr(\'value\'))\n        } else {\n            input.attr(\'id\', input.attr(\'name\') + input.attr(\'value\') + i.toString());\n            input.insertBefore($(this).find(\'label\').attr(\'for\', input.attr(\'name\') + input.attr(\'value\') + i.toString()));\n            arrVal.push(input.attr(\'name\') + input.attr(\'value\') + i.toString());\n        }\n    });\n}\nvar o2 = $(\'.simple-content  label.radio-inline\');\nif (o2.length) {\n    var input;\n    o2.each(function () {\n        input = $(this).find(\'input[type=\"radio\"]\');\n        input.attr(\'id\', input.attr(\'name\') + input.attr(\'value\'))\n        input.insertBefore($(this).attr(\'for\', input.attr(\'name\') + input.attr(\'value\')));\n    });\n}\n\nvar o = $(\'.simple-content  .checkbox\');\n\nif (o.length) {\n    var input;\n    var arrVal = [];\n    o.each(function (i) {\n        input = $(this).find(\'input[type=\"checkbox\"]\');\n        if ($.inArray(input.attr(\'name\') + input.attr(\'value\'), arrVal) == -1) {\n            input.attr(\'id\', input.attr(\'name\') + input.attr(\'value\'))\n            input.insertBefore($(this).find(\'label\').attr(\'for\', input.attr(\'name\') + input.attr(\'value\')));\n            arrVal.push(input.attr(\'name\') + input.attr(\'value\'))\n        } else {\n            input.attr(\'id\', input.attr(\'name\') + input.attr(\'value\') + input.attr(\'value\') + i.toString())\n            input.insertBefore($(this).find(\'label\').attr(\'for\', input.attr(\'name\') + input.attr(\'value\') + input.attr(\'value\') + i.toString()));\n            arrVal.push(input.attr(\'name\') + input.attr(\'value\') + i.toString());\n        }\n    });\n}\n\nvar o2 = $(\'.simple-content  input[name=\\\'agree\\\'][type=\\\'checkbox\\\']\');\nif (o2.length) {\n    o2.attr(\'id\', o2.attr(\'name\') + o2.attr(\'value\'));\n    o2.parent().append(\'<label for=\"\' + o2.attr(\'name\') + o2.attr(\'value\') + \'\"><\/label>\');\n    $(\'label[for=\"\' + o2.attr(\'name\') + o2.attr(\'value\') + \'\"]\').insertAfter(o2);\n}\n\nvar o3 = $(\'.simple-content  .checkbox-inline\');\nif (o3.length) {\n    var input;\n    o3.each(function (i) {\n        input = $(this).find(\'input[type=\"checkbox\"]\');\n        input.attr(\'id\', input.attr(\'name\') + input.attr(\'value\'))\n        input.insertBefore($(this).attr(\'for\', input.attr(\'name\') + input.attr(\'value\')));\n\n    })\n}\n","useAutocomplete":false,"disableJQueryUI":false,"minify":true,"disableStatic":false,"addressFormatsShipping":{"novaposhta.poshtomat":{"uk_ua":"{zone}\n{city}\n{field24}"}}}';
        //UPDATE `oc_setting` SET `store_id` = '1' WHERE `oc_setting`.`setting_id` = 1685;
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET `store_id` = '0', `code`='simple', `key`='simple_license', `value` = 'JDi1y28u-1284tgu!!551hnh19u3t', `serialized` = 0");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET `store_id` = '0', `code`='simple', `key`='module_simple_status', `value` = '1', `serialized` = 0");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET `store_id` = '0', `code`='simple', `key`='simple_settings', `value` = '".$setting_simple."', `serialized` = 0");
        // запит запису про викоритсання першого налаштування
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET `store_id` = '0', `code`='start', `key`='start_settings', `value` = '1', `serialized` = 0");

    }
}
