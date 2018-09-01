<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\Obat;
use App\Models\Penjualan;

class ForcastingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'obat' => Obat::all()
        ];
        return view('forcasting',$data);
    }

    public function hitung(Request $request){
        $search = Penjualan::where('id_obat',$request->input('obat'))->get();
        $total = Penjualan::where('id_obat',$request->input('obat'))->count(); 
        $periode = $request->input('periode');      
        $training = (int)($total * 0.9);
        $testing = $total - $training;
        $data_asli = array();
        $data_training = array();
        $data_testing = array();
        $min_holt = array();
        $min_winter = array();
        $min_metode = array();
        $hasil_forecast = array();

        for($i=0; $i<=$total-1; $i++){
            $data_asli[$i] = $search[$i]->qty;
        }
        for($i=0; $i<=$training-1; $i++){
            $data_training[$i] = $search[$i]->qty;
        }        
        for($i=$training; $i<=$total-1; $i++){
            $data_testing[$i] = $search[$i]->qty;
        }
    //Metode Single
        if($periode==1){
            $hasil = $this->stationer($data_asli);
            $single = [
                "1.",
                "Single",
                $hasil[0],
                "Alpha = ".$hasil[1],
                $hasil[2]
            ];
            $min_metode[0] = $hasil[2];
        }
        else{
            $single = [
                "-",
                "-",
                "-"
            ];
            $min_metode[0] = NULL;
        }
    //End Metode Single
    //Metode Double
        $training_double = $this->doubleTraining($data_training, $periode);
        $testing_double = $this->doubleTesting($data_testing, $training, $training_double[0], $training_double[1], $training_double[2], $training_double[3]);
        $double = [
            "2.",
            "Double",
            "Nilai 1 = ".$testing_double[0]."<br>Nilai 2 = ".$testing_double[1],
            "Alpha = ".$testing_double[3],
            $testing_double[4]
        ];
        $min_metode[1] = $testing_double[4];
    //End Metode Double
    //Metode Holt
        $training_holt = $this->holtTraining($data_training, $periode);        
        $testing_holt = $this->holtTesting($data_testing, $training, $training_holt[0], $training_holt[1], $training_holt[2], $training_holt[3], $training_holt[4]);
        $holt = [
            "3.",
            "Holt",
            "Nilai 1 = $testing_holt[0]<br>Nilai 2 = $testing_holt[1]",
            "Alpha = $testing_holt[3]<br>Beta = $testing_holt[4]",
            $testing_holt[5]
        ];
        $min_metode[2] = $testing_holt[5];
    //End Metode Holt
    //Metode Winter
        $training_winter = $this->winterTraining($data_training, $periode);
        $testing_winter = $this->winterTesting($data_testing, $training, $training_winter[0], $training_winter[1], $training_winter[2], $training_winter[3], $training_winter[4], $training_winter[5], $training_winter[6]);        
        $winter = [
            "4.",
            "Winter",
            "Nilai 1 = ".$testing_winter[0]."<br>Nilai 2 = ".$testing_winter[1]."<br>Nilai 3 = ".$testing_winter[2],
            "Alpha = ".$testing_winter[4]."<br>Beta = ".$testing_winter[5]."<br>Miu = ".$testing_winter[6],
            $testing_winter[7]
        ];        
        $min_metode[3] = $testing_winter[7];
    //End Metode Winter
        $pilih_metode = min($min_metode);
        if($min_metode[0]==$pilih_metode){
            $single_forecast = $this->singleForecast($data_asli, $hasil[0], $hasil[1]);
            $hasil_forecast = $single_forecast[3];
        }
        elseif ($min_metode[1]==$pilih_metode) {
            $double_forecast = $this->doubleForecast($data_asli, $testing_double[0], $testing_double[1], $testing_double[2], $testing_double[3]);
            $hasil_forecast = $double_forecast[5];
        }
        elseif ($min_metode[2]==$pilih_metode) {
            $holt_forecast = $this->holtForecast($data_asli, $testing_holt[0], $testing_holt[1], $testing_holt[2], $testing_holt[3], $testing_holt[4]);
            $hasil_forecast = $holt_forecast[6];
        }
        elseif ($min_metode[3]==$pilih_metode) {
            $winter_forecast = $this->winterForecast($data_asli, $testing_winter[0], $testing_winter[1], $testing_winter[2], $testing_winter[3], $testing_winter[4], $testing_winter[5], $testing_winter[6]);
            $hasil_forecast = $winter_forecast[8];
        }

        $data = [
            'search'         => $search,
            'single'         => $single,
            'double'         => $double,
            'holt'           => $holt,
            'winter'         => $winter,
            'periode'        => $periode,
            'hasil_forecast' => $hasil_forecast
        ];
        // dd($data);
        return view('output', $data);       
    }
//TRAINING
    public function stationer($data){
        $total = count($data);
        $data_stationer = array();
        $data_training = array();
        $data_testing = array();

        for($i=0; $i<=$total-8;$i++){
            $data_stationer[$i] = $data[$i+7] - $data[$i]; 
        }

        $total_stationer = count($data_stationer);
        $training = (int)($total_stationer * 0.9);
        $testing = $total_stationer - $training;

        for($i=0; $i<=$training-1;$i++){
            $data_training[$i] = $data_stationer[$i];
        }
        
        for($i=$training; $i<=$total_stationer-1; $i++){
            $data_testing[$i] = $data_stationer[$i];
        }
        $hasil_training = $this->singleTraining($data_training);
        $parameter = array();
        $parameter['nilai_awal'] = $hasil_training[0];
        $parameter['alpha'] = $hasil_training[1];
        $hasil_testing = $this->singleTesting($data_testing,$training, $parameter['nilai_awal'], $parameter['alpha']);
        return [$hasil_testing[0], $hasil_testing[1], $hasil_testing[2]];       
    }

    public function singleTraining($data_training){
        $data = $data_training;
        $total = count($data_training);
        $alpha = 0;
        $arrayHasil = array();     
        for($i=0;$i<=1;$i++){
            if($i==0){
                $nilai_awal = $data[0];
            }
            else{
                $nilai_awal = ($data[0] + $data[1] + $data[2] + $data[3] + $data[4] + $data[5] + $data[6])/7;
            }
            for($j=1;$j<=99;$j++){
                $alpha = $alpha + 0.01;
                $yt = array();
                $eSquare = array();    
                $sum = 0;
                for($k=0;$k<=$total-1;$k++){
                    if($k==0){
                        $yt[$k] = NULL;
                        $eSquare[$k] = NULL;
                    }
                    elseif ($k==1) {
                        $yt[$k] = ($alpha * $data[$k-1]) + ((1 - $alpha) * $nilai_awal);
                        $eSquare[$k] = pow(($data[$k] - $yt[$k]),2);
                    }
                    elseif (($k>1)&&($k<=$total-1)) {
                        $yt[$k] = ($alpha * $data[$k-1]) + ((1 - $alpha) * $yt[$k-1]);
                        $eSquare[$k] = pow(($data[$k] - $yt[$k]),2);
                    }
                    $sum = $sum + $eSquare[$k];
                }        
                $count = count(array_filter($eSquare));
                $MSE = $sum / $count;
                $arrayHasil[] = [$nilai_awal,$alpha,$MSE];
            }             
            // $arraymse = array_column($arrayHasil,2);
            // array_multisort($arraymse, SORT_ASC,SORT_NUMERIC, $arrayHasil);
            // dd($arrayHasil);
        }
        $min = min(array_column($arrayHasil,2));
        $index = array_search($min, array_column($arrayHasil,2));
        // dd($arrayHasil[$index]);
        return [$arrayHasil[$index][0],$arrayHasil[$index][1], $arrayHasil[$index][2]];
    }

    public function doubleTraining($data_training, $periode){
        $data = $data_training;
        $total = count($data_training);       
        $nilai_awal1 = $data[0];
        $nilai_awal2 = $data[0];
        $arrayHasil = array();
        $alpha = 0;
        for($j=1;$j<=99;$j++){
            $alpha = $alpha + 0.01;
            $sum = 0;
            $count = 0;
            $a1 = array();
            $a2 = array();
            $at = array();
            $bt = array();
            $yt = array();
            $eSquare = array();        
            for($i=0; $i<=$total-1;$i++){
                if($i==0){
                    $a1[$i] = ($alpha * $data[$i]) + ((1 - $alpha) * $nilai_awal1);
                    $a2[$i] = ($alpha * $a1[$i]) + ((1 - $alpha) * $nilai_awal2);
                }
                elseif(($i>0)&&($i<=$total-1)){
                    $a1[$i] = ($alpha * $data[$i]) + ((1 - $alpha) * $a1[$i-1]);
                    $a2[$i] = ($alpha * $a1[$i]) + ((1 - $alpha) * $a2[$i-1]);
                }
                $at[$i] = (2 * $a1[$i]) - $a2[$i];
                $bt[$i] = ($alpha / (1 - $alpha)) * ($a1[$i] - $a2[$i]);
                if($i<$periode){
                    $yt[$i] = NULL;
                    $eSquare[$i] = NULL;
                }
                elseif(($i>=$periode)&&($i<=$total-1)){
                    $yt[$i] = $at[$i-$periode] + $bt[$i-$periode] * $periode;
                    $eSquare[$i] = pow(($data[$i]- $yt[$i]),2);
                }
                $sum = $sum + $eSquare[$i];
            }
            $count = count(array_filter($eSquare));
            $MSE = $sum / $count;
            $arrayHasil[] = [$nilai_awal1, $nilai_awal2, $periode, $alpha, $MSE];
        }
        // $arraymse = array_column($arrayHasil,4);
        // array_multisort($arraymse, SORT_ASC,SORT_NUMERIC, $arrayHasil);
        // dd($arrayHasil);
        $min = min(array_column($arrayHasil, 4));
        $index = array_search($min, array_column($arrayHasil, 4));
        // dd($arrayHasil[$index]);
        return[$arrayHasil[$index][0], $arrayHasil[$index][1], $arrayHasil[$index][2], $arrayHasil[$index][3], $arrayHasil[$index][4]];
    }

    public function holtTraining($data_training, $periode){
        $data = $data_training;
        $total = count($data_training); 
        $sumP = 0;
        $sumA = 0;  
        $arrayHasil = array(); 
        if($periode==1){
            $kombinasi = 1;
        }  
        else{
            $kombinasi = 6;
            for($p=1;$p<=$periode;$p++){
                $sumP = $sumP + ($data[($periode+$p)-1] - $data[$p-1]);
                $sumA = $sumA + $data[$p-1];    
            }
        }
        for($l=1;$l<=$kombinasi;$l++){
            if($l==1){
                $nilai_awal1 = $data[0];
                $nilai_awal2 = $data[1] - $data[0];
            }
            elseif($l==2){
                $nilai_awal1 = $data[0];
                $nilai_awal2 = (1/$periode) * ($sumP/$periode);
            }
            elseif ($l==3) {
                $nilai_awal1 = $data[0];
                $nilai_awal2 = ($data[$periode-1] - $data[0])/($periode-1);
            }
            elseif ($l==4) {
                $nilai_awal1 = (1/$periode) * $sumA;
                $nilai_awal2 = $data[1] - $data[0];
            }
            elseif($l==5){
                $nilai_awal1 = (1/$periode) * $sumA;
                $nilai_awal2 = (1/$periode) * ($sumP/$periode);
            }
            elseif ($l==6) {
                $nilai_awal1 = (1/$periode) * $sumA;
                $nilai_awal2 = ($data[$periode-1] - $data[0])/($periode-1);
            }   
            $beta = 0;
            for($a=1;$a<=99;$a++){
                $beta = $beta + 0.01;
                $alpha = 0;        
                for($b=1;$b<=99;$b++){
                    $alpha = $alpha + 0.01;
                    $sum = 0;
                    $count = 0;
                    $at = array();
                    $tt = array();
                    $yt = array();
                    $eSquare = array();
                    for($i=0;$i<=$total-1;$i++){
                        if($i==0){
                            $at[$i] = $nilai_awal1;
                            $tt[$i] = $nilai_awal2;
                        }
                        elseif (($i>0)&&($i<=$total-1)) {
                            $at[$i] = ($alpha * $data[$i]) + ((1-$alpha)*($at[$i-1]+$tt[$i-1]));
                            $tt[$i] = ($beta * ($at[$i]-$at[$i-1])) + ((1-$beta)*$tt[$i-1]);
                        }
                        if($i<$periode){
                            $yt[$i] = NULL;
                            $eSquare[$i] = NULL;
                        }
                        elseif (($i>=$periode)&&($i<=$total-1)){
                            $yt[$i] = $at[$i-$periode] + $tt[$i-$periode] * $periode;
                            $eSquare[$i] = pow(($data[$i] - $yt[$i]),2);    
                        }
                        $sum = $sum + $eSquare[$i];               
                    }
                    $count = count(array_filter($eSquare));
                    $MSE = $sum / $count;
                    if($l==1 && $a==1 && $b==1){
                        $arrayHasil[0] = [$nilai_awal1, $nilai_awal2, $periode, $alpha, $beta, $MSE];
                    }
                    elseif($MSE<$arrayHasil[0][5]){
                        $arrayHasil[0] = [$nilai_awal1, $nilai_awal2, $periode, $alpha, $beta, $MSE];
                    }    
                }  
            }
        }
        // $arraymse = array_column($arrayHasil,5);
        // array_multisort($arraymse, SORT_ASC,SORT_NUMERIC, $arrayHasil);
        // dd($arrayHasil);        
        return[$arrayHasil[0][0], $arrayHasil[0][1], $arrayHasil[0][2], $arrayHasil[0][3], $arrayHasil[0][4], $arrayHasil[0][5]]; 
    }

    public function winterTraining($data_training, $periode){
        $data = $data_training;
        $total = count($data_training);       
        $sumP = 0;
        $sumA = 0;
        $arrayHasil = array();
        if($periode==1){
            $kombinasi = 1;
        }  
        else{
            $kombinasi = 6;
            for($p=1;$p<=$periode;$p++){
                $sumP = $sumP + ($data[($periode+$p)-1] - $data[$p-1]);
                $sumA = $sumA + $data[$p-1];    
            }
        }
        for($l=1;$l<=$kombinasi;$l++){
            if($l==1){
                $nilai_awal1 = $data[0];
                $nilai_awal2 = $data[1] - $data[0];
            }
            elseif($l==2){
                $nilai_awal1 = $data[0];
                $nilai_awal2 = (1/$periode) * ($sumP/$periode);
            }
            elseif ($l==3) {
                $nilai_awal1 = $data[0];
                $nilai_awal2 = ($data[$periode-1] - $data[0])/($periode-1);
            }
            elseif ($l==4) {
                $nilai_awal1 = (1/$periode) * $sumA;
                $nilai_awal2 = $data[1] - $data[0];
            }
            elseif($l==5){
                $nilai_awal1 = (1/$periode) * $sumA;
                $nilai_awal2 = (1/$periode) * ($sumP/$periode);
            }
            elseif ($l==6) {
                $nilai_awal1 = (1/$periode) * $sumA;
                $nilai_awal2 = ($data[$periode-1] - $data[0])/($periode-1);
            }
            $nilai_awal3 = $data[0]/$nilai_awal1;
            $alpha = 0;
            for($a=1;$a<=99;$a++){
                $alpha = $alpha + 0.01;
                $beta = 0;
                for($b=1;$b<=99;$b++){
                    $beta = $beta + 0.01;
                    $mu = 0;
                    for($m=1;$m<=99;$m++){
                        $mu = $mu + 0.01;
                        $sum = 0;
                        $count = 0;
                        $at = array();
                        $tt = array();
                        $st = array();
                        $yt = array();
                        $eSquare = array();
                        for($i=0;$i<=$total-1;$i++){
                            if($i==0){
                                $at[$i] = $nilai_awal1;
                                $tt[$i] = $nilai_awal2;
                                $st[$i] = $nilai_awal3;
                                $yt[$i] = NULL;
                                $eSquare[$i] = NULL;
                            }
                            elseif(($i>0)&&($i<=$total-1)) {
                                if(($i>=1)&&($i<=7)){
                                    $at[$i] = ($alpha * ($data[$i]/$st[0])) + ((1-$alpha) * ($at[$i-1] + $tt[$i-1]));
                                    $st[$i] = ($mu * ($data[$i]/$at[$i])) + ((1-$mu) * $st[0]);
                                    if($i<$periode){
                                        $yt[$i] = NULL;
                                        $eSquare[$i] = NULL;
                                    }
                                    else{
                                        $yt[$i] = ($at[$i-$periode] - $tt[$i-$periode] * $periode) * $st[0];
                                        $eSquare[$i] = pow(($data[$i]-$yt[$i]),2);
                                    }
                                }
                                elseif($i>7){
                                    $at[$i] = ($alpha * ($data[$i]/$st[$i-7])) + ((1-$alpha) * ($at[$i-1] + $tt[$i-1]));
                                    $st[$i] = ($mu * ($data[$i]/$at[$i])) + ((1-$mu) * $st[$i-7]);
                                    $yt[$i] = ($at[$i-$periode] - $tt[$i-$periode] * $periode) * $st[$i-7];
                                    $eSquare[$i] = pow(($data[$i]-$yt[$i]),2);
                                }
                                $tt[$i] = ($beta * ($at[$i]-$at[$i-1])) + ((1-$beta) * $tt[$i-1]);
                            }
                            $sum = $sum + $eSquare[$i];
                        }
                        $count = count(array_filter($eSquare));
                        $MSE = $sum / $count;
                        if($l==1 && $a==1 && $b==1 && $m==1){
                            $arrayHasil[0] = [$nilai_awal1, $nilai_awal2, $nilai_awal3, $periode, $alpha, $beta, $mu, $MSE];
                        }
                        elseif($MSE<$arrayHasil[0][7]){
                            $arrayHasil[0] = [$nilai_awal1, $nilai_awal2, $nilai_awal3, $periode, $alpha, $beta, $mu, $MSE];
                        }                        
                    }
                }
            }
        } 
        // $arraymse = array_column($arrayHasil,7);
        // array_multisort($arraymse, SORT_ASC,SORT_NUMERIC, $arrayHasil);
        // dd($arrayHasil);  
        return[$arrayHasil[0][0], $arrayHasil[0][1], $arrayHasil[0][2], $arrayHasil[0][3], $arrayHasil[0][4], $arrayHasil[0][5],$arrayHasil[0][6],$arrayHasil[0][7]];
    }
//END TRAINING
//TESTING
    public function singleTesting($data_testing, $index, $nilai_awal, $alpha){
        $data = $data_testing;
        $total = count($data_testing);
        $yt = array();
        $eSquare = array();
        $sum = 0;
        $batas = $index+$total-1;
        for($i=$index;$i<=$batas;$i++){
            if($i==$index){
                $yt[$i] = NULL;
                $eSquare[$i] = NULL;
            }
            elseif ($i==($index+1)) {
                $yt[$i] = ($alpha * $data[$i-1]) + ((1 - $alpha) * $nilai_awal);
                $eSquare[$i] = pow(($data[$i] - $yt[$i]),2);
            }
            elseif (($i>$index+1)&&($i<=$batas)) {
                $yt[$i] = ($alpha * $data[$i-1]) + ((1 - $alpha) * $yt[$i-1]);
                $eSquare[$i] = pow(($data[$i] - $yt[$i]),2);
            }
            $sum = $sum + $eSquare[$i];
        }
        $count = count(array_filter($eSquare));
        $MSE = $sum / $count;
        // dd("Nilai Awal = ".$nilai_awal." Alpha = ".$alpha." MSE = ".$MSE);
        return[$nilai_awal, $alpha, $MSE];
    }

    public function doubleTesting($data_testing, $index, $nilai_awal1, $nilai_awal2, $periode, $alpha){
        $data = $data_testing;
        $total = count($data_testing);
        $a1 = array();
        $a2 = array();
        $at = array();
        $bt = array();
        $yt = array();
        $eSquare = array();
        $sum = 0;
        $batas = $index+$total-1;
        $p = $index+$periode;
        for($i=$index; $i<=$batas;$i++){
            if($i==$index){
                $a1[$i] = ($alpha * $data[$i]) + ((1 - $alpha) * $nilai_awal1);
                $a2[$i] = ($alpha * $a1[$i]) + ((1 - $alpha) * $nilai_awal2);
            }
            elseif(($i>$index)&&($i<=$batas)){
                $a1[$i] = ($alpha * $data[$i]) + ((1 - $alpha) * $a1[$i-1]);
                $a2[$i] = ($alpha * $a1[$i]) + ((1 - $alpha) * $a2[$i-1]);
            }
            $at[$i] = (2 * $a1[$i]) - $a2[$i];
            $bt[$i] = ($alpha / (1 - $alpha)) * ($a1[$i] - $a2[$i]);
            if($i<$p){
                $yt[$i] = NULL;
                $eSquare[$i] = NULL;
            }
            elseif(($i>=$p)&&($i<=$batas)){
                $yt[$i] = $at[$i-$periode] + $bt[$i-$periode] * $periode;
                $eSquare[$i] = pow(($data[$i]- $yt[$i]),2);
            }
            $sum = $sum + $eSquare[$i];
        }
        $count = count(array_filter($eSquare));
        $MSE = $sum / $count;
        // dd("Nilai Awal 1 = ".$nilai_awal1." Nilai Awal 2 = ".$nilai_awal2." Periode = ".$periode." Alpha = ".$alpha." MSE = ".$MSE);
        return[$nilai_awal1, $nilai_awal2, $periode, $alpha, $MSE];
    }

    public function holtTesting($data_testing, $index, $nilai_awal1, $nilai_awal2, $periode, $alpha, $beta){
        $data = $data_testing;
        $total = count($data_testing);
        $at = array();
        $tt = array();
        $yt = array();
        $eSquare = array();
        $sum = 0;
        $batas = $index+$total-1;
        $p = $index+$periode;
        for($i=$index;$i<=$batas;$i++){           
            if($i==$index){
                $at[$i] = $nilai_awal1;
                $tt[$i] = $nilai_awal2;
            }
            elseif (($i>$index)&&($i<=$batas)) {
                $at[$i] = ($alpha * $data[$i]) + ((1-$alpha)*($at[$i-1]+$tt[$i-1]));
                $tt[$i] = ($beta * ($at[$i]-$at[$i-1])) + ((1-$beta)*$tt[$i-1]);
            }
            if($i<$p){
                $yt[$i] = NULL;
                $eSquare[$i] = NULL;
            }
            elseif (($i>=$p)&&($i<=$batas)){
                $yt[$i] = $at[$i-$periode] + $tt[$i-$periode] * $periode;
                $eSquare[$i] = pow(($data[$i] - $yt[$i]),2);    
            }
            $sum = $sum + $eSquare[$i];
        }
        $count = count(array_filter($eSquare));
        $MSE = $sum / $count;
        // dd("Nilai Awal 1 = ".$nilai_awal1." Nilai Awal 2 = ".$nilai_awal2." Periode = ".$periode." Alpha = ".$alpha." Beta = ".$beta." MSE = ".$MSE);
        return[$nilai_awal1, $nilai_awal2, $periode, $alpha, $beta, $MSE];
    }

    public function winterTesting($data_testing, $index, $nilai_awal1, $nilai_awal2, $nilai_awal3, $periode, $alpha, $beta, $mu){
        $data = $data_testing;
        $total = count($data_testing);       
        $at = array();
        $tt = array();
        $st = array();
        $yt = array();
        $eSquare = array();
        $sum = 0;
        $batas = $index+$total-1;
        $p = $index+$periode;
        for($i=$index;$i<=$batas;$i++){            
            if($i==$index){
                $at[$i] = $nilai_awal1;
                $tt[$i] = $nilai_awal2;
                $st[$i] = $nilai_awal3;
                $yt[$i] = NULL;
                $eSquare[$i] = NULL;
            }
            elseif(($i>$index)&&($i<=$batas)) {
                if(($i>=$index+1)&&($i<=$index+7)){
                    $at[$i] = ($alpha * ($data[$i]/$st[$index])) + ((1-$alpha) * ($at[$i-1] + $tt[$i-1]));
                    $st[$i] = ($mu * ($data[$i]/$at[$i])) + ((1-$mu) * $st[$index]);
                    if($i<$p){
                        $yt[$i] = NULL;
                        $eSquare[$i] = NULL;
                    }
                    else{
                        $yt[$i] = ($at[$i-$periode] - $tt[$i-$periode] * $periode) * $st[$index];
                        $eSquare[$i] = pow(($data[$i]-$yt[$i]),2);
                    }
                }
                elseif($i>$index+7){
                    $at[$i] = ($alpha * ($data[$i]/$st[$i-7])) + ((1-$alpha) * ($at[$i-1] + $tt[$i-1]));
                    $st[$i] = ($mu * ($data[$i]/$at[$i])) + ((1-$mu) * $st[$i-7]);
                    $yt[$i] = ($at[$i-$periode] - $tt[$i-$periode] * $periode) * $st[$i-7];
                    $eSquare[$i] = pow(($data[$i]-$yt[$i]),2);
                }
                $tt[$i] = ($beta * ($at[$i]-$at[$i-1])) + ((1-$beta) * $tt[$i-1]);             
            }
            $sum = $sum + $eSquare[$i];
        }
        $count = count(array_filter($eSquare));
        $MSE = $sum / $count;
        // dd("nilai 1 = ".$nilai_awal1." nilai 2 = ".$nilai_awal2." nilai 3 = ".$nilai_awal3." periode = ".$periode." alpha = ".$alpha." beta = ".$beta." mu = ".$mu." MSE = ".$MSE);
        return [$nilai_awal1, $nilai_awal2, $nilai_awal3, $periode, $alpha, $beta, $mu, $MSE];
    }
//END TESTING
//FORECASTING
    public function singleForecast($data_asli, $nilai_awal, $alpha){
        $data = array();
        $total_asli = count($data_asli);
        for($i=0; $i<=$total_asli-8;$i++){
            $data[$i] = $data_asli[$i+7] - $data_asli[$i]; 
        }
        $total = count($data);
        $yt = array();
        $eSquare = array();
        $nilai_asli = array();
        $sum = 0;
        for($i=0;$i<=$total;$i++){
            if($i==0){
                $yt[$i] = NULL;
                $eSquare[$i] = NULL;
            }
            elseif ($i==1) {
                $yt[$i] = ($alpha * $data[$i-1]) + ((1 - $alpha) * $nilai_awal);
                $eSquare[$i] = pow(($data[$i] - $yt[$i]),2);
            }
            elseif(($i>1)&&($i<=$total)){
                $yt[$i] = ($alpha * $data[$i-1]) + ((1 - $alpha) * $yt[$i-1]);
                if($i==$total){
                    $eSquare[$i] = NULL;
                }
                else{
                    $eSquare[$i] = pow(($data[$i] - $yt[$i]),2);
                }                
            }
            $sum = $sum + $eSquare[$i];
        }
        for($i=1;$i<=$total+1;$i++){
            if($i==1){
                $nilai_asli[$i] = (($yt[$i]-((1 - $alpha) * $nilai_awal))/$alpha)+$data_asli[$i-1];
            }
            else{
                if($i==$total+1){
                    $yt[$i] = ((1 - $alpha) * $yt[$i-1]);
                }
                $nilai_asli[$i] = (($yt[$i]-((1 - $alpha) * $yt[$i-1]))/$alpha)+$data_asli[$i-1]; 
            }
        }
        dd($nilai_asli);
        $forecast[1] = $nilai_asli[$total+1];
        $count = count(array_filter($eSquare));
        $MSE = $sum / $count;
        // dd("Nilai Awal = ".$nilai_awal." Alpha = ".$alpha." MSE = ".$MSE);
        return[$nilai_awal, $alpha, $MSE, $forecast];
    }

    public function doubleForecast($data_asli, $nilai_awal1, $nilai_awal2, $periode, $alpha){
        $data = $data_asli;
        $total = count($data_asli);
        $a1 = array();
        $a2 = array();
        $at = array();
        $bt = array();
        $yt = array();
        $eSquare = array();
        $forecast = array();
        $sum = 0;
        $batas = $periode+$total-1;
        for($i=0; $i<=$batas;$i++){
            $j=1;
            if($i<=$total-1){
                if($i==0){
                    $a1[$i] = ($alpha * $data[$i]) + ((1 - $alpha) * $nilai_awal1);
                    $a2[$i] = ($alpha * $a1[$i]) + ((1 - $alpha) * $nilai_awal2);
                }
                elseif($i>0){
                    $a1[$i] = ($alpha * $data[$i]) + ((1 - $alpha) * $a1[$i-1]);
                    $a2[$i] = ($alpha * $a1[$i]) + ((1 - $alpha) * $a2[$i-1]);
                }
                $at[$i] = (2 * $a1[$i]) - $a2[$i];
                $bt[$i] = ($alpha / (1 - $alpha)) * ($a1[$i] - $a2[$i]);
                if($i<$periode){
                    $yt[$i] = NULL;
                    $eSquare[$i] = NULL;
                }
                elseif($i>=$periode){
                    $yt[$i] = $at[$i-$periode] + $bt[$i-$periode] * $periode;
                    $eSquare[$i] = pow(($data[$i]- $yt[$i]),2);
                }
            }            
            elseif($i>$total-1){
                $forecast[$j] = number_format($at[$i-$periode] + $bt[$i-$periode] * $periode);
                $eSquare[$i] = NULL;
                $j = $j+1;
            }

            $sum = $sum + $eSquare[$i];
        }
        $count = count(array_filter($eSquare));
        $MSE = $sum / $count;
        // dd("Nilai Awal 1 = ".$nilai_awal1." Nilai Awal 2 = ".$nilai_awal2." Periode = ".$periode." Alpha = ".$alpha." MSE = ".$MSE);
        return[$nilai_awal1, $nilai_awal2, $periode, $alpha, $MSE, $forecast];
    }

    public function holtForecast($data_asli, $nilai_awal1, $nilai_awal2, $periode, $alpha, $beta){
        $data = $data_asli;
        $total = count($data_asli);
        $at = array();
        $tt = array();
        $yt = array();
        $eSquare = array();
        $forecast = array();
        $sum = 0;
        $batas = $periode+$total-1;
        for($i=0;$i<=$batas;$i++){ 
            $j=1; 
            if($i<=$total-1){
                if($i==0){
                    $at[$i] = $nilai_awal1;
                    $tt[$i] = $nilai_awal2;
                }
                elseif($i>0) {
                    $at[$i] = ($alpha * $data[$i]) + ((1-$alpha)*($at[$i-1]+$tt[$i-1]));
                    $tt[$i] = ($beta * ($at[$i]-$at[$i-1])) + ((1-$beta)*$tt[$i-1]);
                }
                if($i<$periode){
                    $yt[$i] = NULL;
                    $eSquare[$i] = NULL;
                }
                elseif($i>=$periode){
                    $yt[$i] = $at[$i-$periode] + $tt[$i-$periode] * $periode;
                    $eSquare[$i] = pow(($data[$i] - $yt[$i]),2);    
                }
            }    
            elseif($i>$total-1) {
                $forecast[$j] = number_format($at[$i-$periode] + $tt[$i-$periode] * $periode);
                $eSquare[$i] = NULL;
                $j = $j+1;
            }
            $sum = $sum + $eSquare[$i];
        }
        $count = count(array_filter($eSquare));
        $MSE = $sum / $count;
        // dd("Nilai Awal 1 = ".$nilai_awal1." Nilai Awal 2 = ".$nilai_awal2." Periode = ".$periode." Alpha = ".$alpha." Beta = ".$beta." MSE = ".$MSE);
        return[$nilai_awal1, $nilai_awal2, $periode, $alpha, $beta, $MSE, $forecast];
    }

    public function winterForecast($data_asli, $nilai_awal1, $nilai_awal2, $nilai_awal3, $periode, $alpha, $beta, $mu){
        $data = $data_asli;
        $total = count($data_asli);       
        $at = array();
        $tt = array();
        $st = array();
        $yt = array();
        $eSquare = array();
        $forecast = array();
        $sum = 0;
        $batas = $periode+$total-1;
        for($i=0;$i<=$batas;$i++){
            $j=1;
            if($i<=$total-1){
                if($i==0){
                    $at[$i] = $nilai_awal1;
                    $tt[$i] = $nilai_awal2;
                    $st[$i] = $nilai_awal3;
                    $yt[$i] = NULL;
                    $eSquare[$i] = NULL;
                }
                elseif($i>0){
                    if(($i>=1)&&($i<=7)){
                        $at[$i] = ($alpha * ($data[$i]/$st[0])) + ((1-$alpha) * ($at[$i-1] + $tt[$i-1]));
                        $st[$i] = ($mu * ($data[$i]/$at[$i])) + ((1-$mu) * $st[0]);
                        if($i<$periode){
                            $yt[$i] = NULL;
                            $eSquare[$i] = NULL;
                        }
                        else{
                            $yt[$i] = ($at[$i-$periode] - $tt[$i-$periode] * $periode) * $st[0];
                            $eSquare[$i] = pow(($data[$i]-$yt[$i]),2);
                        }
                    }
                    elseif($i>7){
                        $at[$i] = ($alpha * ($data[$i]/$st[$i-7])) + ((1-$alpha) * ($at[$i-1] + $tt[$i-1]));
                        $st[$i] = ($mu * ($data[$i]/$at[$i])) + ((1-$mu) * $st[$i-7]);
                        $yt[$i] = ($at[$i-$periode] - $tt[$i-$periode] * $periode) * $st[$i-7];
                        $eSquare[$i] = pow(($data[$i]-$yt[$i]),2);                    
                    }                
                    $tt[$i] = ($beta * ($at[$i]-$at[$i-1])) + ((1-$beta) * $tt[$i-1]);             
                }
            }     
            elseif($i>$total-1){
                $forecast[$j] = number_format(($at[$i-$periode] - $tt[$i-$periode] * $periode) * $st[$i-7]);
                $eSquare[$i] = NULL;
                $j = $j+1;
            }
            $sum = $sum + $eSquare[$i];
        }
        $count = count(array_filter($eSquare));
        $MSE = $sum / $count;
        // dd("nilai 1 = ".$nilai_awal1." nilai 2 = ".$nilai_awal2." nilai 3 = ".$nilai_awal3." periode = ".$periode." alpha = ".$alpha." beta = ".$beta." mu = ".$mu." MSE = ".$MSE);
        return [$nilai_awal1, $nilai_awal2, $nilai_awal3, $periode, $alpha, $beta, $mu, $MSE, $forecast];
    }
//END FORECASTING
}
