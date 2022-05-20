<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Repository;

use App\Project\Infrastructure\Entity\TemplateType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TemplateType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateType[]    findAll()
 * @method TemplateType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method TemplateType findByName(string $templateType)
 * @method findOneByName(string $templateType)
 */
class TemplateTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateType::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TemplateType $entity, bool $flush = true): void
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
    public function remove(TemplateType $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return TemplateType[] Returns an array of TemplateType objects
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
    public function findOneBySomeField($value): ?TemplateType
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
