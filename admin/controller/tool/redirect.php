<?php
class ControllerToolRedirect extends Controller {
    private $error = array();
    public function index(){
        $this->load->model('tool/redirect');
        $this->load->language('tool/redirect');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_success'] = $this->language->get('text_success');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['column_action'] = $this->language->get('column_action');
        $data['column_old_url'] = $this->language->get('column_old_url');
        $data['column_new_url'] = $this->language->get('column_new_url');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('tool/redirect', 'user_token=' . $this->session->data['user_token'], true)
        );

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }



        $empty_table_redirect =  $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "redirect'"); // Перевіряємо чи існує таблиця в базі даних
        if($empty_table_redirect->num_rows == 0){ // якщо не існує то створюємо визвавши функцію створення таблиці
            $this->installDB();
        }

        $page = 1;
        $redirect_total = $this->model_tool_redirect->getTotalRedirect();

        $data['redirects'] = array();
        $redirects = $this->model_tool_redirect->getRedirects();

        foreach ($redirects as $redirect){
            $data['redirects'][] = array(
                'redirect_id' => $redirect['redirect_id'],
                'old_url' => $redirect['old_url'],
                'new_url' => $redirect['new_url'],
                'edit'       => $this->url->link('tool/redirect/edit', 'user_token=' . $this->session->data['user_token'] . '&redirect_id=' . $redirect['redirect_id'] . '', true)
            );
        }


        $pagination = new Pagination();
        $pagination->total = $redirect_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('tool/redirect', 'user_token=' . $this->session->data['user_token'] . '' . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($redirect_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($redirect_total - $this->config->get('config_limit_admin'))) ? $redirect_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $redirect_total, ceil($redirect_total / $this->config->get('config_limit_admin')));


        $data['add'] = $this->url->link('tool/redirect/add', 'user_token=' . $this->session->data['user_token'] . '', true);
        $data['delete'] = $this->url->link('tool/redirect/delete', 'user_token=' . $this->session->data['user_token'] . '', true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tool/redirect', $data));

    }

    public function add() {
        $this->load->language('tool/redirect');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tool/redirect');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_tool_redirect->addRedirect($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('tool/redirect', 'user_token=' . $this->session->data['user_token'] . '', true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('tool/redirect');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tool/redirect');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_tool_redirect->editRedirect($this->request->get['redirect_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            $this->response->redirect($this->url->link('tool/redirect', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('tool/redirect');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tool/redirect');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $redirect_id) {
                $this->model_tool_redirect->deleteRedirect($redirect_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            $this->response->redirect($this->url->link('tool/redirect', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getForm() {
        $this->load->language('tool/redirect');
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['redirect_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['entry_old_url'] = $this->language->get('entry_old_url');
        $data['entry_new_url'] = $this->language->get('entry_new_url');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('tool/redirect', 'user_token=' . $this->session->data['user_token'] . '', true)
        );

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['old_url'])) {
            $data['error_old_url'] = $this->error['old_url'];
        } else {
            $data['error_old_url'] = '';
        }

        if (isset($this->error['new_url'])) {
            $data['error_new_url'] = $this->error['new_url'];
        } else {
            $data['error_new_url'] = '';
        }


        if (!isset($this->request->get['redirect_id'])) {
            $data['action'] = $this->url->link('tool/redirect/add', 'user_token=' . $this->session->data['user_token'] . '', true);
        } else {
            $data['action'] = $this->url->link('tool/redirect/edit', 'user_token=' . $this->session->data['user_token'] . '&redirect_id=' . $this->request->get['redirect_id'] . '', true);
        }

        $data['cancel'] = $this->url->link('tool/redirect', 'user_token=' . $this->session->data['user_token'] . '', true);

        if (isset($this->request->get['redirect_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $redirect_info = $this->model_tool_redirect->getRedirect($this->request->get['redirect_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['old_url'])) {
            $data['old_url'] = $this->request->post['old_url'];
        } elseif (!empty($redirect_info)) {
            $data['old_url'] = $redirect_info['old_url'];
        } else {
            $data['old_url'] = '';
        }

        if (isset($this->request->post['new_url'])) {
            $data['new_url'] = $this->request->post['new_url'];
        } elseif (!empty($redirect_info)) {
            $data['new_url'] = $redirect_info['new_url'];
        } else {
            $data['new_url'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tool/redirect_form', $data));

    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'tool/redirect')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['old_url']) < 1) || (utf8_strlen($this->request->post['old_url']) > 128)) {
            $this->error['old_url'] = $this->language->get('error_old_url');
        }

        if ((utf8_strlen($this->request->post['new_url']) < 1) || (utf8_strlen($this->request->post['new_url']) > 128)) {
            $this->error['new_url'] = $this->language->get('error_new_url');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'tool/redirect')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function installDB() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "redirect` (
			  `redirect_id` INT(11) NOT NULL AUTO_INCREMENT,
			  `old_url` VARCHAR(256) NOT NULL,
			  `new_url` VARCHAR(256) NOT NULL,
			  `date_added` DATE,
			  PRIMARY KEY (`redirect_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");
    }
}