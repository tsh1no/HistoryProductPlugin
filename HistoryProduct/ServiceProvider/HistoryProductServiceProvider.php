<?php

namespace Plugin\HistoryProduct\ServiceProvider;

use Eccube\Application;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

class HistoryProductServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        
        $app->match('/block/historyproduct', '\Plugin\HistoryProduct\Controller\Block\HistoryProductController::index')->bind('block_history_product');
        
    }

    public function boot(BaseApplication $app)
    {
    }
}
