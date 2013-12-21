<?php

namespace Db\CreatorBundle\Entity;

use Doctrine\ORM\EntityRepository;

class InventorRepository extends EntityRepository{
    
    public function getInventorsCollabs(){
        
        return  $this->createQueryBuilder('i1')
                    ->select('i1.fullName inv1, i2.fullName inv2, count(i1.fullName) number')
                    ->innerJoin('i1.patents', 'p')
                    ->innerJoin('p.inventors', 'i2', 'WITH', 'i2.fullName > i1.fullName')
                    ->groupBy('inv1, inv2')
                    ->orderBy('number','DESC')
                    ->setMaxResults(300)	// For performance purposes
                    ->getQuery()->getResult();
    }
}
