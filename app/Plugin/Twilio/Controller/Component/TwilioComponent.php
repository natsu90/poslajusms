<?php 
	App::import('Vendor', 'Twilio.Twilio');
	class TwilioComponent extends Component{
	
	function send($to, $message)
	{
        $this->Twilio = new Twilio();
		return $this->Twilio->send($to, $message);
	}
}