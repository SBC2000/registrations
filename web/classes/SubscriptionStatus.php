<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 25-5-2015
 * Time: 14:04
 */

class SubscriptionStatus {
    private $paid;
    private $paidOn;
    private $factor;
    private $discount;

    function __construct($timestamp = null, $paid = 0, $factor = 1, $discount = 0)
    {
        $this->discount = $discount;
        $this->factor = $factor;
        $this->paid = $paid;
        $this->paidOn = $timestamp;
    }

    /**
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @return int
     */
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * @return int
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->paidOn;
    }

    public function toArray() {
        $result = array();
        foreach ($this as $key => $value)
        {
            $result[$key] = is_object($value) ? $value->toArray() : $value;
        }
        return $result;
    }
}
