<?php

namespace Crm\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use YandexCheckout\Client;
use YandexCheckout\Model\Payment;

class YandexController extends Controller
{
    /**
     * @Route("/yandex/kassa/test")
     */
    public function indexAction($name)
    {
        $client = new Client();
        $client->setAuth('shopId', 'secretKey');
        $idempotenceKey = uniqid('', true);
        $response = $client->createRefund(
            array(
                'payment_id' => '1005001',
                'amount' => array(
                    'value' => '2.00',
                    'currency' => 'RUB',
                ),
            ),
            $idempotenceKey
        );

        return $response;
    }
}
