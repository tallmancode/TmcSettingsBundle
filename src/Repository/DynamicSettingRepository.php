<?php

namespace TallmanCode\SettingsBundle\Repository;

use TallmanCode\SettingsBundle\Entity\DynamicSetting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DynamicSetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method DynamicSetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method DynamicSetting[]    findAll()
 * @method DynamicSetting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DynamicSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DynamicSetting::class);
    }

    // /**
    //  * @return DynamicSetting[] Returns an array of DynamicSetting objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DynamicSetting
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
