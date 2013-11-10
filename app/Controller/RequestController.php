<?php

class RequestController extends AppController {
	
	// vendor sms
	public function index()
	{
		$txt_my_token = '8e39a9450991a6b9acc80f31abcd1234abcd1234';
		App::uses('HttpSocket', 'Network/Http');
		$HttpSocket = new HttpSocket();
		$gets = $this->request->query;
		// receive MO
		if(isset($gets['id']) && hash_hmac('sha1', $gets['id'], $txt_my_token) == $gets['signature'])
		{
			$msg = explode(' ', str_replace(array("\n","\r"), '', trim($gets['message'])), 3);
			$trackingno = strtoupper(str_replace(" ", '', $msg[2]));
			// send reply
			$reply_result = $HttpSocket->post('http://txt.my/api/reply/', array(
				'inboxid' => $gets['id'],
				'signature' => hash_hmac('sha1', $gets['signature'], $txt_my_token),
				'message' => 'Anda melanggan perkhidmatan PosLaju SMS Tracking untuk nombor '.$trackingno.'. Maklumat status pengeposan terkini akan diterima sebentar lagi.',
				'premium' => 1));
			$reply_detail = json_decode($reply_result, true);
			// save to db
			$this->loadModel('Pretracking');
			$this->Pretracking->saveData(array_merge(array('refid' => $reply_detail['refid'], 'trackingno' => $trackingno), $gets));
		}
	
		// receive DN OR MO from maxis
		if((isset($gets['refid']) && isset($gets['status']) && $gets['status'] == 'Delivered') || (isset($gets['telco']) && $gets['telco'] == 'maxis'))
		{			
			$refid = isset($gets['refid']) ? $gets['refid'] : $reply_detail['refid'];
			$this->loadModel('Pretracking');
			$pretracking = $this->Pretracking->getPretracking($refid);
			if($pretracking)
			{
				$this->loadModel('Tracking');
				$added = $this->Tracking->addTracking($pretracking);
				if($added)
					exec('sh /app/www/app/Console/cake trackingjob send_to '.escapeshellarg($pretracking['phoneno']).' '.escapeshellarg($pretracking['trackingno'].' '.$added).' > /app/apache/logs/access_log &');
			}
		}
		
		exec('sh /app/www/app/Console/cake trackingjob > /app/apache/logs/access_log &');
		
		$display = 'Permission denied';
		$this->set(compact('display'));
	}
}