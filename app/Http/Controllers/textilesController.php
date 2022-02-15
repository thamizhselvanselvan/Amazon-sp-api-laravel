<?php

namespace App\Http\Controllers;

// use RedBeanPHP\R;
use League\Csv\Reader;
use \RedBeanPHP\R as R;
use League\Csv\Statement;
use Illuminate\Http\Request;
use League\Csv\XMLConverter;
use App\Models\universalTextile;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Jobs\universalTextileDataImport;
use Illuminate\Cache\RateLimiting\Limit;

class textilesController extends Controller
{   
    
    public function index(Request $request)
    {  
        
        if($request->ajax()){

            $getData = ['id','textile', 'ean', 'brand','title','size','color','transfer_price','shipping_weight','product_type','quantity'];
            $data = universalTextile::query();
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('textiles_id', function($row){
                    return ($row->textile);
                })
                ->rawColumns(['textiles_id'])
                ->make(true);
        }
        return view('textiles.index');
    }

    public function importTextiles()
    {
        $url ='https://files.channable.com/f8k02iylfY7c5YTsxH-SxQ==.csv';
        $source = file_get_contents($url);
        $path = 'public/universalTextilesImport/textiles.csv';
        Storage::put($path, $source);

        $csv = Reader::createFromPath('../storage/app/'.$path, 'r');
        $csv->setDelimiter("\t");
        $csv->setHeaderOffset(0);

        $stmt = (new Statement())
            ->where(function (array $record) {
                return $record;
            })
            ->offset(0)
            ->limit(1000);

        $converter = (new XMLConverter())
            ->rootElement('csv')
            ->recordElement('record', 'offset')
            ->fieldElement('field', 'name')
        ;
        $records = $stmt->process($csv);

        foreach($records as $key => $record){
            $textiles = [];
            $count=0;
            foreach($record as $key1 => $rec)
            {
                $key1 = lcfirst($key1);
                
                if($count==0){

                    $textiles['textile_id'] = ($rec);
                }
                else{
                    $textiles[$key1] = $rec;
                    
                }
                $count++;
            }
            universalTextile::create($textiles);
        }

        return view('textiles.index');
    }

    
}
