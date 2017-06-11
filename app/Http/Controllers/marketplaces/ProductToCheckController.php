<?php

namespace Sweet\Http\Controllers\marketplaces;

use Illuminate\Http\Request;
use Sweet\Http\Requests;
use Sweet\Http\Controllers\Controller;

use Sweet\Http\Controllers\OrdersController;

use Sweet\ML\Auth;
use Sweet\MlProductsToCheck; //AC
use Sweet\MlOffset; //AC
use Sweet\MlProducts;
use Sweet\Product\Products;
use Sweet\Http\Controllers\StockController;


class ProductToCheckController extends Controller
{
	private $sellerId = '184712077';
	private $client_id = '2778368062781833';
	private $client_secret = '6UqSmKOn0eKRVAE7GYyIORQyGXlAijLy';
	private $url = 'https://api.mercadolibre.com/';
	private $token;
	private $curl;
    private $orderController;
    private $stocks;

	public function __construct(){
        $this->curl = new \anlutro\cURL\cURL;
        $this->auth();

//        $this->orderController = new OrdersController;
//        $token = Auth::first();
        $this->stocks = new StockController;
	}

    public function login(){
        return view('admin.loginml');
    }
	public function auth(){
		$auth = Auth::first();
		if(round(abs(strtotime($auth->updated_at) - time()) / 60) >= 300){
			$url = $this->url.'oauth/token?grant_type=refresh_token&client_id='.$this->client_id.'&client_secret='.$this->client_secret.'&refresh_token='.$auth->refresh_token;

			$response = $this->curl->newRequest('post', $url)
                    ->setOption(CURLOPT_SSL_VERIFYPEER, 0)
                    ->setOption(CURLOPT_SSL_VERIFYHOST, 0)
                    ->send();                                
			$data = json_decode($response->body);
			$auth->access_token = $data->access_token;
			$auth->refresh_token = $data->refresh_token;
			$auth->save();
            $this->token = $data->access_token;
		}else{
            $this->token = $auth->access_token;
        }

    }

    public function callback(Request $request){
        dd($request);
    }
    public function redirect(Request $request){
        $code =$request->input('code');
        $url = 'https://api.mercadolibre.com/oauth/token?grant_type=authorization_code&client_id='.$this->client_id.'&client_secret='.$this->client_secret.'&code='.$code.'&redirect_uri=https://fornecedores.fullhub.com.br/ml/redirect';

        $response = $this->curl->newRequest('post', $url)
                                ->send();
        $response = json_decode($response);
        $auth =  Auth::first();
        $auth->access_token = $response->access_token;
        $auth->refresh_token = $response->refresh_token;
        if($auth->save()){
            return \Redirect::back()->with(['msg', 'Acesso concluido!']);
        }

    }


    public function myGetUpdated(){
         $items = $this->stocks->getUpdated();
         if(!$items){
            return 'Nenhum Produto para Atualizar';
         }
         dd($items);
     }
/*
    public function stock(){
         $items = $this->stocks->getUpdated();
         if(!$items){
            return 'Nenhum Produto para Atualizar';
         }

var_dump($items);
         foreach($items as $item){
            $mlb = MlProducts::where('sku',$item['sku'])->first();


            if(count($mlb) > 0){
                $product = Products::with('prices')->where("sku", $mlb->sku)->first();
                $data['available_quantity'] = $item['total'];
                $data['price'] = $product->prices[0]->price;

                if ($item['total']==0) {
                    $data['status'] = 'paused';
                } else{
                    $data['status'] = 'active';
                }

                $url = 'https://api.mercadolibre.com/items/'.$mlb->mlb.'?access_token='.$this->token;
                $request = $this->curl->newJsonRequest('put', $url, $data)
    ->setOption(CURLOPT_SSL_VERIFYPEER, 0)
    ->setOption(CURLOPT_SSL_VERIFYHOST, 0)
                  ->send();
var_dump($request);
                if($request->statusCode == 200){
                    echo $mlb->mlb.' Estoque Alterado para '.$item['total']."\n";
                    flush();

                }
            }
         }
    }
*/

    //AC BEG
    public function apiHelp(){

        var_dump(date('Y-m-d H:i:s'));
        ini_set('max_execution_time', 200);
        $url = 'https://api.mercadolibre.com/items';
        $request = $this->curl->newJsonRequest('get', $url) 
                ->setOption(CURLOPT_SSL_VERIFYPEER, 0)
                ->setOption(CURLOPT_SSL_VERIFYHOST, 0)
                              ->send();
        $help = json_decode($request->body);
        dd($help);
    }


    public function getMlItems(){
        var_dump(date('Y-m-d H:i:s'));
        ini_set('max_execution_time', 4700);
        
        unset($offSet);

        unset($offSetRec);

        $offSetRec = (MlOffset::all())[0];
        $offSet = $offSetRec['offset'];

        $offSetRec->offset = $offSet + 1000;
        
        $offSetRec->save();
        var_dump(date('Y-m-d H:i:s').' => SAVED');

echo 'offSet'.$offSet."\n";

        flush();
        ob_flush();
        $this->getMlItemsProc($offSet);

        dd($offSet);

    }


    public function getMlItemsProc(int $offSet){
        var_dump(date('Y-m-d H:i:s').' => '.$offSet);
        $myOffSet = $offSet;
        ini_set('max_execution_time', 4700);
        for ($i=0; $i<20; $i++) {
            $url = $this->url.'users/'.$this->sellerId.'/items/search?limit=50&offset='.$myOffSet.'&access_token='.$this->token;
            unset($request);
            unset($prodList);
            $request = $this->curl->newJsonRequest('get', $url)
                ->setOption(CURLOPT_SSL_VERIFYPEER, 0)
                ->setOption(CURLOPT_SSL_VERIFYHOST, 0)
                              ->send();

            $prodList = json_decode($request->body)
                        ->results;

            if (empty($prodList)) {
                dd('No Records to process');
            }

            foreach($prodList as $prod){
                unset($MlProductsToCheck);
                unset($p);
                $p = $this->getProduct($prod);

                $MlProductsToCheck = new MlProductsToCheck();
                $MlProductsToCheck->sku         = $prod;
                if (!$p) {
                    $MlProductsToCheck->status      = 'Not Found by API';
var_dump($p->status.' => '.$prod);
                    if ($p->title) {
                        $MlProductsToCheck->title = $p->title;
                    }
                    if ($p->available_quantity) {
                        $MlProductsToCheck->quantity = $p->available_quantity;
                    }
                    if ($p->price) {
                        $MlProductsToCheck->price = $p->price;
                    }
                    if ($p->permalink) {
                        $MlProductsToCheck->url = $p->permalink;
                    }
                    if ($p->status) {
                        $MlProductsToCheck->status = $p->status;
                    }

                } else {
                    if ($p->status == 'under_review') {
var_dump($p->status.' => '.$prod);
                        if ($p->title) {
                            $MlProductsToCheck->title = $p->title;
                        }
                        if ($p->available_quantity) {
                            $MlProductsToCheck->quantity = $p->available_quantity;
                        }
                        if ($p->price) {
                            $MlProductsToCheck->price = $p->price;
                        }
                        if ($p->permalink) {
                            $MlProductsToCheck->url = $p->permalink;
                        }
                        if ($p->status) {
                            $MlProductsToCheck->status = $p->status;
                        }
                    } else {
                        $MlProductsToCheck->title       = $p->title;
                        $MlProductsToCheck->quantity    = $p->available_quantity;
                        $MlProductsToCheck->price       = $p->price;
                        $MlProductsToCheck->url         = $p->permalink;
                        $MlProductsToCheck->status      = $p->status;
                    }
                }
                $MlProductsToCheck->offset = $myOffSet;
                $MlProductsToCheck->save();
            }

            var_dump($myOffSet);
            $myOffSet=$myOffSet + 50;
        }
        dd(date('Y-m-d H:i:s'));

    }

    public function getProduct($mlb){
        $url = $this->url.'items/'.$mlb.'?access_token='.$this->token;

        $response = $this->curl->newRequest('get', $url)
                ->setOption(CURLOPT_SSL_VERIFYPEER, 0)
                ->setOption(CURLOPT_SSL_VERIFYHOST, 0)
                ->send();
        if($response->statusCode == 200){
            $response = json_decode($response->body);
            return $response;
        }else{
            return false;
        }

    }

    public function getProductTest(){
$mlb = 'MLB826980754';
        $url = $this->url.'items/'.$mlb.'?access_token='.$this->token;

        $response = $this->curl->newRequest('get', $url)
                ->setOption(CURLOPT_SSL_VERIFYPEER, 0)
                ->setOption(CURLOPT_SSL_VERIFYHOST, 0)
                ->send();

        $response = json_decode($response->body);
        dd($response);
    }


    public function getInfo(){

/*
https://api.mercadolibre.com/users/{seller_id}/items/search?access_token=$ACCESS_TOKEN

$url = 'https://api.mercadolibre.com/users/'.$this->sellerId.'/items/search?';


$url = 'https://api.mercadolibre.com/users/'.$this->sellerId.'/items/search';

*/

/* Okay to retrieve by category, it has retrieved 10 lines at once
$url = 'https://api.mercadolibre.com/sites/MLB/search?seller_id='.$this->sellerId.'&category=MLB1620';

Okay to retrieve by seller, it has retrieved 50 lines at once
$url = 'https://api.mercadolibre.com/sites/MLB/search?seller_id='.$this->sellerId;
id,title,price,available_quantity,status

https://api.mercadolibre.com/items?ids={Item_id1},{Item_id2}&attributes={attribute1,attribute2,attribute3

*/

//&attributes={attribute1,attribute2,attribute3}

$url = 'https://api.mercadolibre.com/sites/MLB/search?seller_id='
    .$this->sellerId
    .'&offset=50'
    ;
$request = $this->curl->newJsonRequest('get', $url)
    ->setOption(CURLOPT_SSL_VERIFYPEER, 0)
    ->setOption(CURLOPT_SSL_VERIFYHOST, 0)
                  ->send();
dd($request);
$obj = json_decode($request->body);
//dd($obj);



//for( $i = 0; $i<5; $i++ ) {
var_dump(date('Y-m-d H:i:s'));
ini_set('max_execution_time', 600);
        $mlItems = ProdToCheck::where('status_ml', 'to_check')
            ->limit(10)
            ->get();
        if(!$mlItems){
            return 'Mais nenhum Produto para Verificar';
         }

        foreach($mlItems as $item){
            $url = 'https://api.mercadolibre.com/items/'.$item['mlb_sku'];
            $request = $this->curl->newJsonRequest('get', $url)
                ->setOption(CURLOPT_SSL_VERIFYPEER, 0)
                ->setOption(CURLOPT_SSL_VERIFYHOST, 0)
                              ->send();
            $obj = json_decode($request->body);
//dd($obj);

//if (!$obj->available_quantity) {
//    dd($obj);    
//}
            if ($obj->status == 'under_review'
                OR !$obj->available_quantity) {
                $mlProd = ProdToCheck::where('id',$item['id'])
                            ->update(
                                array( 
                                'check_ml' => date('Y-m-d H:i:\00'),
                                'status_ml' => $obj->status,
                                )
                                );
            } else {

                $mlProd = ProdToCheck::where('id',$item['id'])
                            ->update(
                                array( 
                                'check_ml' => date('Y-m-d H:i:\00'),
                                'stock_ml' => $obj->available_quantity,
                                'price_ml' => $obj->price,
                                'status_ml' => $obj->status,
                                )
                                );                
            }
        }
dd(date('Y-m-d H:i:s'));

//sleep(110);
//unset($mlItems);
//}
//dd(date('Y-m-d H:i:s'));
  
         $items = $this->stocks->getUpdated();

var_dump($items);

         if(!$items){
            return 'Nenhum Produto para Atualizar';
         }
         foreach($items as $item){
            $mlb = MlProducts::where('sku',$item['sku'])->first();

            if(count($mlb) > 0){
                $url = 'https://api.mercadolibre.com/items/'.$mlb->mlb;

var_dump($url);
                $request = $this->curl->newJsonRequest('get', $url)
    ->setOption(CURLOPT_SSL_VERIFYPEER, 0)
    ->setOption(CURLOPT_SSL_VERIFYHOST, 0)
                  ->send();
dd($request);
            }
         }
    }

}
