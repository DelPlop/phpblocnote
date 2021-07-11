<?php

namespace DelPlop\PbnBundle\Repository;

use DelPlop\PbnBundle\Entity\Category;
use DelPlop\PbnBundle\Entity\Note;
use DelPlop\PbnBundle\Entity\NoteCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NoteCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method NoteCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method NoteCategory[]    findAll()
 * @method NoteCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NoteCategory::class);
    }

    public function updatePositionByNoteAndCategory(Note $note, Category $category, int $position): void
    {
        $qb = $this
            ->createQueryBuilder('nc')
            ->update(NoteCategory::class, 'nc')
            ->set('nc.position', ':position')
            ->andWhere('nc.note = :note')
            ->andWhere('nc.category = :category')
            ->setParameter('position', $position)
            ->setParameter('note', $note)
            ->setParameter('category', $category)
        ;
        $qb->getQuery()->execute();
    }
}
