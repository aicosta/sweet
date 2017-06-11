<?php

namespace Sweet\Http\Controllers\marketplaces;

use Illuminate\Http\Request;
use Sweet\Http\Requests;
use Sweet\Http\Controllers\Controller;

use Sweet\Http\Controllers\OrdersController;

use Sweet\Orders\Order;
use Sweet\invoice;
use Sweet\ML\Auth;
use Sweet\MlProducts;
use Sweet\Product\Products;
use Sweet\Product\ProductExternalStock;
use Sweet\Http\Controllers\StockController;
use Sweet\Http\Controllers\PriceController;

class MercadoLivreController extends Controller
{
	private $sellerId = '184712077';
	private $client_id = '2778368062781833';
	private $client_secret = '6UqSmKOn0eKRVAE7GYyIORQyGXlAijLy';
	private $url = 'https://api.mercadolibre.com/';
	private $token;
	private $curl;
    private $orderController;
    private $stocks;
    private $prices;

	public function __construct(){
		$this->curl = new \anlutro\cURL\cURL;
        $this->auth();
        $this->orderController = new OrdersController;
		$token = Auth::first();
        $this->stocks = new StockController;
        $this->prices = new PriceController;
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
    public function orders(){

    	$url = $this->url.'orders/search/recent?seller='.$this->sellerId.'&access_token='.$this->token;
    	$response = $this->curl->newRequest('get', $url)
								->send();
			$data = json_decode($response->body);
			$limit = 50;
			$total = floor($data->paging->total/$limit);
			ob_start();
			for($n = 0; $n<= $total; $n++){
				if($n == 0){
					$data =  $data->results;
				}else{
					$url = $this->url.'orders/search/recent?seller='.$this->sellerId.'&access_token='.$this->token.'&offset='.$limit*$n;
			    	$response = $this->curl->newRequest('get', $url)
											->send();
					$data = json_decode($response->body);
					$data =  $data->results;
				}
				echo 'Página'.$n."\n";
				flush();
				ob_flush();
				foreach($data as $order){
    				$customer['name'] = $order->buyer->first_name.' '.$order->buyer->last_name;
    				$customer['document'] = (isset($order->buyer->billing_info->doc_number))?$order->buyer->billing_info->doc_number:'';
    				$customer['document2'] = '';
    				$customer['type'] = 'PF';
    				$customer['email'] = $order->buyer->email;
    				$customer['phone'] = $order->buyer->phone->area_code.' '.$order->buyer->phone->number;
    				$customer['phone2'] = '';
					if(isset($order->shipping->receiver_address->zip_code)){
						$customer['zip_code'] = $order->shipping->receiver_address->zip_code;
						$customer['address'] = $order->shipping->receiver_address->street_name;
						$customer['number'] = $order->shipping->receiver_address->street_number;
						$customer['complement'] = (isset($order->shipping->receiver_address->additionalInfo))?$order->shipping->receiver_address->additionalInfo:'';
						$customer['quarter'] = 'Nao Informado';
						$customer['reference'] = '';
						$customer['city'] = $order->shipping->receiver_address->city->name;
						$customer['state'] = substr($order->shipping->receiver_address->state->id,-2);
					}else{
						$customer['zip_code'] = 'n info';
						$customer['address'] = 'n info';
						$customer['number'] = 'n info';
						$customer['complement'] = 'n info';
						$customer['quarter'] = 'n info';
						$customer['reference'] = 'n info';
						$customer['city'] = 'n info';
						$customer['state'] = 'nf';
					}

					$ordernew['code']= $order->id;
					$ordernew['total'] = $order->total_amount;
					$ordernew['freight'] = (isset($order->shipping->cost))?$order->shipping->cost:0;
					$ordernew['comission'] = 0;
					$ordernew['origin'] = 'MERCADO LIVRE';
					$ordernew['max_date'] = date('Y-m-d$ H:i:s', strtotime($order->expiration_date));
                    $ordernew['envio'] = $order->shipping->shipping_mode;

                    $item[0]['sku'] = $this->getSkuByMLB($order->order_items[0]->item->id);
                    $item[0]['name'] = $order->order_items[0]->item->title;
                    $item[0]['price'] = $order->order_items[0]->unit_price;
                    $item[0]['quantity'] = $order->order_items[0]->quantity;
                    //$item[0]['orders_id'] = $ordernew->id;

                    $this->orderController->create($customer, $ordernew, $item);
				}
		}
    }

    public function getSkuByMLB($mlb){
        $data = MlProducts::where('mlb',$mlb)->get();
        if(count($data) > 0){
            return $data[0]->sku;
        }else{
            return 'verify';
        }
    	/*$url = 'https://api.mercadolibre.com/items/'.$mlb.'/description';
    	$response = $this->curl->newRequest('get', $url)
											->send();
		$response = json_decode($response);
        if(isset($response->text)){
            $sku = explode('Cod.', $response->text);
            if(isset($sku[1])){
                $sku = explode('</p>', $sku[1]);
                return trim($sku[0]);
            }else{
                $sku = explode('Cod:', $response->text);
                if(isset($sku[1])){
                    $sku = explode('</strong>', $sku[1]);
                    return trim($sku[0]);
                }else{
                    return 'verify';
                }

            }
        }else{
            return 'verify';
        }*/
    }



    public function getShippingId($code){
    	$url = 'https://api.mercadolibre.com/orders/search?seller='.$this->sellerId.'&q='.$code.'&access_token='.$this->token;
    	$response = $this->curl->newRequest('get', $url)
											->send();
		$response = json_decode($response);
		return($response->results[0]->shipping->id);


    }
    public function sendMe2(){
    	$orders = Order::with('invoice','customer')
    					->where('order_statuses_id',2)
    					->where('envio','me2')
    					->orderBy('code','desc')
    					->get();
    	foreach($orders as $o){

    		if(isset($o->invoice->key) && $o->invoice->key != ''){
    			echo $o->code.'<br />';
    			$data['fiscal_key'] = $o->invoice->key;
    			$data['additional_data']['cfop'] = ($o->customer->state == 'SP')?'5102':'6102';

    			$shippingid = $this->getShippingId($o->code);
    			$url = 'https://api.mercadolibre.com/shipments/'.$shippingid.'/invoice_data?access_token='.$this->token.'&siteId=MLB';

				$response = $this->curl->jsonPost($url, $data);
				$response = json_decode($response);

    		}else{
    			echo 'o';
    		}

    	}

    }

    public function fixSku(){
        $orders = OrderItem::where('sku','')->get();
        foreach($orders as $o){
            $order = Order::find($o->orders_id);
            $url = $this->url.'orders/search/recent?seller='.$this->sellerId.'&q='.$order->code.'&access_token='.$this->token;

            $response = $this->curl->newRequest('get', $url)
                                    ->send();
            $response = json_decode($response);
            if($response->paging->total == 0){

            }else{
                $sku = $this->getSkuByMLB($response->results[0]->order_items[0]->item->id);
                $o->sku = $sku;
                $o->save();
            }
        }
    }

    private function getOrder($code){
        $url = $this->url.'orders/search?seller='.$this->sellerId.'&q='.$code.'&access_token='.$this->token;
        $response = $this->curl->newRequest('get', $url)
                                ->send();
        $response = json_decode($response);
        return(isset($response->results[0]))?$response->results[0]:false;
    }
    public function reverseME2(){
        $orders = order::where('origin','mercado livre')
                        ->where('order_statuses_id','3')
                        ->orWhere('order_statuses_id','4')
                        ->orWhere('order_statuses_id','2')
                        ->orWhere('order_statuses_id','9')
                        ->orWhere('order_statuses_id','4')
                        ->get();
        foreach($orders as $order){

            $o = $this->getOrder($order->code);
            
            if($o){
                if($o->shipping->status == 'delivered'){
                    $order->order_statuses_id = 6;
                    $order->save();
                }else if($o->shipping->status == 'pending'){
                        echo $order->code.' - '.$o->shipping->status.' | Pendente <br />';
                }else if($o->shipping->status == 'shipped' ||
                        $o->shipping->status == 'ready_to_ship'){
                    echo $order->code.' - '.$o->shipping->status.' | Em transito<br />';
                    $order->order_statuses_id = 3;
                    $order->save();
                }else if($o->shipping->status == 'not_delivered'){
                    echo $order->code.' - '.$o->shipping->status.' | Finalizado<br />';
                    $order->order_statuses_id = 8;
                    $order->save();
                }else{
                    echo $order->code.' '.$o->shipping->status;
                   //dd($o->shipping);
                }

            }
        }
    }

    public function getProduct($mlb){
        $url = $this->url.'items/'.$mlb.'?access_token='.$this->token;
        $response = $this->curl->newRequest('get', $url)
                                ->send();
        if($response->statusCode == 200){
            $response = json_decode($response->body);
            return $response;
        }else{
            return false;
        }

    }
    public function checkMlb($mlb){

        $check = MlProducts::where('mlb',$mlb)->get();
        return (count($check)==0)?false:true;
    }
    public function productSync(){
        $startpage = 140;
        $offset = 50*$startpage;
        $url = $this->url.'users/'.$this->sellerId.'/items/search?limit=50&offset='.$offset.'&access_token='.$this->token;
        /*$url = $this->url.'sites/MLB/search?limit=200&offset='.$offset.'&seller_id='.$this->sellerId.'&access_token='.$this->token;*/


        $response = $this->curl->newRequest('get', $url)
                                ->send();

        $products = json_decode($response->body);


        $limit = $products->paging->limit;
        $pages = (int)floor($products->paging->total/$limit);
        ob_start();
        for($n=$startpage; $n<= $pages; $n++){
            echo "\nPágina ".$n."\n\n";
            flush();
            ob_flush();
            if($n == $startpage){
                $prodList = $products->results;
            }else{
                $offset = $limit*$n;
                $url = $this->url.'users/'.$this->sellerId.'/items/search?limit=50&offset='.$offset.'&access_token='.$this->token;

                unset($response);
                $response = $this->curl->newRequest('get', $url)
                                ->send();

                $products = json_decode($response->body);
                $prodList = $products->results;
            }


            foreach($prodList as $prod){
                if(!$this->checkMlb($prod)){
                    $p = $this->getProduct($prod);
                    if($p->status == 'active' || $p->status == 'paused'){
                        unset($ins);
                        $ins['sku'] = $this->getSkuByMLB($prod);
                        $ins['mlb'] = $prod;
                        $ins['url'] = $p->permalink;
                        MlProducts::create($ins);
                        echo $ins['sku'].' | '.$ins['mlb']." | Cadastrado\n";
                        flush();
                        ob_flush();
                    }else{
                       echo $prod.' | '.$p->status."\n";
                       flush();
                       ob_flush();
                    }
                }else{
                    echo $prod." Produto já está cadastrado\n";
                }

            }
        }
    }

    public function sendProductBySku($sku){
        //$providerId = 16;
        $products = Products::with('categories','prices','images','fiscals')->where('sku',$sku)->get();
        $this->sendProducts($products);
    }
    public function sendProduct($providerId = 64){
        //$providerId = 16;
        $products = Products::with('categories','prices','images','fiscals')->where('providers_id',$providerId)->get();
        $this->sendProducts($products);
    }

    public function sendProducts($products){
        foreach($products as $product){
            flush();
            ob_flush();
            $check = MlProducts::where('sku', $product->sku)->get();
            if(count($check) == 0 && isset($product->categories->mercado_livre)){
                unset($data);
                $stocks = $this->stocks->getStock($product->sku);
                if($stocks['total'] < 1){
                    continue;
                }

                $data['title'] = substr($product->name,0,60);
                $data['category_id'] = $product->categories->mercado_livre;
                $data['price'] = $product->prices->price;
                $data['currency_id'] = 'BRL';
                $data['official_store_id'] = '360';
                $data['available_quantity'] = $stocks['total'];
                $data['buying_mode'] = 'buy_it_now';
                $data['listing_type_id'] = ($data['price'] > 150)?'gold_pro':'gold_special';
                $data['condition'] = 'new';
                $data['description'] = $this->description($product);
                $data['warranty'] = '90 Dias';
                if($product->fiscals->ean != ''){
                    $data['attributes'][0]['id'] = "EAN";
                    $data['attributes'][0]['value_name'] = $product->fiscals->ean;

                    //$data['variations']['picture_ids'] = $product->images[0]->url;
                }
               

                foreach($product->images as $image){
                    if($image->url != ''){
                        $data['pictures'][]['source'] = $image->url;
                    }
                }

                //$data['pictures'] = array_filter($data['pictures']);
                //return($data);
                $url = 'https://api.mercadolibre.com/items?access_token='.$this->token;

                $response = $this->curl->jsonPost($url, $data);
                if($response->statusCode == 201){
                    $response = json_decode($response->body);
                    $ins['sku'] = $product->sku;
                    $ins['mlb'] = $response->id;
                    $ins['url'] = $response->permalink;
                    $ins['status'] = 'active';
                    MlProducts::create($ins);
                    echo $product->sku." Enviado\n";
                }else{

                    $response = json_decode($response->body);

                    if(isset($response->cause[0]) && $response->cause[0]->code == 'item.category_id.invalid'){
                        echo $product->sku." Categoria invalida\n";
                    }else{
                        if(isset($response->error) && $response->error == 'item.category_id.invalid'){
                            echo $product->sku."\n erro";
                        }else{
                            echo $product->id.' '.$product->sku.' '.$response->message."\n";

                        }

                    }

                }
            }else{
                if(!isset($product->categories->mercado_livre)){
                    echo $product->sku." Categoria invalida\n";
                }else{
                    echo $product->sku." Já cadastrado\n";
                }
            }
        }
    }


    public function description(Products $product){
        $ret = '<table align="center" height="225px" border="0" cellpadding="0" cellspacing="0" width="900px">
                    <tbody>
                        <tr>
                            <td height="244px" width="900px"><img src="http://fornecedores.fullhub.com.br/v2/MERCADOLIVRE/images/1.png" align="middle" border="0" />
                                <noscript>&lt;img src&#61;&#34;http://fornecedores.fullhub.com.br/v2/MERCADOLIVRE/images/1.jpg&#34; align&#61;&#34;middle&#34; border&#61;&#34;0&#34; /&gt;</noscript>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table align="center" height="319px" border="0" cellpadding="0" cellspacing="0" width="900px">
                    <tbody>
                        <tr>
                            <td height="319px" width="900px" align="center">
                                <img src="'.$product->images[0]->url.'" height="450" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="900px">
                    <tbody>
                        <tr>
                            <td align="center"><img src="http://fornecedores.fullhub.com.br/v2/MERCADOLIVRE/images/2.png" align="middle" border="0" />
                                <noscript>&lt;img src&#61;&#34;http://fornecedores.fullhub.com.br/v2/MERCADOLIVRE/images/2.jpg&#34; align&#61;&#34;middle&#34; border&#61;&#34;0&#34; /&gt;</noscript>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="#ceedeb" style="text-align: left;">
                                <h2 style="margin: 20px; color: #333;">'.$product->name.'</h2>
                                <p style="margin: 20px; color: #333;"><strong>Cod. '.$product->sku.'</strong>
                                    <br />
                                    <br />

                                    '.$product->description.'
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td align="center"><img src="http://fornecedores.fullhub.com.br/v2/MERCADOLIVRE/images/3.png" align="middle" border="0" />
                                <noscript>&lt;img src&#61;&#34;http://fornecedores.fullhub.com.br/v2/MERCADOLIVRE/images/3.png&#34; align&#61;&#34;middle&#34; border&#61;&#34;0&#34; /&gt;</noscript>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h2 style="margin: 20px; color: #333;"></h2>
                            </td>
                        </tr>

                        <tr>
                            <td bgcolor="#f58d4c">
                                <h2 style="margin: 20px; color: #222;">Dúvidas Frequentes</h2>
                                <p style="margin: 20px; color: #222;">Prezado cliente,
                                    <br />
                                    <br /> Por gentileza, verifique que as informações abaixo se encontrem no momento da compra nos seus dados cadastrais:
                                    <br /> - Número de telefone, celular ou do escritório. Onde você possa nos atender ou possamos ter contato.
                                    <br /> - CPF
                                    <br /> - Endereço completo, se possível com indicações de como chegar.
                                    <br /> - Caixa Postal
                                    <br /> - Nome completo seu, ou da pessoa que irá receber o produto.
                                    <br /> - Certificar que tenha alguém para receber o produto, podendo ser você ou algum conhecido. Lembre-se que em alguns casos você deve autorizar o porteiro a receber o pedido.</p>
                                <p style="margin: 20px; color: #222;">Caso contrário, a loja pode se recusar a efetuar a entrega do pedido.
                                    <br />
                                    <br /> Perguntas Frequentes:
                                    <br />
                                    <br /> <strong>E se o produto retorna para o lojista?</strong>
                                    <br />
                                    <br /> Caso o produto retorne para nós, realizaremos o envio cobrando um segundo frete, por isso é importante estar atento na primeira entrega.
                                    <br />
                                    <br /> <strong>Os correios entregam para todos os endereços?</strong>
                                    <br /> Existem algumas situações, nas quais os correios classifica a região como de risco, isto ocorre em casos de roubos de agentes do correio na região.
                                    <br />
                                    <br /> <strong>Moro numa área de risco, e agora?</strong>
                                    <br /> Nestes casos os correios pedem para o cliente retirar o produto na agencia mais próxima. Para isso, dê uma olhada no código de rastreio que a gente te passou, ai você encontrará onde pode pegar seu produto
                                    <br />
                                    <br /> <strong>Os produtos são novos?</strong>
                                    <br /> Todos os produtos são novos, originais e com garantia
                                    <br />
                                    <br /> <strong>Acompanha Nota Fiscal?</strong>
                                    <br /> Todos os nossos produtos acompanham nota fiscal em nome do comprador
                                    <br />
                                    <br /> <strong>Disponibilidade?</strong>
                                    <br /> A maior parte de nossos itens são pronta entrega, uma pequena parte tem produção sobre demanda. Nosso manuseio e expedição ocorre entre 48 e 72 horas após o pedido ser aprovado. Expedições coletadas pelos correios no período da manhã tem seus rastreios validados no dia. Expedições coletadas no período da tarde tem seus rastreios válidos no dia seguinte
                                    <br />
                                    <br /> <strong>Qual o valor e o prazo de entrega?</strong>
                                    <br /> O prazo e valor da sua compra é calculado a partir do seu CEP, basta clicar na opção com o desenho de um caminhão e inserir seu CEP que irá calcular automaticamente o valor e o prazo.
                                    <br />
                                    <br /> Horário de atendimento? Segunda a sexta das 8:30 às 17:30. Mesmo assim, fique à vontade de deixar uma mensagem Sábado ou Domingo que a gente responde para você no menor tempo possível.
                                    <br />
                                    <br /> <strong>Como é a realização da entrega?</strong>
                                    <br /> Entregas via transportadoras e correios.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>';
        return $ret;
    }
    public function ajaxItens(){
        $ml = MlProducts::with('item')->get();
        $n = 0;
        foreach($ml as $m){
            $data['data'][$n][0] = $m->sku;
            $data['data'][$n][1] = $m->mlb;
            $data['data'][$n][2] = $m->item->name;
            $n++;
        }
        echo  json_encode($data);
    }

    public function categorizator(){
        $qr = "select products_id from product_categories where mercado_livre = '' or mercado_livre is null";
        $products = \DB::select($qr);

        foreach($products as $product){

            $prod = Products::with('categories')->find($product->products_id);
            $url = 'https://api.mercadolibre.com/sites/MLB/category_predictor/predict?title='.urlencode($prod->name);
            //echo $prod->sku.' '.$url;
            $response = $this->curl->newRequest('get', $url)
                                ->send();
            if($response->statusCode == 200){
                $response = json_decode($response->body);
                $cnova = '';
                foreach($response->path_from_root as $categories){
                    $cnova .= $categories->name.'>';
                    $mercado_livre = $categories->id;
                }
                $cnova = substr($cnova,0, -1);
                //dd($mercado_livre);
                $prod->categories->cnova = $cnova;
                $prod->categories->mercado_livre = $mercado_livre;
                $prod->categories->save();

                echo "$prod->sku Categorizado\n";
            }

            //$cat = \Sweet\Product\ProductCategory::create(['products_id' => $product->id]);

        }

    }

    public function syncStatus(){
        $status = 'paused';
        $mlbs = MlProducts::orderBy('mlb','asc')->get();
        $n =0;
        foreach($mlbs as $mlb){
             if($mlItem = $this->getProduct($mlb->mlb)){
                if($mlItem->status != $mlb->status){
                    $mlb->status = $mlItem->status;
                    $mlb->save();
                    echo $mlb->sku.' '.$mlb->mlb."\n";
                }
             }else{
                echo $mlb->sku.' '.$mlb->mlb." Jump \n";
             }

        flush();
        }
    }
    public function price(){

        ini_set('max_execution_time', 4700);
        echo "\n\n\n ## Preços Mercado Livre ## \n\n\n";
         $items = $this->prices->getUpdated();
         
         if(!$items){
            return 'Nenhum Produto para Atualizar';
         }


         foreach($items as $item){
            $mlb = MlProducts::where('sku',$item['sku'])->first();

            if(count($mlb) > 0){
                $product = Products::with('prices')->where("sku", $mlb->sku)->first();
                $data['price'] = ($item['ml'] <= 0)?$item['b2w_tam_cnova']:$item['ml'];
                

                
                $url = 'https://api.mercadolibre.com/items/'.$mlb->mlb.'?access_token='.$this->token;

                $request = $this->curl->newJsonRequest('put', $url, $data)
                    ->setOption(CURLOPT_SSL_VERIFYPEER, 0)
                    ->setOption(CURLOPT_SSL_VERIFYHOST, 0)
                                      ->send();
                if($request->statusCode == 200){
                    echo $mlb->mlb.' Preço Alterado para '.$data['price']."\n";
                    flush();

                }
            }
         }
    }
    public function stock(){

        echo "\n\n\n ## Estoque Mercado Livre ## \n\n\n";
         $items = $this->stocks->getUpdated();
         
         if(!$items){
            return 'Nenhum Produto para Atualizar';
         }
         foreach($items as $item){
            $mlb = MlProducts::where('sku',$item['sku'])->first();

            if(count($mlb) > 0){
                $product = Products::with('prices')->where("sku", $mlb->sku)->first();
                $data['available_quantity'] = $item['total'];
                //$data['price'] = $product->prices[0]->price;
                
                $url = 'https://api.mercadolibre.com/items/'.$mlb->mlb.'?access_token='.$this->token;

                $request = $this->curl->newJsonRequest('put', $url, $data)
                                      ->send();
                if($request->statusCode == 200){
                    echo $mlb->mlb.' Estoque Alterado para '.$item['total']."\n";
                    flush();

                }
            }
         }
    }
    public function stockAll(){
        $qr = 'select p.sku, pes.quantity from products p
                inner join product_external_stocks pes on pes.products_id = p.id 
                where 
                p.providers_id = 64 AND
                pes.quantity >= 0';
        $res = \DB::select( \DB::raw($qr) );

        foreach($res as $r){
            $prod = MlProducts::where('sku', $r->sku)->first();
            if(count($prod) >0){


                $data['available_quantity'] = $r->quantity;
                $url = 'https://api.mercadolibre.com/items/'.$prod->mlb.'?access_token='.$this->token;

                $request = $this->curl->newJsonRequest('put', $url, $data)
                                      ->send();
                if($request->statusCode == 200){
                    echo $prod->mlb." Estoque Alterado para 0\n";
                    flush();
                }
            }
        }

    }

    

    public function export(Request $request){
      $mlbs = MlProducts::get();
      $n=0;
      foreach($mlbs as $mlb){
        $return[$n]['MLB'] = $mlb->mlb;
        $return[$n]['SKU'] = $mlb->sku;
        $return[$n]['LINK'] = $mlb->url;
        $return[$n]['STATUS'] = $mlb->status;
        $n++;
      }
      \Excel::create('Relatorio de pedidos', function($excel) use ($return){
          $excel->sheet('Sheetname', function($sheet) use ($return) {
              $sheet->fromArray($return);
          });
      })->download('xls');
    }


    public function mlbFix(){
        $mlbs = MlProducts::where('sku','like','%>%')->get();
        foreach($mlbs as $mlb){
            $sku = explode('<',$mlb->sku);
            $mlb->sku = $sku[0];
            $mlb->save();

        }
    }


    public function resendImages(){
        $products = MlProducts::where('sku','like','cromusf%')->get();
        foreach($products as $product){
            $url = $this->url.'items/'.$product->mlb.'?access_token='.$this->token;
            /*dd($url);
            $response = $this->curl->newRequest('get', $url)
                                ->send();
            dd($response);*/
            $p = Products::with('images')->where('sku',$product->sku)->first();
            $n =0;
            foreach($p->images as $i){
                unset($data);
                $data['pictures'][$n]['source'] = $i->url;
                $n++;
            }
            $request = $this->curl->newJsonRequest('put', $url, $data)
                                      ->send();
        
        }
    }


    public function checkProvider($id){
        var_dump(date('Y-m-d H:i:s'));
        ini_set('max_execution_time', 1600);
        $products = Products::where('providers_id',$id)->get();
        $n=0;
        var_dump('Products #'.$products->count());
        foreach($products as $product){ 
            $mess[$n] =  $product->sku;
            $mlProduct = MlProducts::where('sku', $product->sku)->get()->last();
            if(count($mlProduct) == 0){
                $mess[$n] =  '|MLProduct não Localizado';
                $n++;
                continue;
            }
            $stock = $this->stocks->getStock($product->sku);

            $url = $this->url.'items/'.$mlProduct->mlb.'?access_token='.$this->token;

            $request = $this->curl->newRequest('get', $url)
->setOption(CURLOPT_SSL_VERIFYPEER, 0)
->setOption(CURLOPT_SSL_VERIFYHOST, 0)
                        ->send();

            $response = json_decode($request->body);

            if ($response->available_quantity == 0 
                && $stock['total'] < 1){
                continue;
            }

            $mess[$n] =  $product->sku;

            $data['valorML'] = $response->price;
            $data['valorBd'] = $product->prices->price;
            $data['estoqueML'] = $response->available_quantity;
            $data['estoqueBd'] = $stock['total'];


            if($data['valorML'] != $data['valorBd']){
                $mess[$n] .= '|Valores Incorretos';
            }else{
                $mess[$n] .= '|Valores Corretos';
            }

            if($data['estoqueML'] != $data['estoqueBd']){
                if($data['estoqueBd'] < 1){
                    $mess[$n] .= '.|Produto no ar sem estoque';
                }else{
                    if ($data['estoqueML'] = 0){
                        $mess[$n] .= '.|Produto com estoque não está no ar';                    
                    } else {
                        $mess[$n] .= '.|Estoques Incorretos';                                            
                    }
                }
            }else{
                $mess[$n] .= '|Estoques Corretos';  
            }   
            $n++;

        }
        var_dump(date('Y-m-d H:i:s'));
        dd($mess);
        flush();
    }
}
