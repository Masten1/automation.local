<?php
/**
 * 
 * Author: Andrey
 * Date: 04.11.13
 * Time: 16:19 
 */

class Emp_TourHotel extends fvRoot {

    function __toString(){
        return (string)$this->hotel->getName();
    }

    function asAdorned() {
        return new Component_View($this);
    }

    function render($type) {
        return new Component_View($this, $type);
    }

    function getTourists() {
        return Emp_Tourist::getManager()
            ->select()
            ->join("hotels")
            ->leftJoin("phones")
            ->loadRelation( "phones" )
            ->where("hotels.thId = ?", $this->getPk() )
            ->fetchAll();
    }

    function getBusy() {
        return Emp_Tourist::getManager()
            ->select()
            ->join("hotels")
            ->where("hotels.thId = ?", $this->getPk())
            ->getCount();
    }

    function getTouristCount() {
        return Emp_Tourist::getManager()
            ->select()
            ->join("hotels")
            ->leftJoin("phones")
            ->loadRelation( "phones" )
            ->where("hotels.thId = ?", $this->getPk())
            ->getCount();
    }

    function getCountByTransport($id)
    {
        return Emp_Tourist::getManager()
            ->select()
            ->join("hotels")
            ->join("transports")
            ->where(array("hotels.thId" => $this->getPk(), "transports.ttId" => $id))
            ->getCount();
    }

    function getOrderPrices() {
        $stmt =  fvSite::$pdo->query(
            "SELECT `r`.`price`, COUNT(`r`.`price`) as `count` FROM `empTourHotels` `t`
            JOIN `empOrderHotels` `o` ON `o`.`thId` = `t`.`id`
            JOIN `empHotelRooms` `r` ON `o`.`roomId` = `r`.id
            WHERE `t`.id = {$this->getPk()}
            GROUP BY `r`.`price`");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} 