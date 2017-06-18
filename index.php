<?php 
//Building A Simple Bittrex Bot

$apikey='2923c158d5754c29a088c4adea5c6f34';
$apisecret='3f29b39ab1be46afb0834fe56c8e4fea';

function bittrexbalance($apikey, $apisecret){
    $nonce=time();
    $uri='https://bittrex.com/api/v1.1/account/getbalance?apikey='.$apikey.'&currency=BTC&nonce='.$nonce;
    $sign=hash_hmac('sha512',$uri,$apisecret);
    $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $execResult = curl_exec($ch);
    $obj = json_decode($execResult, true);
    $balance = $obj["result"]["Available"];
    return $balance;
}
function bittrexbuy($apikey, $apisecret, $symbol, $quant, $rate){
    $nonce=time();
    $uri='https://bittrex.com/api/v1.1/market/buylimit?apikey='.$apikey.'&market=BTC-'.$symbol.'&quantity='.$quant.'&rate='.$rate.'&nonce='.$nonce;
    $sign=hash_hmac('sha512',$uri,$apisecret);
    $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign));
    $execResult = curl_exec($ch);
    $obj = json_decode($execResult, true);
    return $obj;
}


$cnmkt = "https://api.coinmarketcap.com/v1/ticker/?limit=50";
$fgc = json_decode(file_get_contents($cnmkt), true);
$counter = 0;
for($i=0;$i<50;$i++){
    if($counter < 4){
        $percCng = $fgc[$i]["percent_change_7d"];
        if($percCng < 4 && $percCng > -4){
            $symbol = $fgc[$i]["symbol"];
            $cost = $fgc[$i]["price_btc"];
            $balance = bittrexbalance($apikey, $apisecret);
            $fifthBal = $balance / 5;
            $amounttobuy = $fifthBal / $cost;
            $buy = bittrexbuy($apikey, $apisecret, $symbol, $amounttobuy, $cost);
            print_r($buy);
            $counter++;
        }   
    }
}


?>