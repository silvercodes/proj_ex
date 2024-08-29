<?php

require_once 'Logger.php';

/**
 * Class Parser
 */
class Parser
{
    /**
     * @var object(stdClass)
     */
    private $input;

    /**
     * @var Logger|null
     */
    private $log;

    /**
     * Parser constructor.
     * @param string $inputJson
     * @throws Exception
     */
    public function __construct(string $inputJson) {
        $this->log = Logger::getLogger();

        $decodedObj = json_decode($inputJson, false);
        $this->input = $decodedObj ?? null;
    }

    /**
     * Get card number of user
     * @return string
     */
    public function getCardNumber() {
        $cardNumber = '';
        if ($this->input) {
            $cardNumber = trim($this->input->Pagar[0]->cliente->cedula);
        } else {
            $this->log->error('Parser->getCardNumber() says: input is empty');
        }

        return $cardNumber;
    }

    /**
     * Parsing orders from POS
     * @return array
     */
    public function parseOrders() {
        $result = [];

        if($this->input) {
            $data = $this->input->Pagar;

            foreach ($data as $val) {
                $order = [];

                // card_number (string)
                $order['card_number'] = trim($val->cliente->cedula);

                // till (integer) => hard code
                $order['till'] = 1;

                // receipt_number (string)
                $order['receipt_number'] = trim($val->factura->numerofact);

                // subtotal (float)
                $order['subtotal'] = round(floatval(trim($val->factura->total)), 2);

                // taxes (array) -> ignored
                $order['taxes'] = [];

                // total (float)
                $order['total'] = round(floatval(trim($val->factura->total)),2);

                // payment_method (string)
                $order['payment_method'] = trim($val->pagos[0]->forma);

                // date (integer)
                $order['date'] = intval(trim($val->factura->fecha));

                // cashier (object) => hard code
                $order['cashier'] = [
                    'id' => 1,
                    'name' => 'AvaPos'
                ];

                // Line items processing
                $lineItems = [];
                foreach($val->producto as $item) {
                    // one product (object)
                    $product = [];

                    // id (string)
                    $product['id'] = trim($item->id_producto);

                    // name (string)
                    $product['name'] = trim($item->nombre_producto);

                    // categories -> ignored
                    $product['categories'] = [];

                    // discounts -> ignored
                    $product['discounts'] = [];

                    // price (float)
                    $product['price'] = round(floatval(trim($item->precio_total)), 2);

                    // quantity (float)
                    $product['quantity'] = floatval(trim($item->cant_prod));

                    // modifiers (array)
                    $modifiers = [];
                    $modifiersString = trim($item->detalle_agregados);
                    if ($modifiersString) {
                        $modifiers[] =  $modifiersString;
                    }
                    $product['modifiers'] = $modifiers;

                    // add product to line_items array
                    $lineItems[] = $product;
                }

                // add line_items to order
                $order['line_items'] = $lineItems;

                // add order to result array
                $result[] = $order;
            }
        } else {
            $this->log->error('Parser->parseOrders() says: input is empty');
        }

        return $result;
    }

    /**
     * Parse user info for updating or creating on AvaPOS platform
     * @param $userInfo
     * @return array
     */
    public function parseUser($userInfo) {
        $userObj = [
            'nombre'            => $userInfo->user->first_name . ' ' . $userInfo->user->last_name,
            'identificacion'    => $userInfo->user->cedula,
            'direccion'         => $userInfo->user->address,
            'email'             => $userInfo->user->email_address,
            'telefono'          => $userInfo->user->phone_number,
        ];

        return $userObj;
    }
}