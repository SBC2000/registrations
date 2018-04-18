<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 3-6-2015
 * Time: 20:22
 */

class TeamSubscriptionPaymentDecorator {

    private $teamSubscription;
    private $getTeamPrice;      // delegate to a function knowing the price per team

    function __construct($teamSubscription, $getTeamPrice)
    {
        $this->teamSubscription = $teamSubscription;
        $this->getTeamPrice = $getTeamPrice;
    }

    public function toArray() {
        /** @var TeamSubscription $subscription */
        $subscription = $this->teamSubscription;
        return array(
            "Inschrijfnr" => $subscription->getId(),
            "Club" => $subscription->getClub() . " (" . $subscription->getName() . " " . $subscription->getSurname() . ")",
            //"Contactpersoon" => $subscription->getName() . " " . $subscription->getSurname(),
            //"Club" => $subscription->getClub(),
            "Teams" => array_map(function($team) { return $team->toArray(); }, $subscription->getTeams()),
            "Ingeschreven op" => DateTime::createFromFormat('Y-m-d H:i:s', $subscription->getTimestamp(), new DateTimeZone('Europe/Amsterdam'))->format('d-m-Y'),
            "Bedrag" => $subscription->getTotalPrice($this->getTeamPrice),
            "Betaald" => "<input name='{$subscription->getId()}' type='number' step='0.01' value='{$subscription->getStatus()->getPaid()}' />",
        );
    }
}