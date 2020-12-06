<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VooController extends Controller
{
    
    public function getVoos(){
    	try{

    		return['response' => 'Voos agrupados'];

    	}catch(\Exception $erro){
    		return['response'=>'Erro', 'Erro'=>$erro];
    	}
    }

}4