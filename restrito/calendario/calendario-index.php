<?php
	require('../../config.php');
	require('../../functions.php');
	$con = new DBConnection();
	verificaLogin();
	getNivel();
	getData();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title> <?php echo $titulo_geral; ?> - Sistema Administrativo</title>
	<link rel="icon" href="../../style/img/icone-litoralrent.ico" type="image/x-icon"/>
	<link rel="shortcut icon" href="../../style/img/imagens/icone-litoralrent.ico" type="image/x-icon"/>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
	<link rel="stylesheet" href="../../style/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="../../style/css/bootstrap-combobox.css" />
	<link rel="stylesheet" href="../../style/css/dashboard.css?v04"/>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"/>
	<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed|Ubuntu|Oswald:300' rel='stylesheet' type='text/css'/>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
	 <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="../../style/css/skins/skin-blue.min.css"/>
	<link rel="stylesheet" href="../../plugins/datatables/dataTables.bootstrap.css"/>
	<link rel="stylesheet" href="../../plugins/iCheck/all.css"/>
	<link rel="stylesheet" href="../../plugins/autocomplete/jquery-ui.css"/>
	<link rel="stylesheet" href="../../style/css/uploadfile.min.css"/>
	<link rel="stylesheet" href="../../style/css/restrito-dashboard.css"/>
	<link rel="stylesheet" href="../../style/css/multiple-select.css"/>
	<link rel="stylesheet" href="../../style/css/multiselect.filter.css"/>
	<link href='../../plugins/core/main.css' rel='stylesheet' />
	<link href='../../plugins/daygrid/main.css' rel='stylesheet' />
<style>

  html, body {
    overflow: hidden; /* don't do scrollbars */
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 14px;
  }

  #calendar-container {
    position: fixed;
    top: 50px;
    left: 0;
    right: 0;
    bottom: 0;
  }

  .fc-header-toolbar {
    /*
    the calendar will be butting up against the edges,
    but let's scoot in the header's buttons
    */
    padding-top: 1em;
    padding-left: 1em;
    padding-right: 1em;
  }

</style>
</head>
<body class="inicio-painel" onload='ldy("dashboard.php",".conteudo")'>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php" style="font-family: 'Lobster';"> <img src="../../style/img/litoralrent-logo.png" alt="" width="35%" style="margin-top:-5px; margin-right:10px; float:left;"/></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
			<li><a href="#" onClick="ldy('troca-senha.php','.conteudo')"><i class="fas fa-user-cog"></i> <?= $login_usuario ?></a></li>
            <li><a href="../../logout.php?acao=true"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
          </ul>
        </div>
		
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
			<div class="content-wrapper conteudo" style="margin:10px">

<script>

  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
	  locale: 'pt-br',
      plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
      height: 'parent',
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
      },
      defaultView: 'dayGridMonth',
      defaultDate: '<?= $todayTotal ?>',
      navLinks: true, 
      editable: true,
      eventLimit: true, 
      events: [
        {
			id: '1',
			title: 'Lançar nota fiscal',
			start: '2019-04-01',
			end: '2019-04-02',
			color: '#F8BB3C'
        },
      ]
    });
	calendar.setOption('locale', 'pt-br');
    calendar.render();
  });

</script>
		  <div id='calendar-container'>
			<div id='calendar'></div>
		  </div>
			
			</div>
			<!---------------------------------------------------------------------------------->

			<div id="loading" class="hidden-print" style="width:100%; height:100%; display:none; position:fixed; top:0; left:0; background: rgba(255, 255, 255, 0.5); z-index:9999;">
				<div style="position:relative; top: 40%; text-align:center;">
					<img src="../../style/img/loading.svg"  alt="" width="40px" />
					<h4 style="font-family: 'Lobster', sans-serif; font-size:15px; color: rgba(0, 0, 0, 0.5);">Carregando...</h4>
				</div>
			</div>
			<div id="loadingstart" style="position: absolute; height: 100%; width: 100%; top:0; left: 0; background: #FFF; z-index:9999; 
			font-size: 30px; text-align: center; padding-top: 10px; color: #666;">
				<img src="../../style/img/loading.gif" alt="" width="120px"/>
				<h4 style="font-family: 'Lobster', sans-serif; font-size:15px; color: rgba(0, 0, 0, 0.5);">Carregando...</h4>
			</div>
			<footer class="main-footer hidden-print" style="padding:20px 10px; background:#F5F5F5">
				<div>
					<strong>Copyright &copy; 2018</strong> Todos direitos reservados. <br/> <small>Desenvolvido por: AtlasWare | Soluções tecnologicas</small>
				</div>
				
			</footer>
		</div>
    </div>
	
	<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
	<script src="../../style/js/bootstrap.min.js"></script>
	<script src="../../style/js/app.min.js"></script>
	<script src="../../style/js/bootstrap-combobox.js"></script>
	<script src="../../plugins/datatables/jquery.dataTables.js?v3"></script>
	<script src="../../plugins/datatables/dataTables.bootstrap.js"></script>
	<script src="../../plugins/iCheck/icheck.min.js"></script>
	<script src="../../plugins/autocomplete/jquery-ui.min.js"></script>
	<script src="../../plugins/autocomplete/jquery.select-to-autocomplete.js"></script>
	<script src="../../plugins/input-mask/jquery.maskedinput.js"></script>
   
	<script src="../../plugins/jquery.uploadfile.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js"></script>
	<script src="../../plugins/jquery.slimscroll.min.js"></script>
	
	<script src="../../plugins/jquery.multiple.js"></script>
	<script src="../../plugins/bootstrap-select.js"></script>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script src="../../plugins/jquery.printPage.js"></script>
	<script src="../../plugins/jquery.printElement.js"></script>
	<!-- -->
	<script src="../../style/js/all.js"></script>
	<script src="../../style/js/combobox.js"></script>
	
	
	<script src='../../plugins/core/main.js'></script>
	<script src='../../plugins/core/locales/pt-br.js'></script>
	<script src='../../plugins/interaction/main.js'></script>
	<script src='../../plugins/daygrid/main.js'></script>
	<script>
	$('.buttonClass').click(function(){
		$('li').removeClass("active");
		$(this).addClass("active");
	});
    $(window).load(function() {
        $('#loadingstart').fadeOut(200);
		$('.list-finac').slideToggle('slow');
		$('.list-logistica').slideToggle('slow');
		$('.list-comp').slideToggle('slow');
		$('.list-consulta').slideToggle('slow');
		$('.list-gestor').slideToggle('slow');
    });
	</script>
</body>
</html>


