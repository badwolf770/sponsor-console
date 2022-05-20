<?php

namespace App\Project\Infrastructure\Repository;

use App\Project\Infrastructure\Entity\Month;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Month|null find($id, $lockMode = null, $lockVersion = null)
 * @method Month|null findOneBy(array $criteria, array $orderBy = null)
 * @method Month[]    findAll()
 * @method Month[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MonthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Month::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Month $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Month $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findByNameInsensitive(string $name)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('LOWER(c.name) = LOWER(:val)')
            ->setParameter('val', $name)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Month[] Returns an array of Month objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Month
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
