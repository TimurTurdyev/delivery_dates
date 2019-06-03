<?php
// Вызывается из карточки товара через OCMOD (install.xml), в схемы модуль не ставится.
class ControllerExtensionModuleDeliveryDates extends Controller
{
    public function index($product)
    {
        if (!$this->config->get('delivery_dates_status') || empty($product['product_id'])) {
            return '';
        }

        $this->load->model('extension/module/delivery_dates');
        $this->load->language('extension/module/delivery_dates');

        $rule = $this->model_extension_module_delivery_dates->getRule($product['product_id'], $product['quantity']);

        if (!$rule['methods']) {
            return '';
        }

        $today = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
        $tomorrow = mktime(0, 0, 0, date('n'), date('j') + 1, date('Y'));

        $data['title'] = $this->config->get('delivery_dates_title');
        $data['methods'] = array();

        foreach ($rule['methods'] as $method) {
            $date = $this->model_extension_module_delivery_dates->getDate($today, $method['days']);

            $data['methods'][] = array(
                'name' => $method['name'],
                'date' => $date ? $this->dayText($date, $today, $tomorrow) : '',
            );
        }

        return $this->load->view('extension/module/delivery_dates', $data);
    }

    private function dayText($day, $today, $tomorrow)
    {
        if ($day == $today) {
            return $this->language->get('text_today');
        }

        if ($day == $tomorrow) {
            return $this->language->get('text_tomorrow');
        }

        return $this->dateText($day);
    }

    private function dateText($day)
    {
        return sprintf($this->language->get('text_date'), date('j', $day), $this->month($day));
    }

    private function month($day)
    {
        return $this->language->get('text_month_' . date('n', $day));
    }
}
