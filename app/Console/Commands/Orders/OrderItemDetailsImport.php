<?php

namespace App\Console\Commands\Orders;

use App\Models\order\OrderSellerCredentials;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\API\Order\Order;
use App\Services\SP_API\API\Order\OrderItem;

class OrderItemDetailsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:order-item-details-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Order item details for each order';

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
        $order_item = new OrderItem();

        $seller_id_array = OrderSellerCredentials::where('dump_order', 1)->get();

        foreach ($seller_id_array as $value) {

            $seller_id = $value->seller_id;
            $zoho = $value->zoho;
            $courier_partner = $value->courier_partner;
            $source = $value->source;
            $missing_order_id = NULL;

            $whereIn = [
                '407-5076377-6146731',
                '406-7446071-4894728',
                '402-4739822-5011528',
                '171-5651929-9073141',
                '405-7439970-5107555',
                '171-2846350-7524320',
                '405-0636987-7493107',
                '171-8596532-1389114',
                '406-0063059-0745961',
                '405-9298214-7241168',
                '407-4343131-1679548',
                '404-9471474-7867556',
                '406-8970733-6027549',
                '408-0702857-7617134',
                '404-0258069-3913952',
                '406-6519182-7275527',
                '405-3532673-9673135',
                '407-6514406-3945947',
                '408-1001923-7355547',
                '407-9173786-9561944',
                '403-5657776-9819564',
                '403-8842034-9097123',
                '404-0336474-0826742',
                '405-1114288-9979553',
                '407-1198923-4157122',
                '403-5296753-4748309',
                '403-3435770-6009116',
                '171-1119308-7423505',
                '171-8971889-3451554',
                '405-9643575-2089113',
                '405-1329387-4477120',
                '405-9201951-7623520',
                '171-5112380-4325901',
                '404-2532669-4525116',
                '171-1282883-4968340',
                '403-9044934-6376342',
                '403-8863840-8768302',
                '404-0354904-0347572',
                '402-5712039-5925964',
                '404-3359492-2907543',
                '171-0697719-9096312',
                '406-9879393-6437910',
                '402-8576950-5253964',
                '403-4885225-6125105',
                '406-9674970-4876348',
                '404-8884445-9617953',
                '406-5052269-6673165',
                '405-1969188-5357144',
                '408-7407485-2417917',
                '406-8182160-5752328',
                '402-5031316-4920345',
                '404-3920375-9947521',
                '171-4423497-8637155',
                '404-4810327-9353932',
                '171-0284489-2170759',
                '405-8679230-4481128',
                '171-1469056-0201913',
                '404-0647941-0377902',
                '402-6877284-8712331',
                '403-0616773-5083564',
                '403-4671251-8409156',
                '404-4127456-6713919',
                '406-8101903-2809905',
                '407-0918210-8932334',
                '402-5750795-5447518',
                '408-7605313-9876304',
                '171-9908462-0210729',
                '407-0665894-8207534',
                '403-2490799-5325133',
                '408-7425984-2533137',
                '403-0312192-9382751',
                '407-8544250-3922767',
                '404-5533111-9714728',
                '171-8521685-9478734',
                '402-4111944-7676335',
                '408-5873587-2641139',
                '171-2842589-0492358',
                '402-7516513-9714746',
                '403-2418918-5310748',
                '403-6292158-4561153',
                '405-0181134-2372344',
                '407-1369433-9267510',
                '405-2642006-2029150',
                '407-6089597-9000358',
                '403-1939561-0813903',
                '407-1702123-6849905',
                '402-9264817-4053938',
                '171-3576645-0016345',
                '403-1850756-3997132',
                '405-2392018-1905967',
                '406-1605085-7740316',
                '404-3363124-1722713',
                '171-4157322-8597952',
                '402-5835387-3349916',
                '404-0393200-9421917',
                '407-4423353-6263508',
                '407-1790192-4512352',
                '402-7994402-6705961',
                '404-6274138-0079549',
                '408-9030555-4008345',
                '405-9980910-9163537',
                '403-5663312-6089920',
                '406-3814554-0805157',
                '404-0527378-8333911',
                '171-1047916-9147569',
                '408-2085954-8181139',
                '403-4841144-3786706',
                '403-3846120-2743545',
                '408-9420077-6771514',
                '405-5959243-5369945',
                '403-3752021-1676345',
                '405-9591563-8809910',
                '407-0018292-5653106',
                '402-0839284-3673149',
                '404-6195181-9936369',
                '403-0779063-5165951',
                '404-2121886-6909965',
                '403-3600665-5036314',
                '404-7045991-4144352',
                '403-8418737-6509966',
                '408-8296518-6992350',
                '406-5650019-3845121',
                '406-3206363-9738769',
                '408-8610786-7381114',
                '404-7627315-9046741',
                '408-9000052-1095525',
                '407-8965657-3923517',
                '403-5719069-8987536',
                '408-5199803-3465968',
                '407-0951720-5913168',
                '407-2867330-1232311',
                '405-8835736-7070710',
                '171-1118923-4636319',
                '403-6398630-4202711',
                '404-5396112-5333949',
                '406-7749059-9133935',
                '405-3943451-3360327',
                '171-1355906-5504304',
                '404-5300065-1177938',
                '406-6256518-9601140',
                '402-8642525-8244365',
                '404-7687830-6201909',
                '404-8081156-7237118',
                '402-4956092-2291548',
                '408-6572543-1004300',
                '407-6198779-8799562',
                '408-6871803-2527562',
                '406-4358911-5995511',
                '408-7296637-5140344',
                '403-3034587-9734751',
                '402-2325137-5685135',
                '404-3774386-4094748',
                '405-2988443-5145146',
                '171-1971382-8289108',
                '402-7971129-5905931',
                '404-0375385-6022730',
                '171-8639737-8426738',
                '406-4132693-8977132',
                '403-9664229-9993139',
                '408-7694271-1908314',
                '402-1918653-1973160',
                '402-3948618-9451511',
                '402-3562562-6026767',
                '407-2112581-9132327',
                '405-1948676-2605146',
                '408-6775281-7849917',
                '404-1322822-4076311',
                '406-0516356-1057949',
                '408-2119082-6129910',
                '406-9235215-4865111',
                '407-5670094-9288307',
                '403-2084156-8589111',
                '404-4585347-3068351',
                '402-2042921-0121934',
                '407-8128943-5278729',
                '408-4213022-0646703',
                '402-3744113-8221949',
                '406-7664378-2961102',
                '408-9366452-7134758',
                '402-3362721-2848327',
                '171-5527738-0323569',
                '403-5179244-7944353',
                '407-9451171-8658757',
                '171-5111191-0373142',
                '402-5550886-4241142',
                '171-1394831-0277104',
                '405-6673742-9129953',
                '402-1945109-5660369',
                '405-9557525-0633157',
                '402-2613649-0197159',
                '408-9139970-6653945',
                '402-4609252-5938702',
                '403-8324120-8107546',
                '403-4161529-8589952',
                '404-2654244-2584367',
                '407-3975974-6641926',
                '406-6079327-4096357',
                '405-8775347-3753122',
                '402-5311471-9662736',
                '403-2904118-5839517',
                '402-6302696-7037906',
                '404-1245840-7780310',
                '171-0915750-2469153',
                '403-4759589-8325960',
                '407-3923659-3625934',
                '407-2998968-4593118',
                '408-4854868-6186747',
                '171-5050337-5420329',
                '408-0612763-6265951',
                '402-0763328-5455537',
                '402-3350467-4797905',
                '404-3374404-4740352',
                '402-3747766-7064368',
                '402-8525012-6877942',
                '171-1366514-9407550',
                '171-2090184-9553118',
                '171-3876673-0133161',
                '171-1220094-5402719',
                '405-5942452-6292339',
                '171-0453219-3222731',
                '402-1668585-0558746',
                '408-2469001-5757941',
                '408-3608582-9658713',
                '404-5220622-7165966',
                '407-7225745-4036336',
                '407-0534968-6125125',
                '404-5982138-7527562',
                '402-4582168-2592321',
                '408-8352032-9841918',
                '407-6932102-7888361',
                '171-2242077-1273131',
                '404-4583998-8741166',
                '402-3138091-5145947',
                '404-6498687-3062705',
                '403-5976544-2718729',
                '402-9610627-1900369',
                '403-2415802-9725107',
                '406-5360127-4841958',
                '407-2596350-3809904',
                '171-9307260-4208311',
                '404-2383645-7213159',
                '403-6948063-2630713',
                '171-8960116-0602726',
                '405-5759294-5269943',
                '407-5272151-3817915',
                '402-3806683-5442744',
                '404-4878646-0288361',
                '402-2429377-4958734',
                '403-4953898-7064305',
                '171-5716748-0873950',
                '403-0423198-9853157',
                '171-3053388-9341145',
                '407-0144922-6426755',
                '407-9226806-4013967',
                '407-1112057-8721960',
                '406-3931233-1310743',
                '408-4657425-3145161',
                '404-6606992-9877912',
                '402-1092730-2233931',
                '407-4460112-7497120',
                '402-5276757-1657106',
                '405-3373268-2949939',
                '403-7194192-1481927',
                '404-4568663-0287510',
                '404-3761428-5365911',
                '408-0121827-9685946',
                '408-3491037-8844310',
                '402-4275282-4812345',
                '171-5754733-3954709',
                '406-2732282-3870766',
                '405-6438753-8740367',
                '406-3951460-0328357',
                '402-7522286-6262740',
                '171-6043555-7807534',
                '406-9734762-6505118',
                '405-3457153-3881947',
                '171-2582309-5140323',
                '408-4262386-7475547',
                '408-5088810-2277904',
                '404-4415504-9685902',
                '407-6281926-9865111',
                '407-6057362-6187552',
                '406-1803593-1724360',
                '406-0398797-3319559',
                '403-0156006-7515567',
                '407-0414492-5193156',
                '171-7672016-4188355',
                '403-9594510-4657106',
                '402-1135600-4887508',
                '407-6984290-9725933',
                '407-9346026-9565162',
                '406-6904293-4509908',
                '402-5820640-6233100',
                '402-2580038-9135536',
                '171-8767032-7398764',
                '405-2982327-8361119',
                '407-8583457-9016368',
                '403-7016292-0869923',
                '404-5814117-5397160',
                '404-9259137-2551512',
                '402-4537763-4756361',
                '408-8356252-6429921',
                '171-7802292-4933149',
                '405-0153272-0288312',
                '403-5642979-2071554',
                '408-9343789-1141120',
                '408-0281370-2806730',
                '408-1718200-9047531',
                '403-6300574-4592351',
                '402-3173873-6780360',
                '407-2637670-6777169',
                '406-5696030-2921152',
                '404-8651186-8539569',
                '406-4771573-6311537',
                '402-0153118-8685958',
                '408-0169031-8097932',
                '404-6453653-5594753',
                '405-5317502-7908303',
                '408-3820485-4294761',
                '408-1474088-8509127',
                '408-1505352-3606707',
                '402-0163095-5143510',
                '404-2120242-1596358',
                '407-5360600-0384331',
                '407-5974587-4872309',
                '407-6369063-2869936',
                '408-6447840-6937959',
                '403-0076293-9879540',
                '403-7724231-3989124',
                '405-6200693-1648315',
                '407-7410163-3894756',
                '405-2552244-6015537',
                '407-4610045-6033156',
                '402-4502473-9445919',
                '407-3415000-7960320',
                '406-6536792-8586726',
                '404-3427127-8596326',
                '408-9226397-4181931',
                '405-6413184-4951548',
                '171-2096852-3394767',
                '407-0447338-1345162',
                '405-6740564-3225929',
                '402-9158085-3038754',
                '403-9088052-2704309',
                '404-1867771-4234710',
                '405-5840056-5966761',
                '404-6143134-1862714',
                '404-1916872-4295542',
                '404-8495759-6310763',
                '406-6165480-7032345',
                '406-5113706-6899557',
                '406-1649866-6522726',
                '404-8713229-4490717',
                '407-5210656-7663541',
                '403-4848465-8835534',
                '407-7066882-5045954',
                '406-8633622-1985153',
                '405-6224055-3161947',
                '171-8706359-1568306',
                '408-4988209-9879523',
                '404-1216489-6973968',
                '406-0025987-5327530',
                '403-2845207-5195557',
                '406-6590243-7937920',
                '403-9408570-5478723',
                '407-0040565-3885128',
                '402-4323780-5766717',
                '403-0227256-0726762',
                '406-9027538-8397953',
                '404-2463124-0729931',
                '403-3761707-4478726',
                '407-5607490-3519564',
                '403-8782479-0654742',
                '402-5360230-5547505',
                '406-9925050-7525107',
                '407-8199652-7293946',
                '406-1897200-2985102',
                '403-1042158-2070744',
                '171-7632363-3934756',
                '171-5554821-6724312',
                '405-0107191-1169934',
                '407-7540468-9673922',
                '404-0511461-0560357',
                '406-9982568-6148335',
                '404-4030956-2963515',
                '406-7481072-5089126',
                '406-1836780-2361904',
                '171-7699124-5003528',
                '402-4894715-6943564',
                '407-3104174-3444344',
                '171-8334804-1505937',
                '402-3542034-7726743',
                '407-1215883-0031519',
                '402-7558655-9452352',
                '405-5295615-1923530',
                '402-4948032-6301169',
                '406-1284489-6686733',
                '171-3219480-2897919',
                '406-0047799-2088303',
                '404-7000887-1977106',
                '404-1494514-7192337',
                '402-5284027-2895562',
                '404-2948099-4661957',
                '402-4986088-7845941',
                '407-9672723-4727559',
                '171-0866058-2144304',
                '171-9781180-3193904',
                '171-1025273-1197101',
                '406-5449341-7597900',
                '403-1189910-0900331',
                '171-0676049-7008318',
                '408-9102600-4541148',
                '171-8904420-6537966',
                '406-2584459-5877105',
                '407-1806824-7829905',
                '403-0841392-0128335',
                '403-1031826-9743504',
                '402-5824760-6599504',
                '404-4451057-1885153',
                '171-6792368-9656303',
                '405-3634605-0891520',
                '407-1556089-7986735',
                '404-4350032-1812368',
                '405-3027059-0554707',
                '407-6697692-4099562',
                '408-8524791-1669123',
                '408-7268191-3740357',
                '407-2626780-3525960',
                '406-6893930-3417104',
                '405-7094679-2932359',
                '171-6564633-9516360',
                '405-4236291-3103550',
                '171-8307992-6786751',
                '171-4619383-1613143',
                '405-7030580-1201146',
                '406-2943039-8919562',
                '407-2254922-8325915',
                '407-8391555-2849142',
                '402-1416115-0179561',
                '405-4976467-0266739',
                '405-0001936-7632317',
                '406-3525486-2943543',
                '404-7495509-1638700',
                '403-5427597-6197161',
                '402-8670035-6156354',
                '404-0573131-3202744',
                '404-1980556-3867529',
                '408-6457095-1883513',
                '404-2895284-9482718',
                '407-3240298-3095516',
                '404-2105230-9733963',
                '404-9077807-9924364',
                '407-2450832-5058764',
                '405-5547928-5181941',
                '171-9378645-5030719',
                '402-9780779-4037126',
                '402-1932956-3681916',
                '407-8856075-6103516',
                '171-7140124-3526731',
                '404-0999486-1732348',
                '406-2174862-7605168',
                '405-7325160-7272364',
                '405-2804912-0833935',
                '404-8379896-8578710',
                '405-9378758-3885919',
                '406-5028697-3623516',
                '171-5828353-4353162',
                '405-1575119-2414700',
                '407-0196490-4511530',
                '402-2933098-2121136',
                '403-2724511-3281927',
                '406-5686929-3757953',
                '406-5672510-8291553',
                '403-4685218-6557141',
                '171-1984865-7866719',
                '171-3407782-5908351',
                '171-2196354-9213946',
                '405-0978059-5393947',
                '403-0683306-3252337',
                '407-2466618-7397133',
                '402-3031836-1589915',
                '403-8407460-1287557',
                '404-5913122-4182710',
                '402-1877792-4875540',
                '405-5592075-9084331',
                '406-2751513-6720336',
                '402-8027251-6728369',
                '407-4732091-1889940',
                '171-6246965-3800334',
                '405-0564610-1739513',
                '405-8267606-1692363',
                '404-0682226-0021153',
                '405-0021686-2545919',
                '402-1658775-0849127',
                '408-1509125-9315510',
                '402-3408842-0478768',
                '408-4592604-7401934',
                '403-7843727-9723553',
                '408-9713026-9414751',
                '403-5326179-8713913',
                '403-2266908-4854723',
                '407-1570244-0857154',
                '405-1555199-8197910',
                '408-2219046-9228305',
                '405-9893793-3400311',
                '403-7680428-8030767',
                '171-0475112-9292368',
                '408-6262964-3537958',
                '408-2304699-2012343',
                '171-9492773-6541917',
                '407-6825210-1121901',
                '404-3745204-8857112',
                '404-9154415-8556323',
                '402-7284981-0988356',
                '406-0863509-0833909',
                '406-6389002-6187543',
                '407-4257809-5028338',
                '406-5734598-0616362',
                '407-3035077-6351528',
                '408-5917274-6493963',
                '405-8096460-8335519',
                '405-3956009-5488369',
                '403-2264771-0761150',
                '404-0760952-5255561',
                '402-8557086-2814756',
                '171-7168627-1637127',
                '407-8293170-9465128',
                '405-9767170-6349147',
                '407-3059381-6154761',
                '402-5258474-3634741',
                '171-9691845-6027546',
                '171-6049918-9669930',
                '406-2791915-4393110',
                '404-9559524-4566706',
                '407-7880476-4282753',
                '405-7802049-7768301',
                '404-1419093-3892353',
                '403-9190235-9189164',
                '406-8414931-1351565',
                '405-7311152-4243556',
                '403-5172091-2828325',
                '406-2248663-9343527',
                '406-2195055-2183519',
                '408-6878172-5227541',
                '407-9856236-1553102',
                '407-3785773-5247519',
                '404-3739350-5153123',
                '405-0654631-5569927',
                '406-1832368-5782715',
                '406-3404393-0975511',
                '408-7800131-9009148',
                '407-6381144-7813137',
                '402-4255467-4407546',
                '403-0998415-6462761',
                '402-1631833-1767530',
                '405-6521749-4313155',
                '402-4406747-9231550',
                '403-5237189-0672317',
                '402-4755903-5983514',
                '406-9119974-3267557',
                '403-9700206-4042709',
                '403-1535737-3245935',
                '405-1203167-9489149',
                '404-0446099-2660340',
                '407-9046391-9793111',
                '402-2852797-2153133',
                '171-4071005-6563523',
                '405-8530876-2945910',
                '406-9258760-8133160',
                '406-6186649-3231513',
                '171-4652095-6528354',
                '171-9704763-7972343',
                '408-7879158-4515568',
                '403-2546711-7237142',
                '402-2265088-7222759',
                '171-5904032-9046716',
                '404-0022532-0609148',
                '406-6833331-3099505',
                '406-6197503-5557925',
                '407-3256424-4053124',
                '171-1931048-8619533',
                '407-0307200-8873117',
                '406-8567006-8051569',
                '403-4307053-3265936',
                '406-9121346-4023502',
                '402-6738151-7603518',
                '405-1803834-3717911',
                '403-9642952-7601949',
                '404-2043841-0943565',
                '407-2920755-8197938',
                '404-7622479-7384305',
                '403-7408774-5821949',
                '406-5038830-6612342',
                '408-2982055-3448368',
                '403-3868695-6060305',
                '406-1957109-4054700',
                '402-3428183-5732306',
                '403-3490272-0013135',
                '403-3669858-4214733',
                '403-8932658-2649911',
                '403-0393823-5333969',
                '406-1265667-0683561',
                '407-3783272-6597109',
                '406-6363115-5065165',
                '404-4921970-9780333',
                '405-0101884-1484316',
                '402-3418124-6717158',
                '402-2200785-0526701',
                '171-9027554-6548309',
                '403-8375514-5758736',
                '407-7569173-7480357',
                '403-7461098-4613901',
                '171-3043336-1615531',
                '404-5304661-3048306',
                '404-2417311-1590720',
                '406-5663070-1693161',
                '404-7686288-2415548',
                '171-5355289-0552360',
                '403-4951389-2937109',
                '403-3880593-3230737',
                '406-1912078-9449110',
                '407-6845027-9973165',
                '406-3206502-8385138',
                '404-8086502-7605120',
                '405-4261783-3203547',
                '406-5374415-2096357',
                '402-0621014-7362728',
                '402-2525150-0066729',
                '403-8090226-7535506',
                '406-0140035-7917151',
                '408-0850374-6685904',
                '171-8525112-5441900',
                '403-2647020-0769935',
                '403-8487008-5866764',
                '403-1978704-7641938',
                '171-8953300-5329921',
                '407-6511773-7343513',
                '171-8867635-9404340',
                '408-3451830-4688307',
                '405-8739258-4143531',
                '406-8146753-5269146',
                '404-0701707-9889104',
                '407-7952429-0059562',
                '408-5756952-9510725',
                '171-8433923-4508353',
                '408-0509953-2885116',
                '404-1168526-4589125',
                '403-3989902-9765107',
                '171-5094223-9817929',
                '402-0201510-0502746',
                '408-9143231-0472368',
                '403-8333522-7626702',
                '408-4606639-7529961',
                '407-1204775-0193936',
                '408-8149209-6984337',
                '404-5800297-4866760',
                '408-3298789-6367553',
                '404-7550269-4629930',
                '403-6183766-4585902',
                '405-6832525-4916310',
                '403-7442514-7210728',
                '407-7503622-0376368',
                '407-0360438-2661920',
                '406-7381182-9364341',
                '408-0944881-5397112',
                '407-5957161-6749127',
                '404-2961410-2330712',
                '405-1364407-7152344',
                '404-3980095-1213125',
                '404-6553691-1617958',
                '407-6966282-4893968',
                '407-1321160-2640314',
                '171-4134903-6458761',
                '403-4128393-6412354',
                '402-3173612-2509967',
                '404-8979637-6635536',
                '171-6840133-9615550',
                '408-9097023-0467501',
                '405-7073640-5150725',
                '404-2951761-1547513',
                '408-4388856-1929147',
                '403-6235161-9449140',
                '408-9028324-6721139',
                '405-8960878-7849915',
                '403-8595334-8674725',
                '402-9103523-6946747',
                '402-2355192-2929130',
                '408-2851912-1460350',
                '403-3908973-0372318',
                '403-0875039-9491527',
                '404-1329799-7585159',
                '406-7997906-8555552',
                '406-1962729-0673147',
                '408-9739071-5541961',
                '407-6429784-0837120',
                '407-7917230-5351530',
                '405-6416305-4243533',
                '408-5944726-4762748',
                '403-5254160-5121157',
                '171-8590942-5141146',
                '171-3636889-7280353',
                '402-8583871-5627560',
                '402-0175884-9622712',
                '407-6140964-5240358',
                '405-1746613-7873128',
                '406-9983271-1732312',
                '403-2654113-3786748',
                '403-2754550-4395565',
                '402-4765601-3902745',
                '403-7834248-2043511',
                '405-1292140-9245154',
                '402-2469709-0838751',
                '404-5922535-8761960',
                '404-2479079-8199527',
                '407-7722456-0484324',
                '404-7258523-7299545',
                '408-2155255-3857117',
                '404-2171854-7165157',
                '171-8257104-5889919',
                '403-4891777-0891513',
                '404-1497981-9009933',
                '407-4608964-5380362',
                '407-2203914-0429102',
                '402-3034123-2184344',
                '402-0425542-9949151',
                '403-5158028-7581945',
                '171-2399713-0468314',
                '405-5288234-1378701',
                '403-0746189-9900365',
                '406-4063726-3086759',
                '403-3973059-2398724',
                '408-3299742-7239505',
                '402-0815970-4672308',
                '171-8689260-3082764',
                '171-3073243-0664316',
                '402-7612922-5713142',
                '171-9829016-7382724',
                '403-1884528-9695555',
                '404-0806975-2390714',
                '402-3281964-3082700',
                '171-6824896-5325106',
                '404-1850057-4729136',
                '406-2094353-7537169',
                '407-4665074-4517117',
                '402-8028940-5029119',
                '171-0102976-4898735',
                '402-2259932-7265904',
                '403-5811931-0593107',
                '404-6839923-5278733',
                '407-7128918-0733155',
                '171-7556073-4582719',
                '407-6355890-8084339',
                '407-8977787-4554741',
                '408-9820360-4061960',
                '407-5367701-5814707',
                '404-6417534-0041122',
                '402-2146045-0797139',
                '405-6064878-2279542',
                '404-0203682-0520374',
                '403-7370555-1506700',
                '407-7823081-2262751',
                '404-8455456-7621124',
                '408-8609264-1759527',
                '171-7284052-0778763',
                '403-6672839-0206737',
                '402-2800873-8204349',
                '407-4137945-6362736',
                '171-0580193-3091517',
                '408-1564322-0708366',
                '408-3722719-8907549',
                '406-8812899-9630758',
                '408-8981694-8486702',
                '406-1427889-6899520',
                '405-5111619-9826703',
                '408-1179910-0862763',
                '408-3488714-7853956',
                '408-9925456-4278723',
                '403-8119828-3365912',
                '408-2134251-0861106',
                '171-2133990-3370746',
                '404-7742954-2101159',
                '405-2814596-1999548',
                '405-4693049-7111511',
                '404-6720898-3769906',
                '408-7189722-1125134',
                '402-6154030-2438752',
                '405-3635262-5201909',
                '406-6029581-7489915',
                '408-3900743-0965161',
                '403-4132586-8671501',
                '171-5011930-4229938',
                '404-1768586-0007559',
                '407-2127977-9485967',
                '407-2038251-5633134',
                '408-0114750-7655579',
                '407-6424649-1911524',
                '406-8760389-0184365'
            ];

            if ($seller_id == '35') {

                $missing_order_id = DB::connection('order')
                    ->select("SELECT ord.amazon_order_identifier, ord.our_seller_identifier, ord.country
                from orders as ord
                        left join 
                    orderitemdetails as oids on ord.amazon_order_identifier = oids.amazon_order_identifier 
                where
                    oids.amazon_order_identifier IS NULL 
                        AND ord.our_seller_identifier = '$seller_id' 
                        AND ord.order_status != 'Pending' 
                        AND ord.order_status != 'Canceled' 
                        AND ord.amazon_order_identifier IN ($whereIn)
                order by ord.id asc
                limit 1

            ");
            } else {
                $missing_order_id = DB::connection('order')
                    ->select("SELECT ord.amazon_order_identifier, ord.our_seller_identifier, ord.country
                    from orders as ord
                            left join 
                        orderitemdetails as oids on ord.amazon_order_identifier = oids.amazon_order_identifier 
                    where
                        oids.amazon_order_identifier IS NULL 
                            AND ord.our_seller_identifier = '$seller_id' 
                            AND ord.order_status != 'Pending' 
                            AND ord.order_status != 'Canceled' 
                    order by ord.id desc
                    limit 1
                ");
            }
            Log::debug($missing_order_id);
            foreach ($missing_order_id as $details) {

                $country = $details->country;
                $order_id = $details->amazon_order_identifier;
                $aws_id = $details->our_seller_identifier;

                $order_item->OrderItemDetails($order_id, $aws_id, $country, $source, $zoho, $courier_partner);
            }
        }
    }
}
