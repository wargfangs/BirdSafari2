<?php

namespace Birds\ObservationsBundle\Pager;

use Birds\ObservationsBundle\Repository\ObservationRepository;
use Doctrine\ORM\QueryBuilder;

class Pager
{

    /**
     * Calcule le nombre de page en fonction du nombre d'objets existant et de la limite d'objets fixés.
     * @param $limit :string | int
     * @param $page : string | int
     * @param $nombreDeResultats : string | int
     * @param $orderBy : string | int
     * @param ObservationRepository $repoObs
     * @param QueryBuilder $qb
     * @return mixed
     */
    function matchPageLimitOrderBy($limit, $page, $orderBy, $nombreDeResultats, ObservationRepository $repoObs, QueryBuilder $qb)
    {
        //Page, limite et ordre
        $limit = intval($limit); $page = intval($page);
        //var_dump("page ". $page);
        //var_dump("limite ". $limit);
        if(!is_int($limit))
        {
            $limit = 5;
            $page = 1;
        }

        if($limit > 100)
            $limit = 100;

        //var_dump(ceil($nombreDeResultats/$limit));

        if(ceil($nombreDeResultats/$limit) < $page)     //Si la page demandée est supérieure au nombre de pages possibles
            $page = ceil($nombreDeResultats/$limit);    //On lui attribue le max
        if($page<1)
            $page=1;

        //var_dump($page);
        $qb = $repoObs->startAt(($page-1)*$limit,$qb);
        $qb = $repoObs->limit($limit,$qb);
        $param['limit']= $limit;

        //Ordonner
        if($orderBy > 4 || $orderBy < 0 ) //0 espèces, 1 date, 2 heures, 3 titre, 4 lieu
            $orderBy = 0;
        $qb = $repoObs->orderBy($orderBy,$qb);
        $param['orderBy'] = $orderBy;
        $param['page'] = $page;
        $param['nPages'] = ceil($nombreDeResultats/$limit);
        $param['nbrRes']=$nombreDeResultats;
        $param['query'] = $qb;
        return $param;
    }
}