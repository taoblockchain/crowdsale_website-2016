<?php
require_once('crowdsale.inc.php');
require_once('api.php');

$sql = "
CREATE TEMPORARY TABLE IF NOT EXISTS temp1 (
        SELECT C.qual/100 AS ref_bonus, D.ref_id, D.CoinAddress FROM
            (SELECT COUNT(*) AS qual, ReferralId FROM (SELECT DISTINCT ReferralId, CoinAddress, BTCAddress FROM `transactions` LEFT join Sales ON Sales.btcaddress = transactions.address WHERE transactions.amount >= .5 AND Sales.ReferralId <> '') AS A
            INNER JOIN
            transactions AS B
            ON A.BTCAddress = B.address 
            GROUP BY A.ReferralId) C
            INNER JOIN
                (SELECT DISTINCT LEFT(SHA1(CoinAddress),7) as ref_id, CoinAddress FROM Sales) D
                ON C.ReferralId = D.ref_id
);
CREATE TEMPORARY TABLE IF NOT EXISTS temp2 (
    SELECT SUM(E.amount) as purchased, E.modifier AS modifier , E.address AS BitcoinAddress, F.CoinAddress AS TaoAddress 
    FROM 
        transactions AS E
        , Sales AS F 
    WHERE 
        E.address = F.BTCAddress 
        and E.amount > 0
    GROUP BY E.address
);
UPDATE temp2,temp1 SET temp2.modifier = temp2.modifier + temp1.ref_bonus WHERE temp2.TaoAddress = temp1.CoinAddress;
select TRUNCATE(temp2.purchased * temp2.modifier,8) AS AmtPurchased, BitcoinAddress, TaoAddress from temp2;
select SUM(TRUNCATE(temp2.purchased * temp2.modifier,8)) AS TotalPurchased from temp2;
";
if ($dbc->multi_query($sql)) {
    $result = $dbc->store_result();
    $dbc->next_result();
    $result = $dbc->store_result();
    $dbc->next_result();
    $result = $dbc->store_result();
    $dbc->next_result();
    $result = $dbc->store_result();
    $dbc->next_result();
    $result2 = $dbc->store_result();
}
/*
//Grab useful data.
$sql = "SELECT SUM(amount * modifier) AS TotalSold FROM transactions";
if ($stmt = $dbc->prepare($sql)) {
    $stmt->execute();
    $stmt->bind_result($total);
    while ($stmt->fetch()) {
        $TotalSold = $total;
    }
    $stmt->close();
} else {
    printf("Errormessage: %s\n", $dbc->error);
}
*/
$a = mysqli_fetch_array($result2);
$TotalSold = $a['TotalPurchased'];
$CoinAvailable = $coin_total;
$PercentageRemaining = 100;
//$uri = "https://blockchain.info/ticker";
//$uri = "http://api.coindesk.com/v1/bpi/currentprice/USD.json";
$uri = "https://block.io/api/v2/get_current_price/?api_key=".$blockio_key;
$json = file_get_contents($uri);
if(empty($json)){
    die('fatal error');
}

$data = json_decode($json, true);
$currency = 'USD';
//$value = $data[$currency]["15m"]; // blockchain.info
//$value = $data["bpi"][$currency]['rate']; // CoinDesk
$value = $data["data"]["prices"];
foreach($value as $price)
{
    if (($price["price_base"]=="USD") && ($price["exchange"]=="coinbase"))
        $mkt_price = $price["price"];
}
$btc_price = number_format($mkt_price,2,'.','');
$USDValue = "$".number_format(($TotalSold) * $btc_price,2,'.',',');
$BTCValue = $TotalSold / $coin_total;
$array = array('totalCoins' => number_format($coin_total,0,'.',','), 'coinsRemaining' => number_format($CoinAvailable,8,'.',','), 'percentRemaining' => $PercentageRemaining, 'currentPrice' => number_format($BTCValue,8,'.',','), 'totalPurchased' => number_format($TotalSold,8,'.',','), 'currentUSDValue' => $USDValue);
$summary = json_encode($array);
echo $summary;
?>