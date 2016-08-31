<?php
require_once("api.php");
require_once('crowdsale.inc.php');

function processAddressList($address_list,$blockio_key,$dbc)
{
    $address_list = rtrim($address_list, ",");
    $uri = "https://block.io/api/v2/get_transactions/?api_key=".$blockio_key."&type=received&addresses=".$address_list;
    //$uri = "https://block.io/api/v2/get_address_balance/?api_key=".$blockio_key."&addresses=".$address_list;
    //echo $uri . "<br/>";
    $json = @file_get_contents($uri);
    if(empty($json))
    {   // Error or exists, look it up in the database
        $array = array('error' => 'API offline.');
    } else {
        $data = json_decode($json, true);
        //var_dump($data);
        updateTx($data,$dbc);
    }
}

function updateTx($data, $dbc)
{
    $x = 0;
    //var_dump($data["data"]["txs"]);
    //echo "<hr/>";
    while ($x < count($data["data"]["txs"]))
    {
        $time = $data["data"]["txs"][$x]["time"];
        $y = 0;
        while ($y < count($data["data"]["txs"][$x]["amounts_received"]))
        {
            $txid = $data["data"]["txs"][$x]["txid"];
            $receiver = $data["data"]["txs"][$x]["amounts_received"][$y]["recipient"];
            $amount = $data["data"]["txs"][$x]["amounts_received"][$y]["amount"];
            $sql = "SELECT COUNT(*) AS Counter FROM transactions WHERE txid = ?";
            if (!$stmt = $dbc->prepare($sql))
                var_dump($dbc->error);
            $stmt->bind_param("s", $txid);
            $stmt->bind_result($counter);
            $stmt->execute();
            $stmt->fetch();
            $stmt->close();
            if ($counter <= 0)   // Transaction doesn't exist
            {
                $sql = "INSERT INTO `transactions` (`txid`, `address`, `amount`, `timestamp`, `modifier`) VALUES (?, ?, ?, ?, ?)";
                //Insert into database
                if (!$stmt = $dbc->prepare($sql))
                    var_dump($dbc->error);
                $modifier = 1;
                if ($time < 1470466799)
                {
                    $modifier = 1.25;
                }
                $stmt->bind_param("ssddd", $txid, $receiver, $amount, $time, $modifier);
                $stmt->execute();
                $stmt->close();
            }
            $y++;
        }
        $x++;
    }
}

// Pull the list of BTC addresses
// Group addresses into batches of 10 and process 5 requests every second
//  For each address: 
//      Check to see if there is already an entry
$sql = "SELECT BTCAddress FROM `Sales`";
$result = mysqli_query($dbc, $sql);
$address_list = "";
$i = 0;

while (($data = mysqli_fetch_array($result)) != false) { 
    $i++;
    $address_list .= $data["BTCAddress"].",";
    if ($i % 5 == 0)
    {
        processAddressList($address_list,$blockio_key, $dbc);
        $address_list = "";
    }
}

if (strlen($address_list) > 0)
{
    processAddressList($address_list,$blockio_key,$dbc);
};

?>