<?php

namespace App\Http\Controllers\Cliqnshop;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;


class CliqnshopKeywordController extends Controller
{
    public function keyword_search_log_index(Request $request)
    {

    
        $data = DB::connection('cliqnshop')->table('cns_search_log') 
        ->orderBy('created_at','desc')          
            ->get();

        // dd($data);
         
        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()

                ->editColumn('site_code', function ($data) {

                    $countrys_key_value = array(
                        'af' => 'Afghanistan',
                        'al' => 'Albania',
                        'dz' => 'Algeria',
                        'as' => 'American Samoa',
                        'ad' => 'Andorra',
                        'ao' => 'Angola',
                        'ai' => 'Anguilla',
                        'aq' => 'Antarctica',
                        'ag' => 'Antigua and Barbuda',
                        'ar' => 'Argentina',
                        'am' => 'Armenia',
                        'aw' => 'Aruba',
                        'au' => 'Australia',
                        'at' => 'Austria',
                        'az' => 'Azerbaijan',
                        'bs' => 'Bahamas',
                        'bh' => 'Bahrain',
                        'bd' => 'Bangladesh',
                        'bb' => 'Barbados',
                        'by' => 'Belarus',
                        'be' => 'Belgium',
                        'bz' => 'Belize',
                        'bj' => 'Benin',
                        'bm' => 'Bermuda',
                        'bt' => 'Bhutan',
                        'bo' => 'Bolivia',
                        'ba' => 'Bosnia and Herzegovina',
                        'bw' => 'Botswana',
                        'bv' => 'Bouvet Island',
                        'br' => 'Brazil',
                        'io' => 'British Indian Ocean Territory',
                        'bn' => 'Brunei Darussalam',
                        'bg' => 'Bulgaria',
                        'bf' => 'Burkina Faso',
                        'bi' => 'Burundi',
                        'kh' => 'Cambodia',
                        'cm' => 'Cameroon',
                        'ca' => 'Canada',
                        'cv' => 'Cape Verde',
                        'ky' => 'Cayman Islands',
                        'cf' => 'Central African Republic',
                        'td' => 'Chad',
                        'cl' => 'Chile',
                        'cn' => 'China',
                        'cx' => 'Christmas Island',
                        'cc' => 'Cocos (Keeling) Islands',
                        'co' => 'Colombia',
                        'km' => 'Comoros',
                        'cg' => 'Congo',
                        'cd' => 'Congo, the Democratic Republic of the',
                        'ck' => 'Cook Islands',
                        'cr' => 'Costa Rica',
                        'ci' => 'Cote D\'Ivoire',
                        'hr' => 'Croatia',
                        'cu' => 'Cuba',
                        'cy' => 'Cyprus',
                        'cz' => 'Czech Republic',
                        'dk' => 'Denmark',
                        'dj' => 'Djibouti',
                        'dm' => 'Dominica',
                        'do' => 'Dominican Republic',
                        'ec' => 'Ecuador',
                        'eg' => 'Egypt',
                        'sv' => 'El Salvador',
                        'gq' => 'Equatorial Guinea',
                        'er' => 'Eritrea',
                        'ee' => 'Estonia',
                        'et' => 'Ethiopia',
                        'fk' => 'Falkland Islands (Malvinas)',
                        'fo' => 'Faroe Islands',
                        'fj' => 'Fiji',
                        'fi' => 'Finland',
                        'fr' => 'France',
                        'gf' => 'French Guiana',
                        'pf' => 'French Polynesia',
                        'tf' => 'French Southern Territories',
                        'ga' => 'Gabon',
                        'gm' => 'Gambia',
                        'ge' => 'Georgia',
                        'de' => 'Germany',
                        'gh' => 'Ghana',
                        'gi' => 'Gibraltar',
                        'gr' => 'Greece',
                        'gl' => 'Greenland',
                        'gd' => 'Grenada',
                        'gp' => 'Guadeloupe',
                        'gu' => 'Guam',
                        'gt' => 'Guatemala',
                        'gn' => 'Guinea',
                        'gw' => 'Guinea-Bissau',
                        'gy' => 'Guyana',
                        'ht' => 'Haiti',
                        'hm' => 'Heard Island and Mcdonald Islands',
                        'va' => 'Holy See (Vatican City State)',
                        'hn' => 'Honduras',
                        'hk' => 'Hong Kong',
                        'hu' => 'Hungary',
                        'is' => 'Iceland',
                        'in' => 'India',
                        'id' => 'Indonesia',
                        'ir' => 'Iran, Islamic Republic of',
                        'iq' => 'Iraq',
                        'ie' => 'Ireland',
                        'il' => 'Israel',
                        'it' => 'Italy',
                        'jm' => 'Jamaica',
                        'jp' => 'Japan',
                        'jo' => 'Jordan',
                        'kz' => 'Kazakhstan',
                        'ke' => 'Kenya',
                        'ki' => 'Kiribati',
                        'kp' => "Korea, Democratic People's Republic of",
                        'kr' => 'Korea, Republic of',
                        'kw' => 'Kuwait',
                        'kg' => 'Kyrgyzstan',
                        'la' => "Lao People's Democratic Republic",
                        'lv' => 'Latvia',
                        'lb' => 'Lebanon',
                        'ls' => 'Lesotho',
                        'lr' => 'Liberia',
                        'ly' => 'Libyan Arab Jamahiriya',
                        'li' => 'Liechtenstein',
                        'lt' => 'Lithuania',
                        'lu' => 'Luxembourg',
                        'mo' => 'Macao',
                        'mk' => 'Macedonia, the Former Yugoslav Republic of',
                        'mg' => 'Madagascar',
                        'mw' => 'Malawi',
                        'my' => 'Malaysia',
                        'mv' => 'Maldives',
                        'ml' => 'Mali',
                        'mt' => 'Malta',
                        'mh' => 'Marshall Islands',
                        'mq' => 'Martinique',
                        'mr' => 'Mauritania',
                        'mu' => 'Mauritius',
                        'yt' => 'Mayotte',
                        'mx' => 'Mexico',
                        'fm' => 'Micronesia, Federated States of',
                        'md' => 'Moldova, Republic of',
                        'mc' => 'Monaco',
                        'mn' => 'Mongolia',
                        'ms' => 'Montserrat',
                        'ma' => 'Morocco',
                        'mz' => 'Mozambique',
                        'mm' => 'Myanmar',
                        'na' => 'Namibia',
                        'nr' => 'Nauru',
                        'np' => 'Nepal',
                        'nl' => 'Netherlands',
                        'an' => 'Netherlands Antilles',
                        'nc' => 'New Caledonia'
                    );
                    
                    $country_name= isset($countrys_key_value[$data->site_code]) ? $countrys_key_value[$data->site_code] : '';

                    if($data->site_code =="")
                         return '---';
                    else
                        return $data->site_code.' - ['.$country_name.']';
                 })

                ->editColumn('created_at', function ($data) {
                   return $diw=  \Carbon\Carbon::parse($data->created_at)->diffForHumans();
                   
                })
                

                
                ->make(true);
        }

        return view('Cliqnshop.keywordsearch.keyword_search_index');
    }

    public function keyword_search_log_remove(Request $request)
    {
        


        switch ($request->select_timeline) {
            case ('l-1-h'):
                $del_duration  = Carbon::now()->subHours( 1 );
                break;

            case ('l-24-h'):
                $del_duration  = Carbon::now()->subHours( 24 );
                    break;
            
            case ('l-7-d'):
                $del_duration  = Carbon::now()->subDays( 7 );
                    break;   
                    
            case ('l-4-w'):
                $del_duration  = Carbon::now()->subWeeks( 4 );
                    break; 

            case ('all-time'):
                $del_duration  = '1-1-1';
                    break;
            default:
            return back()->with('error', 'Something went wrong!');
            
        }

         
         $res = DB::connection('cliqnshop')->table('cns_search_log')
                                    ->where('created_at', '>=', $del_duration)                                    
                                    ->delete();
        
        // dd($res);

        return back()->with('success', 'Clear Successfull . ( '.$res.' Logs  affected)  ');
    }
}
