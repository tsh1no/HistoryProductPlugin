<?php

namespace Plugin\HistoryProduct;

use Eccube\Plugin\AbstractPluginManager;
use Eccube\Entity\Master\DeviceType;
use Symfony\Component\Filesystem\Filesystem;

class PluginManager extends AbstractPluginManager
{

    /**
     * @var string コピー元リソースディレクトリ
     */
    private $origin;

    /**
     * @var string コピー先リソースディレクトリ
     */
    private $target;

    /**
     * @var string コピー元ブロックファイル
     */
    private $originBlock;

    public function __construct()
    {
        // コピー元のディレクトリ
        $this->origin = __DIR__;
        
        //プラグインのコード
        $this->code = 'history_product';
    }

    /**
     * プラグインインストール時の処理
     *
     * @param $config
     * @param $app
     * @throws \Exception
     */
    public function install($config, $app)
    {
        
        
        
    }
    
    /**
     * プラグイン削除時の処理
     *
     * @param $config
     * @param $app
     */
    public function uninstall($config, $app)
    {
        
    }

    /**
     * プラグイン有効時の処理
     *
     * @param $config
     * @param $app
     * @throws \Exception
     */
    public function enable($config, $app)
    {
        
        //プラグイン用のブロックを追加
        $em = $app['orm.em'];
        
        //トランザクションを開始
        $em->getConnection()->beginTransaction();
        
        //デバイスの設定
        $DeviceType = $em->getRepository('Eccube\Entity\Master\DeviceType')
            ->find(DeviceType::DEVICE_TYPE_PC);
        
        //ブロックを追加
        $repository = $em->getRepository('\Eccube\Entity\Block');
        
        $block = $repository->findOrCreate(null, $DeviceType);
        
        $timestamp = date('Y-m-d H:i:s');
        
        $block->setName('最近みた商品一覧');
        $block->setFileName($this->code);
        $block->setDeviceType($DeviceType);
        $block->setCreateDate($timestamp);
        $block->setUpdateDate($timestamp);
        $block->setLogicFlg(1);
        $block->setDeletableFlg(1);
        
        $em->persist($block);
        $em->flush();
        
        $originDir  = $this->origin;
        $originFile = $originDir . '/Resource/template/default/Block/' . $this->code . '.twig';
        
        $targetDir  = $app['config']['block_realdir'];
        $targetFile = $targetDir . '/' . $this->code . '.twig';
        
        //テンプレートファイルをコピー
        $fs = new Filesystem();
        if ($fs->exists($originFile)) {
            $fs->copy($originFile, $targetFile, false);
        }
        
        //トランザクションをコミット
        $em->getConnection()->commit();
        
    }

    /**
     * プラグイン無効時の処理
     *
     * @param $config
     * @param $app
     */
    public function disable($config, $app)
    {
        
        //プラグイン用のブロックを追加
        $em = $app['orm.em'];
        
        //トランザクションを開始
        $em->getConnection()->beginTransaction();
        
        //ブロックを削除
        $block_repository = $em->getRepository('\Eccube\Entity\Block');
        
        //デバイスの設定
        $DeviceType = $em->getRepository('Eccube\Entity\Master\DeviceType')
            ->find(DeviceType::DEVICE_TYPE_PC);
        
        $block = $block_repository->findOneBy(array(
            'file_name' => $this->code,
            'DeviceType' => $DeviceType,
        ));
        
        $block_id = $block->getId();
        
        //実際のファイルの削除
        if ($block->getDeletableFlg() > 0) {
            
            $tplDir = $app['config']['block_realdir'];
            $file = $tplDir . '/' . $this->code . '.twig';
            
            $fs = new Filesystem();
            if ($fs->exists($file)) {
                $fs->remove($file);
            }
            
            $em->remove($block);
            $em->flush();
            
            //EC-CUBEのキャッシュを削除
            \Eccube\Util\Cache::clear($app, false);
        }
        
        //トランザクションをコミット
        $em->getConnection()->commit();
    }
    
    public function update($config, $app)
    {
    }

}
