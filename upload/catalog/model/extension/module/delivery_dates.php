<?php
class ModelExtensionModuleDeliveryDates extends Model
{
    private $holidays;

    // Правило статуса, который карточка показывает при нулевом остатке. Товар в наличии сроков не получает.
    public function getRule($product_id, $quantity)
    {
        $rules = (array)$this->config->get('delivery_dates_rules');
        $key = '';

        if ($quantity <= 0) {
            $query = $this->db->query("SELECT stock_status_id FROM `" . DB_PREFIX . "product` WHERE product_id = '" . (int)$product_id . "'");
            $key = $query->num_rows ? $query->row['stock_status_id'] : '';
        }

        return isset($rules[$key]) ? $rules[$key] + array('stock' => '', 'methods' => array()) : array('stock' => '', 'methods' => array());
    }

    // День, с которого заказ уходит в работу: сегодня или ближайший рабочий, если сегодня выходной или прием уже закрыт.
    public function getStartDay($now)
    {
        $day = $this->addDays($now, 0);
        $cutoff = $this->config->get('delivery_dates_cutoff');

        if ($cutoff && date('H:i', $now) >= $cutoff) {
            $day = $this->addDays($day, 1);
        }

        return $this->workday($day);
    }

    // Срок "2" или "2-3" в рабочих днях от дня старта: array(первый день, последний день) или false без срока.
    public function getTerm($start, $days)
    {
        if (!preg_match('/^(\d+)(?:-(\d+))?$/', trim($days), $match)) {
            return false;
        }

        $from = $this->addWorkdays($start, (int)$match[1]);
        $to = isset($match[2]) ? $this->addWorkdays($start, (int)$match[2]) : $from;

        return array($from, $to);
    }

    public function isDayOff($day)
    {
        if ($this->holidays === null) {
            $this->holidays = array_flip(preg_split('/[\s,]+/', (string)$this->config->get('delivery_dates_holidays'), -1, PREG_SPLIT_NO_EMPTY));
        }

        return in_array(date('N', $day), (array)$this->config->get('delivery_dates_weekends'))
            || isset($this->holidays[date('d.m', $day)])
            || isset($this->holidays[date('d.m.Y', $day)]);
    }

    private function addWorkdays($day, $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $day = $this->workday($this->addDays($day, 1));
        }

        return $day;
    }

    // Год вперед - предел на случай, если выходными отмечено все подряд.
    private function workday($day)
    {
        for ($i = 0; $i < 366 && $this->isDayOff($day); $i++) {
            $day = $this->addDays($day, 1);
        }

        return $day;
    }

    // Полночь нужного дня через mktime, чтобы переход на летнее время не сдвигал даты.
    private function addDays($time, $days)
    {
        return mktime(0, 0, 0, date('n', $time), date('j', $time) + $days, date('Y', $time));
    }
}
