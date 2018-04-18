<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 5-5-2015
 * Time: 14:46
 */

class Team {
    const
        Registered = 1,
        Confirmed = 2,
        Rejected = -1;

    private $id;
    private $teamname;
    private $teamtype;
    private $teamlevel;
    private $teamstatus;

    function __construct($id, $name, $type, $level, $status)
    {
        $this->id = $id;
        $this->teamname = $name;
        $this->teamtype = $type;
        $this->teamlevel = $level;
        $this->teamstatus = $status;
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
    public function getLevel()
    {
        return $this->teamlevel;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->teamname;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->teamtype;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->teamstatus;
    }

    public function toArray() {
        return array(
            "Teamnaam" => $this->teamname,
            "Type" => $this->teamtype . (is_null($this->teamlevel) ? '' : ' ' . $this->teamlevel ),
        );
    }
}