<?php

namespace Sweet\Http\Controllers;

use Illuminate\Http\Request;

use Sweet\Http\Requests;

use Sweet\User;
use Sweet\Http\Controllers\AuthController;
use Sweet\Product\Providers;
use Sweet\Http\Controllers\ProductHelperController;

//Products and Dependencies
use Sweet\Product\Products;
use Sweet\Product\ProductAttribute;
use Sweet\Product\ProductDimension;
use Sweet\Product\ProductExternalStock;
use Sweet\Product\ProductFiscal;
use Sweet\Product\ProductImage;
use Sweet\Product\ProductPrices;
use Sweet\Product\ProductOrigin;
use Sweet\Product\ProductLog;
use Sweet\Product\ProductCategory;
use Sweet\Product\StockLog;

use Sweet\Http\Controllers\StockController;

//Batch Actions
use Sweet\Jobs\batchStocks;
use Sweet\Jobs\StockPriceReport;
use Mail;
use DB;
use Sweet\BatchLog;


class ProductController extends Controller
{
    private $stocks;
    protected $report;
    public function __construct(){
        $this->stocks = new StockController;
    }
    public function create(){

        $permissions = ['admin','sac'];
        if(AuthController::checkPermission($permissions)){
            $providers = Providers::orderBy('name')->get();
            $origins = ProductOrigin::orderBy('id')->get();
            return view('products.create')->with(compact('providers','origins'));
        }


    }

    public function listProducts(Request $request){

        $permissions = ['admin','sac'];
        if(AuthController::checkPermission($permissions)){
            $search = $request->input('search');
           if(!$search){
                $products = Products::with('providers','images','stocks','prices')
                                    ->orderBy('id','desc')
                                    ->paginate(10);
           }else{
                $products = Products::with('providers','images','stocks','prices')
                                    ->orderBy('id','desc')
                                    ->where('sku','like','%'.$search.'%')
                                    ->orWhere('name','like','%'.$search.'%')
                                    ->paginate(10);
           }
           $products->appends(['search'=> $search])->links();
    	   return view('products.list')->with(compact('products', 'search'));
        }
    }



    public function edit($id){
        $permissions = ['sac'];
        if(AuthController::checkPermission($permissions)){
            $product = Products::with('providers','images','stocks','prices','dimensions','fiscals')->find($id);
            $providers = Providers::orderBy('name')->get();
            $origins = ProductOrigin::orderBy('id')->get();
            return view('products.edit')->with(compact('product','providers','origins'));
        }
    }

    public function saveProduct(Request $request){
        dd($request);
    }

    public function editSave($id, Request $request){
        $product = Products::find($id);
        $attributes = ProductAttribute::where('products_id',$id)->get();
        $dimensions = ProductDimension::where('products_id',$id)->get();
        $externalStock = ProductExternalStock::where('products_id',$id)->get();
        $fiscals = ProductFiscal::where('products_id',$id)->get();
        $images = ProductImage::where('products_id',$id)->get();
        $prices = ProductPrices::where('products_id',$id)->get();

        $changed = array();

        /**
        *
        Check Product table changes
        *
        **/
        if($product->name != $request->input('name')){
            $changed['name'] =
                    ['old' => $product->name,
                    'new' => $request->input('name')];
            $product->name = $request->input('name');
        }
        if($product->description != $request->input('description')){
            $changed['description'] =
                    ['old' => $product->description,
                     'new' => $request->input('description')];
            $product->description = $request->input('description');
        }
        if($product->short_description != $request->input('short_description')){
            $changed['short_description'] =
                    ['old' => $product->short_description,
                     'new' => $request->input('short_description')];
            $product->short_description = $request->input('short_description');
        }
        if($product->providers_id != $request->input('provider')){
            $changed['provider'] =
                    ['old' => $product->providers_id,
                     'new' => $request->input('provider')];
            $product->providers_id = $request->input('provider');
        }
        if($product->brand != $request->input('brand')){
            $changed['brand'] =
                    ['old' => $product->brand,
                     'new' => $request->input('brand')];
           $product->brand = $request->input('brand');
        }
        if($product->lead_time != $request->input('lead_time')){
            $changed['lead_time'] =
                    ['old' => $product->lead_time,
                    'new' => $request->input('lead_time')];
           $product->lead_time = $request->input('lead_time');
        }

        /**
        *
        Check Stocks table changes
        *
        **/

         if($externalStock[0]->quantity != $request->stock['quantity']){
            $changed['quantity'] =
                    ['old' => $externalStock[0]->quantity,
                    'new' => $request->stock['quantity']];
           $externalStock[0]->quantity = $request->stock['quantity'];
        }

        /**
        *
        Check Dimensions table changes
        *
        **/
        if($dimensions[0]->weight != $request->dimensions['weight']){
            $changed['weight'] =
                    ['old' => $dimensions[0]->weight,
                    'new' => $request->dimensions['weight']];
           $dimensions[0]->weight = $request->dimensions['weight'];
        }
        if($dimensions[0]->width != $request->dimensions['width']){
            $changed['width'] =
                    ['old' => $dimensions[0]->weight,
                    'new' => $request->dimensions['width']];
           $dimensions[0]->width = $request->dimensions['width'];
        }
        if($dimensions[0]->height != $request->dimensions['height']){
            $changed['height'] =
                    ['old' => $dimensions[0]->height,
                    'new' => $request->dimensions['height']];
           $dimensions[0]->height = $request->dimensions['height'];
        }
        if($dimensions[0]->depth != $request->dimensions['depth']){
            $changed['depth'] =
                    ['old' => $dimensions[0]->depth,
                    'new' => $request->dimensions['depth']];
           $dimensions[0]->depth = $request->dimensions['depth'];
        }
        if($dimensions[0]->cube != $request->dimensions['cube']){
            $changed['cube'] =
                    ['old' => $dimensions[0]->cube,
                    'new' => $request->dimensions['cube']];
           $dimensions[0]->cube = $request->dimensions['cube'];
        }

         /**
        *
        Check Fiscals table changes
        *
        **/
        if($fiscals[0]->sku != $request->fiscals['sku']){
            $changed['sku'] =
                    ['old' =>$fiscals[0]->sku,
                    'new' => $request->fiscals['sku']];
           $fiscals[0]->sku = $request->fiscals['sku'];
        }
        if($fiscals[0]->name != $request->fiscals['name']){
            $changed['name'] =
                    ['old' =>$fiscals[0]->name,
                    'new' => $request->fiscals['name']];
           $fiscals[0]->name = $request->fiscals['name'];
        }
        if($fiscals[0]->ean != $request->fiscals['ean']){
            $changed['ean'] =
                    ['old' =>$fiscals[0]->ean,
                    'new' => $request->fiscals['ean']];
           $fiscals[0]->ean = $request->fiscals['ean'];
        }
        if($fiscals[0]->isbn != $request->fiscals['isbn']){
            $changed['isbn'] =
                    ['old' =>$fiscals[0]->isbn,
                    'new' => $request->fiscals['isbn']];
           $fiscals[0]->isbn = $request->fiscals['isbn'];
        }
        if($fiscals[0]->ncm != $request->fiscals['ncm']){
            $changed['ncm'] =
                    ['old' =>$fiscals[0]->ncm,
                    'new' => $request->fiscals['ncm']];
           $fiscals[0]->ncm = $request->fiscals['ncm'];
        }

        if(count($changed) == 0){
            return redirect($request->getPathInfo())->with('error','Nenhum campo alterado!');
        }else{
            $product->save();
            $externalStock[0]->save();
            $dimensions[0]->save();
            $fiscals[0]->save();
            $this->saveLog($id, $changed);
            return redirect($request->getPathInfo())->with('message','Produto Atualizado!');
        }

    }

    public function saveLog($products_id, $changed){
        $log = new ProductLog();
        $log->products_id = $products_id;
        $log->user_id = \Auth::user()->id;
        $log->changed = json_encode($changed);
        $log->save();
        return true;
    }



    public function massAction(){
        return view('products.massAction');
    }

    public function priceUpdate(){
        $data = '[{"sku":"CromusFJ_23010900","price":26.90},{"sku":"CromusFJ_23700184","price":112.90},{"sku":"CromusFJ_23010905","price":21.90},{"sku":"CromusFJ_27010080","price":168.90},{"sku":"CromusFJ_27010081","price":168.90},{"sku":"CromusFJ_27010079","price":150.90},{"sku":"CromusFJ_23010581","price":15.90},{"sku":"CromusFJ_23010903","price":16.90},{"sku":"CromusFJ_27010082","price":168.90},{"sku":"CromusFJ_23010911","price":19.90},{"sku":"CromusFJ_23010889","price":12.90},{"sku":"CromusFJ_23010895","price":18.90},{"sku":"CromusFJ_23010904","price":70.90},{"sku":"CromusFJ_23010881","price":9.90},{"sku":"CromusFJ_23010897","price":48.90},{"sku":"CromusFJ_23010906","price":14.90},{"sku":"CromusFJ_23010163","price":38.90},{"sku":"CromusFJ_23900034","price":24.90},{"sku":"CromusFJ_23010579","price":13.90},{"sku":"CromusFJ_23410039","price":14.90},{"sku":"CromusFJ_23410073","price":9.90},{"sku":"CromusFJ_23410038","price":11.90},{"sku":"CromusFJ_23410072","price":15.90},{"sku":"CromusFJ_23010902","price":41.90},{"sku":"CromusFJ_23610032","price":16.90},{"sku":"CromusFJ_28610312","price":10.90},{"sku":"CromusFJ_28610396","price":9.90},{"sku":"CromusFJ_28610313","price":9.90},{"sku":"CromusFJ_28610081","price":25.90},{"sku":"CromusFJ_28610397","price":11.90},{"sku":"CromusFJ_29000558","price":13.90},{"sku":"CromusFJ_29000914","price":13.90},{"sku":"CromusFJ_29000915","price":13.90},{"sku":"CromusFJ_23010912","price":19.90},{"sku":"CromusFJ_23010907","price":26.90},{"sku":"CromusFJ_23010944","price":7.90},{"sku":"CromusFJ_23010899","price":12.90},{"sku":"CromusFJ_23010910","price":26.90},{"sku":"CromusFJ_23010572","price":21.90},{"sku":"CromusFJ_23010573","price":23.90},{"sku":"CromusFJ_23010909","price":23.90},{"sku":"CromusFJ_23010898","price":62.90},{"sku":"CromusFJ_23010916","price":29.90},{"sku":"CromusFJ_23010915","price":27.90},{"sku":"CromusFJ_23010914","price":27.90},{"sku":"CromusFJ_27010005","price":44.90},{"sku":"CromusFJ_27010002","price":18.90},{"sku":"CromusFJ_27010004","price":30.90},{"sku":"CromusFJ_27010003","price":23.90},{"sku":"CromusFJ_28810070","price":18.90},{"sku":"CromusFJ_28810052","price":17.90},{"sku":"CromusFJ_23010935","price":12.90},{"sku":"CromusFJ_23010582","price":14.90},{"sku":"CromusFJ_23010924","price":12.90},{"sku":"CromusFJ_23010934","price":12.90},{"sku":"CromusFJ_23010942","price":12.90},{"sku":"CromusFJ_23010932","price":12.90},{"sku":"CromusFJ_23010937","price":12.90},{"sku":"CromusFJ_23010930","price":12.90},{"sku":"CromusFJ_23010938","price":12.90},{"sku":"CromusFJ_23010926","price":12.90},{"sku":"CromusFJ_23010921","price":12.90},{"sku":"CromusFJ_23010940","price":12.90},{"sku":"CromusFJ_23010928","price":12.90},{"sku":"CromusFJ_23010941","price":12.90},{"sku":"CromusFJ_23010931","price":12.90},{"sku":"CromusFJ_23010943","price":12.90},{"sku":"CromusFJ_23010939","price":12.90},{"sku":"CromusFJ_23010923","price":12.90},{"sku":"CromusFJ_23010927","price":12.90},{"sku":"CromusFJ_23010933","price":12.90},{"sku":"CromusFJ_23010920","price":12.90},{"sku":"CromusFJ_23010922","price":12.90},{"sku":"CromusFJ_23010925","price":12.90},{"sku":"CromusFJ_23010936","price":12.90},{"sku":"CromusFJ_23010929","price":12.90},{"sku":"CromusFJ_23010195","price":5.90},{"sku":"CromusFJ_23010913","price":18.90},{"sku":"CromusFJ_23700107","price":87.90},{"sku":"CromusFJ_23310062","price":12.90},{"sku":"CromusFJ_24010047","price":31.90},{"sku":"CromusFJ_21010104","price":17.90},{"sku":"CromusFJ_21010150","price":19.90},{"sku":"CromusFJ_21010147","price":19.90},{"sku":"CromusFJ_21010103","price":19.90},{"sku":"CromusFJ_21010102","price":19.90},{"sku":"CromusFJ_21010034","price":15.90},{"sku":"CromusFJ_21010149","price":15.90},{"sku":"CromusFJ_21010146","price":15.90},{"sku":"CromusFJ_21010032","price":15.90},{"sku":"CromusFJ_21010148","price":20.90},{"sku":"CromusFJ_21010145","price":20.90},{"sku":"CromusFJ_21010035","price":20.90},{"sku":"CromusFJ_21010033","price":20.90},{"sku":"CromusFJ_23010901","price":39.90},{"sku":"CromusFJ_23010577","price":9.90},{"sku":"CromusFJ_23010908","price":44.90},{"sku":"CromusFJ_22710054","price":41.90},{"sku":"CromusFJ_22710053","price":37.90},{"sku":"CromusFJ_22710055","price":17.90},{"sku":"CromusFJ_23010896","price":14.90},{"sku":"CromusFJ_25010052","price":22.90},{"sku":"CromusFJ_23010918","price":240.90},{"sku":"CromusFJ_23010919","price":330.90},{"sku":"CromusFJ_23010917","price":510.90}]';

        $products = json_decode($data);
        foreach($products as $product){
            $prod = Products::with('prices','stocks')->where('sku',$product->sku)->first();
            if(count($prod) == 0){
                echo $product->sku.' Não Localizado<Br />';
                continue;
            }
            $prod->prices[0]->price = $product->price;
            if($prod->prices[0]->save()){
                $prod->stocks->quantity = 9999;
                $prod->stocks->save();
                echo $product->sku.' Atualizado para '.$product->price.'<br />';
            }
        }
    }

    public function importProducts(Request $request){

         try {
            $file = $request->file('products');
            if(\File::extension($file->getClientOriginalName()) == 'csv'){
                $destinationPath = '../uploads/products';  
                $fileName = 'products-'.time().'.csv';
                $array = $fields = array(); $i = 0;
                if($file->move($destinationPath, $fileName)){


                    if (($handle = fopen($destinationPath.'/'.$fileName, "r")) !== FALSE) {

                        while (($row = fgetcsv($handle, 5000000 , ";")) !== FALSE) {
                             if (empty($fields)) {
                                    $fields = $row;
                                    continue;
                                }
        
                                foreach ($row as $k=>$value) {
                                    $array[$i][strtolower(trim($fields[$k]))] = $value;


                                }
                                $i++;
                        }
                    }
                    unset($row);
                    $n=0;
                    foreach($array as $line){
                        $product = ProductHelperController::checkSku($line['sku']);   
                        if(!$product && $provider  = ProductHelperController::getProvider($line['fornecedor'])){

                            unset($product);
                            unset($fiscals);
                            unset($dimensions);
                            unset($stocks);
                            unset($category);
                            unset($image);
                            unset($attr);
                            $product = new Products();
                            $product->sku = $line['sku'];
                            //$product->name = utf8_encode($line['nome']);

                            
                            $product->description = iconv('ASCII', 'UTF-8//IGNORE', nl2br($line['descricao']));;
    
                            $product->short_description = '';
                            $product->providers_id = $provider->id;
                            $product->brand = iconv('ASCII', 'UTF-8//IGNORE', nl2br($line['marca']));;
                            $product->lead_time = $line['lead_time'];
                            $product->status = '1';
                            $product->nameml = iconv('ASCII', 'UTF-8//IGNORE', nl2br($line['nomeml']));



                            $fiscals = new ProductFiscal();
                            $fiscals->sku = $line['sku-fiscal'];
                            $fiscals->name = $line['nome-fiscal'];
                            $fiscals->ean = $line['ean'];
                            $fiscals->ncm = $line['ncm'];
                            $fiscals->isbn = $line['isbn'];
                            $fiscals->origin = $line['origem'];

                            $dimensions = new ProductDimension();
                            $dimensions->weight = str_replace(',','.',str_replace('.','',$line['peso']));
                            $dimensions->width = str_replace(',','.',str_replace('.','',$line['altura']));
                            $dimensions->height = str_replace(',','.',str_replace('.','',$line['largura']));
                            $dimensions->depth = str_replace(',','.',str_replace('.','',$line['comprimento']));
                            $dimensions->cube = str_replace(',','.',str_replace('.','',$line['cubagem']));

                            $stocks = new ProductExternalStock();
                            $stocks->quantity = $line['estoque'];

                            $price = new ProductPrices();
                            $price->marketplaces_id = 1;
                            $price->price = $line['valor'];

                            $category = new ProductCategory();
                            $category->b2w = $line['cat_b2w'];
                            $category->cnova = $line['cat_cnova'];
                            $category->mercado_livre = $line['cat_mercado_livre'];
                            $category->tamarindos = $line['cat_tamarindos'];
                            $category->walmart = $line['cat_walmart'];
                            $category->netshoes = $line['cat_netshoes'];
                            $category->amazon = $line['cat_amazon'];

                            if($product->save()){
                                $log[$n] = ['sku'=> $line['sku'], 'message' => 'Produto cadastrado'];

                                $fiscals->products_id = $product->id;
                                $dimensions->products_id = $product->id;
                                $stocks->products_id = $product->id;
                                $price->products_id = $product->id;
                                $category->products_id = $product->id;
                                $fiscals->save();
                                $dimensions->save();
                                $stocks->save();
                                $price->save();
                                $category->save();

                                $images = explode('|', $line['imagens']);

                                    $c = 0;
                                    foreach($images as $i){
                                        $image[$c] = new ProductImage();
                                        $image[$c]->products_id = $product->id;
                                        $image[$c]->url = $i;
                                        $image[$c]->name = ' ';
                                        $image[$c]->order = $c;
                                        $image[$c]->save();
                                        $c++;
                                    }
                                    $log[$n]['imagens'] = 'incluidos';


                                $c=0;

                                $attr[$c] = new ProductAttribute();
                                $attr[$c]->products_id = $product->id;
                                $attr[$c]->name = 'Cor';
                                $attr[$c]->value = iconv('ASCII', 'UTF-8//IGNORE', nl2br($line['cor']));
                                $attr[$c]->save();

                                $c++;
                                $attr[$c] = new ProductAttribute();
                                $attr[$c]->products_id = $product->id;
                                $attr[$c]->name = 'Garantia';
                                $attr[$c]->value = $line['garantia'];
                                $attr[$c]->save();

                                $c++;
                                $attr[$c] = new ProductAttribute();
                                $attr[$c]->products_id = $product->id;
                                $attr[$c]->name = 'Material';
                                $attr[$c]->value = iconv('ASCII', 'UTF-8//IGNORE', nl2br($line['material']));;
                                $attr[$c]->save();
                                $c++;
                                //
                                $attr[$c] = new ProductAttribute();
                                $attr[$c]->products_id = $product->id;
                                $attr[$c]->name = 'Conteúdo da Embalagem';
                                $attr[$c]->value = iconv('ASCII', 'UTF-8//IGNORE', nl2br($line['conteudo_da_embalagem']));
                                $attr[$c]->save();
                                $c++;

                                $attr[$c] = new ProductAttribute();
                                $attr[$c]->products_id = $product->id;
                                $attr[$c]->name = 'Dimensões do Produto';
                                $attr[$c]->value = $line['dimensoes'];
                                $attr[$c]->save();
                                $c++;

                                /*$atributos = (strlen($line['atributos']) > 0)?false:$atributos = explode('|',$line['atributos']);
                                if($atributos){


                                    foreach($atributos as $atr){
                                        unset($dataAttr);
                                        $dataAttr = explode(':', $atr);

                                        $attr[$c] = new ProductAttribute();
                                        $attr[$c]->products_id = $product->id;
                                        $attr[$c]->name = $dataAttr[0];
                                        $attr[$c]->value = $dataAttr[1];
                                        $attr[$c]->save();
                                        $c++;
                                    }
                                }*/
                            }else{

                                
                            }

                        }else{

                            if($product){
                                $product->name = $line['nome']; 
                                $product->description = iconv('ASCII', 'UTF-8//IGNORE', nl2br($line['descricao']));
                                $product->save();   

                            }
                        }
                        $n++;

                    }

                    return redirect('/products/batch')->with('message','Produtos na fila de cadastrados!');
                }else{
                    return redirect('/products/batch')->with('error','Não foi possível ler  o arquivo');
                }
            }else{
                return redirect('/products/batch')->with('error','O arquivo enviado não tem o formato esperado!');
            }
        }catch(Exception $e){

        }

    }

    public function runStockUpdate($file, $user){
        $file = \Excel::load('uploads/stocks/'.$file)->get();
            $n=0;
            foreach($file->toArray() as $row){
                unset($product);
                if(isset($row['sku']) && isset($row['estoque']))        {
                    $product = Products::where('sku',$row['sku'])->first();
                    if($product){
                        $stock = ProductExternalStock::where('products_id', $product->id)->first();

                        $stock->quantity = $row['estoque'];

                        if($stock->save()){
                            unset($log);
                            $log = ['product_id' => $product->id, 'user_id' => $user->id, 'qty' => $row['estoque']];
                            StockLog::create($log);

                        }else{

                        }

                    }else{
                    }
                    $n++;
                }else{
                    $log[0] = 'ERROS ENCONTRADOS NA TABELA';
                
                }
            }

            //$this->sendMail($log, $user);
            return true;


    }

    public function sendMail($payload, $user){

         Mail::send('emails.teste', ['user' => $user, 'log' => $payload], function ($m) use ($user) {
            $m->from('noreply@fullhub.com.br', 'Fullhub Sweet');

            $m->to($user->email, $user->name)->subject('[SWEET] ATUALIZAÇÃO DE ESTOQUE');
        });
    }
    public function importStocks(Request $request){
        try {
            $file = $request->file('stocks');
            if(\File::extension($file->getClientOriginalName()) == 'xlsx'){
                $destinationPath = '../uploads/stocks';
                $fileName = 'stocks-'.time().'.xlsx';
                if($file->move($destinationPath, $fileName)){
                    if($this->runStockUpdate($fileName, \Auth::user())){
                        return redirect('/products')->with('message','Estoques Atualizados! ');
                    }else{
                        return redirect('/products/batch')->with('error','O arquivo enviado não tem o formato esperado!');
                    }
                }

            }else{
                
            }


        } catch (Exception $e) {

        }
    }
    public function runPriceUpdate($file, $user){
    	$file = \Excel::load('uploads/prices/'.$file)->get();
            $n=0;
            foreach($file->toArray() as $row){
                unset($product);

                if(isset($row['sku']))        {
                    $product = Products::where('sku',$row['sku'])->first();
                    if($product){
                    	$product->cost = $row['custo'];

                    	$product->prices->price = $row['b2w_cnova_tamarindos'];
                    	$product->prices->price2 = $row['mobly_e_walmart'];
                    	if(isset($row['mercadolivre'])){
                    		$product->prices->price3 = $row['mercadolivre'];	
                    	}
                    	
                    	
                    	$product->save();
                    	$product->prices->save();
                    	$log[$n] =  ['sku' => $row['sku'], 'message' => "Atualizado"];

                    }else{
                         $log[$n] = ['sku' => $row['sku'], 'error' => 'Produto não localizado'];
                    }
                    $n++;
                }else{
                    $log[0] = 'ERROS ENCONTRADOS NA TABELA';
                
                }
            }
            $data = new BatchLog();
            $data->type = 'precos';
            $log = (isset($log))?'':$log;
            $data->payload = json_encode($log);
            $data->user_id = $user->id;
            $data->save();
            return true;
    }
    public function importPrices(Request $request){
     	$file = $request->file('prices');
     	if(\File::extension($file->getClientOriginalName()) == 'xlsx'){
     		$destinationPath = '../uploads/prices';
            $fileName = 'prices-'.time().'.xlsx';
     		if($file->move($destinationPath, $fileName)){
     			if($this->runPriceUpdate($fileName, \Auth::user())){
                    return redirect('/products')->with('message','Preços Atualizados! ');
                }else{
                    return redirect('/products/batch')->with('error','O arquivo enviado não tem o formato esperado!');
                }
     		}	
     	}
     	   
    }

    public function getProvider($id){
        $provider = Products::where('sku', $id)->with('Providers')->first();

        return $provider->Providers->name;
    }

    public function autocomplete($term){
        $prods = Products::select('sku','name')
                  ->where('sku', 'like',"%$term%")
                  ->orWhere('name','like',"%$term%")
                  ->limit(10)
                  ->get();
        if(count($prods) == 0){
            $ret[]= array();
        }else{
            $n=0;
            foreach($prods as $prod){
                $ret[$n]['name'] = $prod->sku.' | '.$prod->name;
                $n++;
            }
        }

        return $ret;
    }
    public function fullReport(){
        $user = \Auth::user();
        $job = (new StockPriceReport($user));
        $this->dispatch($job);
        return redirect('/products')->with('message','O seu relatório será enviado por email!');
    }

    public function clone($sku){
        $providers = Providers::orderBy('name')->get();
        $origins = ProductOrigin::orderBy('id')->get();
        $product = Products::with('providers','images','stocks','prices')->where('sku',$sku)->first();
        return view('products.clone')->with(compact('product','providers','origins'));
    }

    public function postClone(Request $request){
        $product = $request->all();

        $data['product']['cost'] = 0;
        $data['product']['sku'] = $product['sku'];
        $data['product']['name'] = $product['name'];
        $data['product']['description'] = $product['description'];
        $data['product']['short_description'] = '';
        $data['product']['providers_id'] = $product['provider'];
        $data['product']['brand'] = $product['brand'];
        $data['product']['lead_time'] = $product['lead_time'];
        $data['product']['status'] = 0;
        $data['product']['nameml'] = $product['name'];

        $data['stock'] = $product['stock'];
        $data['dimensions'] = $product['dimensions'];
        $data['fiscals'] = $product['fiscals'];
        $data['prices'] = ['marketplaces_id' => 1, 'price' =>0, 'price2' => 0, 'price3' =>0];
        $data['images'] = ['url' => ''];
        /*Products
        ProductAttribute
        ProductDimension
        ProductExternalStock
        ProductFiscal
        ProductImage
        ProductPrices
        ProductOrigin
        ProductLog
        ProductCategory*/
        dd($data);


    }
}
