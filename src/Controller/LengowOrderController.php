<?php

namespace App\Controller;

use Faker\Factory;
use App\Entity\Order;
use App\Service\Client;
use App\Entity\Customer;
use App\Entity\OrderLine;
use App\Service\OrderConsumer;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LengowOrderController extends AbstractController
{
    /**
     * @Route("/orders/last", name="lengow_orders_last")
     */
    public function ordersLast()
    {
        //
        // Question 1 :
        //
        // - Lister les 20 dernières commandes dont le statut est "new"
        //
        // -> Implémenter la méthode getLastNewOrders()
        // -> Utiliser le queryBuilder, trier par date et id decroissant.
        //

        $orders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->getLastNewOrders();

        return $this->render('lengow_order/new_orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/orders/last_optimized", name="lengow_orders_last_optimized")
     */
    public function ordersLastOptimized()
    {
        //
        // Question 2 :
        //
        // - Lister les 20 dernières commandes dont le statut est "new", version optimisée
        //
        // -> Implémenter la méthode getLastNewOrdersOptimized()
        // -> Utiliser le queryBuilder, trier par date et id decroissant.
        //
        // -> La page ne doit générer qu'une seule requête SQL
        //

        $orders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->getLastNewOrdersOptimized();

        return $this->render('lengow_order/new_orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/orders/new", name="lengow_orders_new")
     */
    public function ordersNew()
    {

        //
        // Question 3 :
        //
        // - Consommer l'API /api/orders/new et enregistrer les nouvelles commandes en base
        //
        // -> Effectuer le travail le plus simplement dans le controlleur.
        // -> Afficher le nombre total de commandes dont le statut est "new"
        //

        // Consommation de l'API
        $url = 'http://localhost:8000/api/orders/new'; // ne marche pas MAIS marche à partir d'un *.php externe à symfony
        $url2 = 'https://pokeapi.co/api/v2/pokemon/ditto/'; // test avec une 2ème api  --> fonctionnel
        /*
         $curl_request = curl_init();
         curl_setopt($curl_request, CURLOPT_URL, $url);
         curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($curl_request, CURLOPT_CONNECTTIMEOUT, 1);
         curl_setopt($curl_request, CURLOPT_TIMEOUT, 10); 
         curl_setopt($curl_request, CURLOPT_RETURNTRANSFER,1);
         var_dump(curl_exec($curl_request));
         curl_close($curl_request);
        */


        // Décompte des commandes dont le statut est "new" avant insertion
        $totalOldNewOrders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->countNewOrders();
            
        //contournement du problème d'appel API
        $response = $this->forward('App\Controller\LengowApiController::apiOrdersNew', [
            'output' => 'json'
        ]);
        $jsonDatas = $response->getContent();
        $decodedOrder = json_decode($jsonDatas);


        $entityManager = $this->getDoctrine()->getManager();
        foreach ($decodedOrder as $newOrder) {
            //création d'un Order
            $order = new Order();
            foreach ($newOrder->orderlines as $tempOrderLine) {
                //Nouvel orderline
                $orderLines = new OrderLine();
                $orderLines->setProduct($tempOrderLine->product);
                $orderLines->setQuantity($tempOrderLine->quantity);
                $orderLines->setPrice($tempOrderLine->price);
                //Ajout de l'orderLine dans l'order
                $order->addOrderLine($orderLines);
            }
            $order->setCreatedAt(new \DateTime($newOrder->date->date));
            $order->setCustomer($entityManager->getRepository(Customer::class)->find($newOrder->customer_id));
            $order->setStatus('new');
            //préparation de la query
            $entityManager->persist($order);
        }
        //insertion en base des orders
        $entityManager->flush();
        
        // Décompte des commandes dont le statut est "new" après insertion
        $totalNewNewOrders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->countNewOrders();


        return $this->render('lengow_order/get_new_orders.html.twig', [
            'total_old_new_orders' => $totalOldNewOrders,
            'total_new_orders' => $totalNewNewOrders
        ]);
    }

    /**
     * @Route("/orders/new_service", name="lengow_orders_new_service")
     */
    public function ordersNewService(OrderConsumer $orderConsumer)
    {
        //
        // Question 4 :
        //
        // - Consommer l'API /api/orders/new et /api/orders/new_xml et enregistrer les nouvelles commandes en base
        //   en utilisant les services.
        //
        // -> Le service prendra en paramètre l'url de l'API et devra traiter les commandes quelque soit le format de l'API (JSON et XML).
        // -> L'ajout d'un ou plusieurs autres format d'API ne devra générer aucune modification de la classe de service.
        //

        // Décompte des commandes dont le statut est "new" avant ajout en base
        $totalNewOrders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->countNewOrders();
        // Consommation de l'API Json
        //$ordersJSON = $orderConsumer->createFromUrl('http://192.168.1.26:8080/api/orders/new');

        // Consommation de l'API XML
        //$ordersXML = $orderConsumer->createFromUrl('http://192.168.1.26:8080/api/orders/new_xml');
        
        
        //Switch case pour traiter des valeurs de retour
        
        
        // Décompte des commandes dont le statut est "new"
        $totalNewOrders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->countNewOrders();

        return $this->render('lengow_order/get_new_orders.html.twig', [
            'total_old_new_orders' => $totalNewOrders,
            'total_new_orders' => $totalNewOrders,
        ]);
    }

    /**
     * @Route("/orders/jquery", name="lengow_orders_jquery")
     */
    public function orderJqueryLoad()
    {
        //
        // Question 5 :
        //
        // - Consommer l'API /api/orders/random en "Ajax" pour récupérer des commandes.
        //
        // -> Utiliser jQuery pour effectuer la requête Ajax
        // -> Filtrage en javascript des résultats sur la sélection du statut.
        //

        return $this->render('lengow_order/jquery_orders.html.twig');
    }

    /**
     * @Route("/orders/vanilla_js", name="lengow_orders_vanilla_js")
     */
    public function orderVanillaJsLoad()
    {
        //
        // Question 6 :
        //
        // - Consommer l'API /api/orders/random en "Ajax" pour récupérer des commandes.
        //
        // -> Utiliser du pure javascript pour effectuer tous les traitements (chargement, affichage, filtre).
        // -> Doit être fonctionnel sous Chrome
        //

        return $this->render('lengow_order/vanilla_js_orders.html.twig');
    }

}
