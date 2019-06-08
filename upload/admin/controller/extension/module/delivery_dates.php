<?php
class ControllerExtensionModuleDeliveryDates extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/delivery_dates');
        $this->load->model('setting/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            $this->model_setting_setting->editSetting('delivery_dates', $this->postedSettings());

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->link('extension/module/delivery_dates'));
        }

        $keys = array(
            'heading_title', 'text_edit', 'text_enabled', 'text_disabled', 'text_no_methods',
            'tab_general', 'tab_rules',
            'column_stock_status', 'column_methods',
            'entry_status', 'entry_title', 'entry_method', 'entry_days',
            'help_title', 'help_days',
            'button_save', 'button_cancel', 'button_add', 'button_remove',
        );

        foreach ($keys as $key) {
            $data[$key] = $this->language->get($key);
        }

        foreach (array('warning', 'title') as $key) {
            $data['error_' . $key] = isset($this->error[$key]) ? $this->error[$key] : '';
        }

        $data['error_methods'] = isset($this->error['methods']) ? $this->error['methods'] : array();

        $data['success'] = '';

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data['breadcrumbs'] = array(
            array('text' => $this->language->get('text_home'), 'href' => $this->link('common/dashboard')),
            array('text' => $this->language->get('text_extension'), 'href' => $this->link('extension/extension', '&type=module')),
            array('text' => $this->language->get('heading_title'), 'href' => $this->link('extension/module/delivery_dates')),
        );

        $data['action'] = $this->link('extension/module/delivery_dates');
        $data['cancel'] = $this->link('extension/extension', '&type=module');

        $defaults = array('status' => 0, 'title' => '', 'rules' => array());

        foreach ($defaults as $name => $default) {
            $key = 'delivery_dates_' . $name;

            if ($this->request->server['REQUEST_METHOD'] == 'POST') {
                $data[$key] = isset($this->request->post[$key]) ? $this->request->post[$key] : (is_array($default) ? array() : '');
            } elseif ($this->config->has($key)) {
                $data[$key] = $this->config->get($key);
            } else {
                $data[$key] = $default;
            }
        }

        $data['rules'] = array();
        $data['method_index'] = 0;

        foreach ($this->stockStatuses() as $key => $name) {
            $rule = isset($data['delivery_dates_rules'][$key]) ? $data['delivery_dates_rules'][$key] : array();
            $methods = isset($rule['methods']) ? (array)$rule['methods'] : array();

            $data['rules'][$key] = array(
                'name'    => $name,
                'methods' => $methods,
            );

            $data['method_index'] = max($data['method_index'], $methods ? max(array_keys($methods)) + 1 : 0);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/delivery_dates', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/delivery_dates')) {
            $this->error['warning'] = $this->language->get('error_permission');

            return false;
        }

        $post = $this->request->post;

        if (isset($post['delivery_dates_title']) && utf8_strlen($post['delivery_dates_title']) > 64) {
            $this->error['title'] = $this->language->get('error_title');
        }

        $rules = isset($post['delivery_dates_rules']) ? (array)$post['delivery_dates_rules'] : array();

        foreach ($rules as $key => $rule) {
            foreach (isset($rule['methods']) ? (array)$rule['methods'] : array() as $index => $method) {
                $name = isset($method['name']) ? trim($method['name']) : '';
                $days = isset($method['days']) ? trim($method['days']) : '';

                if (utf8_strlen($name) < 1 || utf8_strlen($name) > 64) {
                    $this->error['methods'][$key][$index] = $this->language->get('error_method');
                } elseif ($days !== '' && !$this->isTerm($days)) {
                    $this->error['methods'][$key][$index] = $this->language->get('error_days');
                }
            }
        }

        if ($this->error) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    private function postedSettings()
    {
        $post = $this->request->post;
        $rules = array();

        foreach (array_keys($this->stockStatuses()) as $key) {
            $rule = isset($post['delivery_dates_rules'][$key]) ? $post['delivery_dates_rules'][$key] : array();
            $methods = array();

            foreach (isset($rule['methods']) ? (array)$rule['methods'] : array() as $method) {
                $methods[] = array(
                    'name' => trim($method['name']),
                    'days' => isset($method['days']) ? trim($method['days']) : '',
                );
            }

            $rules[$key] = array('methods' => $methods);
        }

        return array(
            'delivery_dates_status'   => isset($post['delivery_dates_status']) ? (int)$post['delivery_dates_status'] : 0,
            'delivery_dates_title'    => isset($post['delivery_dates_title']) ? trim($post['delivery_dates_title']) : '',
            'delivery_dates_rules'    => $rules,
        );
    }

    // Статусы, которые карточка показывает при нулевом остатке.
    private function stockStatuses()
    {
        $this->load->model('localisation/stock_status');

        $statuses = array();

        foreach ($this->model_localisation_stock_status->getStockStatuses() as $status) {
            $statuses[$status['stock_status_id']] = $status['name'];
        }

        return $statuses;
    }

    // Срок в рабочих днях: "2" или интервал "2-3".
    private function isTerm($days)
    {
        if (!preg_match('/^(\d{1,2})(?:-(\d{1,2}))?$/', $days, $match)) {
            return false;
        }

        return !isset($match[2]) || (int)$match[1] <= (int)$match[2];
    }

    private function link($route, $params = '')
    {
        return $this->url->link($route, 'token=' . $this->session->data['token'] . $params, true);
    }
}
