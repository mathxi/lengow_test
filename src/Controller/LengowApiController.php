<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Order;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LengowApiController extends AbstractController
{
    /**
     * @Route("/api/orders/new", name="lengow_api_orders_new", defaults={"output": "json"})
     * @Route("/api/orders/new_xml", name="lengow_api_orders_new_xml", defaults={"output": "xml"})
     */
    public function apiOrdersNew(string $output)
    {
        $orders = [];

        $customers = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->findAll()
        ;

        $faker = Factory::create();

        // On génère 20 nouvelles commandes
        for ($i = 0; $i < 20; $i++) {

            // Order lines
            $orderLines = [];
            $maxLines = rand(1,3);
            for($j = 0; $j < $maxLines; $j++) {
                $orderline['product'] = $faker->ean13;
                $orderline['quantity'] = rand(1,3);
                $orderline['price'] = $faker->randomFloat(2, 1, 100);
                $orderLines[] = $orderline;
            }

            // Order
            $order['date']  = new \DateTime();
            $order['customer_id'] = $customers[rand(0,99)]->getId();
            $order['orderlines'] = $orderLines;

            $orders[] = $order;
        }

        // JSON output
        if ($output === 'json') {
            return new JsonResponse($orders);
        // XML output
        } else {
            $xml = new \SimpleXMLElement('<?xml version="1.0"?><orders></orders>');
            self::arrayToXml($orders, $xml);
            return new Response($xml->asXML(), 200, [
                'Content-Type' => 'text/xml',
            ]);
        }
    }

    /**
     * @Route("/api/orders/random", name="lengow_api_orders_random")
     * @return JsonResponse
     */
    public function apiOrderRandom(): JsonResponse
    {
        $orders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->getRandomOrders()
        ;

        return new JsonResponse($orders);
    }

    /**
     * @param $array
     * @param $xml
     */
    private static function arrayToXml($array, &$xml) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)){
                    $subnode = $xml->addChild($key);
                    self::arrayToXml($value, $subnode);
                } else {
                    $subnode = $xml->addChild('item'.$key);
                    self::arrayToXml($value, $subnode);
                }
            } else {
                if ($value instanceof \DateTime) {
                    $xml->addChild($key, $value->format('c'));
                } else {
                    $xml->addChild($key, htmlspecialchars($value));
                }
            }
        }
    }
}
