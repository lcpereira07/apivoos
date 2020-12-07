<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VooController extends Controller
{
    
	CONST URL_OUTBOUND = "http://prova.123milhas.net/api/flights/?outbound=1";
	CONST URL_INBOUND = "http://prova.123milhas.net/api/flights/?inbound=1";
	CONST URL_VOOS = "http://prova.123milhas.net/api/flights";

    public function getVoos(){
    	try{

    		$grupos = $this->geraGrupos();
    		$voos = $this->agrupaVoos($grupos);

    		return $voos;

    	}catch(\Exception $erro){
    		return['response'=>'Erro', 'Erro'=>$erro];
    	}
    }

    /**
    * Método para buscar base de voos de acordo com o tipo (outbound/inbound)
    */
    public function getVoosBase($url){
    	try{

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

	        $result = curl_exec($ch);
	        curl_close($ch);

	        return json_decode($result);

    	}catch(\Exception $erro){
    		return['response'=>'Erro', 'Erro'=>$erro];
    	}
    }

    /**
    * Método que prepara a lista de voos do tipo outbound de acordo com a tarifa
    */
    public function agrupaOutbound(){
    	try{

    		$outbound = $this->getVoosBase(self::URL_OUTBOUND);
    		$vooAgrupado = array();
    		foreach ($outbound as $voo) {
    			$vooAgrupado [$voo->fare][$voo->price][] = array("id" => $voo->id);
    		}

    		return $vooAgrupado;

    	}catch(\Exception $erro){
    		return['response'=>'Erro', 'Erro'=>$erro];
    	}
    }

    /**
    * Método que prepara a lista de voos do tipo inbound de acordo com a tarifa
    */
    public function agrupaInbound(){
    	try{

    		$inbound = $this->getVoosBase(self::URL_INBOUND);
    		$vooAgrupado = array();
    		foreach ($inbound as $voo) {
    			$vooAgrupado [$voo->fare][$voo->price][] = array("id" => $voo->id);
    		}

    		return $vooAgrupado;

    	}catch(\Exception $erro){
    		return['response'=>'Erro', 'Erro'=>$erro];
    	}
    }

    /**
    * Cria os grupos de voos de acordo com tarifa/preço e retorna os uma lista ordenada pelo preço do grupo
    */
    public function geraGrupos(){
    	try{
    		$vooGrouped = array();

    		# Retorna todos os voos de ida e volta
    		$groupOutbound = $this->agrupaOutbound();
    		$groupInbound  = $this->agrupaInbound();
    		$uniqueId = 1;

    		# Loop nos voos de ida, de acordo com o tipo da tarifa
    		foreach ($groupOutbound as $fareOutbound => $outbound) {

    			# busco voos de volta do mesmo tipo do voo de ida
    			$inbound = $groupInbound[$fareOutbound];
    			# Lista com preços dos voos de ida
    			$priceOutbound = array_keys($outbound);
    			# Lista com preços dos voos de volta
    			$priceInbound = array_keys($inbound);

    			# Loop nos preços dos voos de ida e volta
    			foreach ($priceOutbound as $priceOut) {
    				foreach ($priceInbound as $priceIn) {
    					# Para cada ocorrencia de preço de voo, gera um grupo
						$vooGrouped[] = array(
											"uniqueId"	 => $uniqueId,
											"totalPrice" => $priceOut+$priceIn,
											"outbound" 	 => $outbound[$priceOut], 
											"inbound" 	 => $inbound[$priceIn]
										);
    					$uniqueId++;

    				}
    			}
    		}

    		$vooGrouped = $this->ordenaGrupo($vooGrouped);
    		
    		return $vooGrouped;

    	}catch(\Exception $erro){
    		return['response'=>'Erro', 'Erro'=>$erro];
    	}
    }

    /**
    * Metodo para ordenar a lista de grupo de acordo com o preço total de cada grupo
    */
    public function ordenaGrupo($vooGrouped){
    	usort($vooGrouped, function($a, $b) {
		    return $a['totalPrice'] <=> $b['totalPrice'];
		});

    	return $vooGrouped;
    }

    /**
    * Busca a base de voos e os grupos para montar um response e retornar ao client
    */
    public function agrupaVoos($grupos){
    	try{

    		$voos = $this->getVoosBase(self::URL_VOOS);

    		$result = array(
				"flights" => $voos,
				"groups" => $grupos,
				"totalGroups" => count($this->geraGrupos()),
				"totalFlights" => count($voos),
				"cheapestPrice" => $grupos[0]['totalPrice'],
				"cheapestGroup" => $grupos[0]['uniqueId']
			);

    		return response()->json($result);
    		
    	}catch(\Exception $erro){
    		return['response'=>'Erro', 'Erro'=>$erro];
    	}
    } 

}