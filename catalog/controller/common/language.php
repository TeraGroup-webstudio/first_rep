<?php
class ControllerCommonLanguage extends Controller {
	public function index() {
		$this->load->language('common/language');

		$data['action'] = $this->url->link('common/language/language', '', $this->request->server['HTTPS']);

		$data['code'] = $this->session->data['language'];
        $data['prefix_full'] = $this->config->get('theme_default_prefix_lang');
		$this->load->model('localisation/language');

        if($this->config->get('theme_default_prefix_lang_status') == 0){
            $data['type_lang'] = 0;
        } else {
            $data['type_lang'] = 1;
        }
		$data['languages'] = array();

		$results = $this->model_localisation_language->getLanguages();

		foreach ($results as $result) {
			if ($result['status']) {
				$data['languages'][] = array(
					'name' => $result['name'],
					'code' => $result['code']
				);
			}
		}
//
		if (!isset($this->request->get['route'])) {
		    if($this->config->get('theme_default_prefix_lang_status') == 0){
                $data['redirect'] = $this->url->link('common/home');
            } else {
                $data['redirect_route'] = 'common/home';
                $data['redirect_query'] = '';
                $data['redirect_ssl']   = '';
            }

		} else {
			$url_data = $this->request->get;

			unset($url_data['_route_']);

			$route = $url_data['route'];

			unset($url_data['route']);

			$url = '';

			if ($url_data) {
				$url = '&' . urldecode(http_build_query($url_data, '', '&'));
			}

            if($this->config->get('theme_default_prefix_lang_status') == 0){
                $data['redirect'] = $this->url->link($route, $url, $this->request->server['HTTPS']);
            } else {
                $data['redirect_route'] = $route;
                $data['redirect_query'] = $url;
                $data['redirect_ssl'] = $this->request->server['HTTPS'];
            }

		}

		return $this->load->view('common/language', $data);
	}

	public function language() {
		if (isset($this->request->post['code'])) {
			$this->session->data['language'] = $this->request->post['code'];
		}

        if($this->config->get('theme_default_prefix_lang_status') == 0){
            if (isset($this->request->post['redirect'])) {
                $this->response->redirect($this->request->post['redirect']);
            } else {
                $this->response->redirect($this->url->link('common/home'));
            }
        } else {
            if (isset($this->request->post['redirect_route'])) {
                $url = $this->url->link($this->request->post['redirect_route'],
                    isset($this->request->post['redirect_query']) ? html_entity_decode($this->request->post['redirect_query']) : '',
                    isset($this->request->post['redirect_ssl']) ? $this->request->post['redirect_ssl'] : '');
                $this->response->redirect($url);
            } else {
                $this->response->redirect($this->url->link('common/home'));
            }
        }

	}
}