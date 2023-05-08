<?php 


namespace App\Services\Cliqnshop;

use League\Csv\Writer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CSV_Exporter
{   
    private  $csv_head = array() ;
    private  $csv_body = array();

    private $file_path ;
    private $file_name_prefix = 'export_';

    public function set_file_path($file_path)
    {
        $this->file_path = $file_path ;
    }

    public function set_file_name_prefix($file_name_prefix)
    {
        $this->file_name_prefix = $file_name_prefix ;
    }

    public function set_csv_head(Array $csv_head)
    {
        $this->csv_head = $csv_head ;
    }

    public function set_csv_body(Array $csv_body)
    {
        array_push($this->csv_body,$csv_body);
    }
    
    public function generate()
    {

        $csv_head = $this->csv_head;
        $csv_body = $this->csv_body;        

        // building/updating csv hader --start
            $valuesToReplace = ['image1','image2','image3','image4','image5','image6','image7','image8','image9','image10'];
            $csv_head = $this->ArrayContentReplacer(array:$csv_head , valuesToReplace:$valuesToReplace, target: 'image', isKey : false);            
        // building/updating csv hader --end 
        
        // building/updating csv body --start
            foreach ($csv_body as $key => $value) {                
                
                //  image array  builder  --start
                    $csvRow = $csv_body[$key];
                    $image =  $csvRow[13]; 
                    $asin =  $csvRow[2]; 
                    $imagesArray = $this->arrayStretch(array : $image[$asin] , totalElemens : 10 );
                    $csv_new_row = $this->ArrayContentReplacer(array:$csvRow , valuesToReplace:$imagesArray, target: 13, isKey : true);
                    $csv_body[$key] = $csv_new_row ;
                //  image array  builder  --end

                // generic keyword imploder --start
                    $csvRow = $csv_body[$key];
                    $generic_keyword_row_index = 26; 
                    $generic_keywords = $csvRow[$generic_keyword_row_index];
                    
                    $generic_keywords =  implode(",", array_map(function($item){
                        return implode ('', $item);
                    },$generic_keywords));   
                    
                    $generic_keywords_array = [ 0 => $generic_keywords];
                    $insertable_value = $this->ArrayContentReplacer(array:$csvRow , valuesToReplace:$generic_keywords_array, target: $generic_keyword_row_index, isKey : true);
                    $csv_body[$key] = $insertable_value ;
                // generic keyword imploder --end  
                
                //code to replace the siteid with sitecode data --start 
                    $site = $csv_body[$key][0]; 
                    $siteQry = DB::connection('cliqnshop')->table('mshop_locale_site')->select('code')->where('siteid', $site)->first();
                    $site = $siteQry->code;
                    $csv_body[$key][0] = $site;
                //code to replace the siteid with sitecode data --end
                    unset($csv_body[$key][23]); //removing keywrd coloumn
                    unset($csv_body[$key][24]); //removing short_desciption coloumn
                    unset($csv_body[$key][27]); //removing editor coloumn
                    unset($csv_body[$key][28]); //removing status coloumn
                                    
            }
        // building/updating csv body --end
    
            $maxRowsForSingleFile = 1000000 ;
            $offset = 1;       

            $csv_body_chunks =  array_chunk ($csv_body,$maxRowsForSingleFile) ;
            foreach ($csv_body_chunks as $csv_body_chunk) 
            {
                $file_name = $this->file_name_prefix .  date('Y-m-d_H-i-s') .'_file-'. $offset.".csv" ; 
                $exportFilePath = $this->file_path . $file_name;
                if (!Storage::exists($exportFilePath)) {
                    Storage::put($exportFilePath, '');
                }

                $csv_writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
                $csv_writer->insertOne($csv_head);
                $csv_writer->insertAll($csv_body_chunk);                
                $offset ++;
            }   
    }

    public function ArrayContentReplacer(Array $array, Array $valuesToReplace, $target ,bool $isKey = true) :Array
    {        
        if( $isKey)
        {   
            $index = $target;
            if ($index !== false) {
                array_splice($array, $index, 1, $valuesToReplace);
            }
            return $array;
        }
        else
        {
            $index = array_search($target, $array);
            if ($index !== false) {
                array_splice($array, $index, 1, $valuesToReplace);
            }
            return $array;
        }        
    }

    public function arrayStretch(Array $array , int $totalElemens ) :Array
    {        
        $filteredArray = array_values(array_filter($array)); 
        $newArray =  Array();
        for ($i=1; $i <= $totalElemens; $i++) 
        { 
            if(array_key_exists($i-1 , $filteredArray))
                $newArray[$i] = $filteredArray[$i-1];
            else
                $newArray[$i] = '';
        }
        
        return $newArray ;
    }

}

