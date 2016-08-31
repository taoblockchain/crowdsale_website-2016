<?php
require_once('crowdsale.inc.php');
require_once('api.php');

$array = null;

if (isset($request[0]))
{
    $tao_address = $request[0];
    if (strtolower(substr($tao_address,0,1)) != 't' || strlen($tao_address) != 34) {
        $array = array('error' => 'Not a valid Tao address!');
    } else {
        if (isset($request[1]))
        {
            $incoming_ref = $request[1];
        } else {
            $incoming_ref = "";
        }

        $btc_address = null;
        // Is this address already in the crowdsale?
        $sql = "SELECT BTCAddress FROM `Sales` WHERE CoinAddress = ?";
        $stmt = $dbc->prepare($sql);
        $stmt->bind_param("s", $tao_address);
        $stmt->bind_result($btc_address);
        $stmt->execute();
        $stmt->fetch();
        $stmt->close();

        if ($btc_address == null)
        {
            $create_address = "https://block.io/api/v2/get_new_address/?api_key=".$blockio_key."&label=".$tao_address;

            $label = $tao_address;
            $uri = $create_address;
            $json = @file_get_contents($uri);
            if(empty($json))
            {   // Error or exists, look it up in the database
                $array = array('error' => 'Tao address already registered!');
            } else {
                $data = json_decode($json, true);
                $btc_address = $data["data"]["address"];

                //Insert into database
                $sql = "INSERT INTO `Sales`(`CoinAddress`, `BTCAddress`, `ReferralId`) VALUES (?, ?, ?)";
                $stmt = $dbc->prepare($sql);
                $stmt->bind_param("sss", $tao_address, $btc_address, $incoming_ref);
                $stmt->execute();
                $stmt->close();
            }
        }
        $ref_id = substr(sha1($tao_address),0,7);
        $ref_link = 'http://tao.network/crowdsale.html?ref_id='.$ref_id;
        $qrcode  = "<img src='https://blockchain.info/qr?data=".$btc_address."&size=256' width='256' height='256'/>";
        if (is_null($array)) $array = array('taoAddress' => $tao_address, 'btcAddress' => $btc_address, 'referralLink' => stripcslashes($ref_link), 'qrCode' => stripcslashes($qrcode));
    }
} else {
    $array = array('error' => 'Tao address missing!');
}
$summary = json_encode($array);
echo $summary;
?>