<?php
class ControllerPaymentEFTsecure extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/eftsecure');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('eftsecure', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_authorization'] = $this->language->get('text_authorization');
		$data['text_sale'] = $this->language->get('text_sale');

		$data['entry_username'] = $this->language->get('entry_username');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_test'] = $this->language->get('entry_test');
		$data['entry_transaction'] = $this->language->get('entry_transaction');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['help_test'] = $this->language->get('help_test');
		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['signature'])) {
			$data['error_signature'] = $this->error['signature'];
		} else {
			$data['error_signature'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/eftsecure', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/eftsecure', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['eftsecure_username'])) {
			$data['eftsecure_username'] = $this->request->post['eftsecure_username'];
		} else {
			$data['eftsecure_username'] = $this->config->get('eftsecure_username');
		}

		if (isset($this->request->post['eftsecure_password'])) {
			$data['eftsecure_password'] = $this->request->post['eftsecure_password'];
		} else {
			$data['eftsecure_password'] = $this->config->get('eftsecure_password');
		}

		if (isset($this->request->post['eftsecure_description'])) {
			$data['eftsecure_description'] = $this->request->post['eftsecure_description'];
		} else {
			$data['eftsecure_description'] = $this->config->get('eftsecure_description');
		}

		if (isset($this->request->post['eftsecure_test'])) {
			$data['eftsecure_test'] = $this->request->post['eftsecure_test'];
		} else {
			$data['eftsecure_test'] = $this->config->get('eftsecure_test');
		}

		if (isset($this->request->post['eftsecure_method'])) {
			$data['eftsecure_transaction'] = $this->request->post['eftsecure_transaction'];
		} else {
			$data['eftsecure_transaction'] = $this->config->get('eftsecure_transaction');
		}

		if (isset($this->request->post['eftsecure_total'])) {
			$data['eftsecure_total'] = $this->request->post['eftsecure_total'];
		} else {
			$data['eftsecure_total'] = $this->config->get('eftsecure_total');
		}

		if (isset($this->request->post['eftsecure_order_status_id'])) {
			$data['eftsecure_order_status_id'] = $this->request->post['eftsecure_order_status_id'];
		} else {
			$data['eftsecure_order_status_id'] = $this->config->get('eftsecure_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['eftsecure_geo_zone_id'])) {
			$data['eftsecure_geo_zone_id'] = $this->request->post['eftsecure_geo_zone_id'];
		} else {
			$data['eftsecure_geo_zone_id'] = $this->config->get('eftsecure_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['eftsecure_status'])) {
			$data['eftsecure_status'] = $this->request->post['eftsecure_status'];
		} else {
			$data['eftsecure_status'] = $this->config->get('eftsecure_status');
		}

		if (isset($this->request->post['eftsecure_sort_order'])) {
			$data['eftsecure_sort_order'] = $this->request->post['eftsecure_sort_order'];
		} else {
			$data['eftsecure_sort_order'] = $this->config->get('eftsecure_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/eftsecure.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/eftsecure')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['eftsecure_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (!$this->request->post['eftsecure_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		return !$this->error;
	}
}