<?php
// *	@copyright	TERAGROUP


class ControllerToolData extends Controller {

    public function index() {
        $this->load->language('tool/data');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('tool/data', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['list_import'] = array();

        $files_import = glob(DIR_APPLICATION . 'controller/extension/import/*.php', GLOB_BRACE);

        foreach ($files_import as $file) {
            $extension = basename($file, '.php');

            $this->load->language('extension/import/' . $extension, 'extension');

            if ($this->user->hasPermission('access', 'extension/import/' . $extension)) {

                $data['list_import'][] = array(
                    'code' => $extension,
                    'text' => $this->language->get('extension')->get('heading_title'),
                    'image' => $this->language->get('extension')->get('heading_ico'),
                    'href' => $this->url->link('extension/import/' . $extension, 'user_token=' . $this->session->data['user_token'], true)
                );
            }
        }

        $data['list_export'] = array();

        $files_export = glob(DIR_APPLICATION . 'controller/extension/export/*.php', GLOB_BRACE);

        foreach ($files_export as $file) {
            $extension = basename($file, '.php');

            $this->load->language('extension/export/' . $extension, 'extension');

            if ($this->user->hasPermission('access', 'extension/export/' . $extension)) {

                $data['list_export'][] = array(
                    'code' => $extension,
                    'text' => $this->language->get('extension')->get('heading_title'),
                    'image' => $this->language->get('extension')->get('heading_ico'),
                    'href' => $this->url->link('extension/export/' . $extension, 'user_token=' . $this->session->data['user_token'], true)
                );
            }
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tool/data', $data));
    }
}