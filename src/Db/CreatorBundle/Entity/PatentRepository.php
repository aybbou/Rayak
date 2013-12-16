<?php

namespace Db\CreatorBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PatentRepository extends EntityRepository{
    
    public function findAllByPubDate(){
        return $this->createQueryBuilder('p')
                ->orderBy('p.publicationDate','ASC')
            ->getQuery()->getResult();
    }
}
