<?php
// *	@copyright	OPENCART.PRO 2011 - 2016.
// *	@forum	http://forum.opencart.pro
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerExtensionModuleBoxCategory extends Controller {
	public function index($setting) {

        $data['heading_title'] = html_entity_decode($setting['module_description_title'][$this->config->get('config_language_id')]['title'], ENT_QUOTES, 'UTF-8');

        $data['box1'] = array();
        $data['box2'] = array();
        $data['box3'] = array();
        $data['box4'] = array();
        $info1 = array();
        $info2 = array();
        $info3 = array();
        $info4 = array();

        $data['count_box'] = 4;

        //print_r($setting);

        if($setting['status'] == 1){

            if($setting['module_description']['box1']){
                if($setting['module_description']['box1']['category_info']){
                    foreach ($setting['module_description']['box1']['category_info'] as $key=>$box1){
                        $category_info = $this->Category($box1['main_category_id']);
                        if($category_info){
                            if($box1['image'] !=''){
                                $image = '/image/' . $box1['image'];
                            } else if($category_info['image'] !=''){
                                $image = '/image/' . $category_info['image'];
                            } else {
                                $image = '';
                            }
                            $info1[$key] = array(
                                'color' =>  $box1['color'],
                                'name_category' => $category_info['name'],
                                'href' => $this->url->link('product/category', 'path=' . $box1['main_category_id'] . ''),
                                'image' => $image
                            );
                        }

                    }
                }
                $data['box1'] = array(
                    'maket' => $setting['module_description']['box1']['maket'],
                    'info'=> $info1
                );
            }

            if($setting['module_description']['box2']){
                if($setting['module_description']['box2']['category_info']){
                    foreach ($setting['module_description']['box2']['category_info'] as $key=>$box2){
                        $category_info = $this->Category($box2['main_category_id']);
                        if($category_info) {
                            if($box2['image'] !=''){
                                $image = '/image/' . $box2['image'];
                            } else if($category_info['image'] !=''){
                                $image = '/image/' . $category_info['image'];
                            } else {
                                $image = '';
                            }
                            $info2[$key] = array(
                                'color' => $box2['color'],
                                'name_category' => $category_info['name'],
                                'href' => $this->url->link('product/category', 'path=' . $box2['main_category_id'] . ''),
                                'image' => $image
                            );
                        }
                    }
                }
                $data['box2'] = array(
                    'maket' => $setting['module_description']['box2']['maket'],
                    'info'=> $info2
                );
            }

            if($setting['module_description']['box3']){
                if($setting['module_description']['box3']['category_info']){
                    foreach ($setting['module_description']['box3']['category_info'] as $key=>$box3){
                        $category_info = $this->Category($box3['main_category_id']);
                        if($category_info) {
                            if($box3['image'] !=''){
                                $image = '/image/' . $box3['image'];
                            } else if($category_info['image'] !=''){
                                $image = '/image/' . $category_info['image'];
                            } else {
                                $image = '';
                            }
                            $info3[$key] = array(
                                'color' => $box3['color'],
                                'name_category' => $category_info['name'],
                                'href' => $this->url->link('product/category', 'path=' . $box3['main_category_id'] . ''),
                                'image' => $image
                            );
                        }
                    }
                }
                $data['box3'] = array(
                    'maket' => $setting['module_description']['box3']['maket'],
                    'info'=> $info3
                );
            }

            if($setting['module_description']['box4']){
                if($setting['module_description']['box4']['category_info']){
                    foreach ($setting['module_description']['box4']['category_info'] as $key=>$box4){
                        $category_info = $this->Category($box4['main_category_id']);
                        if($category_info) {
                            if($box4['image'] !=''){
                                $image = '/image/' . $box4['image'];
                            } else if($category_info['image'] !=''){
                                $image = '/image/' . $category_info['image'];
                            } else {
                                $image = '';
                            }
                            $info4[$key] = array(
                                'color' => $box4['color'],
                                'name_category' => $category_info['name'],
                                'href' => $this->url->link('product/category', 'path=' . $box4['main_category_id'] . ''),
                                'image' => $image
                            );
                        }
                    }
                }
                $data['box4'] = array(
                    'maket' => $setting['module_description']['box4']['maket'],
                    'info'=> $info4
                );
            }


            return $this->load->view('extension/module/box_category', $data);
        }



	}

	private function Category($category_id){
        $this->load->model('catalog/category');
        $category_info = $this->model_catalog_category->getCategory($category_id);
        return $category_info;
    }
}