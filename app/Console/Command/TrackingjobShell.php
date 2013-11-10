<?php

App::uses('ComponentCollection', 'Controller');
App::uses('TwilioComponent', 'Twilio.Controller/Component');

class TrackingjobShell extends AppShell {

    public function main() {
		
		$this->loadModel('Tracking');
		$trackings = $this->Tracking->listAll();
		if(!$trackings) $this->out('empty');
		$chunked_trackings = array_chunk($trackings, 10);
		if(!$chunked_trackings)
			$chunked_trackings = array($trackings);
		
		App::uses('HttpSocket', 'Network/Http');
		$HttpSocket = new HttpSocket();
		App::import('Vendor', 'simple_html_dom', array('file'=>'simple_html_dom.php'));
		
		foreach($chunked_trackings as $poslaju_trackings)
		{
			$tracking_results = $HttpSocket->get('http://www.poslaju.com.my/pos_tracking.aspx', 'connoteno='.implode(',',array_map(function($tracking){return $tracking['Tracking']['trackingno'];}, $poslaju_trackings)));
			$html = str_get_html($tracking_results);
			
			foreach($poslaju_trackings as $tracking)
			{	
				$trackingno = $tracking['Tracking']['trackingno'];
				if($html->find('table[id='.$trackingno.'] tbody',0))
				{
					$status = str_replace(array("\r", "\n"), '', $html->find('table[id='.$trackingno.'] tbody',0)->find('tr',1)->find('td',1)->plaintext);
					$time = $html->find('table[id='.$trackingno.'] tbody',0)->find('tr',1)->find('td',0)->plaintext;
					$place = $html->find('table[id='.$trackingno.'] tbody',0)->find('tr',1)->find('td',2)->plaintext;
					$status = $status.' '.$time.' '.$place;
					
					$updated = $this->Tracking->updateStatus($status, $trackingno);
					if($updated)
					{
						foreach($updated as $to)
						{
							exec('sh /app/www/app/Console/cake trackingjob send_to '.escapeshellarg($to).' '.escapeshellarg($trackingno.' '.$status).' > /app/apache/logs/access_log &');
						}
					}	
				}
			}
		}
    }
	
	// send sms with 10 attempts in fibo interval
	public function send_to()
	{
		$Collection = new ComponentCollection();
		$this->Twilio = new TwilioComponent($Collection);
		
		$status = false; $attempt = 10; $n = 0; $fibo = array();
		while(!$status && $n < $attempt)
		{
			$fibo[$n] = $n;
			if($n > 1)
				$fibo[$n] = $fibo[$n-1] + $fibo[$n-2];
			sleep($fibo[$n]);
			$response = $this->Twilio->sms('+'.$this->args[0], $this->args[1]);
			$status = !$response->IsError;
			if($status)
				break;
			$n++;
		} 
		if($response->IsError)
			$this->out($response->ErrorMessage);
	}
	
	// add new tracking 
	// command: cake add EF123456789MY 0123456789
	public function add()
	{
		$this->loadModel('Tracking');
		$this->out($this->Tracking->addTracking(array(
			'trackingno' => $this->args[0],
			'phoneno' => $this->args[1]
		)));
	}
	
	// check tracking from poslaju
	// command: cake check EF123456789MY
	public function check()
	{
		App::uses('HttpSocket', 'Network/Http');
		$HttpSocket = new HttpSocket();
		
		$trackingno = $this->args[0];
		$poslaju_result = $HttpSocket->get('http://www.poslaju.com.my/pos_tracking.aspx', 'connoteno='.$trackingno);
		
		App::import('Vendor', 'simple_html_dom', array('file'=>'simple_html_dom.php'));
		
		//$html = str_get_html($poslaju_result);
		$html = new simple_html_dom();
		$html->load($poslaju_result);
		$output = 'Record Not Found';
		if($html->find('table[id='.$trackingno.'] tbody',0))
		{
			$status = str_replace(array("\r", "\n"), '', $html->find('table[id='.$trackingno.'] tbody',0)->find('tr',1)->find('td',1)->plaintext);
			$time = $html->find('table[id='.$trackingno.'] tbody',0)->find('tr',1)->find('td',0)->plaintext;
			$place = $html->find('table[id='.$trackingno.'] tbody',0)->find('tr',1)->find('td',2)->plaintext;
			$output = $status.' '.$time.' '.$place;
		}
		$this->out($output);
	}
	
	// check tracking from pos.com.my
	// command: cake check_pos EF123456789MY
	public function check_pos()
	{
		$start = time();
		App::uses('HttpSocket', 'Network/Http');
		$HttpSocket = new HttpSocket();
		
		$trackingno = $this->args[0];
		$poslaju_result = $HttpSocket->post('http://www.pos.com.my/emstrack/viewquery.asp', array('ParcelNo' => $trackingno, 'urlparcelno' => '', 'submit' => '&nbsp;'));
		
		App::import('Vendor', 'simple_html_dom', array('file'=>'simple_html_dom.php'));
		
		$html = new simple_html_dom();
		$html->load($poslaju_result);
		$output = 'Record Not Found';
		if($html->find('td a[href=viewdetail.asp?parcelno='.$trackingno.']'))
		{
			$status = $html->find('td a[href=viewdetail.asp?parcelno='.$trackingno.']', 0)->parent()->next_sibling()->next_sibling()->next_sibling()->next_sibling()->plaintext;
			$time = $html->find('td a[href=viewdetail.asp?parcelno='.$trackingno.']', 0)->parent()->next_sibling()->next_sibling()->next_sibling()->plaintext;
			$date = $html->find('td a[href=viewdetail.asp?parcelno='.$trackingno.']', 0)->parent()->next_sibling()->next_sibling()->plaintext;
			$place = $html->find('td a[href=viewdetail.asp?parcelno='.$trackingno.']', 0)->parent()->next_sibling()->next_sibling()->next_sibling()->next_sibling()->next_sibling()->plaintext;
			$output = $status.' '.$date.' '.$time.' '.$place;
		}
		$this->out($output);
		$this->out(time() - $start);
	}
	
	// send sms via twilio
	// command: cake send 0123456789 hello world
	public function send()
	{
		$args = $this->args;
		unset($args[0]);
		$Collection = new ComponentCollection();
		$this->Twilio = new TwilioComponent($Collection);
		
		$status = false; $attempt = 10; $n = 0; $fibo = array();
		while(!$status && $n < $attempt)
		{
			$fibo[$n] = $n;
			if($n > 1)
				$fibo[$n] = $fibo[$n-1] + $fibo[$n-2];
			sleep($fibo[$n]);
			$response = $this->Twilio->send('+'.$this->args[0], implode(' ', $args));
			$status = !$response->IsError;
			if($status)
				break;
			$n++;
		} 
		if($response->IsError)
			$this->out($response->ErrorMessage);
	}
}