<?php

namespace App\Http\Controllers;

// use RedBeanPHP\R;
use League\Csv\Reader;
use \RedBeanPHP\R as R;
use League\Csv\Statement;
use Illuminate\Http\Request;
use League\Csv\XMLConverter;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Jobs\universalTextileDataImport;
use App\Models\universalTextile;
use Illuminate\Cache\RateLimiting\Limit;

class textilesController extends Controller
{
    
    public function index(Request $request)
    {
        if($request->ajax()){

            $data = universalTextile::limit(10000)->get();
            $getData = ['textile', 'ean', 'brand','title','size','color','transfer_price','shipping_weight','product_type','quantity'];
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('textiles', function($row){
                    return ($row->textile);
                })
                ->rawColumns(['textiles'])
                ->make(true);
        }
        return view('textiles.index');
    }

    public function importTextiles()
    {
        $csv = Reader::createFromPath('D:/moshecom/urls.csv', 'r');
        $csv->setDelimiter("\t");
        $csv->setHeaderOffset(0);

        $stmt = (new Statement())
            ->where(function (array $record) {
                return $record;
                })
            ->offset(143361)
            // ->limit(100000)
        ;

        $converter = (new XMLConverter())
            ->rootElement('csv')
            ->recordElement('record', 'offset')
            ->fieldElement('field', 'name')
        ;
        $records = $stmt->process($csv);
        $dataArray = [];

        R::setup('mysql: host=localhost; dbname=sp-api', 'root', 'root'); 
        // R::debug(TRUE);  
        // R::exec('TRUNCATE `textile`');     
        
        foreach($records as $key => $record){
            $textiles = R::dispense("universalTextiles");
            $count=0;
            foreach($record as $key1 => $rec)
            {
                $key1 = lcfirst($key1);
                
                if($count==0){

                    $textiles->textile = ($rec);
                }
                else{
                    $textiles->{$key1} = $rec;
                    
                }
                $count++;
            }
            R::store( $textiles );
            echo 'done \t';
        }
        
    }

    
}
