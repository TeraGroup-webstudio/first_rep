<?php
class ControllerCommonFooter extends Controller {
    public function index() {
        require_once('system/library/Mobile_Detect.php');
        $detect = new Mobile_Detect;

        $this->load->language('common/footer');
        $language_id = $this->config->get('config_language_id');
        $this->load->model('catalog/information');
        $this->load->model('tool/image');
        $data['name'] = $this->config->get('config_name');
        $data['email'] = $this->config->get('config_email');
        //theme_default_product_refresh_price_status
        $data['status_more'] = $this->config->get('theme_default_product_more_status');
        if($this->config->get('theme_default_product_refresh_price_status') == 1){
            $currency_code = $this->session->data['currency'];
            $data['autocalc_currency'] = array(
                'value'           => (float)$this->currency->getValue($currency_code),
                'symbol_left'     => str_replace("'", "\'", $this->currency->getSymbolLeft($currency_code)),
                'symbol_right'    => str_replace("'", "\'", $this->currency->getSymbolRight($currency_code)),
                'decimals'        => (int)$this->currency->getDecimalPlace($currency_code),
                'decimal_point'   => $this->language->get('decimal_point'),
                'thousand_point'  => $this->language->get('thousand_point'),
            );

            $data['autocalc_option_special'] = $this->config->get('theme_default_product_refresh_price_option_special');
            $data['autocalc_option_discount'] = $this->config->get('theme_default_product_refresh_price_option_discount');
            $data['autocalc_not_mul_qty'] = $this->config->get('theme_default_product_refresh_price_not_mul_qty');
            $data['autocalc_select_first'] = $this->config->get('theme_default_product_refresh_price_select_first');

        }

        /* соціальні мережі */
        $data['links_seti'] = array();
        if($this->config->get('theme_default_footer_seti_status') == 1){
            if ($this->config->get('theme_default_footer_seti')){
                $links_seti = $this->config->get('theme_default_footer_seti');
            } else {
                $links_seti = array();
            }

            foreach ($links_seti as $result) {

                if (is_file(DIR_IMAGE . $result['image_peace'])) {
                    $image_peace = $result['image_peace'];
                } else {
                    $image_peace = '';
                }
                $data['links_seti'][] = array(
                    'image_peace' => $this->model_tool_image->resize($image_peace, 24, 24),
                    'link'  			=> $result['link'][$language_id],
                    'sort'  			=> $result['sort']
                );

            }

            if (!empty($data['links_seti'])){
                foreach ($data['links_seti'] as $key => $value) {
                    $sort_add_category_menu[$key] = $value['sort'];
                }
                array_multisort($sort_add_category_menu, SORT_ASC, $data['links_seti']);
            }
        }


        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
            $data['logo'] = $server . 'image/' . $this->config->get('config_logo');
        } else {
            $data['logo'] = '';
        }
        $data['informations'] = array();

        foreach ($this->model_catalog_information->getInformations() as $result) {
            if ($result['bottom']) {
                $data['informations'][] = array(
                    'title' => $result['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }


        /* catalog */
        $this->load->model('catalog/category');

        $data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            $children_data = array();

            $data['categories'][] = array(
                'category_id' => $category['category_id'],
                'name'        => $category['name'],
                'children'    => $children_data,
                'href'        => $this->url->link('product/category', 'path=' . $category['category_id'])
            );
        }

        $data['contact'] = $this->url->link('information/contact');
        $data['return'] = $this->url->link('account/return/add', '', true);
        $data['sitemap'] = $this->url->link('information/sitemap');
        $data['tracking'] = $this->url->link('information/tracking');
        $data['manufacturer'] = $this->url->link('product/manufacturer');
        $data['voucher'] = $this->url->link('account/voucher', '', true);
        $data['affiliate'] = $this->url->link('affiliate/login', '', true);
        $data['special'] = $this->url->link('product/special');
        $data['account'] = $this->url->link('account/account', '', true);
        $data['order'] = $this->url->link('account/order', '', true);
        $data['wishlist'] = $this->url->link('account/wishlist', '', true);
        $data['newsletter'] = $this->url->link('account/newsletter', '', true);
        $data['open'] = nl2br($this->config->get('config_open')[$this->config->get('config_language_id')]);
        $data['address'] = nl2br($this->config->get('config_address')[$this->config->get('config_language_id')]);

        $data['list_phones'] = array();
        if ($this->config->get('config_number')) {
            $additional_phones = $this->config->get('config_number');
        } else {
            $additional_phones = array();
        }

        if ($additional_phones){
            foreach ($additional_phones as $key => $value) {
                $additional_phones_sorted[$key] = $value['sort'];
            }
            array_multisort($additional_phones_sorted, SORT_ASC, $additional_phones);
        }

        foreach ($additional_phones as $phone) {

            $data['list_phones'][] = array(
                'title'        => $phone['title'][$language_id],
                'href'         => $phone['link'][$language_id],
                'hint_text'    => $phone['hint'][$language_id],
                'hint_show'    => false,
                'image' 		   => $this->model_tool_image->resize($phone['image'], 24, 24) ,
                'sort'         => $phone['sort'],
            );

        }
        /* messenger */
        $data['list_messenger'] = array();
        if ($this->config->get('config_messager')) {
            $messengers = $this->config->get('config_messager');
        } else {
            $messengers = array();
        }

        if ($messengers){
            foreach ($messengers as $key => $value) {
                $messengers_sorted[$key] = $value['sort'];
            }
            array_multisort($messengers_sorted, SORT_ASC, $messengers);
        }

        foreach ($messengers as $messenger) {

            $data['list_messenger'][] = array(
                'href'         => $messenger['link'][$language_id],
                'hint_text'    => $messenger['hint'][$language_id],
                'hint_show'    => false,
                'image' 		   => $this->model_tool_image->resize($messenger['image'], 24, 24) ,
                'sort'         => $messenger['sort'],
            );

        }

        $data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));
        $data['status_one_click'] = $this->config->get('theme_default_product_one_click_status');

        // Whos Online
        if ($this->config->get('config_customer_online')) {
            $this->load->model('tool/online');

            if (isset($this->request->server['REMOTE_ADDR'])) {
                $ip = $this->request->server['REMOTE_ADDR'];
            } else {
                $ip = '';
            }

            if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
                $url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
            } else {
                $url = '';
            }

            if (isset($this->request->server['HTTP_REFERER'])) {
                $referer = $this->request->server['HTTP_REFERER'];
            } else {
                $referer = '';
            }

            $this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
        }

        $data['scripts'] = $this->document->getScripts('footer');

        if ($detect->isMobile() ) {
            return $this->load->view('common/footer_mobile', $data);
        } else {
            return $this->load->view('common/footer', $data);
        }


    }
}
