<?php

namespace Sweet\Http\Controllers;

use Sweet\Http\Requests;
use Illuminate\Http\Request;

use Sweet\Orders\Order;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $pending = Order::where('order_statuses_id',1)->count();
        $invoiced = Order::where('order_statuses_id',2)->count();
        $shipped = Order::where('order_statuses_id',3)
                          ->orWhere('order_statuses_id',9)
                          ->count();
        $problems = Order::where('order_statuses_id',4)
                          ->orWhere('order_statuses_id',5)
                          ->count();
        $sac = Order::where('order_statuses_id',7)->count();
        $unaproved = Order::where('order_statuses_id',7)->count();
        $shop = Order::where('order_statuses_id',11)->count();
        $ml = Order::where('order_statuses_id',13)->count();
        $canceled = Order::where('order_statuses_id',8)->count();
        $finished = Order::where('order_statuses_id',6)->count();
        $waiting = Order::where('order_statuses_id',14)->count();
        $cuts = Order::where('order_statuses_id',15)->count();
        $thief = Order::where('order_statuses_id',18)->count();
        $waiting2 = Order::where('order_statuses_id',16)->count();
        $alteracao = Order::where('order_statuses_id',20)->count();
        $ouvidoria = Order::where('order_statuses_id',21)->count();


        $salesGraph = $this->salesGraph();

        return view('home')->with(compact('pending','invoiced', 'shipped',
                                          'problems','sac','unaproved','shop',
                                          'ml','canceled','finished','salesGraph',
                                          'waiting','cuts','thief','waiting2',
                                          'alteracao','ouvidoria'));
    }

    public function salesGraph(){
        $origins = Order::distinct()
                        ->select('origin')
                        ->get();
        $n=0;
        $days = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        for($d =0; $d < $days; $d++){
            $data['labels'][] = $d;
        }
        foreach($origins as $o){
            if($o->orign == 'PU' ||
                $o->orign == 'GROUPON' ||
                $o->orign == 'PECADILLE' ||
                $o->orign == 'TROCA'){
                continue;
            }
            $data['datasets'][$n]['label'] = strtoupper($o->origin);
            switch($o->origin){
                case 'CNOVA':
                    $data['datasets'][$n]['fillColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['strokeColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['pointColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['pointStrokeColor'] = "#c1c7d1";
                    $data['datasets'][$n]['pointHighlightFill'] = "#fff";
                    $data['datasets'][$n]['pointHighlightStroke'] = "rgb(220,220,220)";
                    break;
                case 'MERCADO LIVRE':
                    $data['datasets'][$n]['fillColor'] = "rgb(247, 242, 170)";
                    $data['datasets'][$n]['strokeColor'] = "rgb(247, 242, 170)";
                    $data['datasets'][$n]['pointColor'] = "rgb(247, 242, 170)";
                    $data['datasets'][$n]['pointStrokeColor'] = "#FAF496";
                    $data['datasets'][$n]['pointHighlightFill'] = "#fff";
                    $data['datasets'][$n]['pointHighlightStroke'] = "rgb(220,220,220)";
                    break;
                case 'GROUPON':
                    $data['datasets'][$n]['fillColor'] = "rgb(150, 250, 170)";
                    $data['datasets'][$n]['strokeColor'] = "rgb(150, 250, 170)";
                    $data['datasets'][$n]['pointColor'] = "rgb(150, 250, 170)";
                    $data['datasets'][$n]['pointStrokeColor'] = "#8BFDA2";
                    $data['datasets'][$n]['pointHighlightFill'] = "#fff";
                    $data['datasets'][$n]['pointHighlightStroke'] = "rgb(220,220,220)";
                    break;
                case 'B2W':
                    $data['datasets'][$n]['fillColor'] = "rgb(186, 239, 223)";
                    $data['datasets'][$n]['strokeColor'] = "rgb(186, 239, 223)";
                    $data['datasets'][$n]['pointColor'] = "rgb(186, 239, 223)";
                    $data['datasets'][$n]['pointStrokeColor'] = "#A7E5D2";
                    $data['datasets'][$n]['pointHighlightFill'] = "#fff";
                    $data['datasets'][$n]['pointHighlightStroke'] = "rgb(220,220,220)";
                    break;
                case 'TROCA':
                    $data['datasets'][$n]['fillColor'] = "rgb(238, 153, 153)";
                    $data['datasets'][$n]['strokeColor'] = "rgb(238, 153, 153)";
                    $data['datasets'][$n]['pointColor'] = "rgb(238, 153, 153)";
                    $data['datasets'][$n]['pointStrokeColor'] = "#E97777";
                    $data['datasets'][$n]['pointHighlightFill'] = "#fff";
                    $data['datasets'][$n]['pointHighlightStroke'] = "rgb(220,220,220)";
                    break;
                case 'TAMARINDOS':
                    $data['datasets'][$n]['fillColor'] = "rgb(250, 215, 170)";
                    $data['datasets'][$n]['strokeColor'] = "rgb(250, 215, 170)";
                    $data['datasets'][$n]['pointColor'] = "rgb(250, 215, 170)";
                    $data['datasets'][$n]['pointStrokeColor'] = "#F6C17C";
                    $data['datasets'][$n]['pointHighlightFill'] = "#fff";
                    $data['datasets'][$n]['pointHighlightStroke'] = "rgb(220,220,220)";
                    break;
                case 'PU':
                    $data['datasets'][$n]['fillColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['strokeColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['pointColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['pointStrokeColor'] = "#c1c7d1";
                    $data['datasets'][$n]['pointHighlightFill'] = "#fff";
                    $data['datasets'][$n]['pointHighlightStroke'] = "rgb(220,220,220)";
                    break;
                case 'PECADILLE':
                    $data['datasets'][$n]['fillColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['strokeColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['pointColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['pointStrokeColor'] = "#c1c7d1";
                    $data['datasets'][$n]['pointHighlightFill'] = "#fff";
                    $data['datasets'][$n]['pointHighlightStroke'] = "rgb(220,220,220)";
                    break;
                case 'MOBLY':
                    $data['datasets'][$n]['fillColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['strokeColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['pointColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['pointStrokeColor'] = "#c1c7d1";
                    $data['datasets'][$n]['pointHighlightFill'] = "#fff";
                    $data['datasets'][$n]['pointHighlightStroke'] = "rgb(220,220,220)";
                    break;
                case 'ESTANTE VIRTUAL':
                    $data['datasets'][$n]['fillColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['strokeColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['pointColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['pointStrokeColor'] = "#c1c7d1";
                    $data['datasets'][$n]['pointHighlightFill'] = "#fff";
                    $data['datasets'][$n]['pointHighlightStroke'] = "rgb(220,220,220)";
                    break;
                case 'WALMART':
                    $data['datasets'][$n]['fillColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['strokeColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['pointColor'] = "rgb(210, 214, 222)";
                    $data['datasets'][$n]['pointStrokeColor'] = "#c1c7d1";
                    $data['datasets'][$n]['pointHighlightFill'] = "#fff";
                    $data['datasets'][$n]['pointHighlightStroke'] = "rgb(220,220,220)";
                    break;
            }

            foreach($data['labels'] as $day){
                $month = date('m');
                $year = date('Y');

                $ord = Order::groupBy(\DB::raw("DAY(created_at)"))
                        ->where(\DB::raw("MONTH(created_at)"), $month)
                        ->where(\DB::raw("YEAR(created_at)"), $year)
                        ->where('origin',$o->origin)
                        ->select(
                                \DB::raw("DAY(created_at) as day"),
                                \DB::raw("SUM(total) as totals")
                            )
                        ->get();

                for($d =0; $d< $days; $d++){
                   $data['datasets'][$n]['data'][$d] =0;
                }

                foreach($ord as $or){
                    $data['datasets'][$n]['data'][($or->day-1)] = number_format($or->totals,2,'.','');
                }
            }

            $n++;
        }
        return(json_encode($data));
    }
}
