<?php

namespace App\Repository;

use DateTime;
use App\Utils\Pager;
use App\Entity\Upload;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Upload|null find($id, $lockMode = null, $lockVersion = null)
 * @method Upload|null findOneBy(array $criteria, array $orderBy = null)
 * @method Upload[]    findAll()
 * @method Upload[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UploadRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Upload::class);
    }

    public function getCleanPager(): Pager {
        $qb = $this->createQueryBuilder('u')
            ->where('u.isLocked = FALSE')
            ->andWhere('u.touchedAt < :date')
            ->setParameter('date', (new DateTime())->modify('-1 day'))
            ->orderBy('u.touchedAt', 'ASC')
        ;
        return Pager::factory($qb->getQuery(), 1, 100);
    }

    public function getPager($params = [], bool $withCount = true): Pager {
        $qb = $this->createQueryBuilder('u')->orderBy('u.id', 'ASC');
        return Pager::factory($qb->getQuery(), 1, $params['limit'] ?? 100, $withCount);
    }

}
