<?php

class OrdersModule extends fvModule{

    function __construct()
    {
        $this->moduleName = strtolower( substr( __CLASS__, 0, -6 ) );
        parent::__construct(fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.template"),
            fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.compile"),
            fvSite::$Layoult);
    }

    function showIndex()
    {
        $this->tourFilter = Emp_Tour::getManager()
            ->select()
            ->join("orders", "o")
            ->join("o.persons", "p")
            ->orderBy("date asc")
            ->fetchAll();
        return $this->__display("index.tpl");
    }

    function showResult(){
        $table = new Component_Table_Order(Emp_Order::getManager());

        if($filter = $this->getRequest()->getRequestParameter("filter"))
        {
            $table
                ->addFilter($filter);
        }

        $table
            ->addRelation("payments")
            ->addRelation("persons")
            ->addRelation("addservices")
            ->setTitle("Заказы")
            ->setModule($this->moduleName)
            ->addOrder("ctime desc")
            ->preload();

        $this->total = array_reduce(
            $table->getList(),
            function ($result, $order) {
                /** @var Emp_Order $order */
                $result['sum'] += $order->getPrice();
                $result['paid'] += $order->getPayment();
                $result['rest'] += $order->getRest();
                return $result;
            }
        );

        return $table;
    }

    function showEdit()
    {
        $order =  Emp_Order::getManager()->getByPk($this->getRequest()->id, true);
        if ($order->isNew()) {
            $order->statusId = 1;
            $order->managerId = fvSite::$fvSession->getUser()->getPk();
            $order->save();
        }
        $this->entity = $order;
        $this->sources = Emp_Source::getManager()->getAll();
        $this->directions = $directions = Emp_Direction::getManager()->getAll();
        $this->offers = !$order->isNew() ?
            $order->offer->direction->offers->get()  :
            current($directions)->offers->get();
        $this->tours = Emp_Tour::getManager()
            ->select()
            ->where("date > NOW()")
            ->orderBy("date ASC")
            ->fetchAll();

        $comment = new Component_Comment( $order, "comments" );
        $this->comment = $comment->setAction("/orders/addcomment");
        $log = new Component_Table( Emp_OrderLog::getManager());
        $this->log = $log
            ->setAllowNew( false )
            ->setDeletable(false)
            ->setEditable(false)
            ->addFilter(array("orderId" => $order->getPk()))
            ->addOrder("ctime desc");

        return $this->__display("edit.tpl");
    }

    function showAddComment() {
        $data = $this->getRequest()->getRequestParameter("comment");
        $comment = new Emp_OrderComment($data);
        $comment->save();
        $order = Emp_Order::getManager()->getByPk($data['orderId']);
        $order->hydrate(array("statusId" => $data['statusId']));
        $order->save();
        return $comment->asAdorned()->render();
    }

    function showAddOk() {
        $data = $this->getRequest()->getRequestParameter("comment");
        $comment = new Emp_OrderOkComment($data);
        $comment->save();
        return $comment->asAdorned()->render();
    }

    function showSaveTourist(){
        try {
            $data = $this->getRequest()->getRequestParameter("tourist");
            $person = Emp_Person::getManager()->getByPk($data['id'], true);
            $action = $this->getRequest()->getRequestParameter('new') ? "append" : "update";
            $order = Emp_Order::getManager()->getByPk($this->getRequest()->orderId);
            $touristNew = $person->isNew();
            if($touristNew){
                $form = "new";
            } else
                $form = $person->getPk();
            if( $touristNew xor !$this->getRequest()->getRequestParameter('new') ) {
                $person->hydrate($data);
                if(!$person->isValid()) {
                    throw new ValidationException( json_encode($person->getValidationResult()) );
                }
                $person->save();

                $order->persons->add($person);
                $order->persons->save();

                if( $touristNew or $this->getRequest()->getRequestParameter('new') ) {
                    $log = new Emp_OrderLog();
                    $log->managerId = fvSite::$fvSession->getUser()->getPk();
                    $log->orderId = $order->getPk();
                    if( $touristNew or  $this->getRequest()->getRequestParameter('new') )
                        $log->comment = "В заказ добавлен новый турист {$person->getName()}. Всего туристов в заказе - {$order->getTotalTourists()}";
                    $log->save();
                }


                if($data['phone']) {
                    $phone = Emp_PersonPhone::getManager()
                        ->select()
                        ->where("name = ?", $data['phone'])
                        ->fetchOne();
                    if(!$phone instanceof fvRoot) {
                        $phone = new Emp_PersonPhone(array("personId" => $person->getPk(), "name" => $data['phone']));
                        $phone->save();
                    }
                    elseif(!$person->phones->is($phone)) {
                        if($touristNew)
                            $person->delete();
                        throw new Exception("Телефон {$data['phone']} уже существует в базе");
                    }
                }
            } else {
                $order->persons->add($person);
                $order->persons->save();
            }

            if($this->getRequest()->order) {
                $order->hydrate( $this->getRequest()->order );
                $order->save();
            }

            $this->entity = $order;

            return json_encode( array(
                "msg" => "Запись успешно сохранена",
                "msgclass" => "alert-success",
                "action" => $action,
                "toappend" => $person->render('form')->render()
            ) );
        }
        catch(ValidationException $e){
            return json_encode( array(
                "msg" => "Ошибка валидации, проверьте введённые данные",
                "msgclass" => "alert-danger",
                "action" => "validate",
                "errors" => $e->getMessage(),
                "form" => $form
            ) );
        }
        catch(Exception $e){
            return json_encode( array(
                "msg" => $e->getMessage(),
                "msgclass" => "alert-danger"
            ) );
        }
    }

    function showRemoveTourist() {
        try {
            $tId = $this->getRequest()->getRequestParameter("id", "int");
            $eId = $this->getRequest()->getRequestParameter("eid");

            $order = Emp_Order::getManager()->getByPk($eId);
            $tourist = Emp_Person::getManager()->getByPk($tId);

            $order->persons->remove($tourist->getPk());
            $order->persons->save();

            $log = new Emp_OrderLog();
            $log->managerId = fvSite::$fvSession->getUser()->getPk();
            $log->orderId = $order->getPk();

            $log->comment = "Из заказа удален турист {$tourist->getName()}. Всего туристов в заказе - {$order->getTotalTourists()}";
            $log->save();

            return json_encode( array(
                "msg" => "Запись успешно удалена",
                "msgclass" => "alert-success",
                "action" => "remove"
            ) );
        }
        catch(Exception $e){
            return json_encode( array(
                "msg" => $e->getMessage(),
                "msgclass" => "alert-danger"
            ) );
        }
    }

    function showSave() {
        try {
            $order = $this->getRequest()->getRequestParameter('order');
            $entity = Emp_Order::getManager()->getByPk($this->getRequest()->id, true);
            $shippingData = $this->getRequest()->getRequestParameter('shipping');
            $redirect = $this->getRequest()->redirect;

            $shipping = Emp_Shipping::getManager()
                ->select()
                ->where(array("orderId" => $shippingData['orderId']))
                ->fetchOne();

            if(!$shipping instanceof fvRoot) {
                $shipping = new Emp_Shipping($shippingData);
                $shipping->save();
            }

            $order['shippingId'] = $shipping->getPk();

            $entity->hydrate( $order );
            if(!$entity->isValid()){
                throw new Exception( json_encode( $entity->getShortValidation() ) );
            }
            $entity->save();

            $return = array(
                "msg" => "Запись сохранена успешно",
                "msgclass" => "alert-success"
            );
            $return["direction"] = ($redirect == 1) ? "/orders" : "/orders/edit/{$entity->getPk()}";

            return json_encode($return);

        }
        catch (Exception $e) {
            return json_encode( array(
                    "msg" => $e->getMessage(),
                    "msgclass" => "alert-error",
                    "direction" => "/orders/edit/{$entity->getPk()}"
                )
            );
        }
    }

    function showGetOffers() {
        $pk = $this->getRequest()->getRequestParameter("id");
        $direction = Emp_Direction::getManager()->getByPk($pk);
        $answer = array();
        foreach($direction->offers->get() as $offer){
            $answer[] = array("id" => $offer->getPk(), "text" => $offer->duration->get());
        }
        return json_encode($answer);
    }

    function showSearch() {
        $response = array();
        $search = $this->getRequest()->getRequestParameter('search');
        $tourists = Emp_Person::getManager()
            ->select()
            ->leftJoin('phones')
            ->where("root.name LIKE CONCAT('%',?,'%')", $search)
            ->addOr()
            ->andWhere("root.email LIKE CONCAT('%',?,'%')", $search)
            ->addOr()
            ->andWhere("phones.name LIKE  CONCAT('%',?,'%')", $search)
            ->fetchAll();
        foreach($tourists as $tourist){
            $response[] = array("label" => $tourist->name->get(), "value"=>$tourist->getPk());
        }
        return json_encode($response);
    }

    function showGetTourist() {
        $id = $this->getRequest()->getRequestParameter('id');
        $tourist = Emp_Person::getManager()->getByPk($id);
        $response = array("id" => $tourist->getPk());
        foreach ($tourist->getFields() as $name=>$field){
            if($name === 'phones')
                $response[$name] = $tourist->getPhone();
            else
                $response[$name] = $field->get();
        }
        return json_encode($response);

    }

    function showServices() {
        $orderId = $this->getRequest()->getRequestParameter('id');
        $this->order = $order = Emp_Order::getManager()->getByPk($orderId);
        $comment = new Component_Comment( $order, "comments" );
        $this->comment = $comment->setAction("/orders/addcomment");
        $log = new Component_Table( Emp_OrderLog::getManager());
        $this->log = $log
            ->setAllowNew( false )
            ->setDeletable(false)
            ->setEditable(false)
            ->addFilter(array("orderId" => $order->getPk()))
            ->addOrder("ctime desc");
        return $this->__display("services.tpl");
    }

    function showAddService(){
        try {
            $data = $this->getRequest()->getRequestParameter("data");
            $serviceLink = Emp_OrderService::getManager()->getByPk($data['id'], true);
            $order = Emp_Order::getManager()->getByPk($data['orderId']);
            $oldPrice = $order->getPrice();
            $this->order = $order;
            $new = $serviceLink->isNew();
            $serviceLink->hydrate($data);
            $serviceLink->save();
            $newPrice = $order->getPrice(false);
            $log = new Emp_OrderLog();
            $log->managerId = fvSite::$fvSession->getUser()->getPk();
            $log->orderId = $data['orderId'];
            if($new)
                $log->comment = "Пользователю {$serviceLink->tourist->getName()} добавлена услуга {$serviceLink->offservice->service}.<br />Cтарая стоимость заказа - {$oldPrice}<br />Новая стоимость заказа - {$newPrice}";
            else
                $log->comment = "Пользователю {$serviceLink->tourist->getName()} изменена услуга {$serviceLink->offservice->service}. комментарий - {$serviceLink->comment->get()}<br />Cтарая стоимость заказа - {$oldPrice}<br />Новая стоимость заказа - {$newPrice}";
            $log->save();
            return json_encode(array(
                "body" => $serviceLink->asAdorned()->render(),
                "message" => "Услуга добавлена",
                "type" => "alert-success",
                "name" => (string)$serviceLink->offservice->service,
                "price" => $newPrice
            ));
        } catch (Exception $e) {
            return json_encode(array("message" => $e->getMessage(), "type" => "alert-error"));
        }
    }

    function showAddAService() {
        try {
            $data = $this->getRequest()->getRequestParameter("data");
            $order = Emp_Order::getManager()->getByPk($data['orderId']);
            $oldPrice = $order->getPrice();
            $serviceLink = new Emp_AdditionalService($data);
            $serviceLink->save();
            $newPrice = $order->getPrice(false);
            $log = new Emp_OrderLog();
            $log->managerId = fvSite::$fvSession->getUser()->getPk();
            $log->orderId = $data['orderId'];
            $log->comment = "Добавлена доп. услуга {$serviceLink->comment->get()}.<br />Cтарая стоимость заказа - {$oldPrice}<br />Новая стоимость заказа - {$newPrice}";
            $log->save();
            return json_encode(array(
                "body" => $serviceLink->asAdorned()->render(),
                "message" => "Услуга добавлена",
                "type" => "alert-success",
                "price" => $newPrice
            ));
        } catch (Exception $e) {
            return json_encode(array("message" => $e->getMessage(), "type" => "alert-error"));
        }
    }

    function showDeleteService() {
        try {
            $data = $this->getRequest()->getRequestParameter("id");
            $service = Emp_OrderService::getManager()->getByPk($data);
            $order = $service->tourist->order;
            $oldPrice = $order->getPrice();
            $comment = $service->tourist->getName();
            $service->delete();
            $newPrice = $order->getPrice(false);
            $log = new Emp_OrderLog();
            $log->managerId = fvSite::$fvSession->getUser()->getPk();
            $log->orderId = $order->getPk();
            $log->comment = "Пользователю {$comment} удалена услуга {$service->offservice->service}<br />Cтарая стоимость заказа - {$oldPrice}<br />Новая стоимость заказа - {$newPrice}";
            $log->save();

            return json_encode(array(
                "message" => "Услуга удалена",
                "type" => "alert-success",
                "price" => $newPrice
            ));

        } catch (Exception $e) {
            return json_encode(array("message" => $e->getMessage(), "type" => "alert-error"));
        }
    }

    function showDeleteAService() {
        try {
            $data = $this->getRequest()->getRequestParameter("id");
            $service = Emp_AdditionalService::getManager()->getByPk($data);
            $order = $service->order;
            $oldPrice = $order->getPrice();
            $comment = $service->comment->get();
            $service->delete();
            $newPrice = $order->getPrice();
            $log = new Emp_OrderLog();
            $log->managerId = fvSite::$fvSession->getUser()->getPk();
            $log->orderId = $service->orderId->get();
            $log->comment = "Удалена доп. услуга {$comment}. <br />Cтарая стоимость заказа - {$oldPrice}<br />Новая стоимость заказа - {$newPrice}";
            $log->save();
            $service->delete();
            return json_encode(array(
                "message" => "Услуга удалена",
                "type" => "alert-success",
                "price" => $newPrice
            ));

        } catch (Exception $e) {
            return json_encode(array("message" => $e->getMessage(), "type" => "alert-error"));
        }
    }

    function showPay() {
        $this->order = $order = Emp_Order::getManager()->getByPk( $this->getRequest()->getRequestParameter("id") );
        $comment = new Component_Comment( $order, "comments" );
        $this->comment = $comment->setAction("/orders/addcomment");
        $this->sources = Emp_SourcePayment::getManager()->getAll();
        $log = new Component_Table( Emp_OrderLog::getManager());
        $this->log = $log
            ->setAllowNew( false )
            ->setDeletable(false)
            ->setEditable(false)
            ->addFilter(array("orderId" => $order->getPk()))
            ->addOrder("ctime desc");

        return $this->__display("pay.tpl");
    }

    function showAddPayment() {
        try{
            $data = $this->getRequest()->getRequestParameter("data");
            $payment = new Emp_OrderPayment($data);
            $payment->save();
            $order = Emp_Order::getManager()->getByPk($data['orderId']);
            return json_encode(array(
                "toappend" => $payment->asAdorned()->render(),
                "msg" => "Платёж добавлен",
                "msgclass" => "alert-success",
                "action" => "append-update",
                "price" => $order->getPayment(),
                "rest" => $order->getRest()
            ));

        } catch (Exception $e) {
            return json_encode(array("msg" => $e->getMessage(), "msgclass" => "alert-error"));
        }
    }

    function showOk(){
        $order = Emp_Order::getManager()->getByPk( $this->getRequest()->getRequestParameter('id') );
        $this->order = $order;
        $comment = new Component_Comment( $order, "comments" );
        $this->comment = $comment->setAction("/orders/addcomment");
        $log = new Component_Table( Emp_OrderLog::getManager());
        $this->log = $log
            ->setAllowNew( false )
            ->setDeletable(false)
            ->setEditable(false)
            ->addFilter(array("orderId" => $order->getPk()))
            ->addOrder("ctime desc");
        return $this->__display("ok.tpl");
    }

    function showTransfer() {
        $this->order = $order = Emp_Order::getManager()->getByPk( $this->getRequest()->getRequestParameter('id') );
        $this->vehicles = Emp_Vehicle::getManager()->getAll();
        $comment = new Component_Comment( $order, "comments" );
        $this->comment = $comment->setAction("/orders/addcomment");
        $log = new Component_Table( Emp_OrderLog::getManager());
        $this->log = $log
            ->setAllowNew( false )
            ->setDeletable(false)
            ->setEditable(false)
            ->addFilter(array("orderId" => $order->getPk()))
            ->addOrder("ctime desc");
        return $this->__display("transfer.tpl");
    }

    function showHotelRooms(){
        $hotel = Emp_Hotel::getManager()->getByPk( $this->getRequest()->id );
        if($hotel instanceof fvRoot) {
            $this->rooms = $hotel->rooms->get();
            return $this->__display("rooms.tpl");
        }
        else return "<option value=''>Выберите номер</option>";
    }

    function showTransportSeats(){
        $hotel = Emp_Vehicle::getManager()->getByPk( $this->getRequest()->id );
        if($hotel instanceof fvRoot) {
            $this->rooms = $hotel->seats->get();
            return $this->__display("rooms.tpl");
        }
        else return "<option value='0'>Выберите место</option>";
    }

    function showDeletePayment() {
        try {
            $data = $this->getRequest()->getRequestParameter("id");
            $payment = Emp_OrderPayment::getManager()->getByPk($data);
            $order = $payment->order;

            $payment->delete();

            return json_encode(
                array(
                    "message" => "Услуга удалена",
                    "type" => "alert-success",
                    "rest" => $order->getRest(),
                    "price" => $order->getPayment()
                )
            );
        } catch (Exception $e) {
            return json_encode(array("message" => $e->getMessage(), "type" => "alert-error"));
        }
    }

    function showRecalculate()
    {
        try {
            $order = Emp_Order::getManager()->getByPk(
                $this->getRequest()->getRequestParameter("id", "int")
            );
            $order->price->set(null);
            return json_encode(
                array(
                    "price" => $order->getPrice(false),
                    "message" => "Цена обновлена",
                    "type" => "alert-success"
                )
            );
        } catch (Exception $e) {
            return json_encode(
                array(
                    "message" => $e->getMessage(),
                    "type" => "alert-error"
                )
            );
        }

    }
}
