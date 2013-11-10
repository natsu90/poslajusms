<?php

class Tracking extends AppModel {

	public $primaryKey = '_id';

	// return status string to send
	public function addTracking($data = array())
	{
		if(!$data)
			return false;
	
		$is_existed = $this->find('first', array('conditions' => array('trackingno' => $data['trackingno'])));
		$is_existed = reset($is_existed);
		
		if($is_existed)
		{
			$this->read(null, $is_existed['_id']);
			$this->set('phonenos', in_array($data['phoneno'], $is_existed['phonenos']) ? $is_existed['phonenos'] : array_push($is_existed['phonenos'], $data['phoneno']));
			$this->save();
			return $is_existed['trackingstatus'];
		} else {
			$this->create();
			$insertData = array(
				'trackingno' => $data['trackingno'],
				'phonenos' => array($data['phoneno']),
				'trackingstatus' => 'Record Not Found. No information for the following shipments has been received by PosLaju system yet.',
				'status' => 'sending');
			$this->save($insertData);
		}
		return false;
	}
	
	// return phone numbers array to send
	public function updateStatus($trackingstatus, $trackingno)
	{
		$trackingstatus = str_replace(array("\r", "\n"), '', $trackingstatus);
		$tracking = $this->find('first', array('conditions' => array('trackingno' => $trackingno)));
		$tracking = reset($tracking);
		
		if($trackingstatus != $tracking['trackingstatus'])
		{
			$this->read(null, $tracking['_id']);
			$this->set('trackingstatus', $trackingstatus);
			if(stristr($trackingstatus, 'Item Successfully Delivered') !== false)
				$this->set('status', 'delivered');
			$this->save();
			return $tracking['phonenos'];
		}
		return false;
	}
	
	public function listAll()
	{
		return $this->find('all', array('conditions' => array('status' => 'sending')));
	}
}