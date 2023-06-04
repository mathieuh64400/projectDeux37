<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function save(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


 /**
  * ordonner par premiere lettre les produits
  */
  public function ordonnation(){
    $query= $this->getEntityManager()->createQuery(
        "
        SELECT p FROM App\Entity\Produit p ORDER BY SUBSTRING(p.nom, 1, 1)
        "
    );
  

    return $query->getResult();


}
/**
 * retourne le nombre total de produit
 *
 * 
 * @return void
 */
public function getTotalArticle(){
    $query= $this->createQueryBuilder('p') //p = produit
        ->select('COUNT(p)');
       return $query->getQuery()->getSingleScalarResult();
 
   return $query ->getQuery()->getResult();

}

/**
 * methode pagination des produits
 * @return Void
 */

 public function pagination($page,$limit){
    $query= $this->createQueryBuilder('p');
   
    $query->setFirstResult(($page*$limit)-$limit)
    ->setMaxResults($limit);

    return $query->getQuery()->getResult();
}

/**
 * methode retournant  les recherches sur Produits
 * @return Void
 */
public function search($mot=null){
    $query= $this->createQueryBuilder('p'); //a = articles
   // $query->where() restiction selon critère
   if($mot !=null){
       $query->andWhere('MATCH_AGAINST(p.nom) AGAINST (:mots boolean)>0')
            ->setParameter('mots',$mot);        
   }
   return $query ->getQuery()->getResult();
}    

//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
