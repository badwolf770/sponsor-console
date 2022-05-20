<?php

namespace App\Project\Infrastructure\Repository;

use App\Project\Infrastructure\Entity\TargetAudience;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TargetAudience|null find($id, $lockMode = null, $lockVersion = null)
 * @method TargetAudience|null findOneBy(array $criteria, array $orderBy = null)
 * @method TargetAudience[]    findAll()
 * @method TargetAudience[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TargetAudienceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TargetAudience::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TargetAudience $entity, bool $flush = true): void
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
    public function remove(TargetAudience $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return TargetAudience[] Returns an array of TargetAudience objects
     */

    public function findByName(string $value): array
    {
        $query = $this->createQueryBuilder('t')
            ->orderBy('t.name', 'ASC');

        $lowExpression      = mb_strtolower($value);
        $preparedExpression = "%{$lowExpression}%";
        $hasPos             = mb_strrpos($lowExpression, '*');
        if ($hasPos !== false) {
            $preparedExpression = str_replace('*', '%', $lowExpression);
        }
        $query->where("LOWER(t.name) LIKE :expression")
            ->setParameter('expression', $preparedExpression);

        return $query->setMaxResults(30)
            ->getQuery()
            ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?TargetAudience
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
