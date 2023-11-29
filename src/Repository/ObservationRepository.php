<?php

namespace App\Repository;

use App\Entity\Indicator;
use App\Entity\Observation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Observation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Observation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Observation[]    findAll()
 * @method Observation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Observation::class);
    }

    /**
     * @return Observation|null Returns an array of Observation objects
     */

    public function findLastObservationForIndicator(Indicator $indicators): ?Observation
    {
        $qb = $this->findLastObservationForIndicatorQB($indicators);
        $result = $qb->getResult();
        if ( count($result) > 0 ) {
            return $result[0];
        } else {
            return null;
        }
    }

    public function findLastObservationForIndicatorQB(Indicator $indicator)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.indicator = :indicator')
            ->setParameter('indicator', $indicator)
            ->addOrderBy('o.year','DESC')
            ->addOrderBy('o.month','DESC')
            ->setMaxResults(1)
            ->getQuery();
    }

    public function findObservationByExample(Observation $observation) {
        $result = $this->createQueryBuilder('o')
            ->andWhere('o.indicator = :indicator')
            ->setParameter('indicator', $observation->getIndicator())
            ->andWhere('o.year = :year')
            ->setParameter('year', $observation->getYear())
            ->andWhere('o.month = :month')
            ->setParameter('month', $observation->getMonth())
            ->getQuery()
            ->getOneOrNullResult();
        return $result;
    }

    public function findByIndicatorOrdered(Indicator $indicator) {
        return $this->createQueryBuilder('o')
            ->andWhere('o.indicator = :indicator')
            ->setParameter('indicator', $indicator)
            ->addOrderBy('o.year','DESC')
            ->addOrderBy('o.month','DESC')
            ->getQuery()
            ->getResult();
    } 
    /*
    public function findOneBySomeField($value): ?Observation
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
