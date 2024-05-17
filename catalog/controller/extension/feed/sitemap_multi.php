<?php
class ControllerExtensionFeedSitemapMulti extends Controller {
    private $filename = 'sitemap';
    private $main_ext = '_main';
    private $category_ext = '_categories';
    private $manufacturer_ext = '_manufacturers';
    private $product_ext = '_products';
    private $blog_ext = '_blog';
    private $octblog_ext = '_oct_blog';
    private $clean_blog_ext = '_clean_blog';
    private $keywords_ext = '_keywords';
    private $galleria_ext = '_galleria';
    private $ext = '.xml';
    private $filepath;
    private $static = false;
    private $languages = array();
    private $hreflang = array();
    private $ml = false;
    private $domain = false;
    private $prefix;
    private $prefix_module;
    private $prefix_type;
    private $prefix_code = array();
    private $separate = 0;
    private $changefreq = 0;
    private $priority = 0;
    private $lastmod = 0;
    private $location;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->prefix = (version_compare(VERSION, '3.0', '>=')) ? 'feed_' : '';
        $this->prefix_module = (version_compare(VERSION, '3.0', '>=')) ? 'module_' : '';
        $this->location = dirname(DIR_APPLICATION) . '/';
        $this->filepath_blog = $this->location . $this->filename . $this->blog_ext . $this->ext;
        $this->filepath_octblog = $this->location . $this->filename . $this->octblog_ext . $this->ext;
        $this->filepath_clean_blog = $this->location . $this->filename . $this->clean_blog_ext . $this->ext;
        $this->filepath_category = $this->location . $this->filename . $this->category_ext . $this->ext;
        $this->filepath_manufacturer = $this->location . $this->filename . $this->manufacturer_ext . $this->ext;
        $this->filepath_keywords = $this->location . $this->filename . $this->keywords_ext . $this->ext;
        $this->filepath_galleria = $this->location . $this->filename . $this->galleria_ext . $this->ext;
        $this->default_time = date('Y-m-d\TH:i:sP', time());
        $this->static = $this->config->get($this->prefix.'sitemap_multi_static');
        $this->separate = $this->config->get($this->prefix.'sitemap_multi_separate');
        $this->hreflang = $this->config->get($this->prefix.'sitemap_multi_code');
        $this->prefix_type = $this->config->get($this->prefix.'sitemap_multi_prefix');
        $this->prefix_code = $this->config->get($this->prefix.'sitemap_multi_prefix_code');
        $this->changefreq = $this->config->get($this->prefix.'sitemap_multi_changefreq');
        $this->priority = $this->config->get($this->prefix.'sitemap_multi_priority');
        $this->lastmod = $this->config->get($this->prefix.'sitemap_multi_lastmod');

        $this->load->model('localisation/language');
        foreach ($this->model_localisation_language->getLanguages() as $lang) {
            $this->languages[$lang['language_id']] = $lang['code'];
        }

        $this->ml = (!empty($this->languages) && count($this->languages) > 1) ? true : false;

        if ($this->request->server['HTTPS']) {
            $this->domain = $this->config->get('config_ssl');
        } else {
            $this->domain = $this->config->get('config_url');
        }
    }

    public function index() {
        if ($this->config->get($this->prefix.'sitemap_multi_status')) {

            $status = false;
            if (!$this->config->get($this->prefix.'sitemap_multi_safe') || empty($this->config->get($this->prefix.'sitemap_multi_key'))) {
                $status = true;
            } elseif ($this->config->get($this->prefix.'sitemap_multi_safe') && !empty($this->config->get($this->prefix.'sitemap_multi_key')) && !empty($this->request->get['key'])) {
                if ($this->config->get($this->prefix.'sitemap_multi_key') == $this->request->get['key']) {
                    $status = true;
                }
            }

            if ($status) {
                set_time_limit(0);
                ignore_user_abort(true);
                while(ob_get_level()) ob_end_clean();
                ob_implicit_flush(true);

                $this->load->model('extension/feed/sitemap_multi');

                $header = '<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="' . $this->domain . 'catalog/view/javascript/sitemap_multi/sitemap_urlset.xsl"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
                $footer = '</urlset>';

                $pages = 1;
                $page = 0;
                $filter = array();

                if ($this->separate) {
                    $this->load->model('catalog/product');
                    $pages = $this->config->get($this->prefix.'sitemap_multi_pages') ? intval($this->config->get($this->prefix.'sitemap_multi_pages')) : 1;
                    $total = $this->model_catalog_product->getTotalProducts();
                    $limit = floor($total / $pages);
                    if ($limit == 0) $limit = 1;
                    $page = (!empty($this->request->get['page']) && intval($this->request->get['page'])>=1 && $this->separate && $pages >= 1) ? (int) $this->request->get['page'] : 0;
                    if ($page > $pages) exit();
                    $stock = $total - ($limit * $pages);
                    $filter = array(
                        'start' => ($page && $pages >= $page) ? ($page - 1) * $limit : 0,
                        'limit' => ($page==$pages) ? $limit + $stock : $limit
                    );
                }

                if ($this->static && $this->separate && empty($this->session->data['sitemap_last'])) {
                    $sitemap_namespace = $this->location . $this->filename . '*.xml';
                    array_map('unlink', glob($sitemap_namespace));

                    $filepath_index = $this->location . $this->filename . $this->ext;
                    file_put_contents($filepath_index, '');
                    file_put_contents($filepath_index, '<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="' . $this->domain . 'catalog/view/javascript/sitemap_multi/sitemapindex_urlset.xsl"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', FILE_APPEND | LOCK_EX);
                    file_put_contents($filepath_index, '<sitemap><loc>' . $this->domain . $this->filename . $this->main_ext . $this->ext . '</loc></sitemap>', FILE_APPEND | LOCK_EX);
                    if ($this->config->get($this->prefix.'sitemap_multi_status_blog_category') || $this->config->get($this->prefix.'sitemap_multi_status_blog_article')) {
                        file_put_contents($this->filepath_blog, '');
                        file_put_contents($this->filepath_blog, $header, FILE_APPEND | LOCK_EX);
                        file_put_contents($filepath_index, '<sitemap><loc>' . $this->domain . $this->filename . $this->blog_ext .  $this->ext . '</loc></sitemap>', FILE_APPEND | LOCK_EX);
                    }
                    if ($this->config->get($this->prefix.'sitemap_multi_status_oct_blog_category') || $this->config->get($this->prefix.'sitemap_multi_status_oct_blog_article')) {
                        file_put_contents($this->filepath_octblog, '');
                        file_put_contents($this->filepath_octblog, $header, FILE_APPEND | LOCK_EX);
                        file_put_contents($filepath_index, '<sitemap><loc>' . $this->domain . $this->filename . $this->octblog_ext .  $this->ext . '</loc></sitemap>', FILE_APPEND | LOCK_EX);
                    }
                    if ($this->config->get($this->prefix.'sitemap_multi_status_clean_blog') && $this->model_extension_feed_sitemap_multi->hasCleanBlog()) {
                        file_put_contents($this->filepath_clean_blog, '');
                        file_put_contents($this->filepath_clean_blog, $header, FILE_APPEND | LOCK_EX);
                        file_put_contents($filepath_index, '<sitemap><loc>' . $this->domain . $this->filename . $this->clean_blog_ext .  $this->ext . '</loc></sitemap>', FILE_APPEND | LOCK_EX);
                    }
                    if ($this->config->get($this->prefix.'sitemap_multi_status_keywords') && $this->config->get($this->prefix_module.'keywords_status')) {
                        file_put_contents($this->filepath_keywords, '');
                        file_put_contents($this->filepath_keywords, $header, FILE_APPEND | LOCK_EX);
                        file_put_contents($filepath_index, '<sitemap><loc>' . $this->domain . $this->filename . $this->keywords_ext .  $this->ext . '</loc></sitemap>', FILE_APPEND | LOCK_EX);
                    }
                    if ($this->config->get($this->prefix.'sitemap_multi_status_galleria') && $this->config->get($this->prefix_module.'galleria_status')) {
                        file_put_contents($this->filepath_galleria, '');
                        file_put_contents($this->filepath_galleria, $header, FILE_APPEND | LOCK_EX);
                        file_put_contents($filepath_index, '<sitemap><loc>' . $this->domain . $this->filename . $this->galleria_ext .  $this->ext . '</loc></sitemap>', FILE_APPEND | LOCK_EX);
                    }
                    if ($this->config->get($this->prefix.'sitemap_multi_status_category')) {
                        file_put_contents($this->filepath_category, '');
                        file_put_contents($this->filepath_category, $header, FILE_APPEND | LOCK_EX);
                        file_put_contents($filepath_index, '<sitemap><loc>' . $this->domain . $this->filename . $this->category_ext .  $this->ext . '</loc></sitemap>', FILE_APPEND | LOCK_EX);
                    }
                    if ($this->config->get($this->prefix.'sitemap_multi_status_manufacturer')) {
                        file_put_contents($this->filepath_manufacturer, '');
                        file_put_contents($this->filepath_manufacturer, $header, FILE_APPEND | LOCK_EX);
                        file_put_contents($filepath_index, '<sitemap><loc>' . $this->domain . $this->filename . $this->manufacturer_ext .  $this->ext . '</loc></sitemap>', FILE_APPEND | LOCK_EX);
                    }
                    if ($this->config->get($this->prefix.'sitemap_multi_status_product')) {
                        for ($page=1; $page <= $pages; $page++) {
                            file_put_contents($filepath_index, '<sitemap><loc>' . $this->domain . $this->filename . $this->product_ext . ($pages > 1 ? $page : '') . $this->ext . '</loc></sitemap>', FILE_APPEND | LOCK_EX);
                        }
                    }
                    file_put_contents($filepath_index, '</sitemapindex>', FILE_APPEND | LOCK_EX);
                }

                if ($this->static) {
                    if ($this->separate) {
                        $end = ($pages == 1) ? 1 : $pages;
                        $start = !empty($this->session->data['sitemap_last']) ? intval($this->session->data['sitemap_last']) : 0;
                        for ($page = $start; $page <= $end; $page++) {
                            $this->session->data['sitemap_last'] = $page;
                            $page_param = '';

                            if ($page > 0) {
                                $page_param = $this->product_ext . ($pages > 1 ? $page : '');
                            } else{
                                $page_param = $this->main_ext;
                            }

                            $this->filepath = $this->location . $this->filename . $page_param . $this->ext;
                            file_put_contents($this->filepath, '');
                            file_put_contents($this->filepath, $header, FILE_APPEND | LOCK_EX);

                            if ($page == 0) {
                                $this->generateStaticSitemap();
                            } else {
                                if ($this->config->get($this->prefix.'sitemap_multi_status_product')) {
                                    if ($pages > 1) {
                                        $filter = array(
                                            'start' => ($page && $pages >= $page) ? ($page - 1) * $limit : 0,
                                            'limit' => ($page==$pages) ? $limit + $stock : $limit
                                        );
                                    }
                                    $this->getProducts($filter);
                                }
                            }

                            file_put_contents($this->filepath, $footer, FILE_APPEND | LOCK_EX);

                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_blog_category') || $this->config->get($this->prefix.'sitemap_multi_status_blog_article')) {
                            file_put_contents($this->filepath_blog, $footer, FILE_APPEND | LOCK_EX);
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_oct_blog_category') || $this->config->get($this->prefix.'sitemap_multi_status_oct_blog_article')) {
                            file_put_contents($this->filepath_octblog, $footer, FILE_APPEND | LOCK_EX);
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_clean_blog') && $this->model_extension_feed_sitemap_multi->hasCleanBlog()) {
                            file_put_contents($this->filepath_clean_blog, $footer, FILE_APPEND | LOCK_EX);
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_keywords') && $this->config->get($this->prefix_module.'keywords_status')) {
                            file_put_contents($this->filepath_keywords, $footer, FILE_APPEND | LOCK_EX);
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_galleria') && $this->config->get($this->prefix_module.'galleria_status')) {
                            file_put_contents($this->filepath_galleria, $footer, FILE_APPEND | LOCK_EX);
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_category')) {
                            file_put_contents($this->filepath_category, $footer, FILE_APPEND | LOCK_EX);
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_manufacturer')) {
                            file_put_contents($this->filepath_manufacturer, $footer, FILE_APPEND | LOCK_EX);
                        }

                    } else {

                        $this->filepath = $this->location . $this->filename . $this->ext;
                        file_put_contents($this->filepath, '');
                        file_put_contents($this->filepath, $header, FILE_APPEND | LOCK_EX);

                        $this->generateStaticSitemap();

                        if ($this->config->get($this->prefix.'sitemap_multi_status_product')) {
                            $this->getProducts($filter);
                        }

                        file_put_contents($this->filepath, $footer, FILE_APPEND | LOCK_EX);
                    }

                    unset($this->session->data['sitemap_last']);

                    $response = json_encode(['status' => 'success', 'sitemap' => $this->domain . $this->filename . $this->ext], JSON_UNESCAPED_SLASHES);
                    $this->response->setOutput($response);

                } else {

                    $output = $header;

                    if ($page==0 || $pages==1) {

                        $output .= $this->getPages();

                        if ($this->config->get($this->prefix.'sitemap_multi_status_information')) {
                            $output .= $this->getInformations();
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_category')) {
                            $output .= $this->getCategories(0, '');
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_manufacturer')) {
                            $output .= $this->getManufacturers();
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_blog_category')) {
                            $output .= $this->getOcstoreBlogCategories();
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_blog_article')) {
                            $output .= $this->getOcstoreBlogArticles();
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_oct_blog_category')) {
                            $output .= $this->getOCTBlogCategories();
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_oct_blog_article')) {
                            $output .= $this->getOCTBlogArticles();
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_clean_blog')) {
                            $output .= $this->getCleanBlog();
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_keywords')) {
                            $output .= $this->getKeywordsPages();
                        }

                        if ($this->config->get($this->prefix.'sitemap_multi_status_galleria')) {
                            $output .= $this->getGalleria();
                        }
                    }

                    if (($page>0 || $pages==1) && $this->config->get($this->prefix.'sitemap_multi_status_product')) {
                        $output .= $this->getProducts($filter);
                    }

                    $output .= $footer;

                    $this->response->addHeader('Content-Type: application/xml');
                    $this->response->setOutput($output);
                }
            } else {
                $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
                $this->response->setOutput('Access denied');
            }
        }
    }

    protected function generateStaticSitemap() {
        $this->getPages();

        if ($this->config->get($this->prefix.'sitemap_multi_status_information')) {
            $this->getInformations();
        }

        if ($this->config->get($this->prefix.'sitemap_multi_status_category')) {
            $this->getCategories(0, '');
        }

        if ($this->config->get($this->prefix.'sitemap_multi_status_manufacturer')) {
            $this->getManufacturers();
        }

        if ($this->config->get($this->prefix.'sitemap_multi_status_blog_category')) {
            $this->getOcstoreBlogCategories();
        }

        if ($this->config->get($this->prefix.'sitemap_multi_status_blog_article')) {
            $this->getOcstoreBlogArticles();
        }

        if ($this->config->get($this->prefix.'sitemap_multi_status_oct_blog_category')) {
            $this->getOCTBlogCategories();
        }

        if ($this->config->get($this->prefix.'sitemap_multi_status_oct_blog_article')) {
            $this->getOCTBlogArticles();
        }

        if ($this->config->get($this->prefix.'sitemap_multi_status_clean_blog')) {
            $this->getCleanBlog();
        }

        if ($this->config->get($this->prefix.'sitemap_multi_status_keywords')) {
            $this->getKeywordsPages();
        }

        if ($this->config->get($this->prefix.'sitemap_multi_status_galleria')) {
            $this->getGalleria();
        }
    }

    protected function getProducts($filter) {
        $output = '';
        $products = $this->model_extension_feed_sitemap_multi->getProducts($filter);
        foreach ($products as $product) {
            if ($this->static) $output = NULL;
            $output .= $this->getLinkItem('product', 'product/product', 'product_id=' . $product['product_id'], (strtotime($product['date_modified']) > 0 ? date('Y-m-d\TH:i:sP', strtotime($product['date_modified'])) : date('Y-m-d\TH:i:sP', strtotime($product['date_added']))), $product['image']);
            if ($this->static) file_put_contents($this->filepath, $output, FILE_APPEND | LOCK_EX);
        }
        if (!$this->static) return $output;
    }

    protected function getCategories($parent_id, $current_path = '') {
        $output = '';
        $results = $this->model_extension_feed_sitemap_multi->getCategories($parent_id);
        foreach ($results as $result) {
            if ($this->static) $output = NULL;
            if (!$current_path) {
                $new_path = $result['category_id'];
            } else {
                $new_path = $current_path . '_' . $result['category_id'];
            }
            $output .= $this->getLinkItem('category', 'product/category', 'path=' . $new_path, date('Y-m-d\TH:i:sP', strtotime($result['date_modified'])));
            $output .= $this->getCategories($result['category_id'], $new_path);
            if ($this->static) {
                if ($this->separate) {
                    file_put_contents($this->filepath_category, $output, FILE_APPEND | LOCK_EX);
                } else {
                    file_put_contents($this->filepath, $output, FILE_APPEND | LOCK_EX);
                }
            }
        }

        if (!$this->static) return $output;
    }

    protected function getManufacturers() {
        $output = '';
        $manufacturers = $this->model_extension_feed_sitemap_multi->getManufacturers();
        foreach ($manufacturers as $manufacturer) {
            if ($this->static) $output = NULL;
            $output .= $this->getLinkItem('manufacturer', 'product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id']);
            if ($this->static) {
                if ($this->separate) {
                    file_put_contents($this->filepath_manufacturer, $output, FILE_APPEND | LOCK_EX);
                } else {
                    file_put_contents($this->filepath, $output, FILE_APPEND | LOCK_EX);
                }
            }
        }
        if (!$this->static) return $output;
    }

    protected function getInformations() {
        $output = '';
        $informations = $this->model_extension_feed_sitemap_multi->getInformations();
        foreach ($informations as $information) {
            if ($this->static) $output = NULL;
            $output .= $this->getLinkItem('information', 'information/information', 'information_id=' . $information['information_id']);
            if ($this->static) file_put_contents($this->filepath, $output, FILE_APPEND | LOCK_EX);
        }
        if (!$this->static) return $output;
    }

    protected function getPages() {
        $output = '';

        if ($this->config->get($this->prefix.'sitemap_multi_status_home')) {
            $output .= $this->getLinkItem('home', 'common/home');
        }

        if ($this->config->get($this->prefix.'sitemap_multi_status_special')) {
            $output .= $this->getLinkItem('special', 'product/special');
        }

        if ($this->config->get($this->prefix.'sitemap_multi_status_manufacturers')) {
            $output .= $this->getLinkItem('manufacturers', 'product/manufacturer');
        }

        if ($this->config->get($this->prefix.'sitemap_multi_status_sitemap')) {
            $output .= $this->getLinkItem('sitemap', 'information/sitemap');
        }

        if ($this->config->get($this->prefix.'sitemap_multi_status_contact')) {
            $output .= $this->getLinkItem('contact', 'information/contact');
        }

        $output .= $this->getCustomLinks();

        if ($this->static) {
            file_put_contents($this->filepath, $output, FILE_APPEND | LOCK_EX);
        } else {
            return $output;
        }
    }

    protected function getOcstoreBlogArticles() {
        $output = '';
        $articles = $this->model_extension_feed_sitemap_multi->getOcstoreBlogArticles();
        foreach ($articles as $article) {
            if ($this->static) $output = NULL;
            $output .= $this->getLinkItem('blog_article', 'blog/article', 'article_id=' . $article['article_id'], date('Y-m-d\TH:i:sP', strtotime($article['date_modified'])));
            if ($this->static) {
                if ($this->separate) {
                    file_put_contents($this->filepath_blog, $output, FILE_APPEND | LOCK_EX);
                } else {
                    file_put_contents($this->filepath, $output, FILE_APPEND | LOCK_EX);
                }
            }
        }
        if (!$this->static) return $output;
    }

    protected function getOcstoreBlogCategories() {
        $output = '';
        $results = $this->model_extension_feed_sitemap_multi->getOcstoreBlogCategories();
        foreach ($results as $result) {
            if ($this->static) $output = NULL;
            $output .= $this->getLinkItem('blog_category', 'blog/category', 'blog_category_id=' . $result['blog_category_id'], date('Y-m-d\TH:i:sP', strtotime($result['date_modified'])));
            if ($this->static) {
                if ($this->separate) {
                    file_put_contents($this->filepath_blog, $output, FILE_APPEND | LOCK_EX);
                } else {
                    file_put_contents($this->filepath, $output, FILE_APPEND | LOCK_EX);
                }
            }
        }

        if (!$this->static) return $output;
    }

    protected function getOCTBlogArticles() {
        $output = '';
        $articles = $this->model_extension_feed_sitemap_multi->getOCTBlogArticles();
        foreach ($articles as $article) {
            if ($this->static) $output = NULL;
            $output .= $this->getLinkItem('oct_blog_article', 'octemplates/blog/oct_blogarticle', 'blogarticle_id=' . $article['blogarticle_id'], date('Y-m-d\TH:i:sP', strtotime($article['date_modified'])));
            if ($this->static) {
                if ($this->separate) {
                    file_put_contents($this->filepath_octblog, $output, FILE_APPEND | LOCK_EX);
                } else {
                    file_put_contents($this->filepath, $output, FILE_APPEND | LOCK_EX);
                }
            }
        }
        if (!$this->static) return $output;
    }

    protected function getOCTBlogCategories() {
        $output = '';
        $results = $this->model_extension_feed_sitemap_multi->getOCTBlogCategories();
        foreach ($results as $result) {
            if ($this->static) $output = NULL;
            $output .= $this->getLinkItem('oct_blog_category', 'octemplates/blog/oct_blogcategory', 'blog_path=' . $result['blogcategory_id'], date('Y-m-d\TH:i:sP', strtotime($result['date_modified'])));
            if ($this->static) {
                if ($this->separate) {
                    file_put_contents($this->filepath_octblog, $output, FILE_APPEND | LOCK_EX);
                } else {
                    file_put_contents($this->filepath, $output, FILE_APPEND | LOCK_EX);
                }
            }
        }

        if (!$this->static) return $output;
    }

    public function getCleanBlog() {
        $output = '';
        if ($this->model_extension_feed_sitemap_multi->hasCleanBlog()) {
            $output .= $this->getLinkItem('clean_blog', 'blog/home', '');

            $this->load->model('blog/blog_category');
            $categories_1 = $this->model_blog_blog_category->getBlogCategories(0);
            foreach ($categories_1 as $category_1) {
                $output .= $this->getLinkItem('clean_blog', 'blog/category', 'blogpath=' . $category_1['blog_category_id']);
                $categories_2 = $this->model_blog_blog_category->getBlogCategories($category_1['blog_category_id']);
                foreach ($categories_2 as $category_2) {
                    $categories_2 = $this->model_blog_blog_category->getBlogCategories(0);
                    $output .= $this->getLinkItem('clean_blog', 'blog/category', 'blogpath=' . $category_1['blog_category_id'] . '_' . $category_2['blog_category_id']);
                }
            }

            $this->load->model('blog/blog');
            $blogs = $this->model_blog_blog->getBlogs(array('filter_tag'=>''));
            foreach ($blogs as $blog) {
                $output .= $this->getLinkItem('clean_blog', 'blog/blog', 'blog_id=' . $blog['blog_id']);
            }

            if ($this->static) {
                if ($this->separate) {
                    file_put_contents($this->filepath_clean_blog, $output, FILE_APPEND | LOCK_EX);
                } else {
                    file_put_contents($this->filepath, $output, FILE_APPEND | LOCK_EX);
                }
            }
        }
        if (!$this->static) return $output;
    }

    public function getKeywordsPages() {
        $output = '';
        if ($this->config->get($this->prefix_module.'keywords_status')) {
            $this->load->model('extension/module/keywords');
            $this->load->model('catalog/category');
            $keywords = $this->model_extension_module_keywords->getKeywords();
            foreach ($keywords as $keyword) {
                if ($this->static) $output = NULL;
                $add_path = '';
                if ($keyword['path_id']){
                    $path = $keyword['path_id'];
                    $flag = false;
                    $tid = $keyword['path_id'];
                    while (!$flag) {
                        $c = $this->model_catalog_category->getCategory($tid);
                        if ($c['parent_id']) {
                            $path = $c['parent_id']."_".$path;
                            $tid = $c['parent_id'];
                        } else {
                            $flag = true;
                        }
                    }
                    $add_path = 'path='.$path.'&';
                }
                $output .= $this->getLinkItem('keywords', 'product/keywords', $add_path . 'keyword_id=' . $keyword['keyword_id']);
                if ($this->static) {
                    if ($this->separate) {
                        file_put_contents($this->filepath_keywords, $output, FILE_APPEND | LOCK_EX);
                    } else {
                        file_put_contents($this->filepath, $output, FILE_APPEND | LOCK_EX);
                    }
                }
            }
        }
        if (!$this->static) return $output;
    }

    public function getGalleria() {
        $output = '';
        if ($this->config->get($this->prefix_module.'galleria_status')) {
            $galleria_page_status = $this->config->get($this->prefix_module.'galleria_page_status');
            if ($galleria_page_status) {
                $output .= $this->getLinkItem('galleria', 'extension/module/galleria', '');
            }

            $this->load->model('extension/module/galleria');
            $galleries = $this->model_extension_module_galleria->getGalleries();
            foreach ($galleries as $gallery) {
                $output .= $this->getLinkItem('galleria', 'extension/module/galleria/info', ($gallery['inpage']&&$galleria_page_status ? 'galleria_path=1&galleria_id=' . $gallery['galleria_id'] : 'galleria_id=' . $gallery['galleria_id']));
            }

            if ($this->static) {
                if ($this->separate) {
                    file_put_contents($this->filepath_galleria, $output, FILE_APPEND | LOCK_EX);
                } else {
                    file_put_contents($this->filepath, $output, FILE_APPEND | LOCK_EX);
                }
            }
        }
        if (!$this->static) return $output;
    }

    protected function getLinkItem($type, $route, $params = '', $lastmod = false, $image = false) {
        $items = '';
        foreach ($this->languages as $lang_id=>$lang_code) {
            $this->setCurrentLanguage($lang_id, $lang_code);
            $items .= '<url>';
            $items .= '<loc>' . $this->getLink($lang_id, $lang_code, $this->url->link($route, $params, true)) . '</loc>';
            if ($this->ml) {
                foreach ($this->languages as $id=>$code) {
                    $this->setCurrentLanguage($id, $code);
                    if (!empty($this->hreflang[$id])) {
                        $items .= '<xhtml:link rel="alternate" hreflang="'.$this->hreflang[$id].'" href="'.$this->getLink($id, $code, $this->url->link($route, $params, true)).'"/>';
                    }
                    if ($code == $this->config->get($this->prefix.'sitemap_multi_xdefault')) {
                        $items .= '<xhtml:link rel="alternate" hreflang="x-default" href="'.$this->getLink($id, $code, $this->url->link($route, $params, true)).'"/>';
                    }
                }
            }
            if ($this->changefreq) {
                $items .= '<changefreq>'.$this->config->get($this->prefix.'sitemap_multi_changefreq_'.$type).'</changefreq>';
            }
            if ($this->priority) {
                $items .= '<priority>'.$this->config->get($this->prefix.'sitemap_multi_priority_'.$type).'</priority>';
            }
            if ($this->lastmod) {
                $items .= '<lastmod>' . ($lastmod ? $lastmod : $this->default_time) . '</lastmod>';
            }
            if ($image && $this->config->get($this->prefix.'sitemap_multi_image')) {
                $items .= '<image:image>';
                $items .= '<image:loc>' . $this->domain . 'image/' . $image . '</image:loc>';
                $items .= '</image:image>';
            }
            $items .= '</url>';
        }
        return $items;
    }

    protected function getCustomLinks() {
        $items = '';
        $custom_links = $this->config->get($this->prefix.'sitemap_multi_custom');
        if (!empty($custom_links)) {
            foreach ($custom_links as $custom) {
                foreach ($custom['link'] as $link) {
                    $items .= '<url>';
                    $items .= '<loc>'.$link.'</loc>';
                    if ($this->ml) {
                        foreach ($this->languages as $id=>$code) {
                            if (!empty($custom['link'][$code])) {
                                if (!empty($this->hreflang[$id])) {
                                    $items .= '<xhtml:link rel="alternate" hreflang="'.$this->hreflang[$id].'" href="'.$custom['link'][$code].'"/>';
                                }
                                if ($code == $this->config->get($this->prefix.'sitemap_multi_xdefault')) {
                                    $items .= '<xhtml:link rel="alternate" hreflang="x-default" href="'.$custom['link'][$code].'"/>';
                                }
                            }
                        }
                    }
                    if ($this->changefreq) {
                        $items .= '<changefreq>'.$custom['changefreq'].'</changefreq>';
                    }
                    if ($this->priority) {
                        $items .= '<priority>'.$custom['priority'].'</priority>';
                    }
                    if ($this->lastmod) {
                        $items .= '<lastmod>' . $this->default_time . '</lastmod>';
                    }
                    $items .= '</url>';
                }
            }
        }
        return $items;
    }

    protected function setCurrentLanguage($lang_id, $lang_code) {
        if ($this->ml) {
            $this->config->set('config_language_id', $lang_id);
            $this->session->data['language'] = $lang_code;
        }
    }

    protected function getLink($lang_id, $lang_code, $link) {
        if ($this->prefix_type == 'other') {
            if (!empty($this->prefix_code[$lang_id])) {
                $link = str_replace($this->domain, $this->domain . $this->prefix_code[$lang_id] . '/', $link);
            }
        }
        return $link;
    }

}