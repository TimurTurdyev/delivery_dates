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

        $now = time();
        $today = mktime(0, 0, 0, date('n', $now), date('j', $now), date('Y', $now));
        $start = $this->model_extension_module_delivery_dates->getStartDay($now);

        $data['title'] = $this->config->get('delivery_dates_title');
        $data['methods'] = array();

        foreach ($rule['methods'] as $method) {
            $term = $this->model_extension_module_delivery_dates->getTerm($start, $method['days']);

            $data['methods'][] = array(
                'name' => $method['name'],
                'date' => $term ? $this->termText($term[0], $term[1], $today) : '',
                'cost' => $method['cost'],
            );
        }

        return $this->load->view('extension/module/delivery_dates', $data);
    }

    // Текст наличия из правила, если он задан, иначе тот, что уже посчитала карточка.
    public function stock($args)
    {
        if (!$this->config->get('delivery_dates_status') || empty($args['product']['product_id'])) {
            return $args['stock'];
        }

        $this->load->model('extension/module/delivery_dates');

        $rule = $this->model_extension_module_delivery_dates->getRule($args['product']['product_id'], $args['product']['quantity']);

        return $rule['stock'] !== '' ? $rule['stock'] : $args['stock'];
    }

    private function termText($from, $to, $today)
    {
        $tomorrow = mktime(0, 0, 0, date('n', $today), date('j', $today) + 1, date('Y', $today));

        if ($from == $to) {
            return $this->dayText($from, $today, $tomorrow);
        }

        if ($from == $today && $to == $tomorrow) {
            return sprintf($this->language->get('text_or'), $this->language->get('text_today'), $this->language->get('text_tomorrow'));
        }

        if (date('Y-m', $from) == date('Y-m', $to)) {
            return sprintf($this->language->get('text_days'), date('j', $from), date('j', $to), $this->month($to));
        }

        return sprintf($this->language->get('text_range'), $this->dateText($from), $this->dateText($to));
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
