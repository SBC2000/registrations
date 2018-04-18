<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 4-6-2015
 * Time: 20:00
 */

class TeamSubscriptionAcceptRejectDecorator {

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
            "Club" => $subscription->getClub() . " (" . $subscription->getName() . " " . $subscription->getSurname() . ")",
            //"Club" => $subscription->getClub(),
            //"Ingeschreven op" => DateTime::createFromFormat('Y-m-d H:i:s', $subscription->getTimestamp(), new DateTimeZone('Europe/Amsterdam'))->format('d-m-Y'),
            //"Taal" => $subscription->getLanguage(),
            "Teams" => array_map(function($team) { return $team->toArray(); }, $subscription->getTeams()),
            "Bevestigen" => array_map(function($team) {
                $id = $team->getId();
                return array(
					"<input type='radio' checked='true' id='later-$id' name='$id-accept' value='later'><label for='later-$id'><i class='later fa fa-question-circle fa-lg'></i></label>" .
					"<input type='radio' id='accept-$id' name='$id-accept' value='accept'><label for='accept-$id'><i class='accept fa fa-check-circle fa-lg'></i></label>" .
                    "<input type='radio' id='reject-$id' name='$id-accept' value='reject'><label for='reject-$id'><i class='reject fa fa-times-circle fa-lg'></i></label>");
            }, $subscription->getTeams() ),
        );
    }
}