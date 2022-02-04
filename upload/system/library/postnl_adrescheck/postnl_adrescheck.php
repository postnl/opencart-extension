<?php
/**
 * Copyright (c) De Webmakers
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */

// Checkout Postalcode Check
// https://api.postnl.nl/shipment/checkout/v1/postalcodecheck
// https://developer.postnl.nl/browse-apis/checkout/checkout-api/

// Adrescheck Nationaal
// https://api.postnl.nl/address/national/v1/validate
// https://developer.postnl.nl/apis/adrescheck-nationaal/documentation

// Adrescheck Internationaal V3
// https://api.postnl.nl/address/international/v3/validate
// https://developer.postnl.nl/


class PostNL_Adrescheck {

    private $_errors = array();
    private $_key = '';

    public function checkKey($key=false) {
        $this->setKey($key);

        if(!$result = $this->postJSON('https://api.postnl.nl/shipment/checkout/v1/postalcodecheck', [
            //AN: 'PostalCode' => '2132WT', 'City' => '', 'Street' => '', 'HouseNumber' => 42, 'Addition' => 'A', 'Country' => '',
            'postalcode' => '2132WT', 'housenumber' => 42, 'housenumberaddition' => 'A',
        ])) {
            return false;
        }
        if(isset($result['errors'])) {
            foreach($result['errors'] as $err) $this->_errors[] = $err['title'] .', '. $err['detail'];
            return false;
        }
        return true;
    }

    public function checkAddress($data=[],$key=false) {
        $this->setKey($key);

        $data = array_merge(['PostalCode' => '', 'City' => '', 'Street' => '', 'HouseNumber' => '', 'Addition' => '', 'Country' => ''], $data);
        $data['PostalCode'] = strtoupper(str_replace(' ', '', $data['PostalCode']));
        $data['HouseNumber'] = trim($data['HouseNumber']);
        $data['Addition'] = trim($data['Addition']);
        if(strlen($data['PostalCode']) != 6 || !is_numeric(substr($data['PostalCode'],0,4))) {
            $this->_errors[] = 'Zipcode malformed';
            return false;
        }
        if(!is_numeric($data['HouseNumber']) || $data['HouseNumber'] < 1) {
            $this->_errors[] = 'HouseNumber malformed';
            return false;
        }

        // AN to CPC:
        $data = ['postalcode' => $data['PostalCode'], 'housenumber' => $data['HouseNumber'], 'housenumberaddition' => $data['Addition']];

        $result = $this->postJSON('https://api.postnl.nl/shipment/checkout/v1/postalcodecheck', $data);
        if($result === false) {
            return false;
        }
        if(isset($result['errors'])) {
            foreach($result['errors'] as $err) $this->_errors[] = $err['title'] .', '. $err['detail'];
            return false;
        }
        return $result;
    }

    public function setKey($key) {
        $this->_key = $key;
    }

    public function getErrors() {
        return (count($this->_errors) ? $this->_errors : false);
    }

    /**
     * The following are "fast" function to the actual request function
     */
    public function get($url=null, $data=null) {
        return $this->request('get', $url, $data);
    }
    public function post($url=null, $data=null) {
        return $this->request('post', $url, $data);
    }
    public function postJSON($url=null, $data=null) {
        return $this->request('postJSON', $url, $data);
    }

    private function request($type='get', $url=null, $data=null) {

        if(empty($url)) {
            return $this->error('Empty url');
        }

        $header = [
            'apikey: '. $this->_key,
            'x-api-key: '. $this->_key,
            'user-agent: platform OpenCart '.VERSION
        ];
        if($type == 'postJSON') {
            $header[] = 'Content-Type: application/json';
        }

        $options = array(
            CURLOPT_URL             => $url,
            CURLOPT_HTTPAUTH        => CURLAUTH_BASIC,
            CURLOPT_HTTPHEADER      => $header,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_TIMEOUT         => 1800,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_HEADERFUNCTION  => function($curl, $header) use (&$response_header) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) return $len; // ignore invalid headers
                $response_header[strtolower(trim($header[0]))][] = trim($header[1]);
                return $len;
            },
        );

        switch($type) {
            case 'get':
               $options[CURLOPT_CUSTOMREQUEST] = 'GET';
                if(!empty($data)) $options[CURLOPT_POSTFIELDS] = $data;
            break;
            case 'post':
                $options[CURLOPT_POST] = 1;
                $options[CURLOPT_POSTFIELDS] = $data;
            break;
            case 'postJSON':
                $options[CURLOPT_CUSTOMREQUEST] = 'POST';
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
            break;
        }

        // Make the actual curl-request
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response_output = curl_exec($ch);
        if (($curlError = curl_error($ch)) !== '') {
            return $this->error($curlError);
        }
        if (($errNo = (int)curl_errno($ch)) > 0) {
            return $this->error(sprintf('Curl error %d', $errNo));
        }
        $curlInfo = curl_getinfo($ch);
        curl_close($ch);
        unset($ch);

        // Response status checking
        /*if($curlInfo['http_code'] !== 200) {
            return $this->error('Invalid http code: '. $curlInfo['http_code']);
        }*/
        if(empty($response_output)) {
            if((int)$curlError === CURLE_OPERATION_TIMEDOUT) return $this->error('Operation timed out.');
            else return $this->error('No response received');
        }
        if(substr($response_output,0,9) == '<!DOCTYPE') {
            return $this->error('Er is een probleem met de bron');
        }

        // Validating response data
        $output = json_decode($response_output, true); // as array
        if(json_last_error() != JSON_ERROR_NONE) {
            $this->error('Invalid json: '.json_last_error_msg());
            $this->error($response_output);
            return false;
        }

        // Convert AN to CPC
        $result = [];
        foreach($output as $addr) {
            $result[] = [
                'Street' => (isset($addr['streetName']) ? $addr['streetName'] : ''),
                'HouseNumber' => (isset($addr['houseNumber']) ? $addr['houseNumber'] : ''),
                'Addition' => (isset($addr['houseNumberAddition']) ? $addr['houseNumberAddition'] : ''),
                'PostalCode' => (isset($addr['postalCode']) ? $addr['postalCode'] : ''),
                'City' => (isset($addr['city']) ? $addr['city'] : ''),
            ];
        }

        return $result;
    }

    private function error($msg) {
        $this->_errors[] = $msg;
        return false;
    }
}

// Easy debug
if(!function_exists('wtf')) {
    function wtf() {
        array_map(function($x) {
            if(is_object($x)||is_array($x)) echo '<pre>'.print_r($x,1).'</pre>'.PHP_EOL;
            elseif(function_exists('var_dump_safe')) { var_dump_safe($x); echo '<br>'.PHP_EOL; }
            else { var_dump($x); echo '<br>'.PHP_EOL; }
        }, func_get_args());
    }
}
