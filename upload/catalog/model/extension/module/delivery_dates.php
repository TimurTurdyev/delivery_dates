<?php
class ModelExtensionModuleDeliveryDates extends Model
{
    // Правило статуса, который карточка показывает при нулевом остатке. Товар в наличии сроков не получает.
    public function getRule($product_id, $quantity)
    {
        $rules = (array)$this->config->get('delivery_dates_rules');
        $key = '';

        if ($quantity <= 0) {
            $query = $this->db->query("SELECT stock_status_id FROM `" . DB_PREFIX . "product` WHERE product_id = '" . (int)$product_id . "'");
            $key = $query->num_rows ? $query->row['stock_status_id'] : '';
        }

        return isset($rules[$key]) ? $rules[$key] + array('methods' => array()) : array('methods' => array());
    }

    // Срок "2" или "2-3" в днях от дня старта: array(первый день, последний день) или false без срока.
    public function getTerm($start, $days)
    {
        if (!preg_match('/^(\d+)(?:-(\d+))?$/', trim($days), $match)) {
            return false;
        }

        $from = $this->addDays($start, (int)$match[1]);
        $to = isset($match[2]) ? $this->addDays($start, (int)$match[2]) : $from;

        return array($from, $to);
    }

    // Полночь нужного дня через mktime, чтобы переход на летнее время не сдвигал даты.
    private function addDays($time, $days)
    {
        return mktime(0, 0, 0, date('n', $time), date('j', $time) + $days, date('Y', $time));
    }
}
