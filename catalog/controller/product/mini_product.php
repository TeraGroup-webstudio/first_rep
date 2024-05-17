<?php

class ControllerProductMiniProduct extends Controller {

    public function index($data) {
        $data['is_wishlist'] = false;
        if($data['product_id']){
            $product_id = $data['product_id'];
        } else {
            $product_id = 0;
        }
        $data['lazy_load_status'] = $this->config->get('theme_default_image_lazy_load_status');
        if ($this->customer->isLogged()) {
            $this->load->model('account/wishlist');

            $wl = $this->model_account_wishlist->getWishlistProduct($product_id);

            if(isset($wl)){
                if (in_array($product_id, $wl)) {
                    $data['is_wishlist'] = true;
                }
            }
        } else {
            if(isset($this->session->data['wishlist'])){
                if (in_array($product_id, $this->session->data['wishlist'])) {
                    $data['is_wishlist'] = true;
                }
            }
        }


        $this->load->language('product/thumb');
        $data['button_cart'] = $this->language->get('button_cart');
        $data['text_stock'] = $this->language->get('text_stock');
        $data['text_model'] = $this->language->get('text_model');
        $data['button_wishlist'] = $this->language->get('button_wishlist');
        $data['button_compare'] = $this->language->get('button_compare');
        $data['review_status'] = (int)$this->config->get('config_review_status');

        $data['product_wishlist_status'] =  $this->config->get('theme_default_all_product_wishlist_status');
        $data['product_compare_status'] =  $this->config->get('theme_default_all_product_compare_status');

        return $this->load->view('product/mini_product', $data);
    }
}