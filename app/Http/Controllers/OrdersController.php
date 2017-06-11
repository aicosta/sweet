<?php

namespace Sweet\Http\Controllers;

use Illuminate\Http\Request;

use Sweet\Http\Requests;
use Sweet\Http\Controllers\BlingController;
use Sweet\Http\Controllers\StockController;


// Order Models
use Sweet\Orders\Order;
use Sweet\Orders\OrderItem;
use Sweet\Orders\OrderMobly;
use Sweet\Orders\OrderStatus;
use Sweet\Orders\OrderComment;
use Sweet\Orders\ImportOrderLog;
use Sweet\Orders\OrderLogs;
//Product Models
use Sweet\Product\Providers;
use Sweet\Product\Products;

use Sweet\Customer;
use Sweet\ShippingCompany;

use Sweet\invoice;

use Sweet\Shipping;


use Auth;


class OrdersController extends Controller
{
    private $curl;
    private $bling;
    private $stocks;

    private $defaultStatus = 11;

    public function __construct(){
      $this->curl = new \anlutro\cURL\cURL;
      $this->bling = new BlingController;
      $this->stocks = new StockController;
    }
    public static function getLogs(){
      return ImportOrderLog::count();

    }
    public function orders(Request $request){
      $search = $request->input('search');
      $type= $request->input('type');

      switch($type){
        case 1:
          $orders = \DB::table('orders')
                        ->join('customers', 'customers.id', '=', 'orders.customers_id')
                        ->join('order_statuses', 'order_statuses.id', '=', 'orders.order_statuses_id')
                        ->leftJoin('invoices','invoices.orders_id', '=','orders.id')
                        ->leftJoin('shippings','shippings.orders_id', '=','orders.id')
                        ->where('orders.code', 'like', $search.'%')
                        ->groupBy('orders.id')
                        ->orderBy('orders.created_at','DESC')
                        ->select('orders.id','orders.code','orders.created_at','customers.name','orders.origin','orders.total','orders.freight','invoices.number','shippings.shipping_code','order_statuses.name as status','customers.document', 'invoices.created_at as dtFatura')
                        ->paginate(30);

          break;
        case 2:
          $orders = \DB::table('orders')
                        ->join('customers', 'customers.id', '=', 'orders.customers_id')
                        ->join('order_statuses', 'order_statuses.id', '=', 'orders.order_statuses_id')
                        ->leftJoin('invoices','invoices.orders_id', '=','orders.id')
                        ->leftJoin('shippings','shippings.orders_id', '=','orders.id')
                        ->where('customers.document', 'like', $search.'%')
                        ->groupBy('orders.id')
                        ->orderBy('orders.created_at','DESC')
                        ->select('orders.id','orders.code','orders.created_at','customers.name','orders.origin','orders.total','orders.freight','invoices.number','shippings.shipping_code','order_statuses.name as status','customers.document', 'invoices.created_at as dtFatura')
                        ->paginate(30);

          break;
        case 3:
          $orders = \DB::table('orders')
                        ->join('customers', 'customers.id', '=', 'orders.customers_id')
                        ->join('order_statuses', 'order_statuses.id', '=', 'orders.order_statuses_id')
                        ->leftJoin('invoices','invoices.orders_id', '=','orders.id')
                        ->leftJoin('shippings','shippings.orders_id', '=','orders.id')
                        ->where('invoices.number', 'like', '%'.$search.'%')
                        ->groupBy('orders.id')
                        ->orderBy('orders.created_at','DESC')
                        ->select('orders.id','orders.code','orders.created_at','customers.name','orders.origin','orders.total','orders.freight','invoices.number','shippings.shipping_code','order_statuses.name as status','customers.document', 'invoices.created_at as dtFatura')
                        ->paginate(30);
          break;
        case 4:
          $orders = \DB::table('orders')
                        ->join('customers', 'customers.id', '=', 'orders.customers_id')
                        ->join('order_statuses', 'order_statuses.id', '=', 'orders.order_statuses_id')
                        ->leftJoin('invoices','invoices.orders_id', '=','orders.id')
                        ->leftJoin('shippings','shippings.orders_id', '=','orders.id')
                        ->where('customers.name', 'like', $search.'%')
                        ->groupBy('orders.id')
                        ->orderBy('orders.created_at','DESC')
                        ->select('orders.id','orders.code','orders.created_at','customers.name','orders.origin','orders.total','orders.freight','invoices.number','shippings.shipping_code','order_statuses.name as status','customers.document', 'invoices.created_at as dtFatura')
                        ->paginate(30);
          break;
        case 5:
          $orders = \DB::table('orders')
                        ->join('order_items', 'orders.id', '=', 'order_items.orders_id')
                        ->join('customers', 'customers.id', '=', 'orders.customers_id')
                        ->join('order_statuses', 'order_statuses.id', '=', 'orders.order_statuses_id')
                        ->leftJoin('invoices','invoices.orders_id', '=','orders.id')
                        ->leftJoin('shippings','shippings.orders_id', '=','orders.id')
                        ->where('order_items.sku', 'like', '%'.$search.'%')
                        ->groupBy('orders.id')
                        ->orderBy('orders.created_at','DESC')
                        ->select('orders.id','orders.code','orders.created_at','customers.name','orders.origin','orders.total','orders.freight','invoices.number','shippings.shipping_code','order_statuses.name as status','customers.document', 'invoices.created_at as dtFatura')
                        ->paginate(30);
          break;
        case 6:
          $orders = \DB::table('orders')
                        ->join('customers', 'customers.id', '=', 'orders.customers_id')
                        ->join('order_statuses', 'order_statuses.id', '=', 'orders.order_statuses_id')
                        ->leftJoin('invoices','invoices.orders_id', '=','orders.id')
                        ->leftJoin('shippings','shippings.orders_id', '=','orders.id')
                        ->where('shippings.shipping_code', 'like', '%'.$search.'%')
                        ->groupBy('orders.id')
                        ->orderBy('orders.created_at','DESC')
                        ->select('orders.id','orders.code','orders.created_at','customers.name','orders.origin','orders.total','orders.freight','invoices.number','shippings.shipping_code','order_statuses.name as status','customers.document', 'invoices.created_at as dtFatura')
                        ->paginate(30);
          break;
        default:
          $orders = \DB::table('orders')
                        ->join('customers', 'customers.id', '=', 'orders.customers_id')
                        ->join('order_statuses', 'order_statuses.id', '=', 'orders.order_statuses_id')
                        ->leftJoin('invoices','invoices.orders_id', '=','orders.id')
                        ->leftJoin('shippings','shippings.orders_id', '=','orders.id')
                        ->groupBy('orders.id')
                        ->orderBy('orders.created_at','DESC')
                        ->select('orders.id','orders.code','orders.created_at','customers.name','orders.origin','orders.total','orders.freight','invoices.number','shippings.shipping_code','order_statuses.name as status','customers.document', 'invoices.created_at as dtFatura')
                        ->orderBy('orders.created_at','desc')
                        ->paginate(30);
          break;
      }



        return view('orders.list')->with(compact('orders'));
    }

    public function create(array $customerData, array $orderData, array $itemsData){
      if(!$this->checkOrder($orderData['code'])){
        if($customer = $this->createCustomer($customerData)){
          //Set the customer id
          $orderData['customers_id'] = $customer->id;
          $orderData['order_statuses_id'] = $this->defaultStatus;

          //$orderData['envio'] = ($orderData['order_statuses_id'] == '')?$orderData['order_statuses_id']:'normal';
          if($order = $this->createOrder($orderData)){
            foreach($itemsData as $item){
              $item['orders_id'] = $order->id;
              $this->createItem($item);
            }
            $log['code'] = $orderData['code'];
            $log['message'] = 'Pedido Incluido';
            $log['origin'] = $orderData['origin'];
          }else{
            $log['code'] = $orderData['code'];
            $log['message'] = 'Erro ao cadastrar o pedido';
            $log['origin'] = $orderData['origin'];
          }
        }else{
          $log['code'] = $orderData['code'];
          $log['message'] = 'Erro ao cadastrar o cliente';
          $log['origin'] = $orderData['origin'];
        }
      }else{

      }
      if(isset($log)){
        $this->saveLog($log);
      }
    }

    private function saveLog(array $data){
      $log = ImportOrderLog::create($data);
    }
    public function checkOrder($code){
      $check = Order::where('code',$code)->get();
      return (count($check) > 0)?true:false;
    }
    private function createCustomer($customerData){
      return Customer::create($customerData);
    }
    private function createOrder(array $orderData){
      return Order::create($orderData);
    }
    private function createItem(array $itemData){
      return OrderItem::create($itemData);
    }


   /**
    * @param  oder id
    * @return view order page with order data
    */
    public function view($id){
      $order = Order::with('items','customer')->find($id);
      $statuses = OrderStatus::get();
      $user = \Auth::id();
      $logs = OrderLogs::with('user')->where('order_id', $id)->get();
      $comments = OrderComment::with('user')->where('order_id', $id)->orderBy('id','desc')->get();
        return view('orders.view')->with(compact('order','statuses','user', 'comments','logs'));
    }


    /**
     * @param  unobrigatory string provider
     * @return invoice list view
     */
    public function invoice($id = false){
      $providers = Providers::orderBy('name')->get();
      $companies = ShippingCompany::orderBy('id')->get();
      if($id){
          $orders = \DB::table('orders')
                        ->leftJoin('order_items', 'orders.id', '=', 'order_items.orders_id')
                        ->leftJoin('products', 'order_items.sku', '=', 'products.sku')
                        ->leftJoin('providers', 'providers.id', '=', 'products.providers_id')
                        ->leftJoin('order_statuses', 'order_statuses.id', '=', 'orders.order_statuses_id')
                        ->where('order_statuses_id',1)
                        ->where('providers_id', $id)
                        ->select('orders.*', 'products.*', 'providers.name as fornecedor','order_items.quantity','orders.created_at as date','order_statuses.name as ostatus','order_items.sku as psku','order_items.name as pname')
                        ->get();
        }else{
          $orders = \DB::table('orders')
                        ->leftJoin('order_items', 'orders.id', '=', 'order_items.orders_id')
                        ->leftJoin('products', 'order_items.sku', '=', 'products.sku')
                        ->leftJoin('providers', 'providers.id', '=', 'products.providers_id')
                        ->leftJoin('order_statuses', 'order_statuses.id', '=', 'orders.order_statuses_id')
                        ->where('order_statuses_id',1)
                        ->select('orders.*', 'products.*', 'providers.name as fornecedor','order_items.quantity','orders.created_at as date','order_statuses.name as ostatus','order_items.sku as psku', 'order_items.name as pname')
                        ->get();
        }

         return view('orders.invoice')->with(compact('orders','providers','id','companies'));

      }

      public function invoiceStart(Request $request){

        $orders = $request->input('order');
        $shippingID = $request->input('shipping') ?? 1;
        $orders = array_filter($orders);
        foreach($orders as $order){
          unset($stocks);
          $url = 'https://bling.com.br/Api/v2/notafiscal/json/';
          $orderData = Order::where('code',$order)->first();
          $customer = Customer::find($orderData->customers_id);
          $transportadora = ShippingCompany::find($shippingID);
          $items = OrderItem::where('orders_id', $orderData->id)->get();


          $tipoPessoa = ($customer->type == 'PF')?'F':'J';
          unset($xml);
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
                    <pedido>
                      <tipo>S</tipo>
                      <numero_loja>'.$orderData->id.'</numero_loja>
                        <cliente>
                            <nome><![CDATA['.$customer->name.']]></nome>
                            <tipoPessoa>'.$tipoPessoa.'</tipoPessoa>
                            <cpf_cnpj>'.$customer->document.'</cpf_cnpj>
                            <ie_rg>'.$customer->document2.'</ie_rg>
                            <endereco><![CDATA['.$customer->address.']]></endereco>
                            <numero>'.$customer->number.'</numero>
                            <complemento><![CDATA['.$customer->complement.']]></complemento>
                            <bairro><![CDATA['.$customer->quarter.']]></bairro>
                            <cep>'.$customer->zip_code.'</cep>
                            <cidade><![CDATA['.$customer->city.']]></cidade>
                            <uf>'.$customer->state.'</uf>
                            <fone>'.$customer->phone.'</fone>
                            <email>'.$customer->email.'</email>
                        </cliente>
                        <transporte>
                            <transportadora>'.$transportadora->name.'</transportadora>
                            <cpf_cnpj>'.$transportadora->document1.'</cpf_cnpj>
                            <ie_rg>'.$transportadora->document2.'</ie_rg>
                            <endereco>'.$transportadora->address.'</endereco>
                            <cidade>'.$transportadora->city.'</cidade>
                            <uf>'.$transportadora->uf.'</uf>
                            <tipo_frete>R</tipo_frete>
                            <servico_correios>SEDEX</servico_correios>
                        </transporte>
                        <itens>';
                        foreach($items as $item){
                          $stocks[] = array('sku'=> $item['sku'], 'outs' => $item['quantity']);
                          if($item['sku'] == 'verify'){
                             $log[] = $orderData->code.' O pedido não pode ser faturado com o sku errado<br />';
                             continue;
                          }
                          $xml .= '<item>
                                <codigo>'.$item['sku'].'</codigo>
                                <descricao><![CDATA['.$item['name'].']]></descricao>
                                <ncm>'.$this->getNCM($item['sku']).'</ncm>
                                <un>un</un>
                                <qtde>'.$item['quantity'].'</qtde>
                                <vlr_unit>'.$item['price'].'</vlr_unit>
                                <tipo>P</tipo>
                            </item>';
                        }
                $xml .= '</itens>
                        <parcela>
                          <dias>30</dias>
                          <data>'.date('d/m/Y', strtotime('+30 days')).'</data>
                          <vlr>'.($orderData->total+$orderData->freight).'</vlr>
                          <obs>Somente uso Interno</obs>
                        </parcela>
                        <vlr_frete>'.$orderData->freight.'</vlr_frete>
                        <vlr_desconto>0</vlr_desconto>
                        <obs>Nfe Referente ao pedido '.$orderData->origin.' # '.$orderData->code.' # '.$orderData->id.'</obs>
                        <obs_internas></obs_internas>
                    </pedido>';

            $posts =
            [
              'apikey' => 'c60b88ff5b0c5b10f99cc991aa31b1661bea8275',
              'xml' => $xml
            ];

            //$request = $this->curl->newRequest('post', $url)
                                //->send();

            $request = $this->sendtoBling($url, $posts);
            $request = json_decode($request);

            if(isset($request->retorno->notasfiscais[0]->notaFiscal->numero)){

              $numeroNf = $request->retorno->notasfiscais[0]->notaFiscal->numero;
              $log[] = 'Pedido '.$orderData->code.' Faturado';
              $this->changeStatus($orderData->id, 2);
              //$orderData->order_statuses_id = 2;
              //$orderData->save();

              $invoice = new invoice();

              $invoice->number = $numeroNf;
              $invoice->serie = 1;
              $invoice->orders_id = $orderData->id;
              $invoice->save();
              foreach($stocks as $stock){
                $stock['origin'] = $numeroNf;
                $this->stocks->setStock($stock);
              }
              $keys = $this->fatura($numeroNf, 1);

              if($keys){
                $invoice->url = $keys['url'];
                $invoice->key = $keys['key'];
                $invoice->save();
              }
            }else{
              echo '<pre>';
              print_r($request);
              echo '</pre>';
              if(isset($request->retorno->erros[0]->erro->msg) && $request->retorno->erros[0]->erro->msg == 'Ja existe uma nota fiscal cadastrada com este XML'){
                $this->changeStatus($orderData->id, 2);
                $log[] = 'Pedido '.$orderData->code.' '.$request->retorno->erros[0]->erro->msg;
              }else{
                //$log[] = 'Pedido '.$orderData->code.' Erro ao emitir a nfe '.$request;
              }

            }
            sleep(1);
        }
        echo '<pre>';
        print_r($log);
        echo '</pre>';
      }

      public function getNCM($sku){

        $prod = Products::with('fiscals')->where('sku',$sku)->first();

        return (isset($prod->fiscals->ncm))?$prod->fiscals->ncm:'';
      }
      public function sendtoBling($url, $data){
          $curl_handle = curl_init();
          curl_setopt($curl_handle, CURLOPT_URL, $url);
          curl_setopt($curl_handle, CURLOPT_POST, count($data));
          curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
          curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
          $response = curl_exec($curl_handle);
          curl_close($curl_handle);
          return $response;
      }

      public function fatura($nf, $serie){
        $url = 'https://bling.com.br/Api/v2/notafiscal/json/';
        $posts = array (
            "apikey"    => "c60b88ff5b0c5b10f99cc991aa31b1661bea8275",
            "number"    => $nf,
            "serie"     => $serie,
            "sendEmail" => 'true'
        );
        $request = $this->executeSendFiscalDocument($url, $posts);
        $request = json_decode($request);
        if(isset($request->retorno->notaFiscal->chaveAcesso)){
          $ret['url'] = $request->retorno->notaFiscal->linkDanfe;
          $ret['key'] = $request->retorno->notaFiscal->chaveAcesso;
        }else{
          $ret = false;
        }
        return $ret;
      }

      public function executeSendFiscalDocument($url, $data){
          $curl_handle = curl_init();
          curl_setopt($curl_handle, CURLOPT_URL, $url);
          curl_setopt($curl_handle, CURLOPT_POST, count($data));
          curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
          curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
          $response = curl_exec($curl_handle);
          curl_close($curl_handle);
          return $response;
      }

      public function core(){
        $orders = Order::with('items','customer','invoice','shipping')->get();
        return view('orders.core')->with(compact('orders'));
      }
      public function syncKeys(){
        $invoices = invoice::whereNull('key')->get();

        foreach($invoices as $i){

          $url = 'https://bling.com.br/Api/v2/notafiscal/'.$i->number.'/'.$i->serie.'/json?apikey=c60b88ff5b0c5b10f99cc991aa31b1661bea8275';


          $request = $this->curl->newRequest('get', $url)
                                  ->send();
            $request = json_decode($request);
          if(isset($request->retorno->notasfiscais[0]->notafiscal->chaveAcesso)){
            $i->key = $request->retorno->notasfiscais[0]->notafiscal->chaveAcesso;
            $i->save();
          }else{

          }
        }
      }
      public function toPrint(){
        $list = Order::with(['items' => function ($query) {
                                                          $query->where('sku', 'not like', '978%');
                                                        }
          ,'customer'])->where('printed',0)->get();
        return $list;
      }

      public function printed($id){
        $order = Order::find($id);
        $order->printed = 1;
        $order->save();
      }


      public function itemsFix(){
        $items = OrderItem::where('name','')->get();
        foreach($items as $i){
          $name = $this->getProductName($i->sku);
          if($name){
            $i->name = $name;
            $i->save();
          }

        }
      }

      public function getProductName($sku){
        $product= Products::where('sku',$sku)->first();
        if($product){
          return $product->name;
        }else{
          return false;
        }

      }

      public function invoiceSync(){
        $numbers = file_get_contents('/home/fullhubcom/public_html/fornecedores/sweet/storage/nf.csv');

        $data = explode(PHP_EOL, $numbers);
        ob_start();
        foreach($data as $d){
          $line = explode('|', $d);
          if(!$this->checkNf($line[0])){
            $apikey = "c60b88ff5b0c5b10f99cc991aa31b1661bea8275";
            $documentNumber = (int)$line[0];
            $documentSerie = 1;
            $outputType = "json";

            $url = 'https://bling.com.br/Api/v2/notafiscal/' . $documentNumber . '/'. $documentSerie . '/' . $outputType;
            $ret = $this->executeGetFiscalDocument($url, $apikey);
            $ret = $ret->retorno->notasfiscais;
            foreach($ret as $r){

              $customer = Customer::where('document',$r->notafiscal->cnpj)
                                    ->first();
              if(count($customer) == 0){
                echo $line[0]." Pedido não encontrado\n";
              }else{
                if($r->notafiscal->tipo == 'S'){
                  $order = Order::where('customers_id',$customer->id)->first();
                  if(count($order) == 0){
                    echo $line[0]." Sem Pedido\n";
                  }else{
                    $invoice = new Invoice();
                    $invoice->orders_id = $order->id;
                    $invoice->number = $r->notafiscal->numero;
                    $invoice->serie = $r->notafiscal->serie;
                    $invoice->key = $r->notafiscal->chaveAcesso;

                    if($invoice->save()){
                      echo $line[0]." Inserida\n";
                    }
                  }

                }
              }
            }
          }else{
            //echo $line[0]." já cadastrada<br />";
          }


          flush();
          ob_flush();

        }
      }
      public function checkNf($number){

        $nfe = invoice::where('number',$number)
                        ->orWhere('number',(int)$number)
                        ->get();
        return(count($nfe) >0)?true:false;
      }
      public function executeGetFiscalDocument($url, $apikey){
          $curl_handle = curl_init();
          curl_setopt($curl_handle, CURLOPT_URL, $url . '&apikey=' . $apikey);
          curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
          $response = curl_exec($curl_handle);
          curl_close($curl_handle);
          return json_decode($response);
      }

      public function checkNfE($number){
        $check = invoice::where('number', $number)->get();
        return (count($check) == 0)?true:false;
      }

      public function moblyIndex(){
        return view('mobly.index');
      }
      public function mobly(Request $request){

        $file = $request->file('mobly');

        $orders = file_get_contents($file->getRealPath());


        $data = explode(PHP_EOL, $orders);
        unset($data[0]);
        foreach($data as $d){

          $line = explode(';', $d);

          $n=0;
          foreach($line as $l){
            $dt[$n] = str_replace('"', '', $l);
            $n++;
          }


          $o = OrderMobly::where('order_item_id', $dt[0])->first();

          //dd($o);
          if(count($o) == 0){
            unset($order);

            $oMobly = new OrderMobly();
            $oMobly->order_item_id = $dt[0];
            $oMobly->mobly_id = $dt[1];
            $oMobly->seller_sku = $dt[2];
            $oMobly->mobly_sku = $dt[3];
            $oMobly->created_at = $dt[4];
            $oMobly->updated_at = $dt[5];
            $oMobly->order_number = $dt[6];
            $oMobly->customer_name = $dt[7];
            $oMobly->national_registration_number = $dt[8];
            $oMobly->shipping_name = $dt[9];
            $oMobly->shipping_address = $dt[10];
            $oMobly->shipping_address2 = $dt[11];
            $oMobly->shipping_address3 = $dt[12];
            $oMobly->shipping_address4 = $dt[13];
            $oMobly->shipping_address5 = $dt[14];
            $oMobly->shipping_phone_number = $dt[15];
            $oMobly->shipping_phone_number2 = $dt[16];
            $oMobly->shipping_city = $dt[17];
            $oMobly->shipping_postcode = $dt[18];
            $oMobly->shipping_country = $dt[19];
            $oMobly->billing_name = $dt[20];
            $oMobly->billing_address = $dt[21];
            $oMobly->billing_address2 = $dt[22];
            $oMobly->billing_address3 = $dt[23];
            $oMobly->billing_address4 = $dt[24];
            $oMobly->billing_address5 = $dt[25];
            $oMobly->billing_phone_number = $dt[26];
            $oMobly->billing_phone_number2 = $dt[27];
            $oMobly->billing_city = $dt[28];
            $oMobly->billing_postcode = $dt[29];
            $oMobly->billing_country = $dt[30];
            $oMobly->payment_method = $dt[31];
            $oMobly->paid_price = $dt[32];
            $oMobly->unit_price = $dt[33];
            $oMobly->shipping_fee = $dt[34];
            $oMobly->wallet_credits = $dt[35];
            $oMobly->item_name = $dt[36];
            $oMobly->variation = $dt[37];
            $oMobly->cd_shipping_provider = $dt[38];
            $oMobly->shipping_provider = $dt[39];
            $oMobly->shipment_type_name = $dt[40];
            $oMobly->shipping_provider_type = $dt[41];
            $oMobly->cd_tracking_code = $dt[42];
            $oMobly->tracking_code = $dt[43];
            $oMobly->tracking_url = $dt[44];
            $oMobly->invoice_key = $dt[45];
            $oMobly->promised_shipping_time = $dt[46];
            $oMobly->premium = $dt[47];
            $oMobly->status = $dt[48];
            $oMobly->reason = $dt[49];


            if($oMobly->save()){
              echo $dt[0].' Pedido Incluido<br />';
            }

          }else{
            echo $dt[0].' ok<br />';
          }
        }
        $this->run();
      }
      public function run(){
        $result = \DB::table('orders_mobly')->whereNotIn('order_number', function($q){
            $q->select('code')->from('orders');
        })->get();

        //$result = \DB::table('orders_mobly')->where('order_number','708911987')->get();
        foreach($result as $r){
          $check = Order::where('code',$r->order_number)->first();
          if($r->status == 'pending'  && count($check) == 0){
            $address = explode('-', $r->shipping_address);
            $d = explode(',', $address[0]);

            $data['rua'] = trim($d[0]);
            $data['numero'] = trim($d[1]);

            $data['complemento'] = isset($address[3])?trim($address[2]):'';

            $data['estado'] = trim(end($address));

            $customer = new Customer();
            $customer->name = $r->shipping_name;
            $customer->document = str_replace('.', '', str_replace('-','',str_replace('/','', $r->national_registration_number)));
            $customer->document2 = '';
            $customer->type = 'PF';
            $customer->email = 'hello@fullhub.com.br';
            $customer->phone = $r->shipping_phone_number;
            $customer->phone2 = '';

            $customer->zip_code = str_replace('-','',$r->shipping_postcode);
            $customer->address = $data['rua'];
            $customer->number = $data['numero'];
            $customer->complement = $data['complemento'];
            $customer->quarter = 'ni';
            $customer->reference = '';
            $customer->city = $r->shipping_city;
            $customer->state = $this->convertState($data['estado']);
            if($customer->save()){

              $ordernew = new Order();
              $ordernew->code = $r->order_number;
              $ordernew->total = $r->paid_price;
              $ordernew->freight = $r->shipping_fee;
              $ordernew->comission = 0;
              $ordernew->origin = 'MOBLY';
              $ordernew->max_date = date('Y-m-d H:i:s', strtotime($r->promised_shipping_time));
              $ordernew->order_statuses_id = 11;
              $ordernew->customers_id = $customer->id;
              //$ordernew->created_at = date('Y-m-d H:i:s', strtotime($r->created_at));

                if($ordernew->save()){
                  $items = OrderMobly::where('order_number',$r->order_number)->get();
                  $c=0;
                  $total = 0;
                  $frete = 0;
                  foreach($items as $i){
                    $total = $total+$i->paid_price;
                    $frete = $frete+$i->shipping_fee;

                    $item[$c] =  new OrderItem();
                    $item[$c]->sku = $i->seller_sku;
                    $item[$c]->name = $i->item_name;
                    $item[$c]->price = $i->unit_price;
                    $item[$c]->quantity = 1;
                    $item[$c]->orders_id = $ordernew->id;
                    $item[$c]->save();
                    $c++;
                  }

                  $ordernew->total = $total;
                  $ordernew->freight = $frete;
                  $ordernew->save();
                  echo $r->order_number.' Pedido Incluido<br />';

                }
            }
          }else{
            echo $r->order_number.' ja tem<br />';
          }

        }
      }

    public function changeStatus($id, $status_id){
      $userId = Auth::User()->id ?? 17;
      $order = Order::find($id);

      if($order->order_statuses_id != $status_id){
        $status['order_id'] = (int)$id;
        $status['user_id'] = $userId;
        $status['old_status'] = $order->status->name;

        $status['new_status'] = OrderStatus::find($status_id)->name;


        $order->order_statuses_id = $status_id;

        if($order->save()){
          $this->saveLogs($status);
        }
      }

    }

    public function saveLogs($data){

      return OrderLogs::create($data) ?? false;
    }

    public function convertState($name){

      $data['Acre'] = 'AC';
      $data['Alagoas'] = 'AL';
      $data['Amapá'] = 'AP';
      $data['Amazonas'] = 'AM';
      $data['Bahia'] = 'BA';
      $data['Ceará'] = 'CE';
      $data['Distrito Federal'] = 'DF';
      $data['Espírito Santo'] = 'ES';
      $data['Goiás'] = 'GO';
      $data['Maranhão'] = 'MA';
      $data['Mato Grosso'] = 'MT';
      $data['Mato Grosso do Sul'] = 'MS';
      $data['Minas Gerais'] = 'MG';
      $data['Pará'] = 'PA';
      $data['Paraíba'] = 'PB';
      $data['Paraná'] = 'PR';
      $data['Pernambuco'] = 'PE';
      $data['Piauí'] = 'PI';
      $data['Rio de Janeiro'] = 'RJ';
      $data['Rio Grande do Norte'] = 'RN';
      $data['Rio Grande do Sul'] = 'RS';
      $data['Rondônia'] = 'RO';
      $data['Roraima'] = 'RR';
      $data['Santa Catarina'] = 'SC';
      $data['São Paulo'] = 'SP';
      $data['Sergipe'] = 'SE';
      $data['Tocantins'] = 'TO';
      $data['vitoria'] = 'ES';

      return $data[$name];
    }


    public function byStatus($id = false){

      $statuses = OrderStatus::get();
      if($id){
        $orders = Order::with('customer','invoice','shipping','items')->where('order_statuses_id',$id)->get();
      }else{
        $d1 = date('Y-m-d 23:59:59');
        $d2 = date('Y-m-d 00:00:00', strtotime('-90 days'));
        $orders = Order::with('customer','invoice','shipping','items')
                         ->whereBetween('created_at', [$d2, $d1])
                        ->get();
      }

      $n =0;

      foreach($orders as $order){


        foreach($order->items as $item){

          $stock = $this->stocks->getStock($item->sku);
          $return[$n]['id'] = $order->id;
          $return[$n]['data'] = $order->created_at;
          $return[$n]['pedido'] = $order->code;
          $return[$n]['origem'] = $order->origin;
          $return[$n]['sku'] = $item->sku;
          $return[$n]['nome'] = $item->name;
          $return[$n]['quantidade'] = $item->quantity;
          $return[$n]['status'] = $order->status->name;
          $return[$n]['state'] = $order->customer->state;
          $return[$n]['nota'] = $order->invoice->number ?? '';
          $return[$n]['dataNota'] = $order->invoice->created_at ?? '';
          $return[$n]['rastreio'] = $order->shipping->shipping_code ?? '';
          $return[$n]['estoqueInterno'] = $stock['internal'];
          $return[$n]['estoqueExterno'] = $stock['external'];
          $return[$n]['fornecedor'] = '';
          if(isset($item->product->providers->name)){
            $return[$n]['fornecedor'] .= $item->product->providers->name;
          }
          $n++;
        }


      }


      unset($orders);
      $orders = $return;
      return view('orders.byStatus')->with(compact('statuses','orders'));

    }
    public function list(){
      $statuses = OrderStatus::select('id','name')->orderBy('name')->get();
      $origins = Order::distinct()->orderBy('origin')->get(['origin as name']);
      return view('orders.filterList')->with(compact('statuses','origins'));
    }
    public function filter(Request $request){

      $date = $request->input('date') ?? false;
      $status = $request->input('status') ?? false;
      $origin = $request->input('origin') ?? false;
      $search = $request->input('search') ?? false;
      $appendsArray = array();

      $orders = Order::with('customer','invoice','shipping','items');

      if($date){
        $appendsArray['date'] = $date;
        $date = json_decode($date);
        $date[0] = date('Y-m-d', strtotime(str_replace('/','-',$date[0]))).' 00:00:00';
        $date[1] = date('Y-m-d', strtotime(str_replace('/','-',$date[1]))).' 23:59:00';

        $orders->whereBetween('created_at', [$date[0], $date[1]]);


      }
      if($origin){
        $appendsArray['origin'] = $origin;
        $origin = json_decode($origin);
        if(count($origin) == 1){
          $orders->where('origin',$origin[0]);
        }else{
          $n=0;
          foreach($origin as $o){
            if($n == 0){
              $orders->where('origin',$o);
            }else{
              $orWhere[] = ['origin', $o];
            }
            $n++;
          }
          $orders->orWhere($orWhere);
        }
      }
      $n=0;
      if($status){
        $appendsArray['status'] = $status;
        $status = json_decode($status);
        $orders->where('order_statuses_id',$status[0]);
      }

      $orders = $orders->paginate(20);


      $n =0;

      $return['total'] = $orders->total();
      $return['lastPage'] = $orders->lastPage();
      $return['currentPage'] = $orders->currentPage();
      $return['perPage'] = $orders->perPage();
      $return['nextPage'] = $orders->nextPageUrl();
      foreach($orders as $order){
        foreach($order->items as $item){
          $return['orders'][$n]['id'] = $order->id;
          $return['orders'][$n]['data'] = date('d/m/Y', strtotime($order->created_at));
          $return['orders'][$n]['pedido'] = $order->code;
          $return['orders'][$n]['origem'] = $order->origin;
          $return['orders'][$n]['fornecedor'] = $item->product->providers->name ?? '';
          $return['orders'][$n]['sku'] = $item->sku;
          $return['orders'][$n]['nome'] = $item->name;
          $return['orders'][$n]['quantidade'] = $item->quantity;
          $return['orders'][$n]['status'] = $order->status->name;
          $return['orders'][$n]['state'] = $order->customer->state;
          $return['orders'][$n]['nota'] = $order->invoice->number ?? '';
          $return['orders'][$n]['dataNota'] = isset($order->invoice->created_at)?
                        date('d/m/Y', strtotime($order->invoice->created_at)):
                        '';
          $return['orders'][$n]['rastreio'] = $order->shipping->shipping_code ?? '';
          $n++;
        }
      }
      $return['totalItem'] = $n;

      return response()->json($return);


    }
    public function fullReport($offset =2){
      $limit = 15000;
      $offset = $limit*$offset;

      $orders = Order::with('customer','invoice','shipping','items')->offset($offset)->limit($limit)->get();
      $n=0;
      foreach($orders as $o){


        foreach($o->items as $i){
          $data[$n]['data'] = $o->created_at;
          $data[$n]['Pedido'] = $o->code;
          $data[$n]['Origem'] = $o->origin;
          $data[$n]['Valor'] = number_format($o->total,2,'.','');
          $data[$n]['Frete'] = number_format($o->freight,2,'.','');
          $data[$n]['sku'] = $i->sku;
          $data[$n]['nome'] = $i->name;
          $data[$n]['qty'] = $i->quantity;
          $data[$n]['valorVenda'] = $i->price;
          $data[$n]['fornecedor'] = (isset($i->product->providers->name))?$i->product->providers->name:'';
          $data[$n]['status'] = $o->status->name;
          if(isset($o->invoice->number)){
            $data[$n]['nfe'] = $o->invoice->number;
          }else{
            $data[$n]['nfe'] = '';
          }
          if(isset($o->shipping->shipping_code)){
            $data[$n]['rastreio'] = $o->shipping->shipping_code;
          }else{
            $data[$n]['rastreio'] = '';
          }
          $n++;
        }

      }
      \Excel::create('orders', function($excel) use($data) {
          $excel->sheet('Sheet 1', function($sheet) use($data) {
              $sheet->fromArray($data);
          });
      })->download('csv');
    }

    public function buyOrInvoide($id = false){
      $providers = Providers::orderBy('name')->get();

      if($id){
        $orders = \DB::table('orders')
                        ->leftJoin('customers', 'orders.customers_id', '=', 'customers.id')
                        ->leftJoin('order_items', 'orders.id', '=', 'order_items.orders_id')
                        ->leftJoin('products', 'order_items.sku', '=', 'products.sku')
                        ->leftJoin('providers', 'providers.id', '=', 'products.providers_id')
                        ->leftJoin('order_statuses', 'order_statuses.id', '=', 'orders.order_statuses_id')
                        ->where('order_statuses_id',11)
                        ->where('providers.id',$id)
                        ->select('orders.id as oid','orders.*', 'products.*', 'providers.name as fornecedor','order_items.quantity','orders.created_at as date','order_statuses.name as ostatus','order_items.id as pid','order_items.name as pname','order_items.sku as psku', 'customers.state as estado','products.cost')
                        ->get();
      }else{
        $orders = \DB::table('orders')
                        ->leftJoin('customers', 'orders.customers_id', '=', 'customers.id')
                        ->leftJoin('order_items', 'orders.id', '=', 'order_items.orders_id')
                        ->leftJoin('products', 'order_items.sku', '=', 'products.sku')
                        ->leftJoin('providers', 'providers.id', '=', 'products.providers_id')
                        ->leftJoin('order_statuses', 'order_statuses.id', '=', 'orders.order_statuses_id')
                        ->where('order_statuses_id',11)
                        ->select('orders.id as oid','orders.*', 'products.*', 'providers.name as fornecedor','order_items.quantity','orders.created_at as date','order_statuses.name as ostatus','order_items.id as pid','order_items.name as pname','order_items.sku as psku', 'customers.state as estado','products.cost')
                        ->get();
      }
      $n=0;
      foreach($orders as $o){

        $stock = $this->stocks->getStock($o->sku);

        $ret[$n]['oid'] = $o->oid;
        $ret[$n]['date'] = $o->date;
        $ret[$n]['code'] = $o->code;
        $ret[$n]['origin'] = $o->origin;
        $ret[$n]['estado'] = $o->estado;
        $ret[$n]['pid'] = $o->pid;
        $ret[$n]['psku'] = $o->psku;
        $ret[$n]['pname'] = $o->pname;
        $ret[$n]['quantity'] = $o->quantity;
        $ret[$n]['cost'] = $o->cost;
        $ret[$n]['fornecedor'] = $o->fornecedor;
        $ret[$n]['ostatus'] = $o->ostatus;
        $ret[$n]['estoqueInterno'] = $stock['internal'];
        $ret[$n]['estoqueExterno'] = $stock['external'];

        $n++;
      }

      unset($orders);
      $orders = $ret;
      return view ('orders.buyOrInvoice')->with(compact('providers','orders','id'));
    }


    public function insertShipping(Request $request){
      $order = Order::find($request->input('pedido'));
      if($order){
        $shipping = new Shipping;
        $shipping->orders_id = $order->id;
        $shipping->shipping_companies_id = 1;
        $shipping->weight = 0;
        $shipping->shipping_code = $request->input('rastreio');
        $shipping->created_at = date('Y-m-d H:i:s' ,strtotime($request->input('data')));

        if($shipping->save()){
          $order->order_statuses_id = 3;
          if($order->save()){
            return 1;
          }else{
            return 0;
          }
        }else{
          return 2;
        }
      }else{
        return 0;
      }
    }





    public function cepMobly(){
      $orders = Order::with('customer')->where('origin','mobly')->get();
      foreach($orders as $order){
        $order->customer->zip_code = str_replace('-', '', $order->customer->zip_code);
        if(!is_numeric($order->customer->zip_code)){
          $mobly = OrderMobly::where('order_number',$order->code)->first();
          $cep = str_replace('-','',$mobly->shipping_postcode);
          $old = $order->customer->zip_code;
          $order->customer->zip_code = $cep;
          if($order->customer->save()){
            echo 'Pedido: '.$order->code.' Old: '.$old.' New: '.$cep.'<br />';
          }
        }
      }
    }





    public function comment(Request $request){
          $dataComment = $request->input('comment');
          $orderId = $request->input('orderId');
          $userId = $request->input('userId');
          $father = $request->input('father');

          $comment = new OrderComment;
          $comment->order_id = $orderId;
          $comment->user_id = $userId;
          $comment->content = $dataComment;
          $comment->father = $father;

          if($comment->save()){
            return 1;
          }else{
            return 0;
          }
    }




    public function tamarindos(){

      $curl = curl_init();
      curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => 'http://tamarindos.com.br/sweet/getOrders.php'
      ));

      $orders = curl_exec($curl);
      $orders = json_decode($orders);

      foreach($orders as $order){
          $check = Order::where('code',$order->pedido->codExterno)->first();
          if(count($check) == 0){


            $customer = new Customer();
            $customer->name = $order->cliente->nome;
            $customer->document = str_replace('.', '',
                                    str_replace('-','',
                                      str_replace('/','', $order->cliente->cpf_cnpj)
                                    )
                                  );
            $customer->document2 = '';
            $customer->type = 'PF';
            $customer->email = $order->cliente->email;
            $customer->phone = $order->cliente->fone;
            $customer->phone2 = '';

            $customer->zip_code = str_replace('-','',$order->cliente->cep);
            $customer->address = $order->cliente->endereco;
            $customer->number = $order->cliente->numero;
            $customer->complement = $order->cliente->complemento;
            $customer->quarter = $order->cliente->bairro;
            $customer->reference = '';
            $customer->city = $order->cliente->cidade;
            $customer->state = $order->cliente->uf;
            if($customer->save()){

              $ordernew = new Order();
              $ordernew->code = $order->pedido->codExterno;
              $ordernew->total = $order->pedido->ValorTotal;
              $ordernew->freight = $order->pedido->ValorFrete;
              $ordernew->comission = 0;
              $ordernew->origin = 'TAMARINDOS';
              $ordernew->max_date = date('Y-m-d H:i:s', strtotime('+ 7 Days'));
              $ordernew->order_statuses_id = 12;
              $ordernew->customers_id = $customer->id;
              //$ordernew->created_at = date('Y-m-d H:i:s', strtotime($r->created_at));

                if($ordernew->save()){
                  $c=0;
                  foreach($order->item as $i){
                    $item[$c] =  new OrderItem();
                    $item[$c]->sku = $i->SKU;
                    $item[$c]->name = $i->nome;
                    $item[$c]->price = $i->Valor;
                    $item[$c]->quantity = $i->Qty;
                    $item[$c]->orders_id = $ordernew->id;
                    $item[$c]->save();
                    $c++;
                  }
                  echo $order->pedido->codExterno.' Pedido Incluido<br />';

                }
            }
          }else{
            echo $order->pedido->codExterno.' ja tem<br />';
          }

        }
    }
    

    public function export(Request $request){
      $date = $request->input('date') ?? false;
      $status = $request->input('status') ?? false;
      $origin = $request->input('origin') ?? false;
      $search = $request->input('search') ?? false;
      $appendsArray = array();

      $orders = Order::with('customer','invoice','shipping','items');

      if($date){
        $appendsArray['date'] = $date;
        $date = json_decode($date);
        $date[0] = date('Y-m-d', strtotime(str_replace('/','-',$date[0]))).' 00:00:00';
        $date[1] = date('Y-m-d', strtotime(str_replace('/','-',$date[1]))).' 23:59:00';

        $orders->whereBetween('created_at', [$date[0], $date[1]]);


      }
      if($origin){
        $appendsArray['origin'] = $origin;
        $origin = json_decode($origin);
        if(count($origin) == 1){
          $orders->where('origin',$origin[0]);
        }else{
          $n=0;
          foreach($origin as $o){
            if($n == 0){
              $orders->where('origin',$o);
            }else{
              $orWhere[] = ['origin', $o];
            }
            $n++;
          }
          $orders->orWhere($orWhere);
        }
      }

      if($status){
        $appendsArray['status'] = $status;
        $status = json_decode($status);
        $orders->where('order_statuses_id',$status[0]);
      }

      $orders = $orders->get();
      $n=0;
      foreach($orders as $order){
        foreach($order->items as $item){
          $return[$n]['id'] = $order->id;
          $return[$n]['data'] = date('d/m/Y', strtotime($order->created_at));
          $return[$n]['pedido'] = $order->code;
          $return[$n]['origem'] = $order->origin;
          $return[$n]['fornecedor'] = $item->product->providers->name ?? '';
          $return[$n]['sku'] = $item->sku;
          $return[$n]['nome'] = $item->name;
          $return[$n]['quantidade'] = $item->quantity;
          $return[$n]['status'] = $order->status->name;
          $return[$n]['state'] = $order->customer->state;
          $return[$n]['nota'] = $order->invoice->number ?? '';
          $return[$n]['dataNota'] = isset($order->invoice->created_at)?
                        date('d/m/Y', strtotime($order->invoice->created_at)):
                        '';
          $return[$n]['rastreio'] = $order->shipping->shipping_code ?? '';
          $n++;
        }
      }
      \Excel::create('Relatorio de pedidos', function($excel) use ($return){
          $excel->sheet('Sheetname', function($sheet) use ($return) {
              $sheet->fromArray($return);
          });
      })->download('xls');
    }



  public function autoStatus()
  {
    $orders = Order::with('items')->where('order_statuses_id',11)
                                  ->orWhere('order_statuses_id',14)
                                  ->orderBy('created_at','ASC')
                                  ->get();
    $used = array();
    foreach($orders as $order){
      $ret = false;
      foreach($order->items as $item){
        $stock = $this->stocks->getStock($item->sku);
        if($stock['internal'] >= $item->quantity){
          if(isset($used[$item->sku]) && $used[$item->sku] >= $stock['internal']){
            echo $item->sku.' Todos utilizados pelo sistema<br />';
          }else{
            $used[$item->sku] = $item->quantity;
            $ret = true;
          }
          
        }else{
          $ret = false;
        }
      }

      if($ret){
        $this->changeStatus($order->id, 1);
        echo $order->code.' Liberado para faturamento<br />';
      }else{
        echo $order->code.' Nenhum Disponivel para liberação<br />';
      }
    }
  }
}
