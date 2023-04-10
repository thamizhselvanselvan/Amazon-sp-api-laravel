<?php

namespace App\Console\Commands\Cliqnshop;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class asin_remove_from_cliqnshop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:remove_exported_asin {site}{pid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to remove asin from cliqnshop database ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $site = $this->argument('site');
        $pid = $this->argument('pid');

        $request = [
            'site' => $site,
            'pid'   => $pid 
        ]; 

         
        // \Illuminate\Support\Facades\Log::alert($request);
        // return 0 ;

        $domains = [
            'attribute' =>  [
                'table_name' => 'mshop_attribute',
                'direct_delete' => false,
                'isHandled' => false
            ],
            'media' =>  [
                'table_name' => 'mshop_media',
                'direct_delete' => false,
                'isHandled' => false
            ],
            'price' =>  [
                'table_name' => 'mshop_price',
                'direct_delete' => false,
                'isHandled' => false
            ],
            'text' =>  [
                'table_name' => 'mshop_text',
                'direct_delete' => false,
                'isHandled' => false
            ],
            'supplier' =>  [
                'table_name' => 'mshop_supplier',
                'direct_delete' => false,
                'isHandled' => false
            ],
            'keyword' =>  [
                'table_name' => 'mshop_keyword',
                'direct_delete' => false,
                'isHandled' => false
            ],
        ];

        foreach ($domains as $key => $value) 
        {
            $domain = $key ;
            $table_name = $value['table_name']; 
            $direct_delete = $value['direct_delete']; 

            // \Illuminate\Support\Facades\Log::alert('table_name:'. $table_name . ',domain:'.$domain. 'direct_delete:'.$direct_delete);

        
            $mshop_product_list = DB::connection('cliqnshop')->table('mshop_product_list')
            ->where(['parentid' => $pid, 'domain' => $domain,  'siteid' => $site])
            ->select('refid','domain')->get();

            

                if($this->byAsinDomainRemover($domain,$table_name,$direct_delete,$request,$mshop_product_list))
                {                    
                    DB::connection('cliqnshop')->table('mshop_product_list')
                    ->where(['parentid' => $pid, 'domain' => $domain,  'siteid' => $site])
                    ->delete();              
                    
                }

                $value['isHandled'] = true;
                $domains[$domain] = $value;
                        
                    
        }

        // removing mshop_product[main table] if all the domains are handled --start
            $allHandled = true;
            foreach ($domains as $domain ) 
            {
                if(!$domain['isHandled'])
                {
                    $allHandled = false ;
                    break;
                }            
            }
            if($allHandled)
            {

                $qryMshopStockRemove = DB::connection('cliqnshop')->table('mshop_stock')
                ->where(['prodid' => $pid, 'siteid' => $site])
                ->delete();

                $qryMshopProductRemove = DB::connection('cliqnshop')->table('mshop_product')
                ->where(['id' => $pid, 'siteid' => $site])
                ->delete();
                
                \Illuminate\Support\Facades\Log::info('product with id: '.$request['pid']. ',is removed From Site : '.$request['site'] );
            }
        // removing mshop_product[main table] if all the domains are handled --end 
        
        return 0;
    }


    public function byAsinDomainRemover($domain,$table_name,$direct_delete,$request,$mshop_product_list) :bool 
    {
        
        if ($direct_delete) 
        {

            foreach ($mshop_product_list as $singleItem ) 
            {
                $refId = $singleItem->refid;
                $site = $request['site'];
                $domainListRemoverQry =  DB::connection('cliqnshop')->table($table_name)
                        ->where(['id' => $refId, 'siteid' => $site])
                        ->delete();
                                
            }
            return true;
           
        }
        else
        {
            foreach ($mshop_product_list as $singleItem ) 
            {
                $refId = $singleItem->refid;
                $site = $request['site'];
                $productId = $request['pid'];

                $domainListRemoverQry = DB::connection('cliqnshop')->table('mshop_product_list')
                ->where([ 'domain' => $domain,  'siteid' => $site , 'refid' => $refId])
                ->whereNotIn('parentid' , [$productId])
                ->select('refid','domain','parentid')->get();

                if(count($domainListRemoverQry)==0)
                {
                    DB::connection('cliqnshop')->table($table_name)
                        ->where(['id' => $refId, 'siteid' => $site])
                        ->delete();
                }
            }

            return true;

        }

        return false;
        
    }

    
}
