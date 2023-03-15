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
        $this->load->model('module/postnl_adrescheck');
        list($route, $data) = $this->model_module_postnl_adrescheck->eventHeaderBefore($route, $data);
    }

    public function eventHeaderAfter(&$route, &$data, &$output) {
        $this->load->model('module/postnl_adrescheck');
        list($route, $data, $output) = $this->model_module_postnl_adrescheck->eventHeaderAfter($route, $data, $output);
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
}
