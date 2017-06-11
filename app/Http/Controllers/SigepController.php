<?php

namespace Sweet\Http\Controllers;

use Illuminate\Http\Request;

use Sweet\Http\Requests;
use Sweet\Http\Controllers\OrdersController;
use Sweet\Orders\Order;
use Sweet\Orders\OrderItem;
use Sweet\Orders\Customer;
use Sweet\Product\Products;
use Sweet\Sigep\Etiquetas;
use Sweet\Sigep\Plp;
use Sweet\Sigep\PlpItens;
use Sweet\Shipping;
use Sweet\invoice;

class SigepController extends Controller
{

    private $accessData;
    private $config;
    private $oc;
    private $prazoExtra = 3;
    private $valorExtra = 8;

    public function __construct(){
        $this->oc = new OrdersController;
        $this->accessData = $this->setAccessData();
        $this->setConfig();
        \PhpSigep\Bootstrap::start($this->config);
    }

    public function setAccessData(){
        $accessData = new \PhpSigep\Model\AccessData();
        $accessData->setCodAdministrativo('15278816');
        $accessData->setUsuario('12408070');
        $accessData->setSenha('k0f55b');
        $accessData->setCartaoPostagem('71427732');
        $accessData->setCnpjEmpresa('12408070000162');
        $accessData->setNumeroContrato('9912382275');
        $accessData->setDiretoria(new \PhpSigep\Model\Diretoria(\PhpSigep\Model\Diretoria::DIRETORIA_DR_SAO_PAULO));

        return $accessData;
    }
    public function setConfig(){
        $this->config = new \PhpSigep\Config();
        $this->config->setAccessData($this->accessData);
        $this->config->setEnv(\PhpSigep\Config::ENV_PRODUCTION);
        $this->config->setCacheOptions(
            array(
                'storageOptions' => array(
                    'enabled' => false,
                    'ttl' => 10,
                    'cacheDir' => sys_get_temp_dir(),
                ),
            )
        );

    }
    public function fechaPlp(Request $request, $type = 'pac'){
        $list = $request->input('order');

        $reserve = Etiquetas::where('format', $type)
                            ->where('used',0)
                            ->limit(count($list))
                            ->get();

        $n=0;
        foreach($list as $l){

            $order = Order::with('customer','items','invoice')->where('code',$l)->first();
            foreach($order->items as $item){
                $produto[] = Products::with('dimensions')->where('sku', $item->sku)->first();
            }
            $dimensao[$n] = new \PhpSigep\Model\Dimensao();
            //$dimensao[$n]->setAltura($produto[0]->dimensions->height);
            //$dimensao[$n]->setLargura($produto[0]->dimensions->width);
            //$dimensao[$n]->setComprimento($produto[0]->dimensions->depth);
            $dimensao[$n]->setAltura(20);
            $dimensao[$n]->setLargura(20);
            $dimensao[$n]->setComprimento(20);
            $dimensao[$n]->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);

            $destinatario[$n] = new \PhpSigep\Model\Destinatario();
            $destinatario[$n]->setNome($this->removeAccents($order->customer->name));
            $destinatario[$n]->setLogradouro($this->removeAccents($order->customer->address));
            $destinatario[$n]->setNumero($this->removeAccents($order->customer->number));
            $destinatario[$n]->setComplemento($this->removeAccents($order->customer->complement));

            $destino[$n] = new \PhpSigep\Model\DestinoNacional();
            $destino[$n]->setBairro($this->removeAccents($order->customer->quarter));
            $destino[$n]->setCep($this->removeAccents($order->customer->zip_code));
            $destino[$n]->setCidade($this->removeAccents($order->customer->city));
            $destino[$n]->setUf($order->customer->state);
            $destino[$n]->setNumeroPedido($order->code);
            if(isset($order->invoice->number)){
                $destino[$n]->setNumeroNotaFiscal($order->invoice->number);
                $destino[$n]->setSerieNotaFiscal($order->invoice->serie);
            }


            $etiqueta[$n] = new \PhpSigep\Model\Etiqueta();
            $etiqueta[$n]->setEtiquetaSemDv($reserve[$n]->tag);


            $servicoAdicional[$n] = new \PhpSigep\Model\ServicoAdicional();
            $servicoAdicional[$n]->setCodigoServicoAdicional(\PhpSigep\Model\ServicoAdicional::SERVICE_REGISTRO);
            //$servicoAdicional[$n]->setValorDeclarado($order->total+$order->freight);

            $encomenda[$n] = new \PhpSigep\Model\ObjetoPostal();
            $encomenda[$n]->setServicosAdicionais(array($servicoAdicional[$n]));
            $encomenda[$n]->setDestinatario($destinatario[$n]);
            $encomenda[$n]->setDestino($destino[$n]);
            $encomenda[$n]->setDimensao($dimensao[$n]);
            $encomenda[$n]->setEtiqueta($etiqueta[$n]);
            //$encomenda[$n]->setPeso($produto[0]->dimensions->weight);// 500 gramas
            $encomenda[$n]->setPeso(2);// 500 gramas
            switch ($type) {
                case 'pac':
                    $encomenda[$n]->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_CONTRATO_AGENCIA));
                    break;
                case 'sedex':
                    $encomenda[$n]->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_E_SEDEX_STANDARD));

                    break;
                case 'sedexn':
                    $encomenda[$n]->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_40096));
                    break;
                case 'pacgf':
                    $encomenda[$n]->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_GRANDES_FORMATOS));
                    break;
                default:
                    $encomenda[$n]->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_CONTRATO_AGENCIA));
                    break;
            }

            $n++;
        }

        $remetente = new \PhpSigep\Model\Remetente();
        $remetente->setNumeroContrato('9912382275');
        $remetente->setCodigoAdministrativo('15278816');
        $remetente->setNome('FULLHUB');
        $remetente->setLogradouro('RUA ANTONIO DAS CHAGAS');
        $remetente->setNumero('358');
        $remetente->setComplemento('');
        $remetente->setBairro('CHACARA STO ANTONIO');
        $remetente->setCep('04714-000');
        $remetente->setUf('SP');
        $remetente->setCidade('São Paulo');
        $remetente->setDiretoria(new \PhpSigep\Model\Diretoria(\PhpSigep\Model\Diretoria::DIRETORIA_DR_SAO_PAULO));

        $plp = new \PhpSigep\Model\PreListaDePostagem();
        $plp->setAccessData($this->accessData);
        $plp->setEncomendas($encomenda);
        $plp->setRemetente($remetente);

        $phpSigep = new \PhpSigep\Services\SoapClient\Real();
        $result = $phpSigep->fechaPlpVariosServicos($plp);
        $res = $result->getResult();

        if($res != NULL && $idPlp = $result->getResult()->getIdPlp()){
            $newPlp = new Plp();
            $newPlp->number = $idPlp;
            $newPlp->type = $type;
            if($newPlp->save()){
                $n=0;
                foreach($list as $code){
                    unset($order);
                    $fullTag = $this->geraDv($reserve[$n]->tag);
                    $order = Order::where('code',$code)->first();
                    unset($plpItem);
                    $plpItem = new PlpItens();
                    $plpItem->plp_id = $newPlp->id;
                    $plpItem->order_id = $order->id;
                    $plpItem->tag = $reserve[$n]->tag;
                    $plpItem->fulltag = $fullTag;
                    $plpItem->save();

                    $this->oc->changeStatus($order->id, 9);

                    unset($shipping);
                    $shipping = new Shipping();
                    $shipping->orders_id = $order->id;
                    $shipping->shipping_companies_id = 1;
                    $shipping->weight = 0;
                    $shipping->shipping_code = $fullTag;
                    $shipping->tag = $reserve[$n]->tag;
                    $shipping->save();


                    unset($tag);
                    $tag = Etiquetas::where('tag', $reserve[$n]->tag)->first();
                    $tag->used = 1;
                    $tag->fullTag = $fullTag;
                    $tag->save();
                    $log[] = 'Pedido '.$order->id.' Inserido na PLP '.$newPlp->number.' Com o rastreio ';

                    $n++;
                }

                $return  = 'PLP '.$newPlp->number.' Gerada!<br /><a href="/sigep/print/'.$newPlp->number.'/plp" class="btn btn-block btn-info" target="_blank">PLP</a>
                            <a href="/sigep/print/'.$newPlp->number.'/etiquetas" class="btn btn-block btn-info" target="_blank">Etiquetas</a>';
                return \Redirect::back()->with('msg', $return);

            }
        }else{
            $log[] = $result->getErrorMsg();
            dd($log);
        }


    }



    public function range($tipo = 'pac', $quantity = 1){
        $params = new \PhpSigep\Model\SolicitaEtiquetas();

        $params->setQtdEtiquetas($quantity);

        switch ($tipo) {
            case 'pac':

                $params->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_CONTRATO_AGENCIA));
                break;
            case 'sedex':
                $params->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_E_SEDEX_STANDARD));
                break;
            case 'sedexn':
                $params->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_40096));
                break;
            case 'pacgf':
                $params->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_GRANDES_FORMATOS));
                break;
            default:
                $params->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_CONTRATO_AGENCIA));
                break;
        }
        
        $params->setAccessData($this->accessData);

        
        $phpSigep = new \PhpSigep\Services\SoapClient\Real();
        $range = $phpSigep->solicitaEtiquetas($params);
        if($list = $range->getResult()){
            foreach($list as $l){
                unset($etiqueta);
                $etiqueta = new Etiquetas;
                $etiqueta->tag = $l->getEtiquetaSemDv();
                $etiqueta->format = $tipo;
                $etiqueta->used = 0;
                $etiqueta->save();
            }
        }
    }

    public function getNf($code){
        $order= Order::where('code',$code)->first();
        $invoice = invoice::where('orders_id',$order->id)->first();
        if(count($invoice) > 0){
            $nf['number'] = $invoice->number;
            $nf['serie'] = $invoice->number;

            return $nf;
        }else{
            return false;
        }
    }
    public function geraDv($tag){
        $etiqueta[0] = new \PhpSigep\Model\Etiqueta();
        $etiqueta[0]->setEtiquetaSemDv($tag);

        $params = new \PhpSigep\Model\GeraDigitoVerificadorEtiquetas();
        $params->setAccessData($this->accessData);
        $params->setEtiquetas($etiqueta);

        $phpSigep = new \PhpSigep\Services\SoapClient\Real();
        $result = $phpSigep->geraDigitoVerificadorEtiquetas($params);

        if($result = $result->getResult()){

            $semDv = $result[0]->getEtiquetaSemDv();
            $dv = $result[0]->getDv();
            $comDv = substr($semDv,0,-2).$dv.substr($semDv,-2);
        }else{
            $comDv = substr($tag,0,-2).'0'.substr($tag,-2);
        }


        return $comDv;

    }


    public function removeAccents($str){
        $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
        return strtr( $str, $unwanted_array );
    }

   public function plpPrinter($id, $type, $format = false){
        $data = Plp::with('itens')->where('number',$id)->first();
        $plp = $this->mountPreOrder($data);
        if($type == 'plp'){
            $pdf  = new \PhpSigep\Pdf\ListaDePostagem($plp, $data->number);

            $pdf->render();
        }else if($type == 'etiquetas'){
            $logo = '../resources/images/logo-etiqueta.png';
            $layoutChancela = array(\PhpSigep\Pdf\CartaoDePostagem::TYPE_CHANCELA_SEDEX_2016);
            $pdf = new \PhpSigep\Pdf\CartaoDePostagem2016($plp, $data->number, $logo, $layoutChancela);

            $fileName = '../storage/tags/'.$data->number.'.pdf';
            if(!$format){
                $pdf->render('I', $fileName);    
            }else{
                $pdf->render('F', $fileName);
                unset($pdf);
                $pdf = new \PhpSigep\Pdf\ImprovedFPDF('P', 'mm', 'Letter' );

                $pageCount = $pdf->setSourceFile($fileName);

                $pdf->AddPage();
                $pdf->SetFillColor(0,0,0);
                $pdf->SetFont('Arial','B',16);


                for($i=1;$i<=$pageCount;$i++) {

                    $tplIdx = $pdf->importPage($i, 'MediaBox');
                    $mod = $i % 4;

                    switch ($mod) {
                        case 0:
                            //A4: 210(x) × 297(y)
                            //Letter: 216 (x) × 279 (y)
                            $pdf->useTemplate($tplIdx, 114, 155, 105, 138, true);

                            if ($i !== $pageCount) {
                                $pdf->AddPage();
                                $pdf->SetFillColor(0,0,0);
                                $pdf->SetFont('Arial','B',16);
                            }
                            break;
                        case 1:
                            $pdf->useTemplate($tplIdx, 0, 0, 105, 138, true);
                            break;
                        case 2:
                            $pdf->useTemplate($tplIdx, 114, 0, 105, 138, true);
                            break;
                        case 3:
                            $pdf->useTemplate($tplIdx, 0, 155, 105, 138, true);
                            break;
                    }


                }
                $pdf->Output();
            }
            

            
        }

   }

    public function mountPreOrder(Plp $data){

        $n=0;
        foreach($data->itens as $etiquetas){
            $order = Order::with('customer','items','invoice')->where('id',$etiquetas->order_id)->first();
            foreach($order->items as $item){
                $produto[] = Products::with('dimensions')->where('sku', $item->sku)->first();
            }

            $dimensao[$n] = new \PhpSigep\Model\Dimensao();
            //$dimensao[$n]->setAltura($produto[0]->dimensions->height);
            //$dimensao[$n]->setLargura($produto[0]->dimensions->width);
            //$dimensao[$n]->setComprimento($produto[0]->dimensions->depth);

            $dimensao[$n]->setAltura(20);
            $dimensao[$n]->setLargura(20);
            $dimensao[$n]->setComprimento(20);
            $dimensao[$n]->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);

            $destinatario[$n] = new \PhpSigep\Model\Destinatario();
            $destinatario[$n]->setNome($this->removeAccents($order->customer->name));
            $destinatario[$n]->setLogradouro($this->removeAccents($order->customer->address));
            $destinatario[$n]->setNumero($this->removeAccents($order->customer->number));
            $destinatario[$n]->setComplemento($this->removeAccents($order->customer->complement));

            $destino[$n] = new \PhpSigep\Model\DestinoNacional();
            $destino[$n]->setBairro($this->removeAccents($order->customer->quarter));
            $destino[$n]->setCep($this->removeAccents($order->customer->zip_code));
            $destino[$n]->setCidade($this->removeAccents($order->customer->city));
            $destino[$n]->setUf($order->customer->state);
            $destino[$n]->setNumeroPedido($order->code);
            if(isset($order->invoice->number)){
                $destino[$n]->setNumeroNotaFiscal($order->invoice->number);
                $destino[$n]->setSerieNotaFiscal($order->invoice->serie);
            }


            $etiqueta[$n] = new \PhpSigep\Model\Etiqueta();
            $etiqueta[$n]->setEtiquetaSemDv($etiquetas->tag);

            $servicoAdicional[$n] = new \PhpSigep\Model\ServicoAdicional();
            $servicoAdicional[$n]->setCodigoServicoAdicional(\PhpSigep\Model\ServicoAdicional::SERVICE_REGISTRO);
            //$servicoAdicional[$n]->setValorDeclarado($order->total+$order->freight);

            $encomenda[$n] = new \PhpSigep\Model\ObjetoPostal();
            $encomenda[$n]->setServicosAdicionais(array($servicoAdicional[$n]));
            $encomenda[$n]->setDestinatario($destinatario[$n]);
            $encomenda[$n]->setDestino($destino[$n]);
            $encomenda[$n]->setDimensao($dimensao[$n]);
            $encomenda[$n]->setEtiqueta($etiqueta[$n]);
            //$encomenda[$n]->setPeso($produto[0]->dimensions->weight);
            $encomenda[$n]->setPeso(2);

            switch ($data->type) {
                case 'pac':
                    $encomenda[$n]->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_CONTRATO_AGENCIA));
                    break;
                case 'sedex':
                    $encomenda[$n]->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_E_SEDEX_STANDARD));

                    break;
                case 'sedexn':
                    $encomenda[$n]->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_40096));
                    break;
                case 'pacgf':
                    $encomenda[$n]->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_GRANDES_FORMATOS));
                    break;
                default:
                    $encomenda[$n]->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_CONTRATO_AGENCIA));
                    break;
            }

            $n++;
        }
        $remetente = new \PhpSigep\Model\Remetente();
        $remetente->setNumeroContrato('9912382275');
        $remetente->setCodigoAdministrativo('15278816');
        $remetente->setNome('FULLHUB');
        $remetente->setLogradouro('RUA ANTONIO DAS CHAGAS');
        $remetente->setNumero('358');
        $remetente->setComplemento('');
        $remetente->setBairro('CHACARA STO ANTONIO');
        $remetente->setCep('04714-000');
        $remetente->setUf('SP');
        $remetente->setCidade('São Paulo');
        $remetente->setDiretoria(new \PhpSigep\Model\Diretoria(\PhpSigep\Model\Diretoria::DIRETORIA_DR_SAO_PAULO));

        $plp = new \PhpSigep\Model\PreListaDePostagem();
        $plp->setAccessData($this->accessData);
        $plp->setEncomendas($encomenda);
        $plp->setRemetente($remetente);

        return $plp;
    }

    public function fixTag(){
        $tags = shipping::where('shipping_code','')
                          ->where('shipping_companies_id',1)
                          ->where('tag','<>','')
                          ->get();
        foreach($tags as $tag){
            $comDv = $this->geraDv($tag->tag);
            $tag->shipping_code = $comDv;
            $tag->save();
        }
    }


    public function vipp(){
        $file = file_get_contents('/home/fullhubcom/public_html/fornecedores/sweet/storage/vipp');
        $line = explode(PHP_EOL, $file);
        foreach($line as $l){
            unset($shipping);
            $c = explode(';', $l);

            $shipping = shipping::where("shipping_code",$c[1])->first();
            if(count($shipping) == 0){
                $invoice = invoice::where('number','like',"%$c[0]")->first();
                if(count($invoice) > 0){
                    $shipping = new shipping;
                    $shipping->orders_id = $invoice->orders_id;
                    $shipping->shipping_companies_id = 1;
                    $shipping->weight = 0;
                    $shipping->shipping_code = $c[1];
                    if($shipping->save()){
                        $order = Order::find($shipping->orders_id);
                        $this->oc->changeStatus($order->id, 3);



                        $log[] = $c[1].' '.$c[0].' Incluido';
                    }
                }else{
                    $log[] = $c[1].' '.$c[0].' nf Não localizada';
                }


            }else{
                $log[] = $c[1].' '.$c[0].' Já Localizado';
            }




        }
        dd($log);
    }


    public function calc($cep, $sku,$qty){
        $product = Products::with('dimensions','prices')->where('sku',$sku)->first();
        if($product){
            $dimensao = new \PhpSigep\Model\Dimensao();
            $dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);
            $dimensao->setAltura($product->dimensions->height); // em centímetros
            $dimensao->setComprimento($product->dimensions->width); // em centímetros
            $dimensao->setLargura($product->dimensions->depth); // em centímetros

            $params = new \PhpSigep\Model\CalcPrecoPrazo();
            $params->setAccessData(new \PhpSigep\Model\AccessDataHomologacao());
            $params->setCepOrigem('04714000');
            $params->setCepDestino($cep);
            $params->setServicosPostagem([new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_40096)]);
            $params->setAjustarDimensaoMinima(true);
            $params->setDimensao($dimensao);
            $params->setPeso(($product->dimensions->weight/1000)*$qty);// 150 gramas

            $phpSigep = new \PhpSigep\Services\SoapClient\Real();
            $result = $phpSigep->calcPrecoPrazo($params);
            if(is_null($result->getErrorCode())){
                $n=0;
                foreach($result->getResult() as $calc){
                    $data['sku'] = $sku;
                    $data['Serviço'] = 'Entrega Padrão';
                    $data['valor'] = ($calc->getValor()+$this->valorExtra);
                    $data['prazoEntrega'] = ($calc->getPrazoEntrega()+$product->lead_time+$this->prazoExtra);
                    $data['price'] = $product->prices->price2;
                    $n++;
                }
                return $data;
            }else{
                return false;
            }
        }
    }

    public function setShippingCode(Request $request){

        $shipping = Shipping::find($request->input('ship_id'));

        $shipping->shipping_code = $request->input('ship_code');
        $shipping->save();
    }

    public function tracking(){
        $orders = Order::with('shipping')
                       ->where('order_statuses_id',3)
                       ->orWhere('order_statuses_id', 4)
                       ->orWhere('order_statuses_id', 5)
                       ->orWhere('order_statuses_id', 9)
                       ->orWhere('order_statuses_id', 16)
                       ->orWhere('order_statuses_id', 18)
                       ->get();
        $etiquetas = array();
        foreach($orders as $order){


            if(isset($order->shipping) && substr($order->shipping->shipping_code, -2) == 'BR'){
                unset($etiqueta);

                $etiqueta[0] = new \PhpSigep\Model\Etiqueta();
                $etiqueta[0]->setEtiquetaComDv(trim($order->shipping->shipping_code));

                $params = new \PhpSigep\Model\RastrearObjeto();
                $params->setAccessData($this->accessData);
                $params->setEtiquetas($etiqueta);

                $phpSigep = new \PhpSigep\Services\SoapClient\Real();
                $result = $phpSigep->rastrearObjeto($params);


                if(count($result) > 0){
                    $res = $result->getResult();

                    $eventos = (isset($res[0]))?$res[0]->getEventos():false;

                    if($eventos){
                        $data['status'] = $eventos[0]->getStatus();
                        $data['tipo'] = $eventos[0]->getTipo();
                        $data['message'] = $eventos[0]->getDescricao();
                    }else{
                        $data['status'] = '00';
                        $data['tipo'] = 'NE';
                        $data['message'] = 'Objeto não encontrado na base dos correios';
                    }

                    switch($data['tipo']){
                        case 'BDE';
                             $this->oc->changeStatus($order->id, 6);
                            break;
                        case 'CUN';
                            $this->oc->changeStatus($order->id, 3);
                            break;
                        case 'OEC';
                            $this->oc->changeStatus($order->id, 3);
                            break;
                        case 'DO';
                             $this->oc->changeStatus($order->id, 3);
                            break;
                        case 'BDI';
                             $this->oc->changeStatus($order->id, 3);
                            break;
                        case 'PO';
                             $this->oc->changeStatus($order->id, 3);
                            break;
                        case 'RO';
                             $this->oc->changeStatus($order->id, 3);
                            break;
                        case 'LDI';
                             $this->oc->changeStatus($order->id, 16);
                            break;
                        case 'BDR';
                             $this->oc->changeStatus($order->id, 18);
                            break;
                        case 'FC';
                             $this->oc->changeStatus($order->id, 3);
                            break;
                        case 'BLQ';
                             $this->oc->changeStatus($order->id, 3);
                            break;
                        case 'NE';
                             $this->oc->changeStatus($order->id, 5);
                            break;
                        default:
                            dd($data);
                            break;
                    }
                    $order->shipping->status = $data['message'];
                    $order->shipping->save();
                }else{
                    $this->oc->changeStatus($order->id, 5);

                }
            }else{
                if(isset($order->shipping->shipping_code)){
                    echo $order->id.' | '.$order->code.' | '.$order->shipping->shipping_code.'<br />';
                }else{
                    if($order->envio != 'me2'){
                        $this->oc->changeStatus($order->id, 5);

                    }
                }
            }

        }

    }
}
