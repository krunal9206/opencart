<?php
class ControllerPaymentEFTsecure extends Controller {
	public function index() {
		$this->load->language('payment/eftsecure');

		$data['text_credit_card'] = $this->language->get('text_credit_card');
		$data['text_start_date'] = $this->language->get('text_start_date');
		$data['text_wait'] = $this->language->get('text_wait');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['description'] = nl2br($this->config->get('eftsecure_description'));

		$data['button_confirm'] = $this->language->get('button_confirm');
		
		$data['success_url'] = $this->url->link('payment/eftsecure/success', '', 'SSL');
		$data['cancel_url'] = $this->url->link('checkout/checkout', '', 'SSL');
		
		$eftsecure_username = $this->config->get('eftsecure_username');
		$eftsecure_password = $this->config->get('eftsecure_password');
		
		$curl = curl_init('https://services.callpay.com/api/v1/token');
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_USERPWD, $eftsecure_username . ":" . $eftsecure_password);

		$response = curl_exec($curl);
		curl_close($curl);
		
		$response_data = json_decode($response);
		
		if(isset($response_data->token)){
			$data['token'] = $response_data->token;
			$data['organisation_id'] = $response_data->organisation_id;
			$this->session->data['eftsecure_token'] = $response_data->token;
		} else {
			$data['token'] = '';
			$data['organisation_id'] = '';
			$this->session->data['eftsecure_token'] = '';
		}
		
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$data['merchant_reference'] = 'order_id_'.$order_info['order_id'];
		$data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/eftsecure.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/eftsecure.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/eftsecure.tpl', $data);
		}
	}
	
	public function success() {
		if ($this->session->data['payment_method']['code'] == 'eftsecure') {
			$this->load->model('checkout/order');

			$gateway_reference = $this->request->get['gateway_reference'];
			
			$headers = array(
				'X-Token: '.$this->session->data['eftsecure_token'],
			);
			
			$curl = curl_init('https://services.callpay.com/api/v1/gateway-transaction/'.$gateway_reference);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

			$response = curl_exec($curl);
			curl_close($curl);
			
			$response_data = json_decode($response);
			
			if($response_data->id == $gateway_reference && $response_data->successful == 1) {
				$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('cod_order_status_id'));
				unset($this->session->data['eftsecure_token']);
				$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
			} else {
				$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
			}
		}
	}
}