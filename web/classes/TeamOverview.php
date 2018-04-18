<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 4-6-2015
 * Time: 19:11
 */

class TeamOverview {
    private $subscriptionManager;
    private $getTeamPrice;

    function __construct($subscriptionManager, $getTeamPrice)
    {
        $this->subscriptionManager = $subscriptionManager;
        $this->getTeamPrice = $getTeamPrice;
    }

    /**
     * @return bool
     */
    public function hasRegisteredSubscriptions() {
        return count($this->subscriptionManager->getRegisteredSubscriptions()) > 0;
    }

    /**
     * @return bool
     */
    public function hasConfirmedSubscriptions() {
        return count($this->subscriptionManager->getConfirmedSubscriptions()) > 0;
    }

    /**
     * @return bool
     */
    public function hasPaidSubscriptions() {
        return count($this->subscriptionManager->getPaidSubscriptions()) > 0;
    }

    /**
     * @return bool
     */
    public function hasRejectedSubscriptions() {
        return count($this->subscriptionManager->getRejectedSubscriptions()) > 0;
    }

    /**
     * @return string
     */
    public function getRegisteredSubscriptions()
    {
        return TableCreator::createTable(
            array_map(function($subscription) {
                return new TeamSubscriptionAcceptRejectDecorator($subscription);
            }, $this->subscriptionManager->getRegisteredSubscriptions()));
    }

    /**
     * @return string
     */
    public function getConfirmedSubscriptions()
    {
        $getTeamPrice = $this->getTeamPrice;

        return TableCreator::createTable(
            array_map(function($subscription) use ($getTeamPrice) {
                return new TeamSubscriptionPaymentDecorator($subscription, $getTeamPrice);
            }, $this->subscriptionManager->getConfirmedSubscriptions()));
    }

    /**
     * @return string
     */
    public function getPaidSubscriptions($getTeamPrice)
    {
		$array = array_map(function($subscription) use ($getTeamPrice) {
			return new TeamSubscriptionPaidDecorator($subscription, $getTeamPrice);
        }, $this->subscriptionManager->getPaidSubscriptions());
		$array[] = new TeamSubscriptionTotalPaidDecorator($this->subscriptionManager->getPaidSubscriptions(), $getTeamPrice);
		
        return TableCreator::createTable($array);
    }

    /**
     * @return string
     */
    public function getRejectedSubscriptions()
    {
        return TableCreator::createTable(
            array_map(function($subscription) {
                return new TeamSubscriptionRejectedDecorator($subscription);
            }, $this->subscriptionManager->getRejectedSubscriptions()));
    }
}