<?php
require_once('crowdsale.inc.php');
require_once('api.php');

if (file_exists("update.lock"))
    die("Already running.");  // Already running

$myfile = fopen("update.lock", "w") or die("Unable to open file!");
$txt = "Tao\n";
fwrite($myfile, $txt);
fclose($myfile);

//Sale data.
//$sql = "SELECT SUM(A.amount * A.modifier) AS AmtPurchased, A.address AS BitcoinAddress, B.CoinAddress AS TaoAddress FROM transactions AS A, Sales AS B WHERE A.address = B.BTCAddress and A.amount > 0  GROUP BY A.address";

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
//$sql = "SELECT SUM(A.Amount) AS AmtPurchased, A.BTCAddress AS BitcoinAddress, B.CoinAddress AS TaoAddress FROM Payments AS A, Sales AS B WHERE A.BTCAddress = B.BTCAddress AND A.Amount > 0 GROUP BY A.BTCAddress";
/* execute multi query */
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
$saletable = '<table class="table table-striped" align="left">
        <thead>
            <tr>
            <th colspan="3">Ledger update delays are common.  Please allow 60-90 minutes before your deposit is reflected</th>
            <tr>
                <th align="left" style="width:50px !important;">#</th>
                <th align="left" style="width:350px !important;">Tao Address</th>
                <th align="left" style="width:200px !important;">BTC Deposited</th>
                <th align="left" style="width:200px !important;">Tokens</th>
            </tr>
        </thead>
        <tbody>';
$i=1;
$data2 = mysqli_fetch_array($result2);
while (($data = mysqli_fetch_array($result)) != false) { 
    $saletable .= '<tr>';
    $saletable .= "<td style='width:50px !important;'>".$i."</td>";
    //$link = "<a href='https://blockchain.info/en/address/".$data['BitcoinAddress']."' target='_blank'>".$data['BitcoinAddress']."</a>";
    //$saletable .= "<td>".$link."</td>";
    $saletable .= "<td style='width:350px !important;'>".$data['TaoAddress']."</td>";
    $saletable .= "<td style='width:200px !important;'>".number_format($data['AmtPurchased'],8)."</td>";
    $total = number_format($data['AmtPurchased']/$data2['TotalPurchased'] * $coin_total,8);
    $saletable .= "<td style='width:200px !important;'>".$total." TAO</td>";
    $saletable .= '</tr>';
    
    $i ++;
}
$saletable .= '</tbody></table>';
//

//Top 10 sales
$sql = "SELECT SUM(A.amount * A.modifier) AS AmtPurchased, A.address AS BitcoinAddress, B.CoinAddress AS TaoAddress FROM transactions AS A, Sales AS B WHERE A.address = B.BTCAddress and A.amount > 0  GROUP BY A.address ORDER BY AmtPurchased DESC LIMIT 0,10";
$result = mysqli_query($dbc, $sql);
$top10sales = '<table class="table table-striped" align="center">
        <thead>
            <tr>
                <th>#</th>
                <th>Tao Address</th>
                <th>Tao Purchased</th>
            </tr>
        </thead>
        <tbody>';
$i=1;
while (($data = mysqli_fetch_array($result)) != false) { 
    $top10sales .= '<tr>';
    $top10sales .= "<td>".$i."</td>";
    //$link = "<a href='https://blockchain.info/en/address/".$data['BitcoinAddress']."' target='_blank'>".$data['BitcoinAddress']."</a>";
    //$top10sales .= "<td>".$link."</td>";
    $top10sales .= "<td>".$data['TaoAddress']."</td>";
    $top10sales .= "<td>".number_format($data['AmtPurchased'],8)."</td>";
    $top10sales .= '</tr>';
    
    $i ++;
}
$top10sales .= '</tbody></table>';

//Referral data.
$sql = "SELECT E.CoinAddress AS TaoAddress, D.RefCount AS ReferralCount FROM 
    (SELECT COUNT(*) AS RefCount, B.ReferralId FROM
        Sales AS B, 
        (SELECT A.BTCAddress FROM 
            (SELECT SUM(Amount) / ".$minimum_price." AS Amt, BTCAddress FROM Payments GROUP BY BTCAddress) AS A
        WHERE A.Amt >= .5) AS C  
    WHERE C.BTCAddress = B.BTCAddress GROUP BY B.ReferralId) AS D, 
    Sales AS E
WHERE SUBSTRING(SHA1(E.CoinAddress),1,8) = D.ReferralId
ORDER BY D.RefCount DESC LIMIT 0,10";

$result = mysqli_query($dbc, $sql);
$reftable = '<table class="table table-striped" align="center">
        <thead>
            <tr>
                <th>#</th>
                <th>Tao Address</th>
                <th>Referrals</th>
            </tr>
        </thead>
        <tbody>';
$i=1;
while (($data = mysqli_fetch_array($result)) != false) { 
    $reftable .= '<tr>';
    $reftable .= "<td>".$i."</td>";
    $reftable .= "<td>".$data['TaoAddress']."</td>";
    $reftable .= "<td>".$data['ReferralCount']."</td>";
    $reftable .= '</tr>';
    
    $i ++;
}
$reftable .= '</tbody></table>';

$array = array('SaleTable' => $saletable, 'Top10SalesTable' => $top10sales, 'Top10ReferralsTable' => $reftable);
$summary = json_encode($array);
echo $summary;
unlink("update.lock");
?>