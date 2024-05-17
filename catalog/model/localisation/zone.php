<?php
class ModelLocalisationZone extends Model {
	public function getZone($zone_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "' AND status = '1'");
        $query = $this->db->query("SELECT *, zd.name AS name FROM " . DB_PREFIX . "zone z LEFT JOIN " . DB_PREFIX . "zone_description zd ON(z.zone_id=zd.zone_id)WHERE z.zone_id = '" . (int)$zone_id . "' AND z.status = '1' AND zd.language_id='".(int)$this->config->get('config_language_id')."'");
		return $query->row;
	}

	public function getZonesByCountryId($country_id) {
		//$zone_data = $this->cache->get('zone.' . (int)$country_id);
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone z LEFT JOIN " . DB_PREFIX . "zone_description zd ON(z.zone_id=zd.zone_id) WHERE z.country_id = '" . (int)$country_id . "' AND z.status = '1' AND zd.language_id='".(int)$this->config->get('config_language_id')."' ORDER BY zd.name");
        $zone_data = $query->rows;

        /*if (!$zone_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "' AND status = '1' ORDER BY name");

			$zone_data = $query->rows;

			$this->cache->set('zone.' . (int)$country_id, $zone_data);
		}*/

		return $zone_data;
	}
}