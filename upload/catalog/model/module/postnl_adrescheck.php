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

    public function getPluginVersion() {
        return $this->version;
    }

    private function getStoreBaseUrl() {
        if ($this->config->get('config_ssl')) return HTTPS_SERVER;
        return HTTP_SERVER;
    }

    public function eventHeaderBefore($route, $data) {

        $lang = $this->load->language('extension/module/postnl_adrescheck');

        $this->load->model('setting/setting');
        $setting = $this->model_setting_setting->getSetting('module_postnl_adrescheck');
        $data['module_postnl_adrescheck_status'] = (isset($setting['module_postnl_adrescheck_status']) ? $setting['module_postnl_adrescheck_status'] : '0');

        $json = [];
        foreach(['zipcode_housenumber','manual_input','autocomplete','showlabel','timeout','debug'] as $key) {
            $json[$key] = (isset($setting['module_postnl_adrescheck_'.$key]) ? $setting['module_postnl_adrescheck_'.$key] : '');
        }
        $json['endpoint'] = $this->getStoreBaseUrl().'index.php?route=extension/module/postnl_adrescheck/check';
        $json['theme'] = $this->config->get('config_theme');
        if($json['theme'] == 'theme_default') $json['theme'] = 'default';

        $json['label_img'] = $this->getStoreBaseUrl().'catalog/view/theme/default/image/postnl_adrescheck_label.png';
        foreach(['label_number','label_addition','label_loading','label_noresults','label_chosen','label_timeout','label_reset'] as $key) {
            $json[$key] = $this->language->get($key);
        }
        if($json['theme']=='default') $json['theme'] .= substr(VERSION,0,1);

        $json['elements'] = ['billing' => [], 'shipping' => []];
        switch($json['theme']) {
            case 'default2':
            case 'default3':
                foreach(['billing','shipping'] as $k) {
                    $k2 = ($k=='billing' ? 'payment' : 'shipping');
                    $json['elements'][$k] = [
                        'address1'  => '#input-'.$k2.'-address-1',
                        'address2'  => '#input-'.$k2.'-address-2',
                        'city'      => '#input-'.$k2.'-city',
                        'zipcode'   => '#input-'.$k2.'-postcode',
                        'country'   => '#input-'.$k2.'-country',
                        'zone'      => '#input-'.$k2.'-zone',
                    ];
                }
                $json['elements']['billing']['copy'] = 'input[name=shipping_address]';
            break;
            case 'journal3':
                foreach(['billing','shipping'] as $k) {
                    $k2 = ($k=='billing' ? 'payment' : 'shipping');
                    $json['elements'][$k] = [
                        'address1'  => '#input-'.$k2.'-address-1',
                        'address2'  => '#input-'.$k2.'-address-2',
                        'city'      => '#input-'.$k2.'-city',
                        'zipcode'   => '#input-'.$k2.'-postcode',
                        'country'   => '#input-'.$k2.'-country',
                        'zone'      => '#input-'.$k2.'-zone',
                    ];
                }
                $json['elements']['billing']['copy'] = '.checkout-same-address input[type=checkbox]';
            break;
        }

        $json['countries'] = [];
        $this->load->model('localisation/country');
        foreach($this->model_localisation_country->getCountries() as $row) {
            if($row['iso_code_3']=='NLD') $json['countries'][] = (int)$row['country_id'];
        }
        $json['zone_disabled_countries'] = $json['countries'];

        $data['module_postnl_adrescheck_json'] = json_encode($json);
        return [$route, $data];
    }

    public function eventHeaderAfter($route, $data, $output) {
        $html = $this->load->view('extension/module/postnl_adrescheck', $data);
        $html .= '</head>';

        $output = str_replace('</head>', $html, $output);
        return [$route, $data, $output];
    }
}
