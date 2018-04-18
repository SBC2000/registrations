<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 25-5-2015
 * Time: 16:20
 */

class SubscriptionManager {
    private $year;

    private $subscriptions;
    private $registeredSubscriptions;
    private $confirmedSubscriptions;
    private $paidSubscriptions;
    private $rejectedSubscriptions;

    function __construct($year, $getTeamPrice)
    {
        $this->year = $year;
        $this->loadSubscriptions();
        $this->fillDerivedSubscriptions($getTeamPrice);
    }

    private function loadSubscriptions() {
        $query = "SELECT * FROM inschrijving WHERE jaar = '$this->year' ORDER BY inschrijfnummer";

        $result = pg_query($query) or die('Query failed: ' . pg_last_error());

        $this->subscriptions = array();
        while ($subscription = pg_fetch_assoc($result)) {
            $teamSubscription = new TeamSubscription(
                $subscription["inschrijfnummer"],
                $subscription["vereniging"],
                $subscription["voornaam"],
                $subscription["achternaam"],
                $subscription["email"],
                $subscription["telefoon"],
                $subscription["inschrijfdatum"],
                $subscription["taal"]
            );

            $teamSubscription->setStatus(
                new SubscriptionStatus(
                    $subscription["betaaldatum"],
                    $subscription["betaald"],
                    $subscription["factor"],
                    $subscription["korting"]
                )
            );

            $this->subscriptions[$subscription["id"]] = $teamSubscription;
        }
        pg_free_result($result);

        $query = "SELECT * FROM team";
        $result = pg_query($query) or die('Query failed: ' . pg_last_error());
        while ($team = pg_fetch_assoc($result)) {
            if ($this->subscriptions[$team['inschrijvingsid']]) {
                switch ($team["status"]) {
                    case "ingeschreven":
                        $status = Team::Registered;
                        break;
                    case "bevestigd":
                        $status = Team::Confirmed;
                        break;
                    case "afgewezen":
                        $status = Team::Rejected;
                        break;
                }

                $this->subscriptions[$team['inschrijvingsid']]->addTeam(
                    new Team(
                        $team["id"],
                        $team["teamnaam"],
                        $team["type"],
                        $team["niveau"] === "NVT" ? null : $team["niveau"],
                        $status
                    )
                );
            }
        }
        pg_free_result($result);
    }

    private function fillDerivedSubscriptions($getTeamPrice) {
        $this->registeredSubscriptions = $this->getFilteredSubscriptions(Team::Registered);
        $this->rejectedSubscriptions = $this->getFilteredSubscriptions(Team::Rejected);

        $confirmedSubscriptions = $this->getFilteredSubscriptions(Team::Confirmed);

        $this->confirmedSubscriptions = array_filter($confirmedSubscriptions, function($s) use ($getTeamPrice) { return $s->getStatus()->getPaid() < $s->getTotalPrice($getTeamPrice); });
        $this->paidSubscriptions = array_filter($confirmedSubscriptions, function($s) use ($getTeamPrice) { return $s->getStatus()->getPaid() >= $s->getTotalPrice($getTeamPrice); });
    }

    private function getFilteredSubscriptions($status) {
        return array_filter(array_map(function($s) use ($status) { return $s->getFilteredClone($status); }, $this->subscriptions),
                            function($s) { return count($s->getTeams()) > 0; });
    }

	public function handleConfirmations($array) {
		$ids = array_reduce($this->subscriptions, function($result, $subscription) {
			return array_merge($result, array_map(function($team) {
				return $team->getId();
			}, $subscription->getTeams()));
		}, []);

		foreach ($array as $key => $confirmation) {
			$id = substr($key, 0, -strlen("-accept"));
            if (in_array($id, $ids)) {
				if ($confirmation === "accept") {
					$this->accept($id);
				} else if ($confirmation === "reject") {
					$this->reject($id);
				}
            }
        }
	}

	private function accept($id) {
		$this->setStatus($id, 'bevestigd');
	}

	private function reject($id) {
		$this->setStatus($id, 'afgewezen');
	}

	private function setStatus($id, $status) {
		$query = "
		  UPDATE team
		  SET status='$status'
		  WHERE id=$id
        ";
        pg_query($query) or die('Query failed: ' . pg_last_error());
	}

    public function handlePayments($array) {
        $ids = array_map(function($subscription) { return $subscription->getId(); }, $this->subscriptions);
        foreach ($array as $id => $paid) {
            // we don't know what's in array exactly. better be safe than sorry and only update payment for existing ids
            if (in_array($id, $ids)) {
                $this->updatePaid($id, $paid);
            }
        }
    }

    private function updatePaid($id, $paid) {
        $now = date('Y-m-d');
        $query = "
            UPDATE inschrijving
            SET betaald=$paid, betaaldatum=$now
            WHERE inschrijfnummer=$id
        ";
        pg_query($query) or die('Query failed: ' . pg_last_error());
    }

    public function getConfirmedSubscriptions()
    {
        return $this->confirmedSubscriptions;
    }

    public function getPaidSubscriptions()
    {
        return $this->paidSubscriptions;
    }

    public function getRegisteredSubscriptions()
    {
        return $this->registeredSubscriptions;
    }

    public function getRejectedSubscriptions()
    {
        return $this->rejectedSubscriptions;
    }

    private function loadDummySubscriptions() {
        $subscription1 = new TeamSubscription(1, "SBC2000", "Vincent", "van der Weele", "vincentvanderweele@hotmail.com", "0647512383", "2015-05-20 20:00:00", "NL");
        $subscription2 = new TeamSubscription(2, "SBC2000", "Vincent", "van der Weele", "vincentvanderweele@hotmail.com", "0647512383", "2015-05-21 20:00:00", "NL");
        $subscription3 = new TeamSubscription(3, "SBC2000", "Vincent", "van der Weele", "vincentvanderweele@hotmail.com", "0647512383", "2015-05-22 20:00:00", "NL");
        $subscription4 = new TeamSubscription(4, "SBC2000", "Vincent", "van der Weele", "vincentvanderweele@hotmail.com", "0647512383", "2015-05-23 20:00:00", "NL");
        $subscription5 = new TeamSubscription(5, "SBC2000", "Vincent", "van der Weele", "vincentvanderweele@hotmail.com", "0647512383", "2015-05-24 20:00:00", "NL");
        $subscription6 = new TeamSubscription(6, "SBC2000", "Vincent", "van der Weele", "vincentvanderweele@hotmail.com", "0647512383", "2015-05-25 20:00:00", "NL");
        $subscription7 = new TeamSubscription(7, "SBC2000", "Vincent", "van der Weele", "vincentvanderweele@hotmail.com", "0647512383", "2015-05-26 20:00:00", "NL");
        $subscription8 = new TeamSubscription(8, "SBC2000", "Vincent", "van der Weele", "vincentvanderweele@hotmail.com", "0647512383", "2015-05-27 20:00:00", "NL");

        $subscription1->setStatus(new SubscriptionStatus());
        $subscription2->setStatus(new SubscriptionStatus());
        $subscription3->setStatus(new SubscriptionStatus());
        $subscription4->setStatus(new SubscriptionStatus());
        $subscription5->setStatus(new SubscriptionStatus());
        $subscription6->setStatus(new SubscriptionStatus("2015-05-27 15:00:00", 160));
        $subscription7->setStatus(new SubscriptionStatus("2015-05-27 16:00:00", 150));
        $subscription8->setStatus(new SubscriptionStatus());

        $subscription1->addTeam(new Team(1, "Heren 1", "Heren", "Bond 3", Team::Registered));
        $subscription1->addTeam(new Team(2, "Heren 2", "Heren", "Bond 2", Team::Registered));
        $subscription2->addTeam(new Team(3, "Heren 3", "Heren", "Regio 1", Team::Confirmed));
        $subscription3->addTeam(new Team(4, "Heren 4", "Heren", "Regio 2", Team::Confirmed));
        $subscription3->addTeam(new Team(5, "Heren 5", "Heren", "Regio 3/4", Team::Registered));
        $subscription3->addTeam(new Team(6, "Heren 6", "Heren", "Bond 3", Team::Rejected));
        $subscription4->addTeam(new Team(7, "Heren 7", "Heren", "Bond 2", Team::Registered));
        $subscription5->addTeam(new Team(8, "Heren 8", "Heren", "Regio 2", Team::Confirmed));
        $subscription5->addTeam(new Team(9, "Heren 9", "Heren", "Bond 3", Team::Rejected));
        $subscription6->addTeam(new Team(10, "Dames 1", "Dames", "Bond 2", Team::Registered));
        $subscription6->addTeam(new Team(11, "Dames 1", "Dames", "Regio 1", Team::Registered));
        $subscription7->addTeam(new Team(12, "Dames 1", "Dames", "Bond 3", Team::Registered));
        $subscription7->addTeam(new Team(13, "Jongens 1", "Jongens B", "", Team::Confirmed));
        $subscription7->addTeam(new Team(14, "Meisjes 1", "Meisjes B/C", "", Team::Rejected));
        $subscription8->addTeam(new Team(15, "Heren X", "Heren", "Bond 2", Team::Registered));
        $subscription8->addTeam(new Team(16, "Heren Y", "Heren", "Regio 3/4", Team::Confirmed));

        $this->subscriptions = array(
            $subscription1,
            $subscription2,
            $subscription3,
            $subscription4,
            $subscription5,
            $subscription6,
            $subscription7,
            $subscription8,
        );
    }
}
