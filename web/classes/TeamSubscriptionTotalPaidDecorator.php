<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 10-6-2015
 * Time: 21:31
 */

class TeamSubscriptionTotalPaidDecorator {
    private $teamSubscriptions;
    private $getTeamPrice;      // delegate to a function knowing the price per team

    function __construct($teamSubscriptions, $getTeamPrice)
    {
        $this->teamSubscriptions = $teamSubscriptions;
        $this->getTeamPrice = $getTeamPrice;
    }

    public function toArray() {
		$getTeamPrice = $this->getTeamPrice;
		$totalPrice = array_sum(array_map(function($subscription) use ($getTeamPrice) {
			return $subscription->getTotalPrice($getTeamPrice);
		}, $this->teamSubscriptions));
		$totalPaid = array_sum(array_map(function($subscription) {
			return $subscription->getStatus()->getPaid();
		}, $this->teamSubscriptions));
        return array(
            "Inschrijfnr" => "",
            "Club" => "",
            "Teams" => "<b>Totaal</b>",
            "Bedrag" => "<b>$totalPrice</b>",
            "Betaald" => "<b>$totalPaid</b>",
            "Betaald op" => "",
        );
    }
} 