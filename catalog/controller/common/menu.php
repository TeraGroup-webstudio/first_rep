<?php
class ControllerCommonMenu extends Controller {
    public function index() {
        $this->load->language('common/menu');

        require_once('system/library/Mobile_Detect.php');
        $detect = new Mobile_Detect;

        // Menu
        $this->load->model('catalog/category');

        $this->load->model('catalog/product');
        if ($detect->isMobile() ) {
            $data['url_special'] = $this->url->link('product/special');
            $data['url_manufacturer'] = $this->url->link('product/manufacturer');
        }

        $data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            if ($category['top']) {
                // Level 2
                $children_data = array();

                $children = $this->model_catalog_category->getCategories($category['category_id']);

                foreach ($children as $child) {
                    $filter_data = array(
                        'filter_category_id'  => $child['category_id'],
                        'filter_sub_category' => true
                    );

                    $children_data[] = array(
                        'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                        'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
                    );
                }

                // Level 1
                $data['categories'][] = array(
                    'name'     => $category['name'],
                    'children' => $children_data,
                    'column'   => $category['column'] ? $category['column'] : 1,
                    'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
                );
            }
        }

        //catalog Menu
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

        if (isset($parts[2])) {
            $data['child_id2'] = $parts[2];
        } else {
            $data['child_id2'] = 0;
        }

        if (isset($parts[3])) {
            $data['child_id3'] = $parts[3];
        } else {
            $data['child_id3'] = 0;
        }
        $data['catalog'] = array();

        $catalog = $this->model_catalog_category->getCategories(0);

        foreach ($catalog as $catalog_category) {

            $children_data = array();

            $children = $this->model_catalog_category->getCategories($catalog_category['category_id']);

            foreach ($children as $child) {

                $children2_data = array();
                $children2 = $this->model_catalog_category->getCategories($child['category_id']);

                foreach ($children2 as $child2) {

                    $children3_data = array();
                    $children3 = $this->model_catalog_category->getCategories($child2['category_id']);

                    foreach ($children3 as $child3) {

                        $filter_data3 = array(
                            'filter_category_id'  => $child3['category_id'],
                        );

                        $children3_data[] = array(
                            'category_id' => $child3['category_id'],
                            'name'        => $child3['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data3) . ')' : ''),
                            'href'        => $this->url->link('product/category', 'path=' . $catalog_category['category_id'] . '_' . $child['category_id']. '_' . $child2['category_id']. '_' . $child3['category_id'])
                        );


                    }

                    $filter_data2 = array(
                        'filter_category_id'  => $child2['category_id'],
                    );

                    $children2_data[] = array(
                        'category_id' => $child2['category_id'],
                        'name'        => $child2['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data2) . ')' : ''),
                        'children3'    => $children3_data,
                        'href'        => $this->url->link('product/category', 'path=' . $catalog_category['category_id'] . '_' . $child['category_id']. '_' . $child2['category_id'])
                    );


                }

                $filter_data1 = array(
                    'filter_category_id'  => $child['category_id'],
                );

                $children_data[] = array(
                    'category_id' => $child['category_id'],
                    'name'        => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data1) . ')' : ''),
                    'children2'    => $children2_data,
                    'href'        => $this->url->link('product/category', 'path=' . $catalog_category['category_id'] . '_' . $child['category_id'])
                );
            }

            $filter_data = array(
                'filter_category_id'  => $catalog_category['category_id'],
            );

            $data['catalog_categories'][] = array(
                'category_id' => $catalog_category['category_id'],
                'name'        => $catalog_category['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                'children'    => $children_data,
                'href'        => $this->url->link('product/category', 'path=' . $catalog_category['category_id'])
            );
        }

        return $this->load->view('common/menu', $data);
    }
}
