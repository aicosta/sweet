<?php

namespace Sweet\Http\Controllers;

use Illuminate\Http\Request;

use Sweet\Http\Requests;
use DB;
use Sweet\Product\Products;
use Sweet\Product\ProductExternalStock;
use Sweet\internalStocks;
use Sweet\Http\Controllers\ProductHelperController;
use Sweet\Product\StockLog;

class StockController extends Controller
{
    private $time = 10; // Tempo de busca em minutos



    public function insert(){
        return view('products.stock.create');
    }
    public function view($id){
        $product = Products::find($id);
        $stocks = internalStocks::where('product_id',$id)->paginate(50);
        $n=0;
        $saldo = $stocks[0]->ins ?? 0;
        foreach($stocks as $stock){

            $data[$n]['created_at'] = date('d/m/Y H:i:s', strtotime($stock->created_at));
            $data[$n]['ins'] = $stock->ins;
            $data[$n]['outs'] = $stock->outs;
            $data[$n]['observation'] = $stock->observation;
            $data[$n]['origin'] = $stock->origin;
            $data[$n]['balance'] = $stock->balance;
            $n++;
        }
        unset($stocks);
        $stocks = $data ?? false;
        $stockData = $this->getStock($product->sku);
        $logs = StockLog::where('product_id', $id)->get();
        $logs = (count($logs) == 0)?false:$logs;
        return view('products.stock.view')->with(compact('stocks','product','stockData','logs'));
    }
    public function list(Request $request){

        $search = $request->input('search');
        if(!is_null($search)){
            $products = Products::where('sku','like',"%$search%")->get();
            dd($products);
        }else{
            $stocks = internalStocks::with('product')
                ->select('product_id',DB::raw('(sum(ins) - sum(outs)) as count'))
                ->orderBy('count', 'desc')
                ->groupBy('product_id')
                ->paginate(50);
            $n =0;
            if(count($stocks) == 0){
                return false;
            }

            $ret['page'] = $stocks->currentPage();
            $ret['next'] = $stocks->nextPageUrl();
            $ret['prev'] = $stocks->previousPageUrl();
            $ret['last'] = $stocks->lastPage();
            $ret['hasMorePages'] = $stocks->hasMorePages();
            $ret['firstItem'] = $stocks->firstItem();


            foreach($stocks as $stock){
                $product = Products::find($stock->product_id);
                $ret['items'][$n]['id'] = $product->id;
                $ret['items'][$n]['sku'] = $product->sku;
                $ret['items'][$n]['name'] = $product->name;
                $ret['items'][$n]['stock'] = $stock->count;
                $ret['items'][$n]['provider'] = $product->providers->name;
                $n++;
            }
            unset($stocks);
            $stocks = $ret;
            return view('products.stock.InternalStock')->with(compact('stocks'));
        }


    }
    public function export(){

        $stocks = internalStocks::with('product')
                ->select('product_id',DB::raw('(sum(ins) - sum(outs)) as count'))
                ->orderBy('count', 'desc')
                ->groupBy('product_id')
                ->get();
        if(count($stocks) == 0){ return false;}


        $n=0;
        foreach($stocks as $stock){
            //dd($stock);
            $product = Products::with('stocks')->find($stock->product_id);
            dd($product->stocks);
            $ret[$n]['sku'] = $product->sku;
            $ret[$n]['nome'] = $product->name;
            $ret[$n]['quantidade'] = $stock->count;
            $ret[$n]['fornecedor'] = $product->providers->name;
            $n++;
        }

        \Excel::create('Relatório de estoque', function($excel) use ($ret){
            $excel->sheet('Estoques', function($sheet)  use ($ret){
            $sheet->fromArray($ret);
        });
        })->download('xls');
        //return view('products.stock.InternalStock')->with(compact('stocks'));
    }
    public function getUpdated(){
        $time = date('Y-m-d H:i:\00', strtotime("-$this->time minutes"));

        $internals = $this->getInternalByDate($time);
        $externals = $this->getExternalByDate($time);
        $list = array();
        if($internals){

            foreach($internals as $internal){
                $list[$internal] = $this->getStockById($internal);
            }
        }
        if($externals){

            foreach($externals as $external){
                $list[$external] = $this->getStockById($external);
            }
        }
        if(count($list) > 0){
            return $list;
        }else{
            return false;
        }
    }
    private function getInternalByDate($date){
        $stocks = internalStocks::select('product_id')
                                ->distinct()
                                ->where('created_at','>=', $date)
                                ->get();
        if(count($stocks) > 0){
            foreach($stocks as $stock){
                $ret[] = $stock->product_id;
            }
            return $ret;
        }else{
            return false;
        }

    }
    private function getExternalByDate($date){
        $stocks = ProductExternalStock::select('products_id')
                                ->distinct()
                                ->where('updated_at','>=', $date)
                                ->get();


        if(count($stocks) > 0){
            foreach($stocks as $stock){
                $ret[] = $stock->products_id;
            }

            return $ret;
        }else{
            return false;
        }

    }
    public function getStock($sku){
        $product = ProductHelperController::getProductBySku($sku);
        if($product){
            $stock['product_id'] = $product->id;
            $stock['sku'] = $product->sku;
            $stock['internal'] = $this->countInternalStock($product);
            $stock['external'] = $this->getExternalStock($product);
            $stock['total'] = ($stock['internal'] + $stock['external']);
            return $stock;
        }else{
            $log[] = $sku.' Produto não Localizado';
            return false;
        }
    }
    public function getStockById($id){
        $product = Products::find($id);
        if($product){
            $stock['product_id'] = $product->id;
            $stock['sku'] = $product->sku;
            $stock['internal'] = $this->countInternalStock($product);
            $stock['external'] = $this->getExternalStock($product);
            $stock['total'] = ($stock['internal'] + $stock['external']);
            return $stock;
        }else{
            $log[] = $sku.' Produto não Localizado';
            return false;
        }
    }
    private function countInternalStock(Products $product){

        $stock = internalStocks::select(DB::raw('(sum(ins) - sum(outs)) as count'))
                                ->where('product_id',$product->id)
                                ->first();
        return $stock->count ?? 0;
    }
    private function getExternalStock(Products $product){
        $stock = ProductExternalStock::where('products_id',$product->id)
                                       ->select('quantity')
                                       ->first();
        return $stock->quantity ?? 0;
    }
    public function setInternalStock(array $data){

    }
    public function setExternalStock($sku){

    }


    public function insertStockData(array $data){
        $stock =  DB::table('internal_stocks')->where('product_id', $data['product_id'])->where('id', DB::raw("(select max(`id`) from internal_stocks)"))->get();
        if(count($stock) > 0){
            $data['balance'] = (($stock[0]->balance+$data['ins'])-$data['outs']);
        }else{
            $data['balance'] = $data['ins']-$data['outs'];
        }
        $i = internalStocks::create($data);
    }
    public function init(){
        $file = file_get_contents('../storage/interno');
        $list = explode(PHP_EOL, $file);
        unset($list[0]);
        $sku = array();
        foreach($list as $l){
            $line = explode('|', $l);
            $prod = Products::where('sku',trim($line[0]))->first();
            if($prod){
                if(!in_array($prod->sku, $sku)){
                    unset($data);
                    $data['product_id'] = $prod->id;
                    $data['ins'] = $line[1];
                    $data['outs'] = 0;
                    $data['observation'] = 'Linha de Corte';
                    $data['origin'] = 'Planilha';

                    $this->insertStockData($data);
                    $sku[] = $prod->sku;
                }else{
                    $log[] = trim($line[0]).' Duplicado';
                }

            }else{
                $log[] = trim($line[0]).' Não Localizado';
            }
        }

    }


    public function setStock(array $data){

        $product = Products::where('sku',$data['sku'])->first();
        if($product){
            $data['product_id'] = $product->id;
            $data['ins'] = 0;
            $data['outs'] = $data['outs'];
            $data['observation'] = '';
            $data['origin'] = $data['origin'];
             $this->insertStockData($data);
        }

    }

    public function insertStock(Request $request){
        $produto = $request->input('produto');
        $ins = $request->input('ins');
        $outs = $request->input('outs');
        $observation = $request->input('observation');
        $origin = $request->input('origin');

        $produto = explode('|', $produto);
        $product = Products::where('sku',trim($produto[0]))->first();
         if($product){
            $data['product_id'] = $product->id;
            $data['ins'] = $ins;
            $data['outs'] = $outs;
            $data['observation'] = $observation;
            $data['origin'] = $origin;
            $this->insertStockData($data);

            $status = $product->sku.' Estoque Inserido';
            return back()->with('status', $status);
        }else{
            return back()->withErrors(['msg', $produto[0].' Produto não Localizado']);
        }
    }

    public function report($type, $term){
        $res = array();
        switch($type){
            case 'sku';
                $products = Products::where('sku','like',"%$term%")->get();
                foreach($products as $product){
                    $stocks = internalStocks::with('product')->where('product_id',$product->id)->get();
                    foreach($stocks as $stock){
                        $res[] = $stock;
                    }
                }
                break;
            case 'fornecedor';

                break;
            case 'data';
                $stocks = internalStocks::with('product')->where('created_at','like',"$term%")->get();
                    foreach($stocks as $stock){
                        $res[] = $stock;
                    }
                break;
            case 'nfe';
                $stocks = internalStocks::with('product')->where('origin','like',"%$term%")->get();
                    foreach($stocks as $stock){
                        $res[] = $stock;
                    }
                break;
        }
        $n=0;
        foreach($res as $r){
            $st = $this->getStock($r->product->sku);
            $json['data'][$n][] = date('d/m/Y', strtotime($r->created_at));
            $json['data'][$n][] = $r->product->sku;
            $json['data'][$n][] = $r->product->name;
            $json['data'][$n][] = $r->product->providers->name;
            $json['data'][$n][] = $r->ins;
            $json['data'][$n][] = $r->outs;
            $json['data'][$n][] = $r->origin;
            $json['data'][$n][] = $r->balance;


            $n++;
        }
        return $json;
    }
    public function insertall(){
        $prods = Products::get();
        foreach($prods as $prod){
            $check = internalStocks::where('product_id', $prod->id)->get();
            if(count($check) == 0){
                $data['product_id'] = $prod->id;
                $data['ins'] = 0;
                $data['outs'] = 0;
                $data['observation'] = 'start';
                $data['origin'] = '';
                $this->insertStockData($data);
            }
        }
    }

    public function fixStock(){
       return 'nothing to do';
    }
    public function test($id = false){
        return $this->getUpdated();


    }
}
