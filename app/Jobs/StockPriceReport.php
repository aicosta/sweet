<?php

namespace Sweet\Jobs;

use Sweet\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Sweet\Product\Products;
use Sweet\Http\Controllers\StockController;
use Mail;

class StockPriceReport extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $user;
    protected $stocks;

    public function __construct($user)
    {
        $this->user = $user;
        $this->stocks = new StockController;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
           Products::chunk(1000,function($products){
            
            foreach($products as $product){
                
                $stock = $this->stocks->getStock($product->sku);
                $this->report['precos'][] = [
                            'sku' => $product->sku,
                            'nome' => $product->name,
                            'Fornecedor' => $product->providers->name,
                            'custo' => $product->cost,
                            'Preço de B2W/Cnova/Tamarindos' => number_format($product->prices->price,2,'.',''),
                            'Preço de Walmart/Mobly' => number_format($product->prices->price2,2,'.',''),
                            'Preço de ML' => number_format($product->prices->price3,2,'.','')
                            ];
                $this->report['estoques'][] = [
                                        'sku' => $product->sku,
                                        'Fornecedor' => $product->providers->name,
                                        'Estoque Interno' => (int)$stock['internal'],
                                        'Estoque Externo' => (int)$stock['external'],
                                        'Total' => $stock['total']
                                      ];

              $peso = (isset($product->dimensions->weight))?$product->dimensions->weight:0;
              $altura = (isset($product->dimensions->height))?$product->dimensions->height:0;
              $largura = (isset($product->dimensions->width))?$product->dimensions->width:0;
              $comprimento = (isset($product->dimensions->depth))?$product->dimensions->depth:0;
                $this->report['dimensions'][] = [
                                          'sku' => $product->sku,
                                          'Fornecedor' => $product->providers->name,
                                          'peso' => $peso,
                                          'altura' => $altura,
                                          'largura' => $largura,
                                          'comprimento' => $comprimento
                                        ];

            }
          });

        $data= $this->report;
        $fileName = 'Relatório Geral de Preços e Estoques '.date('d-m-Y H-i-s').'-'.rand(11111,99999);
        $ex = \Excel::create($fileName, function($excel) use ($data) {



            // Build the spreadsheet, passing in the payments array
            $excel->sheet('Preços', function($sheet) use ($data) {
                $sheet->fromArray($data['precos']);
            });
            $excel->sheet('Estoques', function($sheet) use ($data) {
                $sheet->fromArray($data['estoques']);
            });
            $excel->sheet('Dimensoes', function($sheet) use ($data) {
                $sheet->fromArray($data['dimensions']);
            });
        })->store('xlsx', storage_path('excel/price-stock'));

        $path = $ex->storagePath.'/'.$fileName.'.xlsx';
        $this->sendMail($path, $this->user, $fileName);

            return true;
        }catch(Exception $e){
            dd($e);
        }
    }

     public function sendMail($file, $user ,$fileName){

         Mail::send('emails.report', ['user' => $user], function ($m) use ($user, $file, $fileName) {
            $m->from('noreply@fullhub.com.br', 'Fullhub Sweet');
            $options = [];
            $m->attach($file, $options);
            $m->to($user->email, $user->name)->subject('[SWEET] '.$fileName);
        });
    }
}
