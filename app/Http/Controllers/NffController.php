<?php

namespace Sweet\Http\Controllers;

use Illuminate\Http\Request;

use Sweet\Http\Requests;
use Sweet\nff;
use Sweet\nffIten;
use Sweet\stockProblem;
use Sweet\Orders\Order;

use Sweet\Product\Products;
use Sweet\Product\ProductFiscal;
use Sweet\productIn;

use XmlParser;
class NffController extends Controller
{

    public function index(){
        return view('nff.index');
    }

    public function in(Request $request){
        $apikey = "c60b88ff5b0c5b10f99cc991aa31b1661bea8275";
        $documentNumber = $request->input('nff') ?? false;
        $documentSerie = $request->input('serie') ?? 0;
        $outputType = "json";

        if($documentNumber){
            $url = 'https://bling.com.br/Api/v2/notafiscal/' . $documentNumber . '/'. $documentSerie . '/' . $outputType;
            $retorno = json_decode($this->executeGetFiscalDocument($url, $apikey));

            $retorno = $retorno->retorno->notasfiscais ?? false;

            if($retorno){
              foreach($retorno as $nf){

                if($nf->notafiscal->tipo == 'E'){
                    
                    $xml = str_replace('s%26', '', $nf->notafiscal->xml);
                    $xml = $this->getXML($xml);

                    $data = simplexml_load_string($xml);

                    $n=0;
                    $ret['nff'] = (string)$data->NFe->infNFe->ide->nNF ?? '';
                    $ret['serie'] = (string)$data->NFe->infNFe->ide->serie ?? '';
                    $ret['emit']['cnpj'] = (string)$data->NFe->infNFe->emit->CNPJ ?? '';
                    $ret['emit']['ie'] = (string)$data->NFe->infNFe->emit->IE ?? '';
                    $ret['emit']['name'] = (string)$data->NFe->infNFe->emit->xNome ?? '';
                    $ret['total'] = (double)$data->NFe->infNFe->total->ICMSTot->vBC;


                    $totalItem = 0;
                    foreach($data->NFe->infNFe->det as $produto){
                        $totalItem = $totalItem+(double)$produto->prod->vProd;
                        $product = $this->checkProduct($produto->prod->cProd, $produto->prod->cEAN);
                        $ret['items'][$n]['sku'] = $product->sku ?? '';
                        $ret['items'][$n]['ean'] = (string)$produto->prod->cEAN ?? '';
                        $ret['items'][$n]['name'] = (string)$produto->prod->xProd ?? '';
                        $ret['items'][$n]['quantity'] = (int)$produto->prod->qCom ?? '';
                        $n++;
                    }
                    $ret['soma'] = $totalItem;


                    $in = $ret;
                    return view('nff.index')->with(compact('in'));

                }
              }
            }

        }


    }

    public function checkProduct($sku, $ean){
        $product = Products::where('sku', $sku)->first();
        if(count($product) == 0){
            $fiscals = ProductFiscal::where('ean', $ean)->first();
            if(count($fiscals) > 0){
                $product = Products::find($fiscals->products_id);
            }else{
                $product = false;
            }
        }
        return $product;

    }
    public function executeGetFiscalDocument($url, $apikey){
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url . '&apikey=' . $apikey);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $response;
    }

    public function getXML($url){
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $response;
    }







    public function validAndInsert(Request $request){
        $skus = $request->input('sku') ?? false;
        $quantity = $request->input('quantity') ?? false;
        $nff = $request->input('nff') ?? false;
        $message = '<h3>Inconcistencias</h3><br />';
        if($skus && $quantity){
            $n=0;
            foreach($skus as $sku){
                $product[$n]['sku'] = $sku;

                $n++;
            }
            $n=0;
            foreach($quantity as $q){
                $product[$n]['quantity'] = $q;
                $n++;
            }
            $n=0;
            foreach($product as $prod){

                //dd($pr);
                $pr = Products::where('sku', $prod['sku'])->first();
                if(count($pr) == 0){
                    unset($problem);
                    $problem = new stockProblem;
                    $problem->sku = $prod['sku'];
                    $problem->observation = 'Produto não localizado na base de dados | '.$prod['quantity'];
                    $problem->save();
                    $message .= $prod['sku'].' Não Localizado na base de dados.<br />';
                }else{
                    $stock[$n]['product_id'] = $pr->id;
                    $stock[$n]['sku'] = $prod['sku'];
                    $stock[$n]['ins'] = $prod['quantity'];
                    $stock[$n]['outs'] = 0;
                    $stock[$n]['observation'] = 'Entrada Comum';
                    $stock[$n]['origin'] = $nff;

                    $p[$n] = new productIn;
                    $p[$n]->sku = $prod['sku'];
                    $p[$n]->in = $prod['quantity'];
                    $p[$n]->save();
                    $n++;
                }


            }
            $stocks = $this->insertStock($stock);
            $ret = $this->vinculaPedidos($p);

             $message .= $stocks;
             $message .= $ret;


            echo $message;
        }
    }

    public function insertStock(array $data){
        $stock = new \Sweet\Http\Controllers\StockController;

        $message = '<h3>Estoques</h3><br />';
        if(is_array($data)){
            foreach($data as $s){
                $message .= $s['sku'].' Inseridos: '.$s['ins'].'<br />';
                $stock->insertStockData($s);
            }
            return $message;
        }
    }

    public function vinculaPedidos($prod){
            $log = '<h3>Liberação de pedidos</h3><br />';
        foreach($prod as $p){

            $used = 0;
            $orders = \DB::table('orders')
                        ->join('order_items','order_items.orders_id', '=','orders.id')
                        ->where('orders.order_statuses_id',14)
                        ->where('order_items.sku',$p->sku)
                        ->orderBy('order_items.id','Desc')
                        ->select('orders.id','order_items.sku','order_items.quantity')
                        ->get();
            if(count($orders) ==0){
                $log .= 'Nenhum '.$p->sku.' Localizado<br />';
            }else{
                foreach($orders as $order){
                    if($used >= $p->in){
                        $log .= 'Todos '.$p->sku.' Utilizados<br />';
                    }else{
                        $o = Order::find($order->id);
                        if($order->quantity <= $p->in){

                            $o->order_statuses_id = $order->quantity;
                            $o->save();
                            $log .= "Pedido ".$o->code." Liberado para Faturamento<br />";
                            $used = $order->quantity;
                            $p->out = $used;
                            $p->save();

                        }else{
                            $log .= "Pedido ".$o->code." Quantidade maior que o disponível<br />";
                        }
                    }
                }
            }
        }
        return $log;
    }
}
