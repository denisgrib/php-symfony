<?php

namespace App\Repository;

use App\Entity\Leads;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Leads|null find($id, $lockMode = null, $lockVersion = null)
 * @method Leads|null findOneBy(array $criteria, array $orderBy = null)
 * @method Leads[]    findAll()
 * @method Leads[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeadsRepository extends ServiceEntityRepository
{
    private $criteria;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Leads::class);

        $this->criteria = Criteria::create();
    }

    /**
     * @param int $page
     * @param int $createdBy
     * @param string $status
     * @return array
     */
    public function getLeadsByFilter(int $page = null, int $createdBy = null, string $status = null): array
    {
        $this->pagination($page);
        $this->findByCreatedBy($createdBy);
        $this->findByStatus($status);

        $this->criteria
            ->orderBy(['id' => 'ASC']);

        return $this->matching($this->criteria)->toArray();
    }

    /**
     * @param integer $page
     */
    public function pagination(int $page = null)
    {
        if (!$page || $page < 1) {
            return;
        }

        $cntOnPage = 3;
        $page--;

        $this->criteria
            ->setFirstResult($cntOnPage * $page)
            ->setMaxResults($cntOnPage);
    }

    /**
     * @param int $createdBy
     */
    public function findByCreatedBy(int $createdBy = null)
    {
        if ($createdBy) {
            $this->criteria
                ->andWhere(Criteria::expr()->eq('created_by', $createdBy));
        }
    }

    /**
     * @param string $status
     */
    public function findByStatus(string $status = null)
    {
        if ($status) {
            $this->criteria
                ->andWhere(Criteria::expr()->eq('status', $status));
        }
    }
}
