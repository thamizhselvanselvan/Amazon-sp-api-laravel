<?php

namespace App\Http\Controllers;

// use RedBeanPHP\R;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Http\Request;
use League\Csv\XMLConverter;
use App\Jobs\universalTextileDataImport;
use \RedBeanPHP\R as R;


class importCsvController extends Controller
{
    

    public function importCSV()
    {
       

        $csv = Reader::createFromPath('D:/moshecom/urls.csv', 'r');
        $csv->setDelimiter("\t");
        $csv->setHeaderOffset(0);

        $stmt = (new Statement())
            ->where(function (array $record) {
                return $record;
                })
            ->offset(0)
            ->limit(1000)
        ;

        $converter = (new XMLConverter())
            ->rootElement('csv')
            ->recordElement('record', 'offset')
            ->fieldElement('field', 'name')
        ;
        $records = $stmt->process($csv);
        $dataArray = [];

        R::setup('mysql: host=localhost; dbname=sp-api', 'root', 'root');   
        R::exec('TRUNCATE `textile`');     
        $textiles = R::dispense('textile');
        
        foreach($records as $key => $record){

            foreach($record as $key1 => $rec)
            {
                $dataArray[] = json_encode($rec);
                $textiles->{$key1} = json_encode($rec);
            }
            R::store($textiles);
        }

        // $universalTextileDataImportJobs = new universalTextileDataImport($dataArray);
        // $universalTextileDataImportJobs->dispatch($dataArray);
        
        return $dataArray;



        // $dom = $converter->convert($records);
        // $dom->formatOutput = true;
        // $dom->encoding = 'iso-8859-15';
        //     return $dom;

        // echo '<pre>', PHP_EOL;
        // echo htmlentities($dom->saveXML());
    }
}
