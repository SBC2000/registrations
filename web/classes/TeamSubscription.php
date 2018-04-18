<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 5-5-2015
 * Time: 14:24
 */

class TeamSubscription {
    private $id;
    private $club;
    private $name;
    private $surname;
    private $email;
    private $phone;
    private $timestamp;
    private $language;
    private $teams;
    private $status;

    function __construct($id, $club, $name, $surname, $email, $phone, $timestamp, $language, $teams = array(), $status = null)
    {
        $this->id = $id;
        $this->club = $club;
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->phone = $phone;
        $this->timestamp = $timestamp;
        $this->language = $language;
        $this->teams = $teams;
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function addTeam($team) {
        $this->teams[] = $team;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return Team[]
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @return SubscriptionStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getTotalPrice($getTeamPrice) {
        $price = 0;
        foreach ($this->teams as $team) {
            if ($team->getStatus() === Team::Confirmed) {
                $price += $getTeamPrice($team);
            }
        }
        return $price * $this->getStatus()->getFactor() - $this->getStatus()->getDiscount();
    }

    /**
     * @param $status
     * @return TeamSubscription
     */
    public function getFilteredClone($status) {
        $teams = array_filter($this->teams, function($t) use ($status) { return $t->getStatus() === $status; });

        return new TeamSubscription($this->id,
                                    $this->club,
                                    $this->name,
                                    $this->surname,
                                    $this->email,
                                    $this->phone,
                                    $this->timestamp,
                                    $this->language,
                                    $teams,
                                    $this->status);
    }

    public function toArray() {
        $result = array();
        foreach ($this as $key => $value)
        {
            if (is_object($value)) {
                $result = array_merge($result, $value->toArray());
            } else {
                $result[$key] = is_array($value) ? array_map(function($v) { return $v->toArray(); }, $value) : $value;
            }
        }
        return $result;
//        return array(
//            "Inschrijfnummer" => $this->id,
//            "Contactpersoon" => $this->name . " " . $this->surname,
//            "Club" => $this->club,
//            "Email" => $this->email,
//            "Telefoon" => $this->phone,
//            "Datum" => $this->timestamp,
//            "Teams" => array_map(function($team) { return $team->toArray(); }, $this->teams),
//        );
    }

    public function toSimpleArray() {
        return array(
            "Inschrijfnummer" => $this->id,
            "Voornaam" => $this->name,
            "Achternaam" => $this->surname,
            "Club" => $this->club,
            "Teams" => array_map(function($team) { return $team->toArray(); }, $this->teams),
        );
    }
} 