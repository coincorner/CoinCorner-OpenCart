<?php

class ModelPaymentcoincorner extends Model
{
    public function getMethod($address, $total)
    {
        $this->load->language('payment/coincorner');

        $method_data = array(
        'code'		 => 'coincorner',
        'title'		 => $this->language->get('text_title'),
        'terms'		 => '',
        'sort_order' => $this->config->get('payment_coincorner_sort_order')
      );

        return $method_data;
    }
}
