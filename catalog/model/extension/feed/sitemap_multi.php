<?php
class ModelExtensionFeedSitemapMulti extends Model {

    private $prefix;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->prefix = (version_compare(VERSION, '3.0', '>=')) ? 'feed_' : '';
    }

    public function getProducts($data = array()) {
        $sql = "SELECT p.product_id, p.date_added, p.date_modified, p.image";
        $sql .= " FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        if ($this->config->get($this->prefix.'sitemap_multi_noindex_product') && $this->hasNoindex('product')) {
            $sql .= " AND p.noindex = '1'";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 9999999;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getCategories($parent_id = 0) {
        $sql = "SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1'";

        if ($this->config->get($this->prefix.'sitemap_multi_noindex_category') && $this->hasNoindex('category')) {
            $sql .= " AND c.noindex = '1'";
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getManufacturers($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        if ($this->config->get($this->prefix.'sitemap_multi_noindex_manufacturer') && $this->hasNoindex('manufacturer')) {
            $sql .= " AND m.noindex = '1'";
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getInformations() {
        $sql = "SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'";

        if ($this->config->get($this->prefix.'sitemap_multi_noindex_information') && $this->hasNoindex('information')) {
            $sql .= " AND i.noindex = '1'";
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getOcstoreBlogCategories() {
        $blog_categories = array();

        $query_table = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "blog_category'");
        if ($query_table->num_rows) {
            $sql = "SELECT * FROM " . DB_PREFIX . "blog_category c LEFT JOIN " . DB_PREFIX . "blog_category_to_store c2s ON (c.blog_category_id = c2s.blog_category_id) WHERE c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'";

            if ($this->config->get($this->prefix.'sitemap_multi_noindex_blog_category') && $this->hasNoindex('blog_category')) {
                $sql .= " AND c.noindex = '1'";
            }

            $query = $this->db->query($sql);

            foreach ($query->rows as $row) {
                $blog_categories[] = $row;
            }
        }

        return $blog_categories;
    }

    public function getOcstoreBlogArticles() {
        $article_data = array();

        $query_table = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "article'");
        if ($query_table->num_rows) {
            $sql = "SELECT * FROM " . DB_PREFIX . "article a LEFT JOIN " . DB_PREFIX . "article_to_store a2s ON (a.article_id = a2s.article_id)";

            $sql .= " WHERE a.status = '1' AND a2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

            if ($this->config->get($this->prefix.'sitemap_multi_noindex_blog_article') && $this->hasNoindex('article')) {
                $sql .= " AND a.noindex = '1'";
            }

            $query = $this->db->query($sql);

            foreach ($query->rows as $result) {
                $article_data[] = array(
                    'article_id'       => $result['article_id'],
                    'date_modified'    => $result['date_modified']
                );
            }
        }

        return $article_data;
    }

    public function getOCTBlogCategories() {
        $blog_categories = array();

        $query_table = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "oct_blogcategory'");
        if ($query_table->num_rows) {
            $sql = "SELECT * FROM " . DB_PREFIX . "oct_blogcategory c LEFT JOIN " . DB_PREFIX . "oct_blogcategory_to_store c2s ON (c.blogcategory_id = c2s.blogcategory_id) WHERE c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'";

            if ($this->config->get($this->prefix.'sitemap_multi_noindex_blog_category') && $this->hasNoindex('oct_blogcategory')) {
                $sql .= " AND c.noindex = '1'";
            }

            $query = $this->db->query($sql);

            foreach ($query->rows as $row) {
                $blog_categories[] = $row;
            }
        }

        return $blog_categories;
    }

    public function getOCTBlogArticles() {
        $article_data = array();

        $query_table = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "oct_blogarticle'");
        if ($query_table->num_rows) {
            $sql = "SELECT * FROM " . DB_PREFIX . "oct_blogarticle a LEFT JOIN " . DB_PREFIX . "oct_blogarticle_to_store a2s ON (a.blogarticle_id = a2s.blogarticle_id)";

            $sql .= " WHERE a.status = '1' AND a2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

            if ($this->config->get($this->prefix.'sitemap_multi_noindex_blog_article') && $this->hasNoindex('oct_blogarticle')) {
                $sql .= " AND a.noindex = '1'";
            }

            $query = $this->db->query($sql);

            foreach ($query->rows as $result) {
                $article_data[] = array(
                    'blogarticle_id'       => $result['blogarticle_id'],
                    'date_modified'       => $result['date_modified']
                );
            }
        }

        return $article_data;
    }

    public function hasCleanBlog() {
        $query_table = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "blog_related'");
        if ($query_table->num_rows) {
            return true;
        } else {
            return false;
        }
    }

    protected function hasNoindex($table) {
        $query_table = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . $table . "'");
        if ($query_table->num_rows) {
            $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . $table . "` LIKE 'noindex'");
            return $query->num_rows ? true : false;
        } else {
            return false;
        }
    }

}