<?php

class ModelExtensionPaymentcoincorner extends Model
{
    public function install()
    {
        $this->load->model('setting/setting');

        // Default Setting Values
        $defaults = array();
        $defaults['payment_coincorner_order_status_id'] = 1;
        $defaults['payment_coincorner_pending_status_id'] = 1;
        $defaults['payment_coincorner_confirming_status_id'] = 1;
        $defaults['payment_coincorner_paid_status_id'] = 5;
        $defaults['payment_coincorner_invalid_status_id'] = 10;
        $defaults['payment_coincorner_expired_status_id'] = 14;
        $defaults['payment_coincorner_canceled_status_id'] = 7;
        $defaults['payment_coincorner_refunded_status_id'] = 11;
        $defaults['payment_coincorner_sort_order'] = 0;
        $defaults['payment_coincorner_user_id'] = 0;
        $defaults['payment_coincorner_invoice_currency'] = 'GBP';
        $defaults['payment_coincorner_settlement_currency'] = 'GBP';
        $this->model_setting_setting->editSetting('payment_coincorner', $defaults);
    }

    public function uninstall()
    {
    }
}
