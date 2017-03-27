<?php
require('clEPNCabinetAccess.php');
$api = new clEpnCabinetAccess('apikey','privatekey');
// add requests
$api->AddRequestGetTransactions('transactions',0,'2017-03-22','2017-03-23','open','waiting','sub',1,200);
// Execute queries
$api->RunRequests();
// Dump the results
print_r($api->GetRequestResult('transactions'));
print_r($api->LastError());

?>