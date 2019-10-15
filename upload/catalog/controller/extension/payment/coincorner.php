<?php

class ControllerExtensionPaymentcoincorner extends Controller
{
    public function index()
    {
        $this->load->language('extension/payment/coincorner');
        $this->load->model('checkout/order');

        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['action'] = $this->url->link('extension/payment/coincorner/checkout', '', true);

        return $this->load->view('extension/payment/coincorner', $data);
    }


    public function Generate_Sig($nonce)
    {
        $api_secret = strtolower($this->config->get('payment_coincorner_api_auth_private'));
        $account_id = strtolower($this->config->get('payment_coincorner_api_user_id'));
        $api_public = strtolower($this->config->get('payment_coincorner_api_auth_public'));

        return strtolower(hash_hmac('sha256', $nonce . $account_id . $api_public, $api_secret));
    }

    public function checkout()
    {
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/coincorner');
        $order_id = $this->session->data['order_id'];
        $order_info = $this->model_checkout_order->getOrder($order_id);
        $api_public = strtolower($this->config->get('payment_coincorner_api_auth_public'));
        $date  = date_create();
        $nonce = date_timestamp_get($date);
        
        $sig = $this->Generate_Sig($nonce);
        $amount      = floatval(number_format($order_info['total'], 8, '.', ''));
        $notify_url = $this->url->link('extension/payment/coincorner/callback');
        $redirect_url = $this->url->link('extension/payment/coincorner/success');
        $fail_URL   =  $this->url->link('extension/payment/coincorner/cancel');

        foreach ($this->cart->getProducts() as $product) {
            $description[] = $product['quantity'] . ' x ' . $product['name'];
        }

        $data  = array(
            'APIKey' => $api_public,
            'Signature' => strtoupper($sig),
            'InvoiceCurrency' => $this->config->get('payment_coincorner_invoice_currency'),
            'SettleCurrency' => $this->config->get('payment_coincorner_settlement_currency'),
            'Nonce' => $nonce,
            'InvoiceAmount' => $amount,
            'NotificationURL' => $notify_url,
            'ItemDescription' => join($description, ', '),
            'ItemCode' => '',
            'OrderId' => $order_id,
            'SuccessRedirectURL' => $redirect_url,
            'FailRedirectURL' => $fail_URL,
        );
    

        $url  = 'https://checkout.coincorner.com/api/CreateOrder';
        $curl = curl_init();
        $curl_options = array(CURLOPT_RETURNTRANSFER => 1,CURLOPT_URL  => $url);
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        array_merge($curl_options, array(CURLOPT_POST => 1));
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

        curl_setopt_array($curl, $curl_options);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = json_decode(curl_exec($curl), TRUE);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);


        if($http_status != 200) {

            $error_message = $response["Message"];

            $message = "Payment could not be started, CoinCorner returned an error: " . $error_message;
            $this->model_checkout_order->addOrderHistory($order_info['order_id'], $this->config->get('payment_coincorner_invalid_status_id'), $message);
            $this->response->redirect($this->url->link('checkout/cart', ''));

        }
        else {

            $invoice = explode("/Checkout/", $response);

            if (count($invoice) < 2) {
                $message = "Payment could not be started, CoinCorner returned an error.";
                $this->model_checkout_order->addOrderHistory($order_info['order_id'], $this->config->get('payment_coincorner_invalid_status_id'), $message);
                $this->response->redirect($this->url->link('checkout/cart', ''));
            } else {
                $this->model_checkout_order->addOrderHistory($order_info['order_id'], $this->config->get('payment_coincorner_order_status_id'), "Customer redirected to CoinCorner.com. InvoiceID : " . $invoice[1]);
                $this->response->redirect($response);
            }

        }
    }

    public function cancel()
    {
        $this->response->redirect($this->url->link('checkout/cart', ''));
    }

    public function success()
    {
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/coincorner');

        $this->response->redirect($this->url->link('checkout/success'));
    }

    public function callback()
    {
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/coincorner');
        $response = json_decode(file_get_contents('php://input'));

        $order_id = $response->OrderId;
        $order = $this->model_checkout_order->getOrder($order_id);
        $api_public = strtolower($this->config->get('payment_coincorner_api_auth_public'));

        try {
            if (!$order || !$order_id) {
                throw new Exception('Order #' . $order_id . ' does not exists');
            }
            if (strcmp($response->APIKey, strtolower($api_public)) !== 0) {
                throw new Exception('API Keys Mismatch' . $response->APIKey . " : " . $api_public);
            }
            
            
            $date  = date_create();
            $nonce = date_timestamp_get($date);
            
            $sig = $this->Generate_Sig($nonce);
            $data = array(
                'APIKey' => $api_public,
                'Signature' => $sig,
                'Nonce' => $nonce,
                'OrderId' => $order_id
            );

            $url = 'https://checkout.coincorner.com/api/CheckOrder';
            $curl = curl_init();
            $curl_options = array(CURLOPT_RETURNTRANSFER => 1,CURLOPT_URL  => $url);
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            array_merge($curl_options, array(CURLOPT_POST => 1));
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

            curl_setopt_array($curl, $curl_options);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = json_decode(curl_exec($curl), TRUE);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);


            if($http_status != 200) {
                $this->response->addHeader('HTTP/1.1 400 FAIL');
            }
            else {
                switch ($response["OrderStatusText"]) {
                    case 'Complete':
                        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_coincorner_paid_status_id'), 'Payment is confirmed on the network, and has been credited to the merchant. Purchased goods/services can be securely delivered to the buyer.');
                        break;
                    case 'Pending Confirmation':
                        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_coincorner_confirming_status_id'), 'Payment Authorising.');
                        break;
                    case 'Expired':
                        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_coincorner_expired_status_id'), 'Buyer did not pay within the required time and the invoice expired.');
                        break;
                    case 'Cancelled':
                        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_coincorner_canceled_status_id'), 'Buyer canceled the invoice');
                        break;
                    case 'Refunded':
                        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_coincorner_refunded_status_id'), 'Payment was refunded to the buyer.');
                        break;
                    case 'N/A':
                    default:
                        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_coincorner_invalid_status_id'), 'There was a problem with the order. Error:'.$response["Error"]);
                        break;
                }
                $this->response->addHeader('HTTP/1.1 200 OK');
            }
            
        } catch (Exception $e) {
            error_log('Caught exception: '. $e->getMessage());
            $this->response->addHeader('HTTP/1.1 400 FAIL');
        }
    }
}
