<?php

class ControllerExtensionFeedSitemapMulti extends Controller {
    private $error = array();
    private $languages = array();

    public function index() {
        $this->load->language('extension/feed/sitemap_multi');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('feed_sitemap_multi', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_data_feed'] = $this->language->get('entry_data_feed');
        $data['button_add'] = $this->language->get('button_add');




        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['tab_general'] = $this->language->get('tab_general');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/feed/sitemap_multi', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/feed/sitemap_multi', 'user_token=' . $this->session->data['user_token'], true);

        $data['dynamic_link'] = HTTP_CATALOG . 'index.php?route=extension/feed/sitemap_multi';
        $data['static_link'] = HTTP_CATALOG . 'sitemap.xml';
        $data['update_link'] = HTTP_CATALOG . 'index.php?route=extension/feed/sitemap_multi';

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true);

        $data['items'] = array();


        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();
        foreach ($this->model_localisation_language->getLanguages() as $lang) {
            $this->languages[$lang['language_id']] = $lang['code'];
        }

        if (isset($this->request->post['feed_sitemap_multi_priority'])) {
            $data['feed_sitemap_multi_priority'] = $this->request->post['feed_sitemap_multi_priority'];
        } else {
            $data['feed_sitemap_multi_priority'] = $this->config->get('feed_sitemap_multi_priority');
        }

        if (isset($this->request->post['feed_sitemap_multi_changefreq'])) {
            $data['feed_sitemap_multi_changefreq'] = $this->request->post['feed_sitemap_multi_changefreq'];
        } else {
            $data['feed_sitemap_multi_changefreq'] = $this->config->get('feed_sitemap_multi_changefreq');
        }

        if (isset($this->request->post['feed_sitemap_multi_lastmod'])) {
            $data['feed_sitemap_multi_lastmod'] = $this->request->post['feed_sitemap_multi_lastmod'];
        } else {
            $data['feed_sitemap_multi_lastmod'] = $this->config->get('feed_sitemap_multi_lastmod');
        }

        if (isset($this->request->post['feed_sitemap_multi_image'])) {
            $data['feed_sitemap_multi_image'] = $this->request->post['feed_sitemap_multi_image'];
        } else {
            $data['feed_sitemap_multi_image'] = $this->config->get('feed_sitemap_multi_image');
        }

        if (isset($this->request->post['feed_sitemap_multi_code'])) {
            $data['feed_sitemap_multi_code'] = $this->request->post['feed_sitemap_multi_code'];
        } else {
            $data['feed_sitemap_multi_code'] = $this->config->get('feed_sitemap_multi_code');
        }

        if (isset($this->request->post['feed_sitemap_multi_xdefault'])) {
            $data['feed_sitemap_multi_xdefault'] = $this->request->post['feed_sitemap_multi_xdefault'];
        } else {
            $data['feed_sitemap_multi_xdefault'] = $this->config->get('feed_sitemap_multi_xdefault');
        }

        if (isset($this->request->post['feed_sitemap_multi_separate'])) {
            $data['feed_sitemap_multi_separate'] = $this->request->post['feed_sitemap_multi_separate'];
        } else {
            $data['feed_sitemap_multi_separate'] = $this->config->get('feed_sitemap_multi_separate');
        }

        if (isset($this->request->post['feed_sitemap_multi_safe'])) {
            $data['feed_sitemap_multi_safe'] = $this->request->post['feed_sitemap_multi_safe'];
        } else {
            $data['feed_sitemap_multi_safe'] = $this->config->get('feed_sitemap_multi_safe');
        }

        if (isset($this->request->post['feed_sitemap_multi_pages'])) {
            $data['feed_sitemap_multi_pages'] = $this->request->post['feed_sitemap_multi_pages'];
        } else {
            $data['feed_sitemap_multi_pages'] = $this->config->get('feed_sitemap_multi_pages');
        }

        if (isset($this->request->post['feed_sitemap_multi_safe'])) {
            $data['feed_sitemap_multi_safe'] = $this->request->post['feed_sitemap_multi_safe'];
        } else {
            $data['feed_sitemap_multi_safe'] = $this->config->get('feed_sitemap_multi_safe');
        }

        if (isset($this->request->post['feed_sitemap_multi_prefix'])) {
            $data['feed_sitemap_multi_prefix'] = $this->request->post['feed_sitemap_multi_prefix'];
        } else {
            $data['feed_sitemap_multi_prefix'] = $this->config->get('feed_sitemap_multi_prefix');
        }

        if (isset($this->request->post['feed_sitemap_multi_static'])) {
            $data['feed_sitemap_multi_static'] = $this->request->post['feed_sitemap_multi_static'];
        } else {
            $data['feed_sitemap_multi_static'] = $this->config->get('feed_sitemap_multi_static');
        }

        $data['params'] = array('home', 'product', 'category','manufacturer','information','special','manufacturers','sitemap','contact');

        if (isset($this->request->post['feed_sitemap_multi_priority_home'])) {
            $data['feed_sitemap_multi_priority_home'] = $this->request->post['feed_sitemap_multi_priority_home'];
        } else {
            $data['feed_sitemap_multi_priority_home'] = $this->config->get('feed_sitemap_multi_priority_home');
        }

        if (isset($this->request->post['feed_sitemap_multi_priority_product'])) {
            $data['feed_sitemap_multi_priority_product'] = $this->request->post['feed_sitemap_multi_priority_product'];
        } else {
            $data['feed_sitemap_multi_priority_product'] = $this->config->get('feed_sitemap_multi_priority_product');
        }

        if (isset($this->request->post['feed_sitemap_multi_priority_category'])) {
            $data['feed_sitemap_multi_priority_category'] = $this->request->post['feed_sitemap_multi_priority_category'];
        } else {
            $data['feed_sitemap_multi_priority_category'] = $this->config->get('feed_sitemap_multi_priority_category');
        }

        if (isset($this->request->post['feed_sitemap_multi_priority_manufacturer'])) {
            $data['feed_sitemap_multi_priority_manufacturer'] = $this->request->post['feed_sitemap_multi_priority_manufacturer'];
        } else {
            $data['feed_sitemap_multi_priority_manufacturer'] = $this->config->get('feed_sitemap_multi_priority_manufacturer');
        }

        if (isset($this->request->post['feed_sitemap_multi_priority_information'])) {
            $data['feed_sitemap_multi_priority_information'] = $this->request->post['feed_sitemap_multi_priority_information'];
        } else {
            $data['feed_sitemap_multi_priority_information'] = $this->config->get('feed_sitemap_multi_priority_information');
        }

        if (isset($this->request->post['feed_sitemap_multi_priority_special'])) {
            $data['feed_sitemap_multi_priority_special'] = $this->request->post['feed_sitemap_multi_priority_special'];
        } else {
            $data['feed_sitemap_multi_priority_special'] = $this->config->get('feed_sitemap_multi_priority_special');
        }

        if (isset($this->request->post['feed_sitemap_multi_priority_manufacturers'])) {
            $data['feed_sitemap_multi_priority_manufacturers'] = $this->request->post['feed_sitemap_multi_priority_manufacturers'];
        } else {
            $data['feed_sitemap_multi_priority_manufacturers'] = $this->config->get('feed_sitemap_multi_priority_manufacturers');
        }

        if (isset($this->request->post['feed_sitemap_multi_priority_sitemap'])) {
            $data['feed_sitemap_multi_priority_sitemap'] = $this->request->post['feed_sitemap_multi_priority_sitemap'];
        } else {
            $data['feed_sitemap_multi_priority_sitemap'] = $this->config->get('feed_sitemap_multi_priority_sitemap');
        }

        if (isset($this->request->post['feed_sitemap_multi_priority_contact'])) {
            $data['feed_sitemap_multi_priority_contact'] = $this->request->post['feed_sitemap_multi_priority_contact'];
        } else {
            $data['feed_sitemap_multi_priority_contact'] = $this->config->get('feed_sitemap_multi_priority_contact');
        }

        /*  --------------------------------------- */
        if (isset($this->request->post['feed_sitemap_multi_changefreq_home'])) {
            $data['feed_sitemap_multi_changefreq_home'] = $this->request->post['feed_sitemap_multi_changefreq_home'];
        } else {
            $data['feed_sitemap_multi_changefreq_home'] = $this->config->get('feed_sitemap_multi_changefreq_home');
        }

        if (isset($this->request->post['feed_sitemap_multi_changefreq_product'])) {
            $data['feed_sitemap_multi_changefreq_product'] = $this->request->post['feed_sitemap_multi_changefreq_product'];
        } else {
            $data['feed_sitemap_multi_changefreq_product'] = $this->config->get('feed_sitemap_multi_changefreq_product');
        }

        if (isset($this->request->post['feed_sitemap_multi_changefreq_category'])) {
            $data['feed_sitemap_multi_changefreq_category'] = $this->request->post['feed_sitemap_multi_changefreq_category'];
        } else {
            $data['feed_sitemap_multi_changefreq_category'] = $this->config->get('feed_sitemap_multi_changefreq_category');
        }

        if (isset($this->request->post['feed_sitemap_multi_changefreq_manufacturer'])) {
            $data['feed_sitemap_multi_changefreq_manufacturer'] = $this->request->post['feed_sitemap_multi_changefreq_manufacturer'];
        } else {
            $data['feed_sitemap_multi_changefreq_manufacturer'] = $this->config->get('feed_sitemap_multi_changefreq_manufacturer');
        }

        if (isset($this->request->post['feed_sitemap_multi_changefreq_information'])) {
            $data['feed_sitemap_multi_changefreq_information'] = $this->request->post['feed_sitemap_multi_changefreq_information'];
        } else {
            $data['feed_sitemap_multi_changefreq_information'] = $this->config->get('feed_sitemap_multi_changefreq_information');
        }

        if (isset($this->request->post['feed_sitemap_multi_changefreq_special'])) {
            $data['feed_sitemap_multi_changefreq_special'] = $this->request->post['feed_sitemap_multi_changefreq_special'];
        } else {
            $data['feed_sitemap_multi_changefreq_special'] = $this->config->get('feed_sitemap_multi_changefreq_special');
        }

        if (isset($this->request->post['feed_sitemap_multi_changefreq_manufacturers'])) {
            $data['feed_sitemap_multi_changefreq_manufacturers'] = $this->request->post['feed_sitemap_multi_changefreq_manufacturers'];
        } else {
            $data['feed_sitemap_multi_changefreq_manufacturers'] = $this->config->get('feed_sitemap_multi_changefreq_manufacturers');
        }


        if (isset($this->request->post['feed_sitemap_multi_changefreq_sitemap'])) {
            $data['feed_sitemap_multi_changefreq_sitemap'] = $this->request->post['feed_sitemap_multi_changefreq_sitemap'];
        } else {
            $data['feed_sitemap_multi_changefreq_sitemap'] = $this->config->get('feed_sitemap_multi_changefreq_sitemap');
        }

        if (isset($this->request->post['feed_sitemap_multi_changefreq_contact'])) {
            $data['feed_sitemap_multi_changefreq_contact'] = $this->request->post['feed_sitemap_multi_changefreq_contact'];
        } else {
            $data['feed_sitemap_multi_changefreq_contact'] = $this->config->get('feed_sitemap_multi_changefreq_contact');
        }

        /*  --------------------------------------- */
        if (isset($this->request->post['feed_sitemap_multi_noindex_home'])) {
            $data['feed_sitemap_multi_noindex_home'] = $this->request->post['feed_sitemap_multi_noindex_home'];
        } else {
            $data['feed_sitemap_multi_noindex_home'] = $this->config->get('feed_sitemap_multi_noindex_home');
        }

        if (isset($this->request->post['feed_sitemap_multi_noindex_product'])) {
            $data['feed_sitemap_multi_noindex_product'] = $this->request->post['feed_sitemap_multi_noindex_product'];
        } else {
            $data['feed_sitemap_multi_noindex_product'] = $this->config->get('feed_sitemap_multi_noindex_product');
        }

        if (isset($this->request->post['feed_sitemap_multi_noindex_category'])) {
            $data['feed_sitemap_multi_noindex_category'] = $this->request->post['feed_sitemap_multi_noindex_category'];
        } else {
            $data['feed_sitemap_multi_noindex_category'] = $this->config->get('feed_sitemap_multi_noindex_category');
        }

        if (isset($this->request->post['feed_sitemap_multi_noindex_manufacturer'])) {
            $data['feed_sitemap_multi_noindex_manufacturer'] = $this->request->post['feed_sitemap_multi_noindex_manufacturer'];
        } else {
            $data['feed_sitemap_multi_noindex_manufacturer'] = $this->config->get('feed_sitemap_multi_noindex_manufacturer');
        }

        if (isset($this->request->post['feed_sitemap_multi_noindex_information'])) {
            $data['feed_sitemap_multi_noindex_information'] = $this->request->post['feed_sitemap_multi_noindex_information'];
        } else {
            $data['feed_sitemap_multi_noindex_information'] = $this->config->get('feed_sitemap_multi_noindex_information');
        }

        if (isset($this->request->post['feed_sitemap_multi_noindex_special'])) {
            $data['feed_sitemap_multi_noindex_special'] = $this->request->post['feed_sitemap_multi_noindex_special'];
        } else {
            $data['feed_sitemap_multi_noindex_special'] = $this->config->get('feed_sitemap_multi_noindex_special');
        }

        if (isset($this->request->post['feed_sitemap_multi_noindex_manufacturers'])) {
            $data['feed_sitemap_multi_noindex_manufacturers'] = $this->request->post['feed_sitemap_multi_noindex_manufacturers'];
        } else {
            $data['feed_sitemap_multi_noindex_manufacturers'] = $this->config->get('feed_sitemap_multi_noindex_manufacturers');
        }


        if (isset($this->request->post['feed_sitemap_multi_noindex_sitemap'])) {
            $data['feed_sitemap_multi_noindex_sitemap'] = $this->request->post['feed_sitemap_multi_noindex_sitemap'];
        } else {
            $data['feed_sitemap_multi_noindex_sitemap'] = $this->config->get('feed_sitemap_multi_noindex_sitemap');
        }

        if (isset($this->request->post['feed_sitemap_multi_noindex_contact'])) {
            $data['feed_sitemap_multi_noindex_contact'] = $this->request->post['feed_sitemap_multi_noindex_contact'];
        } else {
            $data['feed_sitemap_multi_noindex_contact'] = $this->config->get('feed_sitemap_multi_noindex_contact');
        }
        /* ------------------------------------ */

        if (isset($this->request->post['feed_sitemap_multi_status_home'])) {
            $data['feed_sitemap_multi_status_home'] = $this->request->post['feed_sitemap_multi_status_home'];
        } else {
            $data['feed_sitemap_multi_status_home'] = $this->config->get('feed_sitemap_multi_status_home');
        }

        if (isset($this->request->post['feed_sitemap_multi_status_product'])) {
            $data['feed_sitemap_multi_status_product'] = $this->request->post['feed_sitemap_multi_status_product'];
        } else {
            $data['feed_sitemap_multi_status_product'] = $this->config->get('feed_sitemap_multi_status_product');
        }

        if (isset($this->request->post['feed_sitemap_multi_status_category'])) {
            $data['feed_sitemap_multi_status_category'] = $this->request->post['feed_sitemap_multi_status_category'];
        } else {
            $data['feed_sitemap_multi_status_category'] = $this->config->get('feed_sitemap_multi_status_category');
        }

        if (isset($this->request->post['feed_sitemap_multi_status_manufacturer'])) {
            $data['feed_sitemap_multi_status_manufacturer'] = $this->request->post['feed_sitemap_multi_status_manufacturer'];
        } else {
            $data['feed_sitemap_multi_status_manufacturer'] = $this->config->get('feed_sitemap_multi_status_manufacturer');
        }

        if (isset($this->request->post['feed_sitemap_multi_status_manufacturers'])) {
            $data['feed_sitemap_multi_status_manufacturers'] = $this->request->post['feed_sitemap_multi_status_manufacturers'];
        } else {
            $data['feed_sitemap_multi_status_manufacturers'] = $this->config->get('feed_sitemap_multi_status_manufacturers');
        }

        if (isset($this->request->post['feed_sitemap_multi_status_information'])) {
            $data['feed_sitemap_multi_status_information'] = $this->request->post['feed_sitemap_multi_status_information'];
        } else {
            $data['feed_sitemap_multi_status_information'] = $this->config->get('feed_sitemap_multi_status_information');
        }

        if (isset($this->request->post['feed_sitemap_multi_status_special'])) {
            $data['feed_sitemap_multi_status_special'] = $this->request->post['feed_sitemap_multi_status_special'];
        } else {
            $data['feed_sitemap_multi_status_special'] = $this->config->get('feed_sitemap_multi_status_special');
        }

        if (isset($this->request->post['sitemap_multi_status_manufacturers'])) {
            $data['sitemap_multi_status_manufacturers'] = $this->request->post['sitemap_multi_status_manufacturers'];
        } else {
            $data['sitemap_multi_status_manufacturers'] = $this->config->get('sitemap_multi_status_manufacturers');
        }

        if (isset($this->request->post['feed_sitemap_multi_status_sitemap'])) {
            $data['feed_sitemap_multi_status_sitemap'] = $this->request->post['feed_sitemap_multi_status_sitemap'];
        } else {
            $data['feed_sitemap_multi_status_sitemap'] = $this->config->get('feed_sitemap_multi_status_sitemap');
        }

        if (isset($this->request->post['feed_sitemap_multi_status_contact'])) {
            $data['feed_sitemap_multi_status_contact'] = $this->request->post['feed_sitemap_multi_status_contact'];
        } else {
            $data['feed_sitemap_multi_status_contact'] = $this->config->get('feed_sitemap_multi_status_contact');
        }
        /* ----------------------------- */

        $data['items'] = array();
        $custom_links = $this->config->get('feed_sitemap_multi_custom');
        if (!empty($custom_links)) {
            foreach ($custom_links as $custom) {
                    foreach ($this->languages as $id=>$code) {
                        if (!empty($custom['link'][$code])) {
                           $link[$code] = $custom['link'][$code];
                        }
                    }
                $data['items'][] = array(
                    'link' => $link,
                    'changefreq' => $custom['changefreq'],
                    'priority' => $custom['priority']
                );
            }
        }

        if (isset($this->request->post['feed_sitemap_multi_status'])) {
            $data['feed_sitemap_multi_status'] = $this->request->post['feed_sitemap_multi_status'];
        } else {
            $data['feed_sitemap_multi_status'] = $this->config->get('feed_sitemap_multi_status');
        }


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/feed/sitemap_multi', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/feed/sitemap_multi')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

}