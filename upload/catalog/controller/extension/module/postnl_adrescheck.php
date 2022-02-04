<?php
/**
 * Copyright (c) De Webmakers
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */

require_once(DIR_SYSTEM . 'library/postnl_adrescheck/postnl_adrescheck.php');

class ControllerExtensionModulePostnlAdrescheck extends Controller {

    public function eventHeaderBefore(&$route, &$data) {

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
    }

    public function eventHeaderAfter(&$route, &$data, &$output) {
        $html = $this->load->view('extension/module/postnl_adrescheck', $data);
        $html .= '</head>';

        $output = str_replace('</head>', $html, $output);
    }

    public function check() {
        $this->response->addHeader('Content-Type: application/json');
        $return = ['success' => false];

        if(!isset($this->request->post['zipcode']) || trim($this->request->post['zipcode']) == '') {
            $return['msg'] = 'Missing zipcode';
        }
        if(!isset($this->request->post['number']) || trim($this->request->post['number']) == '') {
            $return['msg'] = 'Missing number';
        }
        if(isset($return['msg'])) {
            return $this->response->setOutput(json_encode($return));
        }

        $requestData = [
            'PostalCode' => trim($this->request->post['zipcode']),
            'HouseNumber' => trim($this->request->post['number']),
            'Addition' => trim($this->request->post['addition']),
        ];

        $obj = new PostNL_Adrescheck();
        $post = array_merge(['PostalCode' => '', 'City' => '', 'Street' => '', 'HouseNumber' => '', 'Addition' => '', 'Country' => ''], $requestData);
        $result = $obj->checkAddress($post, $this->config->get('module_postnl_adrescheck_api_key'));
        if($result === false) $return['msg'] = 'Error in api: '. implode(', ', $obj->getErrors());
        else {
            $addresses = [];
            foreach($result as $addr) {
                $address = [
                    'street' => (isset($addr['Street']) ? $addr['Street'] : ''),
                    'number' => (isset($addr['HouseNumber']) ? $addr['HouseNumber'] : ''),
                    'addition' => (isset($addr['Addition']) ? $addr['Addition'] : ''),
                    'zipcode' => (isset($addr['PostalCode']) ? $addr['PostalCode'] : ''),
                    'city' => (isset($addr['City']) ? ucfirst(strtolower($addr['City'])) : ''),
                    'address1' => '',
                    'address2' => '',
                    'country' => ''
                ];
                $address['address1'] = implode(' ', [$address['street'],$address['number'],$address['addition']]);
                $addresses[] = $address;
            }
            $return = ['success' => true, 'result' => $addresses];
        }

        $this->response->setOutput(json_encode($return));
    }

    private function getStoreBaseUrl() {
        if ($this->config->get('config_ssl')) return HTTPS_SERVER;
        return HTTP_SERVER;
    }
}