<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Customer;
use App\Entity\OrderLine;
use App\Service\OrderConsumer;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Client;

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
        $url = 'http://localhost:8000/api/orders/new';
        $url2 = 'https://pokeapi.co/api/v2/pokemon/ditto/';
 

        // Fonctionne avec un *.php externe à symfony
        // $curl_handle = curl_init();
        // curl_setopt($curl_handle, CURLOPT_URL, $url);
        // curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 1);
        // curl_setopt($curl_handle, CURLOPT_TIMEOUT, 10); 
        // curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,1);
        // var_dump(curl_exec($curl_handle));
        // curl_close($curl_handle);
        $response = $this->forward($url);
        echo '<pre>';
        var_dump($response);
        echo '</pre>';

        // Décompte des commandes dont le statut est "new"
        $totalNewOrders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->countNewOrders();

        return $this->render('lengow_order/get_new_orders.html.twig', [
            'total_new_orders' => $totalNewOrders,
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


        // Consommation de l'API Json


        // Consommation de l'API XML


        // Décompte des commandes dont le statut est "new"
        $totalNewOrders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->countNewOrders();

        return $this->render('lengow_order/get_new_orders.html.twig', [
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
