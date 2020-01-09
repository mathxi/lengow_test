<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;


/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Retourner les 20 dernières commandes
     * @return array
     *
     * -> utiliser le query builder
     *
     */
    public function getLastNewOrders(): array
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->from(Order::class,'ord')
            ->where('o.status = :status')
            ->orderBy('o.createdAt ','DESC')
            //->setMaxResults(20)
            ->setParameter('status', 'new');
            $query = $queryBuilder->getQuery();
            
        return $query->getResult();
    }


    /**
     * Retourner les 20 dernières commandes, version optimisée (1 seule reqête générée)
     * @return array
     *
     * -> utiliser le query builder
     *
     */
    public function getLastNewOrdersOptimized(): array
    {
    }

    /**
     * Décompte les commandes dans le status 'new'
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * -> utiliser le query builder
     *
     */
    public function countNewOrders(): int
    {
    }

    /**
     * Retourne 50 commandes (aléatoire)
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getRandomOrders(): array
    {
        $sql = 'SELECT o.*, c.* 
            FROM lgw_test_order AS o
              JOIN lgw_test_orderline AS ol ON ol.order_id = o.id 
              JOIN lgw_test_customer AS c ON o.customer_id = c.id
            ORDER BY RAND()
            LIMIT 50
         ';

        $stmt = $this->getEntityManager()
            ->getConnection()
            ->executeQuery($sql);

        return $stmt->fetchAll();
    }
}
