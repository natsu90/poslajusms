PosLaju SMS
=======

PosLaju SMS membolehkan anda menerima perkembangan terbaru penghantaran Poslaju melalui SMS. Hanya hantar SMS 'GET PL {No Tracking Poslaju}' ke 36828.
Dibina menggunakan CakePHP dan MongoDB. Anda memerlukan akaun TXT.MY utk menerima arahan SMS dan Twilio utk menghantar SMS.

Konfigurasi
-----------

1.	Letakkan token API [TXT.MY](http://txt.my) pada **$txt_my_token** di `app/Controller/RequestController.php`
	
2.	Letakkan konfigurasi [Twilio](http://www.twilio.com), `app/Plugin/Twilio/Config/twilio.php`
	
3.	Upload ke [Heroku](http://heroku.com)
