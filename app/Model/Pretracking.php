<?php

class Pretracking extends AppModel {

	public $primaryKey = '_id';

	public function saveData($data = array())
	{
		if(!isset($data['id']) || !$data)
			return false;
		
		$insertData['refid'] = $data['refid'];
		$insertData['inboxid'] = $data['id'];
		$insertData['phoneno'] = $data['phone'];
		$insertData['trackingno'] = $data['trackingno'];
		
		$this->create();
		if($this->save($insertData))
			return $insertData;
			
		return false;
	}
	
	public function getPretracking($refid)
	{
		return reset($this->find('first', array('conditions' => array('refid' => $refid))));
	}
}