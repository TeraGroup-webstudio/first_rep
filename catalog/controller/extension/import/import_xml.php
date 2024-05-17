<?php
class ControllerExtensionImportImportXml extends Controller {
    public function index() {  // Import all data
       /* setting */
        $type_upload = $this->config->get('import_import_xml_type');
        $status_category = $this->config->get('import_import_xml_status_category'); // Статус категорій, імпортувати чи ні
        $status_product = $this->config->get('import_import_xml_status_product'); // Статус товарів, імпортувати чи ні
        $quantity = '';// кількість товару
        $stock_status_id = 0;
       /* setting */

        //echo 'Тип завантаження - '.$type_upload.'<br>';
        if($type_upload == 0){ //Працюємо з файлом
            echo 'Дані взято з файлу<br/>';
            $filename = $_SERVER['DOCUMENT_ROOT'].'upload/file/xml/import.xml';
            $xml = simplexml_load_file($filename);
        } else if($type_upload == 1){ //Працюємо з посиланням
            $url = $this->config->get('import_import_xml_url');
            echo 'Дані взято з посилання! <br/>';
            if($url != ''){
                $xml = file_get_contents($url);
            } else {
                echo 'Поле для посилання не заповнене';

            }
        }

        if($xml){
            if($status_category == 1){ // Якщо статус включений для оновлення даних по категоріях
                foreach($xml->shop->categories->category as $category) {
                    $category_id = $category['id'];
                    echo 'category_id - '.$category_id.'<br/>';
                }
            }
            if($status_product == 1){
                foreach ($xml->shop->offers->offer as $product) {
                    $product_id = $product['id'];
                    echo 'product_id - '.$product_id.'<br/>';
                }
            }

        }


    }

    /* Додаткові функції */




}
