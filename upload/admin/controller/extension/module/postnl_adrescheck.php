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

    private $error = array();

    public function index() {
        $data = $this->load->language('extension/module/postnl_adrescheck');

        $links = [
            '%link_products%' => 'https://www.postnl.nl/zakelijke-oplossingen/slimme-dataoplossingen/adrescheck/',
            '%link_api%' => 'https://mijn.postnl.nl/',
            '%link_extension%' => 'https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=43309',
            '%link_manual_docs%' => 'https://github.com/postnl/opencart-extension',
            '%link_terms%' => 'https://www.postnl.nl/algemene-voorwaarden/',
        ];
        foreach($data as $k => $v) {
            foreach($links as $f => $t) $data[$k] = str_replace($f, $t, $data[$k]);
        }

        $user_token = false;
        if(version_compare(VERSION, '3.0.0.0') >= 0) $user_token = 'user_token=' . $this->session->data['user_token'];
        elseif(version_compare(VERSION, '2.0.0.0') >= 0) $user_token = 'token=' . $this->session->data['token'];

        $data['error_warning'] = '';
        $data['success_message'] = '';

        $this->document->setTitle(strip_tags($this->language->get('heading_title')));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['module_postnl_adrescheck_status'] = 1;
            $this->model_setting_setting->editSetting('module_postnl_adrescheck', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/postnl_adrescheck', $user_token, true));
        }

        if(!empty($this->session->data['success'])) {
            $data['success_message'] = $this->session->data['success'];
            $this->session->data['success'] = '';
        }

        $this->load->model('extension/module/postnl_adrescheck');

        if ($this->config->get('config_maintenance')) {
            $data['error_warning'] = $this->language->get('error_maintenance_mode');
        }

        $data['redirect_uri'] = $this->url->link('extension/module/postnl_adrescheck', $user_token, true);

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $user_token, true),
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link((version_compare(VERSION, '3.0.0.0') >= 0 ? 'marketplace' : 'extension').'/extension', $user_token . '&type=module', true),
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/postnl_adrescheck', $user_token, true)
        );
        $data['action'] = $this->url->link('extension/module/postnl_adrescheck', $user_token, true);
        $data['cancel'] = $this->url->link('marketplace/extension', $user_token . '&type=module', true);

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        }

        $setting = $this->model_setting_setting->getSetting('module_postnl_adrescheck');
        if(!isset($this->request->post['module_postnl_adrescheck_type']) || $this->request->post['module_postnl_adrescheck_type'] == 'zipcode_housenumber') {
            $this->request->post['module_postnl_adrescheck_zipcode_housenumber'] = 'on';
            $this->request->post['module_postnl_adrescheck_autocomplete'] = '';
        } else {
            $this->request->post['module_postnl_adrescheck_zipcode_housenumber'] = '';
            $this->request->post['module_postnl_adrescheck_autocomplete'] = 'on';
        }
        foreach(['status','api_key','zipcode_housenumber','manual_input','autocomplete','showlabel','timeout','debug'] as $key) {
            $key = 'module_postnl_adrescheck_'.$key;
            $data[$key] = (isset($this->request->post[$key]) ? $this->request->post[$key] : (isset($setting[$key]) ? $setting[$key] : ''));
        }

        $plugin_version = $this->model_extension_module_postnl_adrescheck->getPluginVersion();
        $data['text_plugin_version'] = sprintf($this->language->get('text_plugin_version'), $plugin_version);
        $data['label_img'] = $this->getStoreBaseUrl().'../catalog/view/theme/default/image/postnl_adrescheck_label.png';

        // Check if we want all other settings when we have a valid key
        if(isset($setting['api_key']) && !empty($setting['api_key'])) {
            // Question: do we want to check the key on each page request?
            //$obj = new PostNL_Adrescheck(); if($obj->checkKey($this->request->post['module_postnl_adrescheck_api_key'])) { }

        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/postnl_adrescheck', $data));
    }

    public function install() {
        if (!$this->user->hasPermission('modify', 'extension/extension/module')) {
            return;
        }

        $this->load->model('extension/module/postnl_adrescheck');
        $this->model_extension_module_postnl_adrescheck->install();

        if(version_compare(VERSION, '3.0.0.0') >= 0) {
            $this->load->model('setting/event');
            $mdl = $this->model_setting_event;
        } else {
            $this->load->model('extension/event');
            $mdl = $this->model_extension_event;
        }
        $mdl->addEvent('postnl_adrescheck', 'admin/view/common/dashboard/after', 'extension/module/postnl_adrescheck/eventDashboardAfter');
        $mdl->addEvent('postnl_adrescheck', 'catalog/view/common/header/before', 'extension/module/postnl_adrescheck/eventHeaderBefore');
        if(version_compare(VERSION, '3.0.0.0') >= 0) {
            $mdl->addEvent('postnl_adrescheck', 'catalog/view/common/header/after', 'extension/module/postnl_adrescheck/eventHeaderAfter');
        } else {
            $mdl->addEvent('postnl_adrescheck', 'catalog/view/*/common/header/after', 'extension/module/postnl_adrescheck/eventHeaderAfter');
        }

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_postnl_adrescheck', array('module_postnl_adrescheck_status' => 1));
    }

    public function uninstall() {
        if (!$this->user->hasPermission('modify', 'extension/extension/module')) {
            return;
        }

        $this->load->model('extension/module/postnl_adrescheck');
        $this->model_extension_module_postnl_adrescheck->uninstall();

        if(version_compare(VERSION, '3.0.0.0') >= 0) {
            $this->load->model('setting/event');
            $this->model_setting_event->deleteEvent('postnl_adrescheck');
        } else {
            $this->load->model('extension/event');
            $this->model_extension_event->deleteEvent('postnl_adrescheck');
        }

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_postnl_adrescheck', array('module_postnl_adrescheck_status' => 0));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/postnl_adrescheck')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        // Validate key:
        $obj = new PostNL_Adrescheck();
        if(!$obj->checkKey($this->request->post['module_postnl_adrescheck_api_key'])) {
            $this->error['warning'] = $this->language->get('error_api_key') .': '. implode(', ', $obj->getErrors());
        }
        return !$this->error;
    }

    public function updateSettings() {
        $json = array();
        $this->load->model('extension/module/postnl_adrescheck');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_extension_module_postnl_adrescheck->updateSettings($this->request->post);
            $json['success'] = true;
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function deleteSettings() {
        $json = array();
        $this->load->model('setting/setting');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->deleteSetting('postnl_adrescheck');
            $json['success'] = true;
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function eventDashboardAfter(&$route, &$data, &$output) {
        $this->load->language('extension/module/postnl_adrescheck');
        $this->load->model('extension/module/postnl_adrescheck');
        if ($this->model_extension_module_postnl_adrescheck->isNewExtensionAvailable()) {
            $search = '<div class="container-fluid">';
            $pos = strrpos($output, $search);
            if ($pos !== false) {
                $html =  '<div class="container-fluid">';
                $html .= '  <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> ' . $this->language->get('text_upgrade_message');
                $html .= '    <button type="button" class="close" data-dismiss="alert">&times;</button>';
                $html .= '  </div>';
                $output = substr_replace($output, $html, $pos, strlen($search));
            }
        }
    }

    private function getStoreBaseUrl() {
        if ($this->config->get('config_ssl')) return HTTPS_SERVER;
        return HTTP_SERVER;
    }

}
