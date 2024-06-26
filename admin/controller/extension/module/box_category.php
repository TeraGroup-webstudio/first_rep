<?php
// *	@copyright	OPENCART.PRO 2011 - 2016.
// *	@forum	http://forum.opencart.pro
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerExtensionModuleBoxCategory extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/box_category');
        $this->document->addScript('view/javascript/minicolor/jquery.minicolors.min.js');
        $this->document->addStyle('view/javascript/minicolor/jquery.minicolors.css');
		$this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tool/image');
        $this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('box_category', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/box_category', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/box_category', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/box_category', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/box_category', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

        if (isset($this->request->post['module_description_title'])) {
            $data['module_description_title'] = $this->request->post['module_description_title'];
        } elseif (!empty($module_info) && isset($module_info['module_description'])) {
            $data['module_description_title'] = $module_info['module_description_title'];
        } else {
            $data['module_description_title'] = array();
        }

		if (isset($this->request->post['module_description'])) {
			$data['module_description'] = $this->request->post['module_description'];
		} elseif (!empty($module_info) && isset($module_info['module_description'])) {
			//$data['module_description'] = $module_info['module_description'];
			if($module_info['module_description']){
			    foreach ($module_info['module_description'] as $key=>$item){
                    $category_info = array();
			        if(isset($item['category_info'])){
			            foreach($item['category_info'] as  $k=>$category_item){
			                if(isset($category_item['image']) && $category_item['image'] != ''){
                                $image = $this->model_tool_image->resize($category_item['image'], 26, 26);
                            } else {
                                $image = $this->model_tool_image->resize('no_image.png', 26, 26);
                            }
                            $category_info[$k] = array(
                                'color' => $category_item['color'],
                                'image' => $category_item['image'],
                                'thumb' => $image,
                                'main_category_id' => $category_item['main_category_id'],
                            );
                        }
                    }
                    $data['module_description'][$key] = array(
                        'maket' => $item['maket'],
                        'category_info' => $category_info,
                    );
                }
            }
		} else {
			$data['module_description'] = array();
		}

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 26, 26);
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

        // Categories
		$data['categories'] = array();
        $this->load->model('catalog/category');

        $categories = $this->model_catalog_category->getAllCategories();

		
		$list_categories = $this->model_catalog_category->getCategories($categories);
		if($list_categories){
			foreach($list_categories as $category){
				$data['categories'][] = array(
					'category_id' => $category['category_id'],
					'name' => str_replace("'","`",$category['name'])
				);
			}
			
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/box_category', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/box_category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		return !$this->error;
	}
}