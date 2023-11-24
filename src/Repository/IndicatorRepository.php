<?php

namespace App\Repository;

use App\Entity\Indicator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Indicator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Indicator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Indicator[]    findAll()
 * @method Indicator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndicatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Indicator::class);
    }

    /**
     * @return Indicator[] Returns an array of Indicator objects
     */
    public function findByRoles($roles): array
    {
        return $this->findByRolesQB($roles)->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder Returns a QueryBuilder of Indicator by roles
     */
    public function findByRolesQB($roles): QueryBuilder
    {
        $qb = $this->createQueryBuilder('i');
        $i = 0; 
        foreach ($roles as $rol) {
            $qb->orWhere("i.requiredRoles like :rol$i")
               ->setParameter("rol$i", '%' . $rol . '%');
            $i++;
        }
        $qb->orWhere('i.requiredRoles is null')
           ->orderBy('i.id', 'ASC');
        return $qb;
    }

    /**
     * @return QueryBuilder Returns a QueryBuilder of all Indicators
     */
    public function findAllQB(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('i')
           ->orderBy('i.id', 'ASC');
        return $qb;
    }

    /*
    public function findOneBySomeField($value): ?Indicator
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
