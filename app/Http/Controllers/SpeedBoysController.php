<?php

namespace Sweet\Http\Controllers;

use Illuminate\Http\Request;

use Sweet\Http\Requests;
use Sweet\Order;

class SpeedBoysController extends Controller
{
   	
   	public $url = 'http://www.speedlogexpress.com.br/Log/servico';
   	public $email = 'marcelo@fullhub.com.br';
   	public $senha = 'HEUTABW';
   	public $token;
   	public $curl;

   	public function __construct(){
   		$this->curl = new \anlutro\cURL\cURL;

   		$this->token = $this->generateToken();
   	}

   	public function generateToken(){
   		$url = $this->url.'/login/criar/';

   		$data = ['email' => $this->email, 'senha' => $this->senha];
   		$response = $this->curl->jsonPost($url, $data);
   		$response = json_decode($response);
   		return ($response->tokenServico);

   	}
   	public function run()
    {
    	$orders = [''];
		$url = $this->url.'/ordemServico/criar/';
		$data['tokenServico'] = $this->token;

		$n=0;
		foreach($orders as $o){
			$order = Order::with('customer','invoice')->where('code',$o)->first();
			
			if(count($order) == 1){
				$plp[$n]['rastreio'] = 'FUH'.rand(1111111111111,9999999999999);
				$plp[$n]['orders_id'] = $order->id;
				
				$data['ordens'][$n]['numeroOrdemDeServico'] = $plp[$n]['rastreio'];
				$data['ordens'][$n]['endereco'] = $order->customer->address;
				$data['ordens'][$n]['descricao'] = '';
				$data['ordens'][$n]['destinatarioOrdemDeServico'] = $order->customer->name;
				$data['ordens'][$n]['numeroNotaFiscal'] = $order->invoice->number;
				$data['ordens'][$n]['cep'] = $order->customer->zip_code;
				$data['ordens'][$n]['referencia'] = $order->customer->complement;
				$data['ordens'][$n]['enderecoBairro'] = $order->customer->quarter;
				$data['ordens'][$n]['enderecoCidade'] = $order->customer->city;
				$data['ordens'][$n]['enderecoEstado'] = $order->customer->state;
				$data['ordens'][$n]['enderecoNumero'] = $order->customer->number;
				$data['ordens'][$n]['dddTelefone'] = $order->customer->phone;
				$data['ordens'][$n]['email'] = 'hello@fullhub.com.br';
				//$data['ordens'][$n]['dataAgendamentoEntrega'] = '';
				//$data['ordens'][$n]['dataInicioAgendamentoEntrega'] = '';
				//$data['ordens'][$n]['dataFimAgendamentoEntrega'] = '';
				$data['ordens'][$n]['acao'] = '1';
				$data['ordens'][$n]['chaveNotaFiscal'] = $order->invoice->key;
				$data['ordens'][$n]['valorNotaFiscal'] = $order->customer;
				$data['ordens'][$n]['numeroOSExterno'] = $plp[$n]['rastreio'];
				$data['ordens'][$n]['gramas'] = '1000';
				$data['ordens'][$n]['destinatarioOrdemDeServicoContrato'] = $order->code;

			}

			dd($data);
			
		}
    }
}
