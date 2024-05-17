<?php

class ModelToolRedirect extends Model {
    public function addRedirect($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "redirect SET old_url = '" . $this->db->escape($data['old_url']) . "', new_url = '" . $this->db->escape($data['new_url']) . "', date_added = NOW()");

        $redirect_id = $this->db->getLastId();

        $this->cache->delete('redirect');

        return $redirect_id;
    }


    public function editRedirect($redirect_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "redirect SET old_url = '" . $this->db->escape($data['old_url']) . "', new_url = '" . $this->db->escape($data['new_url']) . "' WHERE redirect_id = '" . (int)$redirect_id . "'");

        $this->cache->delete('redirect');
    }

    public function deleteRedirect($redirect_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "redirect WHERE redirect_id = '" . (int)$redirect_id . "'");


        $this->cache->delete('redirect');
    }

    public function getRedirect($redirect_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "redirect WHERE redirect_id = '" . (int)$redirect_id . "'");

        return $query->row;
    }

    public function getRedirects() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "redirect");

        return $query->rows;
    }

    public function getTotalRedirect() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "redirect ");

        return $query->row['total'];
    }
}