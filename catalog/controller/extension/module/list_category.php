<?php
class ControllerExtensionModuleListCategory extends Controller {
    public function index($setting) {
        static $module = 0;

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();
        $language_id = $this->config->get('config_language_id');

        $this->load->language('extension/module/list_category');

        $this->load->model('tool/image');

        $this->load->model('setting/setting');

        if ($setting['type'] == 0) {
            $this->document->addStyle('catalog/view/javascript/owl-carousel/owl.carousel.min.css');
            $this->document->addScript('catalog/view/javascript/owl-carousel/owl.carousel.min.js');
        }

        $data['type'] = $setting['type'];
        $data['click_action'] = $setting['click_action'];


        if (!$setting['limit']) {
            $setting['limit'] = 4;
        }

        if ($setting['title']) {
            $data['heading_title'] = $setting['title'][$language_id];
        }

        $data['controls'] = isset($setting['controls']) ? $setting['controls'] : array();

        $data['autoplay'] = $setting['autoplay'];
        $data['autoplay_speed'] = $setting['autoplay_speed'];

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

        if (isset($this->request->get['path'])) {
            $parts = explode('_', (string)$this->request->get['path']);
        } else {
            $parts = array();
        }

        if (isset($this->request->get['path'])) {
            $parts = explode('_', (string)$this->request->get['path']);
        } else {
            $parts = array();
        }

        if (isset($parts[0])) {
            $data['category_id'] = $parts[0];
        } else {
            $data['category_id'] = 0;
        }

        if (isset($parts[1])) {
            $data['child_id'] = $parts[1];
        } else {
            $data['child_id'] = 0;
        }

        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $data['categories'] = array();
        $categories_list = array();
        if($setting['type_category'] == 0){
            $categories = $this->model_catalog_category->getCategories(0);
            if($categories){
                foreach ($categories as $category) {
                    $categories_list[] = $category['category_id'];
                }
            }
        } else {
            $categories_list = $setting['category'];
        }


        $categories = array();

        foreach ($categories_list as $category_id) {
            $category_info = $this->model_catalog_category->getCategory($category_id);

            if ($category_info) {
                $categories[] = $category_info;
            }
        }

        foreach ($categories as $category) {
            $children_data = array();

            $children = $this->model_catalog_category->getCategories($category['category_id']);

            $children = array_slice($children, 0, $setting['limit']);

            foreach($children as $child) {
                $filter_data = array('filter_category_id' => $child['category_id'], 'filter_sub_category' => true);

                $children_data[] = array(
                    'category_id' => $child['category_id'],
                    'name' => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                    'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
                );
            }




            $filter_data = array(
                'filter_category_id'  => $category['category_id'],
                'filter_sub_category' => true
            );

            if ($category['image']) {
                $image = $this->model_tool_image->resize($category['image'], $setting['width'], $setting['height']);
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
            }

            $data['categories'][] = array(
                'category_id' => $category['category_id'],
                'thumb'       => $image,
                'img_width'   => $setting['width'] . 'px',
                'img_height'  => $setting['height'] . 'px',
                'thumb_holder'=> $this->model_tool_image->resize('catalog/default/src_holder.png', $setting['width'], $setting['height']),
                'active'     	=> ($category['category_id'] == $data['category_id']) ? true : false,
                'name'        => $category['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                'children'    => $children_data,
                'href'        => $this->url->link('product/category', 'path=' . $category['category_id'])
            );
        }

        $data['module'] = $module++;

        if ($data['categories']) {
            return $this->load->view('extension/module/list_category', $data);
        }
    }
}
