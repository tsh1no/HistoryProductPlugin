<?php
namespace Plugin\HistoryProduct;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class HistoryProductEvent
{

    /** @var  \Eccube\Application $app */
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function onRenderHistoryProductProductDetailBefore(FilterResponseEvent $event){
        
        //リクエストオブジェクトの取得
        $request = $event->getRequest();
        
        //レスポンスオブジェクトの取得
        $response = $event->getResponse();
        
        //商品IDを取得
        $id = intval($request->get('id'));
        
        $historyProductIds = array();
        
        //cookieの値を取得
        $historyProductIds = $request->cookies->get('history_product');
        
        $newHistoryProductIds   = array();
        $newHistoryProductIds[] = $id;
        
        if(count($historyProductIds) > 0 && is_array($historyProductIds)) {
            
            foreach($historyProductIds as $index => $product_id) {
                
                //最近みた商品は4件まで
                if($product_id != $id && count($newHistoryProductIds) < 5) {
                    $newHistoryProductIds[] = $product_id;
                }
                
            }
            
        }
        
        if(count($newHistoryProductIds) > 0){
            
            foreach($newHistoryProductIds as $index => $product_id) {
                $name = 'history_product[' . $index .  ']';
                $response->headers->setCookie(new Cookie($name, $product_id, 0, '/', null, false, false));
            }
            
        }
        
    }
    
}
