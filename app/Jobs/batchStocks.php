<?php

namespace Sweet\Jobs;

use Sweet\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Sweet\Product\Products;
use Sweet\Product\ProductExternalStock;
use Sweet\BatchLog;
use Mail;

class batchStocks extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $file;
    protected $user;

    public function __construct($file, $user)
    {
        $this->file = $file;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            return true;
            $file = \Excel::load('uploads/stocks/'.$this->file)->get();
            $n=0;
            foreach($file->toArray() as $row){
                unset($product);
                if(isset($row['sku']) && isset($row['estoque']))        {
                    $product = Products::where('sku',$row['sku'])->first();
                    if($product){
                        $stock = ProductExternalStock::where('products_id', $product->id)->first();

                        $log[$n] = ['sku' => $row['sku'], 'estoqueAntigo' => $stock->quantity];
                        $stock->quantity = $row['estoque'];

                        if($stock->save()){
                            $log[$n]['estoqueAtual'] = $row['estoque'];
                            $log[$n]['success'] = 'Estoque Atualizado';
                        }else{
                            $log[$n]['error'] = 'Falha na atualização do produto';
                        }

                    }else{
                         $log[$n] = ['sku' => $row['sku'], 'error' => 'Produto não localizado'];
                    }
                    $n++;
                }else{
                    $log[0] = 'ERROS ENCONTRADOS NA TABELA';
                
                }
            }
            $data = new BatchLog();
            $data->type = 'estoque';
            $log = (isset($log))?'':$log;
            $data->payload = json_encode($log);
            $data->user_id = $this->user->id;
            $data->save();

            $this->sendMail($log, $this->user);

        }catch(Exception $e){
            dd($e);
        }
    }



    public function sendMail($payload, $user){

         Mail::send('emails.teste', ['user' => $user, 'log' => $payload], function ($m) use ($user) {
            $m->from('noreply@fullhub.com.br', 'Fullhub Sweet');

            $m->to($user->email, $user->name)->subject('[SWEET] ATUALIZAÇÃO DE ESTOQUE');
        });
    }
}
