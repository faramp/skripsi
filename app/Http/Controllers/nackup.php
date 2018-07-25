public function holtTraining($data_training, $periode, $loop){
        $data = $data_training;
        $total = count($data_training);       
        $sumP = ($data[1] - $data[0]);
        $sumA = $data[0];
        $arrayHasil = array();
        $arrayHasil['nilai_awal1'] = array();
        $arrayHasil['nilai_awal2'] = array();
        $arrayHasil['periode'] = array();
        $arrayHasil['alpha'] = array();
        $arrayHasil['beta'] = array();
        $arrayHasil['MSE'] = array();

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
                $arrayBeta = array();
                $arrayBeta['nilai_awal1'] = array();
                $arrayBeta['nilai_awal2'] = array();
                $arrayBeta['periode'] = array();
                $arrayBeta['alpha'] = array();
                $arrayBeta['beta'] = array();
                $arrayBeta['MSE'] = array();

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

                    $arrayBeta['nilai_awal1'][$j] = $nilai_awal1;
                    $arrayBeta['nilai_awal2'][$j] = $nilai_awal2;
                    $arrayBeta['periode'][$j] = $periode;
                    $arrayBeta['alpha'][$j] = $alpha;
                    $arrayBeta['beta'][$j] = $beta;
                    $arrayBeta['MSE'][$j] = $MSE;
                } 
                $min = min($arrayBeta['MSE']);

                for($i=1;$i<=999;$i++){
                    if($arrayBeta['MSE'][$i]==$min){
                        $arrayHasil['nilai_awal1'][$k] = $arrayBeta['nilai_awal1'][$i];
                        $arrayHasil['nilai_awal2'][$k] = $arrayBeta['nilai_awal2'][$i];
                        $arrayHasil['periode'][$k] = $arrayBeta['periode'][$i];
                        $arrayHasil['alpha'][$k] = $arrayBeta['alpha'][$i];
                        $arrayHasil['beta'][$k] = $arrayBeta['beta'][$i];
                        $arrayHasil['MSE'][$k] = $arrayBeta['MSE'][$i];
                    }
                }  
            }
        $min_akhir = min($arrayHasil['MSE']);
        for($i=1;$i<=999;$i++){
            if($arrayHasil['MSE'][$i]==$min_akhir){
                $nilai_awal1 = $arrayHasil['nilai_awal1'][$i];
                $nilai_awal2 = $arrayHasil['nilai_awal2'][$i];
                $periode = $arrayHasil['periode'][$i];
                $alpha = $arrayHasil['alpha'][$i];
                $beta = $arrayHasil['beta'][$i];
                $MSE = $arrayHasil['MSE'][$i];
            }
        }
        // dd("nilai 1 = ".$nilai_awal1." nilai 2 = ".$nilai_awal2." periode = ".$periode." alpha = ".$alpha." beta = ".$beta." MSE = ".$MSE);
        return[$nilai_awal1, $nilai_awal2, $periode, $alpha, $beta, $MSE]; 
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
            $arrayAlpha = array();
            $arrayAlpha['nilai_awal1'] = array();
            $arrayAlpha['nilai_awal2'] = array();
            $arrayAlpha['nilai_awal3'] = array();
            $arrayAlpha['periode'] = array();
            $arrayAlpha['alpha'] = array();
            $arrayAlpha['beta'] = array();
            $arrayAlpha['mu'] = array();
            $arrayAlpha['MSE'] = array();

            for($b=1;$b<=999;$b++){
                $beta = $beta + 0.001;
                $mu = 0;
                $arrayBeta = array();
                $arrayBeta['nilai_awal1'] = array();
                $arrayBeta['nilai_awal2'] = array();
                $arrayBeta['nilai_awal3'] = array();
                $arrayBeta['periode'] = array();
                $arrayBeta['alpha'] = array();
                $arrayBeta['beta'] = array();
                $arrayBeta['mu'] = array();
                $arrayBeta['MSE'] = array();

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

                    $arrayBeta['nilai_awal1'][$m] = $nilai_awal1;
                    $arrayBeta['nilai_awal2'][$m] = $nilai_awal2;
                    $arrayBeta['nilai_awal3'][$m] = $nilai_awal3;
                    $arrayBeta['periode'][$m] = $periode;
                    $arrayBeta['alpha'][$m] = $alpha;
                    $arrayBeta['beta'][$m] = $beta;
                    $arrayBeta['mu'][$m] = $mu;
                    $arrayBeta['MSE'][$m] = $MSE;
                }
                $min_mu = min($arrayBeta['MSE']);

                for($i=1;$i<=999;$i++){
                    if($arrayBeta['MSE'][$i]==$min_mu){
                        $arrayAlpha['nilai_awal1'][$b] = $arrayBeta['nilai_awal1'][$i];
                        $arrayAlpha['nilai_awal2'][$b] = $arrayBeta['nilai_awal2'][$i];
                        $arrayAlpha['nilai_awal3'][$b] = $arrayBeta['nilai_awal3'][$i];
                        $arrayAlpha['periode'][$b] = $arrayBeta['periode'][$i];
                        $arrayAlpha['alpha'][$b] = $arrayBeta['alpha'][$i];
                        $arrayAlpha['beta'][$b] = $arrayBeta['beta'][$i];
                        $arrayAlpha['mu'][$b] = $arrayBeta['mu'][$i];
                        $arrayAlpha['MSE'][$b] = $arrayBeta['MSE'][$i]; 
                    }
                }
            }
            $min_beta = min($arrayAlpha['MSE']);

            for($i=1;$i<=999;$i++){
                if($arrayAlpha['MSE'][$i]==$min_beta){
                    $arrayHasil['nilai_awal1'][$b] = $arrayAlpha['nilai_awal1'][$i];
                    $arrayHasil['nilai_awal2'][$b] = $arrayAlpha['nilai_awal2'][$i];
                    $arrayHasil['nilai_awal3'][$b] = $arrayAlpha['nilai_awal3'][$i];
                    $arrayHasil['periode'][$b] = $arrayAlpha['periode'][$i];
                    $arrayHasil['alpha'][$b] = $arrayAlpha['alpha'][$i];
                    $arrayHasil['beta'][$b] = $arrayAlpha['beta'][$i];
                    $arrayHasil['mu'][$b] = $arrayAlpha['mu'][$i];
                    $arrayHasil['MSE'][$b] = $arrayAlpha['MSE'][$i]; 
                }
            }
        }

        $min_akhir = min($arrayHasil['MSE']);
        for($i=1;$i<=999;$i++){
            if($arrayHasil['MSE'][$i]==$min_akhir){
                $nilai_awal1 = $arrayHasil['nilai_awal1'][$i];
                $nilai_awal2 = $arrayHasil['nilai_awal2'][$i];
                $nilai_awal3 = $arrayHasil['nilai_awal3'][$i];
                $periode = $arrayHasil['periode'][$i];
                $alpha = $arrayHasil['alpha'][$i];
                $beta = $arrayHasil['beta'][$i];
                $mu = $arrayHasil['mu'][$i];
                $MSE = $arrayHasil['MSE'][$i];
            }
        }
        dd("nilai 1 = ".$nilai_awal1." nilai 2 = ".$nilai_awal2." nilai 3 = ".$nilai_awal3." periode = ".$periode." alpha = ".$alpha." beta = ".$beta." mu = ".$mu." MSE = ".$MSE);
        return [$nilai_awal1, $nilai_awal2, $nilai_awal3, $periode, $alpha, $beta, $mu, $MSE];
    }
