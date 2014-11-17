<?php

class ToursModule extends fvModule{

    function __construct(){
        $this->moduleName = strtolower( substr( __CLASS__, 0, -6 ) );
        parent::__construct( fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.template" ),
                             fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.compile" ),
                             fvSite::$Layoult );
    }

    function showIndex(){
        $table = new Component_Table_Tour(Emp_Shipping::getManager());
        $this->table = $table
            ->setTitle("Отправка")
            ->setAllowNew(false)
            ->setModule($this->moduleName)
            //->addOrder("date desc")
            ->preload();

        return $this->__display( "index.tpl" );
    }

    function showEdit() {
        $tour =  Emp_Tour::getManager()
            ->select()
            ->leftJoin( "orders" )
            ->leftJoin( "transports")
            ->leftJoin( "hotels" )

            ->loadRelation("orders")
            ->loadRelation("orders.tourists")

            ->loadRelation("transports")
            ->loadRelation("transports.passengers")

            ->loadRelation("hotels")
            ->loadRelation("hotels.tourists")

            ->where(array("id" => $this->getRequest()->id))
            ->fetchAll();

        $tour = current( $tour );
        $comment = new Component_Comment_Tour($tour, "comments");

        $this->tourLog = Emp_OrderLog::getManager()
            ->select()
            ->join("order", "o")
            ->where("o.tourId = {$tour->getPk()}")
            ->orderBy("ctime desc")
            ->fetchAll();

        $this->comment = $comment->setAction("/tours/addcomment")->setForeignKey("tourId");

        $this->tour = $tour;
        return $this->__display("edit.tpl");
    }

    function showAddComment() {
        $data = $this->getRequest()->getRequestParameter("comment");
        $comment = new Emp_TourComment($data);
        $comment->save();
        return $comment->asAdorned()->render();
    }

    function showUpdateTransportTransport() {
        try {
            $data = $this->getRequest()->getRequestParameter("data");
            if(!$data["newttId"])
                throw new VerboseException();

            $orderTransport = Emp_OrderTransport::getManager()
                ->select()
                ->where(array("touristId" => $data['touristId'], "ttId" => $data['ttId']))
                ->fetchOne();
            if(!$orderTransport instanceof fvRoot)
                $orderTransport = new Emp_OrderTransport();

            $data['ttId'] = $data['newttId'];

            $orderTransport->hydrate($data);

            if(!$orderTransport->ttId->isChanged())
                return false;

            $orderTransport->save();

            $this->tour = $orderTransport->transport->tour;

            return json_encode( array(
                "element" => $orderTransport->tourist->render("noservice")->render(),
                "vehicleName" => (string)$orderTransport->transport->vehicle,
                "message" => "Транспорт обновлён",
                "type" => "add",
                "class" => "alert-success"
            ) );
        } catch (VerboseException $e ) {
            $orderTransport = Emp_OrderTransport::getManager()
                ->select()
                ->where(array( "ttId" => $data['ttId'], "touristId" => $data['touristId'] ))
                ->fetchOne();
            if(!$orderTransport instanceof fvRoot )
                return false;
            $this->tour = $orderTransport->transport->tour;
            $tourist = $orderTransport->tourist;
            $orderTransport->delete();
            return json_encode( array(
                "element" => $tourist->render("noservice")->render(),
                "message" => "Пассажир убран из транспорта",
                "type" => "remove",
                "class" => "alert-success"
            ) );
        } catch (Exception $e) {
            return json_encode( array(
                "message" => $e->getMessage(),
                "type" => "error",
                "class" => "alert-error"
            ) );

        }
    }

    function showUpdateHotel() {
        try {
            $data = $this->getRequest()->getRequestParameter("data");
            if(!$data["newthId"])
                throw new VerboseException();

            $orderHotel = Emp_OrderHotel::getManager()
                ->select()
                ->where(array("touristId" => $data['touristId'], "thId" => $data['thId']))
                ->fetchOne();
            if(!$orderHotel instanceof fvRoot)
                $orderHotel = new Emp_OrderHotel();
            $data['thId'] = $data['newthId'];

            $orderHotel->hydrate($data);

            if(!$orderHotel->thId->isChanged())
                return false;

            $orderHotel->save();

            $this->tour = $orderHotel->tourhotel->tour;

            return json_encode( array(
                "element" => $orderHotel->tourist->render("noservice")->render(),
                "hotelName" => (string)$orderHotel->tourhotel->hotel,
                "message" => "Отель обновлён",
                "type" => "add",
                "class" => "alert-success"
            ) );
        } catch (VerboseException $e ) {
            $orderHotel = Emp_OrderHotel::getManager()
                ->select()
                ->where(array( "thId" => $data['thId'], "touristId" => $data['touristId'] ))
                ->fetchOne();
            if(!$orderHotel instanceof fvRoot )
                return false;
            $this->tour = $orderHotel->tourhotel->tour;
            $tourist = $orderHotel->tourist;
            $orderHotel->delete();
            return json_encode( array(
                "element" => $tourist->render("noservice")->render(),
                "message" => "Жилец убран из отеля",
                "type" => "remove",
                "class" => "alert-success"
            ) );
        } catch (Exception $e) {
            return json_encode( array(
                "message" => $e->getMessage(),
                "type" => "error",
                "class" => "alert-error"
            ) );

        }
    }

    function showAddNewVehicle() {
        try {
            $item = Emp_Vehicle::getManager()->getByPk($this->getRequest()->id);
            $new = new Emp_TourTransport(
                array(
                    "vehicleId" => $item->getPk(),
                    "tourId" => $this->getRequest()->tourId)
            );
            $this->tour = Emp_Tour::getManager()->getByPk($this->getRequest()->tourId);
            $new->save();
            return json_encode( array("block" => $new->render("tour")->render(), "list" => $new->render("tourlist")->render() ) );
        } catch (Exception $e) {
            return json_encode( array(
                "message" => $e->getMessage(),
                "type" => "error",
                "class" => "alert-error"
            ) );
        }
    }

    function showAddNewHotel() {
        try {
            $item = Emp_Hotel::getManager()->getByPk($this->getRequest()->id);
            $new = new Emp_TourHotel(
                array(
                    "hotelId" => $item->getPk(),
                    "tourId" => $this->getRequest()->tourId)
            );
            $this->tour = Emp_Tour::getManager()->getByPk($this->getRequest()->tourId);
            $new->save();
            return json_encode( array("block" => $new->render("tour")->render(), "list" => $new->render("tourlist")->render() ) );
        } catch (Exception $e) {
            return json_encode( array(
                "message" => $e->getMessage(),
                "type" => "error",
                "class" => "alert-error"
            ) );
        }
    }

    function showRemoveTransport(){
        try {
            $transport = Emp_TourTransport::getManager()->getByPk($this->getRequest()->getRequestParameter("id"));
            $tourists = $transport->getTourists();
            $this->tour = $transport->tour;
            $transport->delete();
            $return = array();
            foreach($tourists as $tourist){
                $return[] = array( "id" => $tourist->getPk(), "body" => $tourist->render("noservice")->render() );
            }
            return json_encode($return);

        } catch (Exception $e) {
            return json_encode(array($e->getMessage()));
        }
    }

    function showRemoveHotel(){
        try {
            $transport = Emp_TourHotel::getManager()->getByPk($this->getRequest()->getRequestParameter("id"));
            $tourists = $transport->getTourists();
            $this->tour = $transport->tour;
            $transport->delete();
            $return = array();
            foreach($tourists as $tourist){
                $return[] = array( "id" => $tourist->getPk(), "body" => $tourist->render("noservice")->render() );
            }
            return json_encode($return);

        } catch (Exception $e) {
            return json_encode(array($e->getMessage()));
        }
    }

    function showAddDriver() {
        try {
            $item = Emp_TourTransport::getManager()->getByPk($this->getRequest()->id);
            if(!$this->getRequest()->driverId)
            {
                $item->hydrate(array("driverId" => null));
                $phone = "";

            } else {
                $driver = Emp_Driver::getManager()->getByPk($this->getRequest()->driverId);
                $item->hydrate(array("driverId" => $this->getRequest()->driverId));
                $phone = $driver->phone->get();
            }
            $item->save();
            return json_encode( array(
                "message" => "Водитель сохранён",
                "class" => "alert-success",
                "phone" => $phone
                )
            );
        } catch (Exception $e) {
            return json_encode( array(
                "message" => $e->getMessage(),
                "class" => "alert-error"
            ) );
        }
    }

    function showList()
    {
        $id = $this->getRequest()->getRequestParameter("id");
        $this->tr = $tr = Emp_TourTransport::getManager()->getByPk($id);

        header("Content-Type: application/html; charset=utf-8");
        header("Content-Disposition: attachment; filename=путевой_лист_".$tr."_".$tr->tour->date->asAdorned().".html");
        header("Pragma: no-cache");
        header("Expires: 0");

        return $this->__display("list.tpl");
    }

    function showInsurance()
    {
        $id = $this->getRequest()->getRequestParameter("id");

        $this->insurance = $insurance = Emp_OrderService::getManager()->getByPk($id);
        header("Content-Type: application/vnd.ms-word; charset=utf-8");
        header("Content-Disposition: attachment; filename=страховой_договор_".$insurance->order->tour->date->asString()."_".date("d-m-Y").".doc");
        header("Pragma: no-cache");
        header("Expires: 0");

        return $this->__display("insurance.tpl");
    }

    function showSaveTourComment()
    {
        $tour = Emp_Tour::getManager()->getByPk($this->getRequest()->id);
        $tour->comment = $this->getRequest()->comment;
        $tour->save();
        return json_encode(
            array(
                "message" => "Комментарий обновлён",
                "type" => "alert-success"
            )
        );
    }

    function showHotelFinance()
    {
        $this->tour = $tour = Emp_Tour::getManager()->getByPk($this->getRequest()->id);
        header("Content-Type: application/vnd.ms-word; charset=utf-8");
        header("Content-Disposition: attachment; filename=расчётный_лист_".$tour->date->asString().".doc");
        header("Pragma: no-cache");
        header("Expires: 0");
        return $this->__display("finance.tpl");
    }

}
