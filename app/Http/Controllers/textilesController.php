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

        $csv = Reader::createFromPath(Storage::url('app/'.$path), 'r');

        $csv->setDelimiter("\t");
        $csv->setHeaderOffset(0);

        $stmt = (new Statement())
            ->where(function (array $record) {
                return $record;
            })
            ->offset(0);
            // ->limit(100000);

        $converter = (new XMLConverter())
            ->rootElement('csv')
            ->recordElement('record', 'offset')
            ->fieldElement('field', 'name');
        
        $records = $stmt->process($csv);

        $textiles = [];
      
            $count = 0;
            $tagger = 0;
            foreach($records as $key => $record)
            {
                if(isset($record['id'])) {

                    $record['textile_id'] = $record['id'];
                    unset($record['id']);

                }   

                if($count == 3000) {

                    ++$tagger;
                    $count = 0;
                   
                }

                $textiles[$tagger][] = $record;
               ++$count;
            }
            
            foreach($textiles as $textile)
            {
                 universalTextile::insert($textile);   
            }
            
        return view('textiles.index');
    }

    
}
