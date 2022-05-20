<?php

namespace App\Project\Infrastructure\Repository;

use App\Project\Infrastructure\Entity\TemplateImport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TemplateImport|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateImport|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateImport[]    findAll()
 * @method TemplateImport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method findByProject(string $id)
 */
class TemplateImportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateImport::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TemplateImport $entity, bool $flush = true): void
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
    public function remove(TemplateImport $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return TemplateImport[] Returns an array of TemplateImport objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TemplateImport
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
