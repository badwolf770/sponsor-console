<?php

namespace App\Project\Infrastructure\Repository;

use App\Project\Infrastructure\Entity\SponsorType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SponsorType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SponsorType|null findOneBy(array $criteria, array $orderBy = null)
 * @method SponsorType[]    findAll()
 * @method SponsorType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SponsorTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SponsorType::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(SponsorType $entity, bool $flush = true): void
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
    public function remove(SponsorType $entity, bool $flush = true): void
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
    //  * @return SponsorType[] Returns an array of SponsorType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SponsorType
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
