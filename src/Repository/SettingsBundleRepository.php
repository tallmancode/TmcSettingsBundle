<?php

namespace TallmanCode\SettingsBundle\Repository;

use TallmanCode\SettingsBundle\Entity\SettingsBundleSetting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SettingsBundleSetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettingsBundleSetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettingsBundleSetting[]    findAll()
 * @method SettingsBundleSetting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingsBundleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SettingsBundleSetting::class);
    }
}
