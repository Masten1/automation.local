<?php
/**
 * @package FWW/NewName/Automation
 * @author Zakhar Korniev (kornevz@gmail.com)
 * Date: 28.12.13 16:37
 */

class TouristsPool implements ArrayAccess{
    /** @var TouristData[] */
    private $_data;

    /** @var Emp_Tour */
    private $_tour;


    public function __construct( Emp_Tour $tour ){
        $this->_tour = $tour;
        $this->_init();
    }

    public function offsetExists( $touristId ){
        return isset( $this->_data[$touristId] );
    }

    public function offsetGet( $touristId ){
        return $this->_data[$touristId];
    }

    public function offsetSet( $touristId, $tourist ){
        throw new Exception( "You cannot set!" );
    }

    public function offsetUnset( $offset ){
        throw new Exception( "You cannot unset!" );
    }

    private function _init(){
        /** @var Emp_Tourist[] $tourists */
        $tourists = $this->_tour->orders->tourists
            ->select()
            ->leftJoin('phones')
            ->loadRelation('phones')
            ->fetchAll();

        foreach ($tourists as $tId => $tourist) {
            $item = new TouristData();
            $item->tourist = $tourist;
            $this->_data[$tId] = $item;
        }

        $orderTransports = Emp_OrderTransport::getManager()
            ->select()
            ->join("transport")
            ->join("seat")
            ->loadRelation("transport")
            ->loadRelation("seat")
            ->andWhereIn("touristId", array_keys($tourists))
            ->andWhere(array("transport.tourId", $this->_tour->getPk()))
            ->fetchAll();

        foreach ($orderTransports as $transport) {
            $this->_data[$transport->touristId->get()]->orderTransport = $transport;
        }

        $orderHotels = Emp_OrderTransport::getManager()
            ->select()
            ->join("hotel")
            ->join("room")
            ->loadRelation("hotel")
            ->loadRelation("room")
            ->andWhereIn("touristId", array_keys($tourists))
            ->andWhere(array("hotel.tourId", $this->_tour->getPk()))
            ->fetchAll();

        foreach ($orderHotels as $hotel) {
            $this->_data[$hotel->touristId->get()]->orderHotel = $hotel;
        }











    }
}