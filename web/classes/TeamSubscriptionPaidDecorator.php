<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 10-6-2015
 * Time: 21:31
 */

class TeamSubscriptionPaidDecorator {
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
            "Bedrag" => $subscription->getTotalPrice($this->getTeamPrice),
            "Betaald" => $subscription->getStatus()->getPaid(),
            "Betaald op" => DateTime::createFromFormat('Y-m-d', $subscription->getStatus()->getTimestamp(), new DateTimeZone('Europe/Amsterdam'))->format('d-m-Y'),
        );
    }
} 