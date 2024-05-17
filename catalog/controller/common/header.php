<?php
class ControllerCommonHeader extends Controller {
	public function index() {
        require_once('system/library/Mobile_Detect.php');
        $detect = new Mobile_Detect;


        $data['no_index'] = $this->config->get('theme_default_no_index_status');

        if (!empty($this->config->get('theme_default_webfont_status'))) {
            $data['webfont'] = $this->config->get('theme_default_webfont_status');
        } else {
            $data['webfont'] = false;
        }

        if ($this->config->get('theme_default_webfont_link')) {
            $data['webfont_link'] = html_entity_decode($this->config->get('theme_default_webfont_link'), ENT_QUOTES, 'UTF-8');
        } else {
            $data['webfont_link'] = false;
        }

        if ($this->config->get('theme_default_webfont_style')) {
            $data['webfont_style'] = html_entity_decode($this->config->get('theme_default_webfont_style'), ENT_QUOTES, 'UTF-8');
        } else {
            $data['webfont_style'] = false;
        }

        if ($this->config->get('theme_default_fontawesome_status')) {
            $data['fontawesome'] = $this->config->get('theme_default_fontawesome_status');
        } else {
            $data['fontawesome'] = false;
        }

		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();
        $data['tc_og'] = $this->document->getTc_og();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');
		$data['lazy_load_status'] = $this->config->get('theme_default_image_lazy_load_status');
		if($this->config->get('theme_default_lazy_load_image') && $this->config->get('theme_default_lazy_load_image') != '' ){
            $data['lazy_image'] = 'image/'.$this->config->get('theme_default_lazy_load_image');
        } else {
            $data['lazy_image'] = 'catalog/view/javascript/lazyload/loading.gif';
        }

        $data['informations'] = array();
        $this->load->model('catalog/information');
        foreach ($this->model_catalog_information->getInformations() as $result) {
            if ($result['bottom']) {
                $data['informations'][] = array(
                    'title' => $result['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }

        $data['version_bootstrap'] = $this->config->get('theme_default_version_bootstrap');
        if ($this->config->get('theme_default_prefix_lang_status')) {
            $data['alter_lang'] = $this->getAlterLanguageLinks($this->document->getLinks());
        }

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		// Wishlist
        if ($this->customer->isLogged()) {
            $this->load->model('account/wishlist');

            $data['text_wishlist'] = $this->language->get('text_wishlist');
            $data['wishlist_count'] = $this->model_account_wishlist->getTotalWishlist();
        } else {
            $data['text_wishlist'] = $this->language->get('text_wishlist');
            $data['wishlist_count'] = (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0);
        }

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		
		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');

        // For page specific og tags
        if (isset($this->request->get['route'])) {
            if (isset($this->request->get['product_id'])) {
                $class = '-' . $this->request->get['product_id'];
                $this->document->addOGMeta('property="og:type"', 'product');
            } elseif (isset($this->request->get['path'])) {
                $class = '-' . $this->request->get['path'];
            } elseif (isset($this->request->get['manufacturer_id'])) {
                $class = '-' . $this->request->get['manufacturer_id'];
            } elseif (isset($this->request->get['information_id'])) {
                $class = '-' . $this->request->get['information_id'];
                $this->document->addOGMeta('property="og:type"', 'article');
            } else {
                $class = '';
            }
            $data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
        } else {
            $data['class'] = 'common-home';
            $this->document->addOGMeta('property="og:type"', 'website');
        }
        $this->load->model('tool/image');
        $data['logo_meta'] = str_replace(' ', '%20', $this->model_tool_image->resize($this->config->get('config_logo'), 300, 300));
        $data['ogmeta'] = $this->document->getOGMeta();

        $home_url = $_SERVER['REQUEST_URI'];
        if ($home_url == '/' || $home_url == '/index.php?route=common/home') {
            $data['is_home'] = true;
        } else {
            $data['is_home'] = false;
        }

		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

        $data['microdata'] = $this->load->controller('common/microdata');

        if ($detect->isMobile() ) {
            $data['search_link'] = $this->url->link('product/search', '', true);
            return $this->load->view('common/header_mobile', $data);
        } else {
            return $this->load->view('common/header', $data);
        }
	}

    protected function getAlterLanguageLinks($links) {
        $result = array();
        $languages = array();
        $prefix_full = $this->config->get('theme_default_prefix_lang');
        $hreflangs = $this->config->get('theme_default_prefix_lang');

        $this->load->model('localisation/language');
        foreach ($this->model_localisation_language->getLanguages() as $lang) {
            if ($prefix_full) {
                $languages[$lang['language_id']] = $lang['code'];
            } else {
                $languages[$lang['language_id']] = substr($lang['code'],0,2);
            }
        }

        if (!empty($this->request->get['route'])) {
            if (isset($this->session->data['language'])){
                if ($prefix_full) {
                    $cur_language_code = $this->session->data['language'];
                } else {
                    $cur_language_code = substr($this->session->data['language'],0,2);
                }
            }
            $cur_language_id = $this->config->get('config_language_id');

            $_route = $this->request->get['route'];
            $_params = $this->request->get;
            unset($_params['route'], $_params['_route_']);
            foreach ($languages as $id=>$lcode) {
                $this->config->set('config_language_id', $id);
                $this->session->data['language'] = $lcode;
                $hreflang = !empty($hreflangs[$id]) ? $hreflangs[$id] : $lcode;
                $result[$hreflang] = $this->url->link($_route, http_build_query($_params), true);
            }

            if (isset($cur_language_code)) {
                $this->session->data['language'] = $cur_language_code;
            }
            $this->config->set('config_language_id', $cur_language_id);
        }

        return $result;
    }

}
