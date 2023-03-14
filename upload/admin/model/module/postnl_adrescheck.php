<?php
/**
 * Copyright (c) De Webmakers
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */

class ModelModulePostnlAdrescheck extends Model {
    private $version = '1.0.2';

    public function install() {

    }

    public function uninstall() {

    }

    public function updateSettings($data = array()) {
        foreach ($data as $key => $value) {
            $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'postnl_adrescheck' AND `key` = '" . $this->db->escape($key) . "'");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET store_id = '0', `code` = 'postnl_adrescheck', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
        }
    }

    public function isNewExtensionAvailable() {
        $last_check_date = $this->config->get('postnl_adrescheck_last_upgrade_check_date');

        // Check for upgrades once a day only if extension is the latest version
        if ($last_check_date && strcmp(date("Y-m-d"), $last_check_date) == 0) {
            return false;
        }

        $latest_version = $this->getLatestVersion();
        if ($latest_version > $this->version) {
            return true;
        }

        $data = array('postnl_adrescheck_last_upgrade_check_date' => date("Y-m-d"));
        $this->updateSettings($data);
        return false;
    }

    public function getPluginVersion() {
        return $this->version;
    }

    private function getLatestVersion() {
        /*try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://api.github.com/repos/X/X/releases/latest'); // We don't know the url yet
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_USERAGENT, "curl");

            ob_start();
            curl_exec($curl);
            curl_close($curl);
            $lines = ob_get_contents();
            ob_end_clean();
            $json = json_decode($lines, true);

            if (!$json || !isset($json['tag_name'])) {
                return false;
            }

            $latest_version = $json['tag_name'];

            return (substr($latest_version, 0, 1) == 'v') ? substr($latest_version, 1) : false;
        } catch (Exception $e) {*/
            return false;
        //}
    }
}
