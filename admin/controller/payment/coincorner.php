<?php

// Admin page controller
class ControllerPaymentcoincorner extends Controller
{
    private $error = array();

    public function __construct($registry)
    {
        parent::__construct($registry);
  
        $this->load->language('payment/coincorner');
        $this->load->model('setting/setting');
        $this->load->model('localisation/order_status');
    }

    public function make_link($path)
    {
        return ($this->config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?route='.$path.'&token=' . $this->session->data['token'];
    }

    public function index()
    {
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('localisation/order_status');
        $this->load->model('localisation/geo_zone');
        $this->load->model('setting/extension');
        $model = $this->model_extension_extension;

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('coincorner', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->make_link('extension/payment'));
        }

        $data['action']             = $this->url->link('payment/coincorner', 'token=' . $this->session->data['token'], true);
        $data['cancel']             = $this->url->link('extension/payment', 'token=' . $this->session->data['token'] . '&type=payment', true);
        $data['order_statuses']     = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
        'text' => $this->language->get('text_home'),
        'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
    );
        $data['breadcrumbs'][] = array(
        'text' => $this->language->get('text_extension'),
        'href' => $this->url->link('marketplace/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
    );
        $data['breadcrumbs'][] = array(
        'text' => $this->language->get('heading_title'),
        'href' => $this->url->link('extension/payment/coincorner', 'token=' . $this->session->data['token'], true)
    );

        $fields = array('coincorner_status', 'payment_coincorner_api_user_id', 'payment_coincorner_api_auth_public', 'payment_coincorner_api_auth_private',
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

        $fieldzs = array('text_success', 'text_test_mode_on', 'text_test_mode_off', 'status_label', 'api_auth_token_label', 'entry_api_user_id_label',
        'api_auth_token_label', 'api_auth_token_private_label', 'entry_api_invoice_currency', 'entry_api_settlement_currency', 'entry_order_status', 'entry_pending_status',
        'entry_confirming_status', 'entry_paid_status', 'entry_invalid_status', 'entry_expired_status', 'entry_canceled_status', 'entry_refunded_status', 
        'entry_failed_status', 'heading_title', 'button_save', 'button_cancel'
    );
  
        foreach ($fieldzs as $field) {
            $data[$field] = $this->language->get($field);
        }
        
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $statuses = array('new', 'cancelled', 'expired', 'failed', 'completed', 'refunded');

        foreach ($data['order_statuses'] as $order_status) {
            foreach ($statuses as $status) {
                $cc_order_status = ($status == 'new' ? 'Pending' : ucfirst($status));
                $cc_order_status = ($cc_order_status == 'Completed' ? 'Complete' : $cc_order_status);
                $cc_order_status = ($cc_order_status == 'Cancelled' ? 'Canceled' : $cc_order_status);
      
                if ($cc_order_status == $order_status['name']) {
                    $data["coincorner_{$status}_order_status_id"] = (isset($this->request->post["coincorner{$status}_order_status_id"]) ? $this->request->post["coincorner{$status}_order_status_id"] : ($this->config->get("coincorner_{$status}_order_status_id") != '' ? $this->config->get("coincorner_{$status}_order_status_id") : $order_status['order_status_id']));
                }
            }
        }
      

        $this->template = 'payment/coincorner.tpl';

        $this->data = $data;
  
        $this->children = array(
          'common/header',
          'common/footer',
        );
  
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'payment/coincorner')) {
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
}
