<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 10-6-2015
 * Time: 21:33
 */

class TeamSubscriptionRejectedDecorator {
    private $teamSubscription;

    function __construct($teamSubscription)
    {
        $this->teamSubscription = $teamSubscription;
    }

    public function toArray() {
        /** @var TeamSubscription $subscription */
        $subscription = $this->teamSubscription;
        return array(
            "Inschrijfnr" => $subscription->getId(),
            "Contactpersoon" => $subscription->getName() . " " . $subscription->getSurname(),
            "Club" => $subscription->getClub(),
            "Teams" => array_map(function($team) { return $team->toArray(); }, $subscription->getTeams()),
        );
    }
} 