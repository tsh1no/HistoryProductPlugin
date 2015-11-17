<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Plugin\HistoryProduct\Controller\Block;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class HistoryProductController
{
    public function index(Application $app, Request $request)
    {
        
        $historyProductIds = $request->cookies->get('history_product');
        
        $repository = $app['orm.em']->getRepository('\Eccube\Entity\Product');
        
        $qb = $repository->createQueryBuilder('p')
            ->innerJoin('p.ProductCategories', 'pct');
        
        if(count($historyProductIds) > 0){
            
            $qb
                ->andWhere($qb->expr()->in('p.id', ':Id'))
                ->setParameter('Id', $historyProductIds);
            
        }
        
        $query = $qb->getQuery();
        
        $historyProductList = $query->getResult();
        
        return $app['view']->render('Block/history_product.twig', array(
            'historyProductList' => $historyProductList,
        ));
    }
}
