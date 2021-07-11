<?php

namespace DelPlop\PbnBundle\Repository;

use DelPlop\PbnBundle\Entity\Category;
use DelPlop\PbnBundle\Entity\Note;
use App\Entity\ApplicationUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function searchNotes(ApplicationUser $user, string $searchTerm): ArrayCollection
    {
        $terms = explode(' ', $searchTerm);
        $qb = $this
            ->createQueryBuilder('n');
        foreach ($terms as $term) {
            $qb->orWhere('n.title like :term')
                ->orWhere('n.content like :term')
                ->setParameter('term', '%'.$term.'%');
        }
        $qb
            ->andWhere('n.user = :user')
            ->setParameter('user', $user)
            ->orderBy('n.id', 'DESC');

        return new ArrayCollection($qb->getQuery()->getResult());
    }

    public function getNotesByCategoryAndUser(Category $category, ApplicationUser $user): array
    {
        $qb = $this
            ->createQueryBuilder('n')
            ->join('n.noteCategories', 'nc', 'with', 'nc.category = :cat')
            ->andWhere('n.user = :user')
            ->setParameter('cat', $category)
            ->setParameter('user', $user)
            ->orderBy('nc.position', 'asc');

        return $qb->getQuery()->getResult();
    }
}
