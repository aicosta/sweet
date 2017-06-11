<?php

namespace Sweet\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
        Commands\CallRoute::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    	ob_start();
        //B2W
        $schedule->call(function()
        {
            $b2w = new \Sweet\Http\Controllers\marketplaces\B2wController;
            $cnova = new \Sweet\Http\Controllers\marketplaces\CnovaController;
            $ml= new \Sweet\Http\Controllers\marketplaces\MercadoLivreController;
            $order = new \Sweet\Http\Controllers\OrdersController;
            $sigep = new \Sweet\Http\Controllers\SigepController;

            $order->syncKeys();
            //Pedidos
            $b2w->orders();
            $cnova->orders();
            $ml->orders();

            //Faturamento
            $b2w->invoice();
            $b2w->shipping();
            $cnova->tracking();

            //Finaliza
            $b2w->finish();
            $cnova->finish();
            $ml->reverseME2();

            $order->itemsFix();
            $sigep->tracking();
        })->hourly();

        //Atualizações de Estoque
        $schedule->call(function()
        {
            $b2w = new \Sweet\Http\Controllers\marketplaces\B2wController;
            $ml = new \Sweet\Http\Controllers\marketplaces\MercadoLivreController;
            $cnova = new \Sweet\Http\Controllers\marketplaces\CnovaController;
            $walmart = new \Sweet\Http\Controllers\marketplaces\WalmartController;
            $sweet = new \Sweet\Http\Controllers\marketplaces\SweetController;

            $sweet->dev();
            
            $b2w->stock();
            flush(); ob_flush();
            $cnova->stock();
            flush(); ob_flush();
            $ml->stock();
            flush(); ob_flush();
            $walmart->stock();
            flush(); ob_flush();


            $b2w->price();
            flush(); ob_flush();
            $cnova->price();
            flush(); ob_flush();
            $ml->price();
            flush(); ob_flush();
            $walmart->price();
            flush(); ob_flush();
        })->everyMinute();


        /*$schedule->call(function()
        {
            $b2w = new \Sweet\Http\Controllers\marketplaces\B2wController;
            $ml= new \Sweet\Http\Controllers\marketplaces\MercadoLivreController;
            $cnova = new \Sweet\Http\Controllers\marketplaces\CnovaController;
            $walmart = new \Sweet\Http\Controllers\marketplaces\WalmartController;

            $b2w->stockAll();
            $cnova->stockAll();
            ;$ml->stockAll();
            $walmart->stockAll();
        })->dailyAt('19:00');*/
        //$schedule->command('inspire')->hourly();
    }
}
