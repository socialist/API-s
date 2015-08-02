<?php
require_once dirname(__FILE__) . '/tools/Autoloader.php';

use tools\Autoloader as Autoloader;
use advertise\Advertise;


$adv = new Advertise('889613f71009111b');
$adv->setParams(['order_id'=>'CD1580-2', 'client_id'=>'8900515131', 'amount'=>'2054.15']);

try {
	echo $adv->noticeImage();
	echo $adv->noticeScript();
	$adv->deleteParams('tracking');
	print_r($adv->noticeCurl());

	print_r($adv->setStatusRequest(Advertise::STATUS_TAKEN, 'akjsdbvmzhbdjfhvbxmhbfv'));
} catch(Exception $e) {
	echo $e->getMessage();
}
?>
