<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <?php echo $this->Html->meta('icon');?>
	
	<link rel="apple-touch-icon" sizes="75x75" href="img/poslaju-apple-75.png" />
	<link rel="apple-touch-icon" sizes="100x100" href="img/poslaju-apple-100.png" />
	<link rel="apple-touch-icon" sizes="175x175" href="img/poslaju-apple-175.png" />

    <title>PosLaju SMS Tracking</title>

    <!-- Bootstrap core CSS -->
    <?php echo $this->Html->css('bootstrap.css');?>

    <!-- Custom styles for this template -->
    <?php echo $this->Html->css('jumbotron-narrow.css');?>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <?php echo $this->Html->script('html5shiv.js');?>
      <?php echo $this->Html->script('respond.min.js');?>
		
    <![endif]-->
  </head>
   <body>

    <div class="container">
      <div class="header">
        <ul class="nav nav-pills pull-right">
          <li class="active"><a href="javascript:void(0)">Home</a></li>
		  <li><a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="Perkhidmatan ini tiada kerjasama dengan pihak Pos Malaysia">Disclaimer</a></li>
          <li><a href="mailto:herp@derp.com.my" data-toggle="tooltip" data-placement="right" title="What's up?">Contact</a></li>
        </ul>
        <h3 class="text-muted">PosLaju SMS Tracking</h3>
      </div>

      <div class="jumbotron">
        <h1>Track & Trace</h1>
        <p class="lead">Risaukan penghantaran pos anda? Anda menyemak status pengeposan setiap masa? Risaukan anda tiada di rumah ketika penghantaran pos tiba? Langgan perkembangan penghantaran pos anda melalui SMS sekarang!</p>
        <p><a class="btn btn-lg btn-success" href="javascrip:void(0)" role="button" style="background-color: #47a447;" data-toggle="tooltip" title="e.g, GET PL EF123456789MY Hanya untuk rangkaian Maxis, Celcom dan Digi. SMS pertama dikenakan caj RM0.50, SMS seterusnya adalah PERCUMA." data-placement="bottom">Hantar "GET PL &lt;No. Tracking&gt;" ke 36828</a></p>
      </div>

      <div class="row marketing">
        <div class="col-lg-6">
          <h4>Arahan Ringkas</h4>
          <p>Hanya hantar SMS "GET&lt;jarak&gt;PL&lt;jarak&gt;&lt;Nombor Tracking PosLaju&gt;" ke 36828</p>

          <h4>Maklumat Lengkap</h4>
          <p>Maklumat status pengeposan diambil terus dari laman web www.poslaju.com.my</p>
        </div>

        <div class="col-lg-6">
          <h4>Capaian Mudah</h4>
          <p>Tidak perlu lagi menyemak, terima perkembangan terbaru status pengeposan melalui SMS.</p>

          <h4>Semakan Tepat</h4>
          <p>Semakan dibuat setiap 10 minit untuk ketepatan maklumat terbaru pengeposan.</p>
        </div>
      </div>

      <div class="footer">
        <p>&copy; Mohd Sulaiman 2013</p>
      </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
	<?php 
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
		echo $this->Html->script('tooltip.js');
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
	<script>
		$(document).ready(function() {
			$("a").tooltip();
		});
	</script>
  </body>
</html>
