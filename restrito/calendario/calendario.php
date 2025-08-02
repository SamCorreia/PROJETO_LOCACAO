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
	<link href='../plugins/core/main.css' rel='stylesheet' />
	<link href='../plugins/daygrid/main.css' rel='stylesheet' />
<style>
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

			
		</div>
    </div>
	
	<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
	
	
	<script src='../plugins/core/main.js'></script>
	<script src='../plugins/core/locales/pt-br.js'></script>
	<script src='../plugins/interaction/main.js'></script>
	<script src='../plugins/daygrid/main.js'></script>
</body>
</html>


