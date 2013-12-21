<?php

namespace Db\CreatorBundle\Entity;

use Doctrine\ORM\EntityRepository;

class InventorRepository extends EntityRepository{
    
    public function getInventorsCollabs(){
        
        return  $this->createQueryBuilder('i1')
                    ->select('i1.fullName source, i2.fullName target, count(i1.fullName) value')
                    ->innerJoin('i1.patents', 'p')
                    ->innerJoin('p.inventors', 'i2', 'WITH', 'i2.fullName > i1.fullName')
                    ->groupBy('source, target')
                    ->orderBy('value','DESC')
                    ->setMaxResults(300)	// For performance purposes
                    ->getQuery()->getResult();
    }
}
