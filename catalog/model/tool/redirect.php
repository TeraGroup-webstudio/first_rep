<?php
// *	@copyright	OPENCART.PRO 2011 - 2016.
// *	@forum	http://forum.opencart.pro
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelToolRedirect extends Model {
    public function getRedirect($redirect) {
        $url = str_replace('&', '&amp;', $redirect) ;
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "redirect WHERE old_url = '" . $url . "'");

        return $query->row;
    }
 

}