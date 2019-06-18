<?php

// Admin page controller
class ControllerExtensionPaymentcoincorner extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/coincorner');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('localisation/order_status');
        $this->load->model('localisation/geo_zone');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_coincorner', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        $data['action']             = $this->url->link('extension/payment/coincorner', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel']             = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
        $data['order_statuses']     = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }   

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
        'text' => $this->language->get('text_home'),
        'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
    );
        $data['breadcrumbs'][] = array(
        'text' => $this->language->get('text_extension'),
        'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
    );
        $data['breadcrumbs'][] = array(
        'text' => $this->language->get('heading_title'),
        'href' => $this->url->link('extension/payment/coincorner', 'user_token=' . $this->session->data['user_token'], true)
    );

        $fields = array('payment_coincorner_status', 'payment_coincorner_api_user_id', 'payment_coincorner_api_auth_public', 'payment_coincorner_api_auth_private',
      'payment_coincorner_order_status_id', 'payment_coincorner_pending_status_id', 'payment_coincorner_confirming_status_id', 'payment_coincorner_paid_status_id',
      'payment_coincorner_invalid_status_id', 'payment_coincorner_expired_status_id', 'payment_coincorner_canceled_status_id', 'payment_coincorner_refunded_status_id',
      'payment_coincorner_invoice_currency', 'payment_coincorner_settlement_currency' );


        foreach ($fields as $field) {
            if (isset($this->request->post[$field])) {
                $data[$field] = $this->request->post[$field];
            } else {
                $data[$field] = $this->config->get($field);
            }
        }

        $data['payment_coincorner_sort_order'] = isset($this->request->post['payment_coincorner_sort_order']) ?  $this->request->post['payment_coincorner_sort_order'] :  $this->config->get('payment_coincorner_sort_order');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/coincorner', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/coincorner')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (empty($this->request->post['payment_coincorner_invoice_currency']) || !ctype_alpha($this->request->post['payment_coincorner_invoice_currency'])) {
            $this->error['warning'] = $this->language->get('error_invoice_currency');
        }

        if (empty($this->request->post['payment_coincorner_settlement_currency']) || !ctype_alpha($this->request->post['payment_coincorner_settlement_currency'])) {
            $this->error['warning'] = $this->language->get('error_settlement_currency');
        }

        return !$this->error;
    }

    public function install()
    {
        $this->load->model('extension/payment/coincorner');

        $this->model_extension_payment_coincorner->install();
    }

    public function uninstall()
    {
        $this->load->model('extension/payment/coincorner');

        $this->model_extension_payment_coincorner->uninstall();
    }
}
