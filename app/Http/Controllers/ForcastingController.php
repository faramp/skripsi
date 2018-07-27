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
        $training = (int)($total * 0.9);
        $testing = $total - $training;
        $data_training = array();
        $data_testing = array();
        $metode = $request->input('metode');

        for($i=0; $i<=$training-1;$i++){
            $data_training[$i] = $search[$i]->qty;
        }
        
        for($i=$training; $i<=$total-1; $i++){
            $data_testing[$i] = $search[$i]->qty;
        }

        $min_hasil = array();
        $min_hasil['nilai_awal1'] = array();
        $min_hasil['nilai_awal2'] = array();
        $min_hasil['nilai_awal3'] = array();
        $min_hasil['periode'] = array();
        $min_hasil['alpha'] = array();
        $min_hasil['beta'] = array();
        $min_hasil['mu'] = array();
        $min_hasil['MSE'] = array();
        $index = 1;

        if($metode==1){
            $this->stationer($search);
        }
        elseif ($metode==2) {
            $this->doubleTraining($data_training);
        }
        elseif ($metode==3) {
            for($p=1;$p<=7;$p++){
                if($p==1){
                    $loop = 2;
                }
                else{
                    $loop = 6;
                }

                for($l=1;$l<=$loop;$l++){
                    $hasil_training = $this->holtTraining($data_training, $p, $l);
                    $min_hasil['nilai_awal1'][$index] = $hasil_training[0];
                    $min_hasil['nilai_awal2'][$index] = $hasil_training[1];
                    $min_hasil['periode'][$index] = $hasil_training[2];
                    $min_hasil['alpha'][$index] = $hasil_training[3];
                    $min_hasil['beta'][$index] = $hasil_training[4];
                    $min_hasil['MSE'][$index] = $hasil_training[5];

                    $index = $index+1;
                }
            }
            $min_MSE = min($min_hasil['MSE']);
            for($i=1;$i<=38;$i++){
                if($min_hasil['MSE'][$i]==$min_MSE){
                    $nilai_awal1 = $min_hasil['nilai_awal1'][$i];
                    $nilai_awal2 = $min_hasil['nilai_awal2'][$i];
                    $periode = $min_hasil['periode'][$i];
                    $alpha = $min_hasil['alpha'][$i];
                    $beta = $min_hasil['beta'][$i];
                    $MSE = $min_hasil['MSE'][$i];
                }
            }
            dd("nilai 1 = ".$nilai_awal1." nilai 2 = ".$nilai_awal2." periode = ".$periode." alpha = ".$alpha." beta = ".$beta." MSE = ".$MSE);
        }

        elseif ($metode==4) {
            $hasil_training = $this->holtTraining($data_training, 1, 1);
            $nilai_awal1 = $hasil_training[0];
            $nilai_awal2 = $hasil_training[1];
            $periode = $hasil_training[2];
            $alpha = $hasil_training[3];
            $beta = $hasil_training[4];
            $MSE = $hasil_training[5];

            dd("nilai 1 = ".$nilai_awal1." nilai 2 = ".$nilai_awal2." periode = ".$periode." alpha = ".$alpha." beta = ".$beta." MSE = ".$MSE);
        }
        
        elseif ($metode==5) {
            for($p=1;$p<=7;$p++){
                if($p==1){
                    $loop = 2;
                }
                else{
                    $loop = 6;
                }

                for($l=1;$l<=$loop;$l++){
                    $hasil_training = $this->winterTraining($data_training, $p, $l);
                    $min_hasil['nilai_awal1'][$index] = $hasil_training[0];
                    $min_hasil['nilai_awal2'][$index] = $hasil_training[1];
                    $min_hasil['nilai_awal3'][$index] = $hasil_training[2];
                    $min_hasil['periode'][$index] = $hasil_training[3];
                    $min_hasil['alpha'][$index] = $hasil_training[4];
                    $min_hasil['beta'][$index] = $hasil_training[5];
                    $min_hasil['mu'][$index] = $hasil_training[6];
                    $min_hasil['MSE'][$index] = $hasil_training[7];

                    $index = $index+1;
                }
            }
            $min_MSE = min($min_hasil['MSE']);
            for($i=1;$i<=38;$i++){
                if($min_hasil['MSE'][$i]==$min_MSE){
                    $nilai_awal1 = $min_hasil['nilai_awal1'][$i];
                    $nilai_awal2 = $min_hasil['nilai_awal2'][$i];
                    $nilai_awal3 = $min_hasil['nilai_awal3'][$i];
                    $periode = $min_hasil['periode'][$i];
                    $alpha = $min_hasil['alpha'][$i];
                    $beta = $min_hasil['beta'][$i];
                    $mu = $min_hasil['mu'][$i];
                    $MSE = $min_hasil['MSE'][$i];
                }
            }
            dd("nilai 1 = ".$arrayHasil[0]." nilai 2 = ".$arrayHasil[1]." nilai 3 = ".$arrayHasil[2]." periode = ".$arrayHasil[3]." alpha = ".$arrayHasil[4]." beta = ".$arrayHasil[5]." mu = ".$arrayHasil[6]." MSE = ".$arrayHasil[7]);
        }

        elseif ($metode==6) {
            $hasil_training = $this->winterTraining($data_training, 1, 1);
            $nilai_awal1 = $hasil_training[0];
            $nilai_awal2 = $hasil_training[1];
            $nilai_awal3 = $hasil_training[2];
            $periode = $hasil_training[3];
            $alpha = $hasil_training[4];
            $beta = $hasil_training[5];
            $mu = $hasil_training[6];
            $MSE = $hasil_training[7];

            dd("nilai 1 = ".$arrayHasil[0]." nilai 2 = ".$arrayHasil[1]." nilai 3 = ".$arrayHasil[2]." periode = ".$arrayHasil[3]." alpha = ".$arrayHasil[4]." beta = ".$arrayHasil[5]." mu = ".$arrayHasil[6]." MSE = ".$arrayHasil[7]);
        }
        

        // dd($min_hasil['MSE']);
        // $this->holtTraining($data_training, 1, 2);
        // $parameter = array();
        // $parameter['nilai_awal'] = $hasil_training[0];
        // $parameter['periode'] = $hasil_training[1];
        // $parameter['alpha'] = $hasil_training[2];
        // $this->doubleTesting($data_testing, $training, $parameter['periode'], $parameter['nilai_awal'], $parameter['alpha']);
        return view('home');       
    }

    public function stationer($data){
        $search = $data;
        $total = count($data);
        $data_stationer = array();
        $data_training = array();
        $data_testing = array();

        for($i=0; $i<=$total-8;$i++){
            $data_stationer[$i] = $search[$i+7]->qty - $search[$i]->qty; 
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
        $this->singleTesting($data_testing,$training, $parameter['nilai_awal'], $parameter['alpha']);
        return view('home');       
    }

    public function singleTraining($data_training){
        $data = $data_training;
        $total = count($data_training);
        $alpha = 0;
        $arrayHasil = array();
        $arrayHasil['nilai_awal'] = array();
        $arrayHasil['alpha'] = array();
        $arrayHasil['MSE'] = array();
    
        for($i=0;$i<=1;$i++){
            if($i==0){
                $nilai_awal = $data[0];
            }
            else{
                $nilai_awal = ($data[0] + $data[1] + $data[2] + $data[3] + $data[4] + $data[5] + $data[6])/7;
            }

            for($j=1;$j<=999;$j++){
                $alpha = $alpha + 0.001;
                $arrayData = array();
                $arrayData['per1'] = array();
                $arrayData['per2'] = array();
                $arrayData['per3'] = array();
                $arrayData['per4'] = array();
                $arrayData['per5'] = array();
                $sum = 0;

                for($k=0;$k<=$total-1;$k++){

                    if($k==0){
                        $per2 = (1 - $alpha) * $nilai_awal;
                        $per3 = NULL;
                        $per4 = NULL;
                        $per5 = NULL;
                    }

                    elseif (($k>0)&&($k<=$total-1)) {
                        $per3 = $arrayData['per1'][$k-1] + $arrayData['per2'][$k-1];
                        $per2 = (1 - $alpha) * $per3;
                        $per4 = $data[$k] - $per3;
                        $per5 = pow($per4,2);
                    }
                    $arrayData['per1'][$k] = $alpha * $data[$k];
                    $arrayData['per2'][$k] = $per2;
                    $arrayData['per3'][$k] = $per3;
                    $arrayData['per4'][$k] = $per4;
                    $arrayData['per5'][$k] = $per5;

                    $sum = $sum + $arrayData['per5'][$k];
                }
                
                $MSE = $sum / ($total-1);

                if(count($arrayHasil['MSE'])>=999){
                    $arrayHasil['nilai_awal'][999+$j] = $nilai_awal;
                    $arrayHasil['alpha'][999+$j] = $alpha;
                    $arrayHasil['MSE'][999+$j] = $MSE;
                }
                else{
                    $arrayHasil['nilai_awal'][$j] = $nilai_awal;
                    $arrayHasil['alpha'][$j] = $alpha;
                    $arrayHasil['MSE'][$j] = $MSE;
                }
            }
        }

        $min = min($arrayHasil['MSE']);
        for($i=1;$i<=1998;$i++){
            if($arrayHasil['MSE'][$i]==$min){
                $printNilaiAwal = $arrayHasil['nilai_awal'][$i];
                $printAlpha = $arrayHasil['alpha'][$i];
                $printMSE = $arrayHasil['MSE'][$i];
            }
        }
        dd("Nilai Awal = ".$printNilaiAwal." Alpha = ".$printAlpha." MSE = ".$printMSE);
        // return [$printNilaiAwal,$printAlpha];
    }

    public function doubleTraining($data_training){
        $data = $data_training;
        $total = count($data_training);       
        $nilai_awal1 = $data[0];
        $nilai_awal2 = $data[0];
        $arrayHasil = array();
        $arrayHasil['periode'] = array();
        $arrayHasil['alpha'] = array();
        $arrayHasil['MSE'] = array();

        for($p=1;$p<=7;$p++){
            $alpha = 0;
            for($j=1;$j<=999;$j++){
                $alpha = $alpha + 0.001;
                $sum = 0;
                $count = 0;

                $arrayData = array();
                $arrayData['per1'] = array();
                $arrayData['per2'] = array();
                $arrayData['per3'] = array();
                $arrayData['per4'] = array();
                $arrayData['per5'] = array();
                $arrayData['per6'] = array();
                $arrayData['per7'] = array();
                $arrayData['per8'] = array();
                $arrayData['per9'] = array();
                $arrayData['per10'] = array();
                $arrayData['per11'] = array();
                $arrayData['per12'] = array();
            
                for($i=0; $i<=$total-1;$i++){

                    if($i==0){
                        $per2 = (1 - $alpha) * $nilai_awal1;
                        $per5 = (1 - $alpha) * $nilai_awal2;
                    }

                    elseif(($i>0)&&($i<=$total-1)){
                        $per2 = (1 - $alpha) * $arrayData['per3'][$i-1];
                        $per5 = (1 - $alpha) * $arrayData['per6'][$i-1];
                    }

                    $per3 = ($alpha * $data[$i]) + $per2;
                    $per6 = ($alpha * $per3) + $per5;
                    $per8 = (2 * $per3) - $per6;
                    $per9 = ($alpha / (1 - $alpha)) * ($per3 - $per6);

                    if($i<$p){
                        $per10 = NULL;
                        $per11 = NULL;
                        $per12 = NULL;
                    }
                    elseif(($i>=$p)&&($i<=$total-1)){
                        $per10 = $arrayData['per8'][$i-$p] + $arrayData['per9'][$i-$p] * $p;
                        $per11 = $data[$i] - $per10;
                        $per12 = pow($per11,2);
                    }

                    $arrayData['per1'][$i] = $alpha * $data[$i];
                    $arrayData['per2'][$i] = $per2;
                    $arrayData['per3'][$i] = $per3;
                    $arrayData['per4'][$i] = $alpha * $per3;
                    $arrayData['per5'][$i] = $per5;
                    $arrayData['per6'][$i] = $per6;
                    $arrayData['per7'][$i] = $per3 - $per6;
                    $arrayData['per8'][$i] = $per8;
                    $arrayData['per9'][$i] = $per9;
                    $arrayData['per10'][$i] = $per10;
                    $arrayData['per11'][$i] = $per11;
                    $arrayData['per12'][$i] = $per12;

                    $sum = $sum + $arrayData['per12'][$i];
                }
                $count = count(array_filter($arrayData['per11']));
                $MSE = $sum / $count;

                if($p==1){
                    $arrayHasil['periode'][$j] = $p;
                    $arrayHasil['alpha'][$j] = $alpha;
                    $arrayHasil['MSE'][$j] = $MSE;    
                }
                else{
                    $arrayHasil['periode'][(($p-1)*999)+$j] = $p;
                    $arrayHasil['alpha'][(($p-1)*999)+$j] = $alpha;
                    $arrayHasil['MSE'][(($p-1)*999)+$j] = $MSE;    
                }
            }
        }
        $min = min($arrayHasil['MSE']);

        for($i=1;$i<=6993;$i++){
            if($arrayHasil['MSE'][$i]==$min){
                $printPeriode = $arrayHasil['periode'][$i];
                $printAlpha = $arrayHasil['alpha'][$i];
                $printMSE = $arrayHasil['MSE'][$i];
            }
        }
        dd("nilai awal 1 = ".$nilai_awal1." nilai awal 2 = ".$nilai_awal1." periode = ".$printPeriode." alpha = ".$printAlpha);
        // return[$nilai_awal1, $printPeriode, $printAlpha];
    }

    public function holtTraining($data_training, $periode, $loop){
        $data = $data_training;
        $total = count($data_training);       
        $sumP = ($data[1] - $data[0]);
        $sumA = $data[0];
        $arrayHasil = array();
        
        for($p=1;$p<=$periode;$p++){
            if($p>1){
                $sumP = $sumP + ($data[($p+$p)-1] - $data[$p-1]);
                $sumA = $sumA + $data[$p-1]; 
            }

            for($l=1;$l<=$loop;$l++){
                if ($p==1) {
                    $nilai_awal1 = $data[0];
                    if($l==1){
                    $nilai_awal2 = $data[1] - $data[0];
                    }
                    elseif($l==2){
                        $nilai_awal2 = 0;
                    }
                }
                else{
                    if($l==1){
                        $nilai_awal1 = $data[0];
                        $nilai_awal2 = $data[1] - $data[0];
                    }
                    elseif($l==2){
                        $nilai_awal1 = $data[0];
                        $nilai_awal2 = (1/$p) * ($sumP/$p);;
                    }
                    elseif ($l==3) {
                        $nilai_awal1 = $data[0];
                        $nilai_awal2 = ($data[$p-1] - $data[0])/($p-1);
                    }
                    elseif ($l==4) {
                        $nilai_awal1 = (1/$p) * $sumA;
                        $nilai_awal2 = $data[1] - $data[0];
                    }
                    elseif($l==5){
                        $nilai_awal1 = (1/$p) * (1/$p) * $sumA;
                        $nilai_awal2 = (1/$p) * ($sumP/$p);;
                    }
                    elseif ($l==6) {
                        $nilai_awal1 = (1/$p) * $sumA;
                        $nilai_awal2 = ($data[$p-1] - $data[0])/($p-1);
                    }
                }
            }
        }
            $beta = 0;

            for($k=1;$k<=999;$k++){
                $beta = $beta + 0.001;
                $alpha = 0;
               
                for($j=1;$j<=999;$j++){
                    $alpha = $alpha + 0.001;
                    $sum = 0;
                    $count = 0;

                    $arrayData = array();
                    $arrayData['per1'] = array();
                    $arrayData['per2'] = array();
                    $arrayData['per3'] = array();
                    $arrayData['per4'] = array();
                    $arrayData['per5'] = array();
                    $arrayData['per6'] = array();
                    $arrayData['per7'] = array();
                    $arrayData['per8'] = array();
                    $arrayData['per9'] = array();
                    $arrayData['per10'] = array();
                    $arrayData['per11'] = array();

                    for($i=0;$i<=$total-1;$i++){

                        if($i==0){
                            $per1 = NULL;
                            $per2 = NULL;
                            $per3 = NULL;
                            $per4 = $nilai_awal1;
                            $per5 = NULL;
                            $per6 = NULL;
                            $per7 = NULL;
                            $per8 = $nilai_awal2;
                            $per9 = NULL;
                            $per10 = NULL;
                            $per11 = NULL;
                        }

                        elseif (($i>0)&&($i<=$total-1)) {
                            $per1 = $alpha * $data[$i];
                            $per2 = $arrayData['per4'][$i-1] + $arrayData['per8'][$i-1];
                            $per3 = (1 - $alpha) * $per2;
                            $per4 = $per1 + $per3;
                            $per5 = $per4 - $arrayData['per4'][$i-1];
                            $per6 = $beta * $per5;
                            $per7 = (1 - $beta) * $arrayData['per8'][$i-1];
                            $per8 = $per6 + $per7;
                            if($i<$p){
                                $per9 = NULL;
                                $per10 = NULL;
                                $per11 = NULL;
                            }
                            else{
                                $per9 = $arrayData['per4'][$i-$p] + $arrayData['per8'][$i-$p] * $p;
                                $per10 = $data[$i] - $per9;
                                $per11 = pow($per10,2);    
                            }
                        }

                        $arrayData['per1'][$i] = $per1;
                        $arrayData['per2'][$i] = $per2;
                        $arrayData['per3'][$i] = $per3;
                        $arrayData['per4'][$i] = $per4;
                        $arrayData['per5'][$i] = $per5;
                        $arrayData['per6'][$i] = $per6;
                        $arrayData['per7'][$i] = $per7;
                        $arrayData['per8'][$i] = $per8;
                        $arrayData['per9'][$i] = $per9;
                        $arrayData['per10'][$i] = $per10;
                        $arrayData['per11'][$i] = $per11; 

                        $sum = $sum + $arrayData['per11'][$i];               
                    }
                    $count = count(array_filter($arrayData['per10']));
                    $MSE = $sum / $count;

                    if(($k==1)&&($j==1)){
                       $arrayHasil[0] = $nilai_awal1;
                       $arrayHasil[1] = $nilai_awal2;
                       $arrayHasil[2] = $periode;
                       $arrayHasil[3] = $alpha;
                       $arrayHasil[4] = $beta;
                       $arrayHasil[5] = $MSE;
                    }
                    else{
                        if($MSE < $arrayHasil[5]){
                            $arrayHasil[0] = $nilai_awal1;
                            $arrayHasil[1] = $nilai_awal2;
                            $arrayHasil[2] = $periode;
                            $arrayHasil[3] = $alpha;
                            $arrayHasil[4] = $beta;
                            $arrayHasil[5] = $MSE;
                        }
                    }
                }  
            }

        // dd("nilai 1 = ".$arrayHasil[0]." nilai 2 = ".$arrayHasil[1]." periode = ".$arrayHasil[2]." alpha = ".$arrayHasil[3]." beta = ".$arrayHasil[4]." MSE = ".$arrayHasil[5]);
        return[$arrayHasil[0], $arrayHasil[1], $arrayHasil[2], $arrayHasil[3], $arrayHasil[4], $arrayHasil[5]]; 
    }

    public function winterTraining($data_training, $periode, $loop){
        $data = $data_training;
        $total = count($data_training);       
        $sumP = ($data[1] - $data[0]);
        $sumA = $data[0];
        $arrayHasil = array();
        $arrayHasil['nilai_awal1'] = array();
        $arrayHasil['nilai_awal2'] = array();
        $arrayHasil['nilai_awal3'] = array();
        $arrayHasil['periode'] = array();
        $arrayHasil['alpha'] = array();
        $arrayHasil['beta'] = array();
        $arrayHasil['mu'] = array();
        $arrayHasil['MSE'] = array();

        for($p=1;$p<=$periode;$p++){
            if($p>1){
                $sumP = $sumP + ($data[($p+$p)-1] - $data[$p-1]);
                $sumA = $sumA + $data[$p-1]; 
            }

            for($l=1;$l<=$loop;$l++){
                if ($p==1) {
                    $nilai_awal1 = $data[0];
                    $nilai_awal3 = $data[0]/$nilai_awal1;
                    if($l==1){
                    $nilai_awal2 = $data[1] - $data[0];
                    }
                    elseif($l==2){
                        $nilai_awal2 = 0;
                    }
                }
                else{
                    if($l==1){
                        $nilai_awal1 = $data[0];
                        $nilai_awal2 = $data[1] - $data[0];
                    }
                    elseif($l==2){
                        $nilai_awal1 = $data[0];
                        $nilai_awal2 = (1/$p) * ($sumP/$p);;
                    }
                    elseif ($l==3) {
                        $nilai_awal1 = $data[0];
                        $nilai_awal2 = ($data[$p-1] - $data[0])/($p-1);
                    }
                    elseif ($l==4) {
                        $nilai_awal1 = (1/$p) * $sumA;
                        $nilai_awal2 = $data[1] - $data[0];
                    }
                    elseif($l==5){
                        $nilai_awal1 = (1/$p) * (1/$p) * $sumA;
                        $nilai_awal2 = (1/$p) * ($sumP/$p);;
                    }
                    elseif ($l==6) {
                        $nilai_awal1 = (1/$p) * $sumA;
                        $nilai_awal2 = ($data[$p-1] - $data[0])/($p-1);
                    }
                    $nilai_awal3 = $data[0]/$nilai_awal1;
                }
            }
        }

        $alpha = 0;
        for($a=1;$a<=999;$a++){
            $alpha = $alpha + 0.001;
            $beta = 0;

            for($b=1;$b<=999;$b++){
                $beta = $beta + 0.001;
                $mu = 0;

                for($m=1;$m<=999;$m++){
                    $mu = $mu + 0.001;
                    $sum = 0;
                    $arrayData = array();
                    $arrayData['per1'] = array();
                    $arrayData['per2'] = array();
                    $arrayData['per3'] = array();
                    $arrayData['per4'] = array();
                    $arrayData['per5'] = array();
                    $arrayData['per6'] = array();
                    $arrayData['per7'] = array();
                    $arrayData['per8'] = array();
                    $arrayData['per9'] = array();
                    $arrayData['per10'] = array();
                    $arrayData['per11'] = array();
                    $arrayData['per12'] = array();
                    $arrayData['per13'] = array();
                    $arrayData['per14'] = array();
                    $arrayData['per15'] = array();
                    $arrayData['per16'] = array();

                    for($i=0;$i<=$total-1;$i++){

                        if($i==0){
                            $per1 = NULL;
                            $per2 = NULL;
                            $per3 = NULL;
                            $per4 = NULL;
                            $per5 = $nilai_awal1;
                            $per6 = NULL;
                            $per7 = NULL;
                            $per8 = NULL;
                            $per9 = $nilai_awal2;
                            $per10 = NULL;
                            $per11 = NULL;
                            $per12 = NULL;
                            $per13 = $nilai_awal3;
                            $per14 = NULL;
                            $per15 = NULL;
                            $per16 = NULL;
                        }

                        elseif(($i>0)&&($i<=$total-1)) {
                            if(($i>=1)&&($i<=7)){
                                $per1 = $data[$i];
                                $per12 = (1 - $mu) * $nilai_awal3;
                                if($i<$periode){
                                    $per14 = NULL;
                                }
                                else{
                                    $per14 = ($arrayData['per5'][$i-$periode] - $arrayData['per9'][$i-$periode] * $periode) * $arrayData['per13'][0];
                                }
                            }
                            elseif($i>7){
                                $per1 = $data[$i] / $arrayData['per13'][$i-7];
                                $per12 = (1 - $mu) * $arrayData['per13'][$i-7];
                                $per14 = ($arrayData['per5'][$i-$periode] - $arrayData['per9'][$i-$periode] * $periode) * $arrayData['per13'][$i-7];
                            }

                            $per2 = $alpha * $per1;
                            $per3 = $arrayData['per5'][$i-1] + $arrayData['per9'][$i-1];
                            $per4 = (1 - $alpha) * $per3;
                            $per5 = $per2 + $per4;
                            $per6 = $per5 - $arrayData['per5'][$i-1];
                            $per7 = $beta * $per6;
                            $per8 = (1 - $beta) * $arrayData['per9'][$i-1];
                            $per9 = $per7 + $per8;
                            $per10 = $data[$i] / $per5;
                            $per11 = $mu * $per10;
                            $per13 = $per11 + $per12;
                            $per15 = $data[$i] - $per14;
                            $per16 = pow($per15,2);
                        }

                        $arrayData['per1'][$i] = $per1;
                        $arrayData['per2'][$i] = $per2;
                        $arrayData['per3'][$i] = $per3;
                        $arrayData['per4'][$i] = $per4;
                        $arrayData['per5'][$i] = $per5;
                        $arrayData['per6'][$i] = $per6;
                        $arrayData['per7'][$i] = $per7;
                        $arrayData['per8'][$i] = $per8;
                        $arrayData['per9'][$i] = $per9;
                        $arrayData['per10'][$i] = $per10;
                        $arrayData['per11'][$i] = $per11;
                        $arrayData['per12'][$i] = $per12;
                        $arrayData['per13'][$i] = $per13;
                        $arrayData['per14'][$i] = $per14;
                        $arrayData['per15'][$i] = $per15;
                        $arrayData['per16'][$i] = $per16;

                        $sum = $sum + $arrayData['per16'][$i];
                    }
                    $count = count(array_filter($arrayData['per15']));
                    $MSE = $sum / $count;

                    if(($a==1)&&($b==1)&&($m==1)){
                        $arrayHasil[0] = $nilai_awal1;
                        $arrayHasil[1] = $nilai_awal2;
                        $arrayHasil[2] = $nilai_awal3;
                        $arrayHasil[3] = $periode;
                        $arrayHasil[4] = $alpha;
                        $arrayHasil[5] = $beta;
                        $arrayHasil[6] = $mu;
                        $arrayHasil[7] = $MSE;
                    }
                    else{
                        if($MSE < $arrayHasil[7]){
                            $arrayHasil[0] = $nilai_awal1;
                            $arrayHasil[1] = $nilai_awal2;
                            $arrayHasil[2] = $nilai_awal3;
                            $arrayHasil[3] = $periode;
                            $arrayHasil[4] = $alpha;
                            $arrayHasil[5] = $beta;
                            $arrayHasil[6] = $mu;
                            $arrayHasil[7] = $MSE;
                        }
                    }
                    
                }
            }
        }
        // dd("nilai 1 = ".$arrayHasil[0]." nilai 2 = ".$arrayHasil[1]." nilai 3 = ".$arrayHasil[2]." periode = ".$arrayHasil[3]." alpha = ".$arrayHasil[4]." beta = ".$arrayHasil[5]." mu = ".$arrayHasil[6]." MSE = ".$arrayHasil[7]);
        return[$arrayHasil[0], $arrayHasil[1], $arrayHasil[2], $arrayHasil[3], $arrayHasil[4], $arrayHasil[5],$arrayHasil[6],$arrayHasil[7]];
    }

    public function singleTesting($data_testing, $index, $nilai_awal, $alpha){
        $data = $data_testing;
        $total = count($data_testing);
        $alpha = $alpha;
        $nilai_awal = $nilai_awal;
        $arrayData = array();
        $arrayData['per1'] = array();
        $arrayData['per2'] = array();
        $arrayData['per3'] = array();
        $arrayData['per4'] = array();
        $arrayData['per5'] = array();
        $sum = 0;
        $batas = $index+$total-1;

        for($i=$index;$i<=$batas;$i++){
            if($i==$index){
                $per2 = (1 - $alpha) * $nilai_awal;
                $per3 = NULL;
                $per4 = NULL;
                $per5 = NULL;
            }

            elseif (($i>$index)&&($i<=$batas)) {
                $per3 = $arrayData['per1'][$i-1] + $arrayData['per2'][$i-1];
                $per2 = (1 - $alpha) * $per3;
                $per4 = $data[$i] - $per3;
                $per5 = pow($per4,2);
            }
            $arrayData['per1'][$i] = $alpha * $data[$i];
            $arrayData['per2'][$i] = $per2;
            $arrayData['per3'][$i] = $per3;
            $arrayData['per4'][$i] = $per4;
            $arrayData['per5'][$i] = $per5;

            $sum = $sum + $arrayData['per5'][$i];
        }

        $MSE = $sum / ($total-1);
        dd("Nilai Awal = ".$nilai_awal." Alpha = ".$alpha." MSE = ".$MSE);
    }

    public function doubleTesting($data_testing, $index, $periode, $nilai_awal, $alpha){
        $data = $data_testing;
        $total = count($data_testing);       
        $alpha = $alpha;
        $nilai_awal1 = $nilai_awal;
        $nilai_awal2 = $nilai_awal;
        $arrayData = array();
        $arrayData['per1'] = array();
        $arrayData['per2'] = array();
        $arrayData['per3'] = array();
        $arrayData['per4'] = array();
        $arrayData['per5'] = array();
        $arrayData['per6'] = array();
        $arrayData['per7'] = array();
        $arrayData['per8'] = array();
        $arrayData['per9'] = array();
        $arrayData['per10'] = array();
        $arrayData['per11'] = array();
        $arrayData['per12'] = array();
        $sum = 0;
        $batas = $index+$total-1;
        $p = $index+$periode;

        for($i=$index; $i<=$batas;$i++){
            if($i==$index){
                $per2 = (1 - $alpha) * $nilai_awal1;
                $per5 = (1 - $alpha) * $nilai_awal2;
            }

            elseif(($i>$index)&&($i<=$batas)){
                $per2 = (1 - $alpha) * $arrayData['per3'][$i-1];
                $per5 = (1 - $alpha) * $arrayData['per6'][$i-1];
            }

            $per3 = ($alpha * $data[$i]) + $per2;
            $per6 = ($alpha * $per3) + $per5;
            $per8 = (2 * $per3) - $per6;
            $per9 = ($alpha / (1 - $alpha)) * ($per3 - $per6);

            if($i<$p){
                $per10 = NULL;
                $per11 = NULL;
                $per12 = NULL;
            }
            elseif(($i>=$p)&&($i<=$batas)){
                $per10 = $arrayData['per8'][$i-$periode] + $arrayData['per9'][$i-$periode] * $periode;
                $per11 = $data[$i] - $per10;
                $per12 = pow($per11,2);
            }

            $arrayData['per1'][$i] = $alpha * $data[$i];
            $arrayData['per2'][$i] = $per2;
            $arrayData['per3'][$i] = $per3;
            $arrayData['per4'][$i] = $alpha * $per3;
            $arrayData['per5'][$i] = $per5;
            $arrayData['per6'][$i] = $per6;
            $arrayData['per7'][$i] = $per3 - $per6;
            $arrayData['per8'][$i] = $per8;
            $arrayData['per9'][$i] = $per9;
            $arrayData['per10'][$i] = $per10;
            $arrayData['per11'][$i] = $per11;
            $arrayData['per12'][$i] = $per12;

            $sum = $sum + $arrayData['per12'][$i];
        }
        $count = count(array_filter($arrayData['per11']));
        $MSE = $sum / $count;
        dd("Nilai Awal = ".$nilai_awal." periode = ".$periode." Alpha = ".$alpha." MSE = ".$MSE);
    }

    public function holtTesting($data_testing, $index, $periode, $nilai_awal1, $nilai_awal2, $alpha, $beta){
        $data = $data_testing;
        $total = count($data_testing);       
        $arrayData = array();
        $arrayData['per1'] = array();
        $arrayData['per2'] = array();
        $arrayData['per3'] = array();
        $arrayData['per4'] = array();
        $arrayData['per5'] = array();
        $arrayData['per6'] = array();
        $arrayData['per7'] = array();
        $arrayData['per8'] = array();
        $arrayData['per9'] = array();
        $arrayData['per10'] = array();
        $arrayData['per11'] = array();
        $sum = 0;
        $batas = $index+$total-1;
        $p = $index+$periode;

        for($i=$index;$i<=$batas;$i++){
           
            if($i==$index){
                $per1 = NULL;
                $per2 = NULL;
                $per3 = NULL;
                $per4 = $nilai_awal1;
                $per5 = NULL;
                $per6 = NULL;
                $per7 = NULL;
                $per8 = $nilai_awal2;
                $per9 = NULL;
                $per10 = NULL;
                $per11 = NULL;
            }

            elseif (($i>$index)&&($i<=$batas)) {
                $per1 = $alpha * $data[$i];
                $per2 = $arrayData['per4'][$i-1] + $arrayData['per8'][$i-1];
                $per3 = (1 - $alpha) * $per2;
                $per4 = $per1 + $per3;
                $per5 = $per4 - $arrayData['per4'][$i-1];
                $per6 = $beta * $per5;
                $per7 = (1 - $beta) * $arrayData['per8'][$i-1];
                $per8 = $per6 + $per7;

                if($i<$p){
                    $per9 = NULL;
                    $per10 = NULL;
                    $per11 = NULL;
                }
                else{
                    $per9 = $arrayData['per4'][$i-$p] + $arrayData['per8'][$i-$p] * $p;
                    $per10 = $data[$i] - $per9;
                    $per11 = pow($per10,2);    
                }
            }

            $arrayData['per1'][$i] = $per1;
            $arrayData['per2'][$i] = $per2;
            $arrayData['per3'][$i] = $per3;
            $arrayData['per4'][$i] = $per4;
            $arrayData['per5'][$i] = $per5;
            $arrayData['per6'][$i] = $per6;
            $arrayData['per7'][$i] = $per7;
            $arrayData['per8'][$i] = $per8;
            $arrayData['per9'][$i] = $per9;
            $arrayData['per10'][$i] = $per10;
            $arrayData['per11'][$i] = $per11;

            $sum = $sum + $arrayData['per11'][$i];
        }
        $count = count(array_filter($arrayData['per10']));
        $MSE = $sum / $count;
        dd($MSE);
        return view('home');
    }

    public function winterTesting($data_testing, $index, $periode, $nilai_awal1, $nilai_awal2, $nilai_awal3, $alpha, $beta, $mu){
        $data = $data_testing;
        $total = count($data_testing);       
        $arrayData = array();
        $arrayData['per1'] = array();
        $arrayData['per2'] = array();
        $arrayData['per3'] = array();
        $arrayData['per4'] = array();
        $arrayData['per5'] = array();
        $arrayData['per6'] = array();
        $arrayData['per7'] = array();
        $arrayData['per8'] = array();
        $arrayData['per9'] = array();
        $arrayData['per10'] = array();
        $arrayData['per11'] = array();
        $arrayData['per12'] = array();
        $arrayData['per13'] = array();
        $arrayData['per14'] = array();
        $arrayData['per15'] = array();
        $arrayData['per16'] = array();
        $sum = 0;
        $batas = $index+$total-1;
        $p = $index+$periode;

        for($i=$index;$i<=$batas;$i++){
            
            if($i==$index){
                $per5 = $nilai_awal1;
                $per9 = $nilai_awal2;
                $per13 = $nilai_awal3;
                $per1 = NULL;
                $per2 = NULL;
                $per3 = NULL;
                $per4 = NULL;
                $per6 = NULL;
                $per7 = NULL;
                $per8 = NULL;
                $per10 = NULL;
                $per11 = NULL;
                $per12 = NULL;
                $per14 = NULL;
                $per15 = NULL;
                $per16 = NULL;
            }

            elseif(($i>$index)&&($i<=$batas)) {
                if(($i>=$index+1)&&($i<$index+7)){
                    $per1 = $data[$i];
                    $per12 = (1 - $mu) * $nilai_awal3;
                    if($i<$p){
                        $per14 = NULL;
                    }
                    else{
                        $per14 = ($arrayData['per5'][$i-$periode] - $arrayData['per9'][$i-$periode] * $periode) * $arrayData['per13'][$index];
                    }
                }
                elseif($i>$index+7){
                    $per1 = $data[$i] / $arrayData['per13'][$i-7];
                    $per12 = (1 - $mu) * $arrayData['per13'][$i-7];
                    $per14 = ($arrayData['per5'][$i-$periode] - $arrayData['per9'][$i-$periode] * $periode) * $arrayData['per13'][$i-7];
                }

                $per2 = $alpha * $per1;
                $per3 = $arrayData['per5'][$i-1] + $arrayData['per9'][$i-1];
                $per4 = (1 - $alpha) * $per3;
                $per5 = $per2 + $per4;
                $per6 = $per5 - $arrayData['per5'][$i-1];
                $per7 = $beta * $per6;
                $per8 = (1 - $beta) * $arrayData['per9'][$i-1];
                $per9 = $per7 + $per8;
                $per10 = $data[$i] / $per5;
                $per11 = $mu * $per10;
                $per13 = $per11 + $per12;
                $per15 = $data[$i] - $per14;
                $per16 = pow($per15,2);
            }

            $arrayData['per1'][$i] = $per1;
            $arrayData['per2'][$i] = $per2;
            $arrayData['per3'][$i] = $per3;
            $arrayData['per4'][$i] = $per4;
            $arrayData['per5'][$i] = $per5;
            $arrayData['per6'][$i] = $per6;
            $arrayData['per7'][$i] = $per7;
            $arrayData['per8'][$i] = $per8;
            $arrayData['per9'][$i] = $per9;
            $arrayData['per10'][$i] = $per10;
            $arrayData['per11'][$i] = $per11;
            $arrayData['per12'][$i] = $per12;
            $arrayData['per13'][$i] = $per13;
            $arrayData['per14'][$i] = $per14;
            $arrayData['per15'][$i] = $per15;
            $arrayData['per16'][$i] = $per16;

            $sum = $sum + $arrayData['per16'][$i];
        }
        $count = count(array_filter($arrayData['per15']));
        $MSE = $sum / $count;
        dd($MSE);
        return view('home');
    }
}
