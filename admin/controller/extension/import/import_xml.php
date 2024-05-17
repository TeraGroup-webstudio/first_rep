<?php
class ControllerExtensionImportImportXml extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('extension/import/import_xml');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('import_import_xml', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            if (isset($this->request->get['route'])) {
                $get = explode("/", $this->request->get['route']);
                $folder = $get[0];
                $file = $get[1];
                if(isset($get[2])){
                    $file .= '/'.$get[2];
                }

                //$table = $file;

                //$id = $this->model_setting_setting->getLastId($table, $file);
                $url = '';
                $route = $folder.'/'.$file;
                $editroute = $folder.'/'.$file;

                if (isset($this->request->post['apply']) && $this->request->post['apply'] == '1') {
                    $this->response->redirect($this->url->link($editroute, 'user_token=' . $this->session->data['user_token'] . $url, true));
                }
            }

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=import', true));
        }

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
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=import', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/import/import_xml', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['user_token'] = $this->session->data['user_token'];

        $data['action'] = $this->url->link('extension/import/import_xml', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=import', true);

        if (isset($this->request->post['import_import_xml_type'])) {
            $data['import_import_xml_type'] = $this->request->post['import_import_xml_type'];
        } else {
            $data['import_import_xml_type'] = $this->config->get('import_import_xml_type');
        }

        if (isset($this->request->post['import_import_xml_url'])) {
            $data['import_import_xml_url'] = $this->request->post['import_import_xml_url'];
        } else {
            $data['import_import_xml_url'] = $this->config->get('import_import_xml_url');
        }

        if (isset($this->request->post['import_import_xml_status_category'])) {
            $data['import_import_xml_status_category'] = $this->request->post['import_import_xml_status_category'];
        } else {
            $data['import_import_xml_status_category'] = $this->config->get('import_import_xml_status_category');
        }

        if (isset($this->request->post['import_import_xml_status_product'])) {
            $data['import_import_xml_status_product'] = $this->request->post['import_import_xml_status_product'];
        } else {
            $data['import_import_xml_status_product'] = $this->config->get('import_import_xml_status_product');
        }

        if (isset($this->request->post['import_import_xml_status'])) {
            $data['import_import_xml_status'] = $this->request->post['import_import_xml_status'];
        } else {
            $data['import_import_xml_status'] = $this->config->get('import_import_xml_status');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/import/import_xml', $data));
    }

    public function send(){ //Зберігамо файл на фтп
        if(isset($_FILES)) {
            //Переданный массив сохраняем в переменной
            $file = $_FILES['trs'];

            $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/upload/file/xml/';

            if (move_uploaded_file($file['tmp_name'], $uploaddir . basename('import.xml'))) {
                $files[] = realpath($uploaddir . $file['name']);
            } else {
                $error = true;
            }

        }
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/import/import_xml')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}