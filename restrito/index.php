<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');
require('../config.php');
require('../functions.php');
verificaLogin();
$con = new DBConnection();
getNivel();
getData();

// MELHORIA 3: VERIFICAÇÃO DE ACESSO TOTAL (ADICIONADO NO INÍCIO)
function isFullAdmin() {
    global $nivel_acesso;
    return ($nivel_acesso === 'admin_total' || $nivel_acesso === 'superadmin');
}

if ($nivel_acesso == 'admin' || temPermissao('gerente')) {
    // MELHORIA 5: REGISTRO DE LOG (ADICIONADO DENTRO DAS VERIFICAÇÕES EXISTENTES)
    if (function_exists('logAdminAction')) {
        logAdminAction('ACESSO_PAINEL', 'Acessou área administrativa');
    }
}


if ($nivel_acesso == 'admin' || temPermissao('gerente'))

// Verifica se o usuário é gerente
if (temPermissao('gerente')) {
   
}

// Verifica permissão específica
if (temPermissao('financeiro')) {
    
}

// VERIFICA SE AS COLUNAS EXISTEM (MELHORIA ADICIONADA)
function colunaExiste($con, $tabela, $coluna) {
    try {
        $stmt = $con->prepare("SHOW COLUMNS FROM $tabela LIKE ?");
        $stmt->execute([$coluna]);
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        return false;
    }
}

// PREPARA CONDIÇÕES DINAMICAMENTE (MELHORIA ADICIONADA)
$condicoes_equipamentos = "sub_categoria = notas_cat_sub.id";
if(colunaExiste($con, 'notas_equipamentos', 'status')) {
    $condicoes_equipamentos .= " AND status IN(0)";
}
if(colunaExiste($con, 'notas_equipamentos', 'situacao')) {
    $condicoes_equipamentos .= " AND situacao IN(2,1)";
}
if(colunaExiste($con, 'notas_equipamentos', 'controle')) {
    $condicoes_equipamentos .= " AND controle IN(0)";
}

// Adicione no início do arquivo, junto com outras verificações
if(colunaExiste($con, 'notas_cat_sub', 'oculto')) {
    $condicoes_cat_sub = "oculto = '0'";
} else {
    $condicoes_cat_sub = "1=1"; // Condição sempre verdadeira
}
?>
<!DOCTYPE html>
<html>
<head>
	<!-- CABEÇALHO ORIGINAL INTACTO -->
	<meta charset="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>LOCAL FERRAMENTAS - Sistema Administrativo</title>
	<link rel="icon" href="../style/img/icone-litoralrent.ico" type="image/x-icon"/>
	<link rel="shortcut icon" href="../style/img/imagens/icone-litoralrent.ico" type="image/x-icon"/>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
	<link rel="stylesheet" href="../style/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="../style/css/bootstrap-combobox.css" />
	<link rel="stylesheet" href="../style/css/dashboard.css?<?php echo filemtime('../style/css/dashboard.css'); ?>">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"/>
	<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed|Ubuntu|Oswald:300' rel='stylesheet' type='text/css'/>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
	 <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="../style/css/skins/skin-blue.min.css"/>
	<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css"/>
	<link rel="stylesheet" href="../plugins/iCheck/all.css"/>
	<link rel="stylesheet" href="../plugins/autocomplete/jquery-ui.css"/>
	<link rel="stylesheet" href="../style/css/uploadfile.min.css"/>
	<link rel="stylesheet" href="../style/css/restrito-dashboard.css"/>
	<link rel="stylesheet" href="../style/css/multiple-select.css"/>
	<link rel="stylesheet" href="../style/css/multiselect.filter.css"/>
	
	<link href='../plugins/core/main.css' rel='stylesheet' />
    <link href='../plugins/daygrid/main.css' rel='stylesheet' />

	<!-- jQuery FIRST with fallback -->
	<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
	<script>window.jQuery || document.write('<script src="../plugins/jQuery/jquery-2.2.4.min.js"><\/script>')</script>

	<!-- Bootstrap JS with fallback -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
	<script>window.bootstrap || document.write('<script src="../style/js/bootstrap.min.js"><\/script>')</script>

    <script src='../plugins/core/main.js'></script>
    <script src='../plugins/daygrid/main.js'></script>
	
	<script src='../plugins/core/locales/pt-br.js'></script>
	<script>
	    // DEFINIÇÃO DA FUNÇÃO POST()
        window.post = function(path, params, method = 'post') {
            const form = document.createElement('form');
            form.method = method;
            form.action = path;

            if (params) {
                for (const key in params) {
                    if (params.hasOwnProperty(key)) {
                        const hiddenField = document.createElement('input');
                        hiddenField.type = 'hidden';
                        hiddenField.name = key;
                        hiddenField.value = params[key];
                        form.appendChild(hiddenField);
                    }
                }
            }

            document.body.appendChild(form);
            form.submit();
        };
        
      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
		  locale: 'pt-br',
		  plugins: ['dayGrid'],
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
				start: '2019-04-09',
				end: '2019-04-09',
				color: '#F8BB3C'
			},
			{
				id: '2',
				title: 'Teste Calendario',
				start: '2019-04-09',
				end: '2019-04-09',
				color: '#EB5900'
			},
		  ]
		});
		calendar.setOption('locale', 'pt-br');
		calendar.render();
      });
    </script>
</head>
<body class="inicio-painel">
    <!-- NAVBAR ORIGINAL INTACTO -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php" style="font-family: 'Lobster';"> <img src="../style/img/projetta-logo.png" alt="" width="35%" style="margin-top:-5px; margin-right:10px; float:left;"/></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
			<li><a href="#" onClick="ldy('troca-senha.php','.conteudo')"><i class="fas fa-user-cog"></i> <?= $login_usuario ?></a></li>
            <li><a href="../logout.php?acao=true"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <!-- SIDEBAR ORIGINAL INTACTO -->
        <div id="navBarLeft" class="col-sm-3 col-md-2 sidebar">
				<ul class="nav nav-sidebar">
				<!-- FINANCEIRO -->
					<?php if ($financeiro_array == $id_usuario_logado) { ?>
					<li class="header button-header" onClick="clickFunction1();"><i class="fas fa-dollar-sign"></i> Financeiro<i class="fa fa-caret-down pull-right" aria-hidden="true"></i></li>
						<li class="header sub-header list-block list-finac"> Cadastro</li>
							<li class="list-block list-finac buttonClass"><a href="#" onClick="ldy('financeiro/cadastro-contrato.php','.conteudo')"><i class="fas fa-file-contract"></i> <span>Contrato</span></a></li>
						<li class="header sub-header list-block list-finac"> Consulta</li>
							<li class="list-block list-finac buttonClass"><a href="#" onClick="ldy('financeiro/consulta-contrato.php','.conteudo')"><i class="fas fa-search-dollar"></i> <span>Contrato (Locação)</span></a></li>
							<li class="list-block list-finac buttonClass"><a href="#" onClick="ldy('financeiro/consulta-contrato-devolucao.php','.conteudo')"><i class="fas fa-search-dollar"></i> <span>Contrato (Devolução)</span></a></li>
						<li class="header sub-header list-block list-finac"> Relatório</li>
							<li class="list-block list-finac buttonClass"><a href="#" onClick="ldy('almoxarifado/consulta-equipamentos-2.php','.conteudo')"><i class="fas fa-truck-pickup"></i> <span>Equipamentos</span></a></li>
							<li class="list-block list-finac buttonClass"><a href="#" onClick="ldy('financeiro/relatorio-contrato.php','.conteudo')"><i class="fas fa-newspaper"></i> <span>Relatorio Contrato</span></a></li>
					<?php } ?>
				<!-- COMPRAS -->
					<?php if($compras_array == $id_usuario_logado) { ?>
					<li class="header button-header" onClick="clickFunction2();"><i class="fas fa-hand-holding-usd"></i> Comercial <i class="fa fa-caret-down pull-right" aria-hidden="true"></i></li>
					<li class="header sub-header list-block list-comp"> Cadastro</li>
						<li class="list-block list-comp buttonClass"><a href="#" onClick="ldy('financeiro/cadastro-empresa.php','.conteudo')"><i class="fas fa-briefcase"></i> <span>Cadastro Empresas</span></a></li>
						<li class="list-block list-comp buttonClass"><a href="#" onClick="ldy('financeiro/cadastro-orcamento.php','.conteudo')"><i class="far fa-money-bill-alt"></i> <span>Cadastro Orçamento</span></a></li>
					<li class="header sub-header list-block list-comp"> Consulta</li>
						<li class="list-block list-comp buttonClass"><a href="#" onClick="ldy('financeiro/consulta-empresas.php','.conteudo')"><i class="fas fa-search"></i> <span>Consulta Empresas</span></a></li>
						<li class="list-block list-comp buttonClass"><a href="#" onClick="ldy('financeiro/consulta-orcamento.php','.conteudo')"><i class="fas fa-briefcase"></i> <span>Orçamento</span></a></li>
						<li class="list-block list-comp buttonClass"><a href="#" onClick="ldy('financeiro/consulta-itens-2.php','.conteudo')"> <i class="fas fa-folder-open"></i> <span>Itens</span></a></li>
					<?php } ?>
				<!-- CONSULTA -->
					<li class="header button-header" onClick="clickFunction3();"><span class="glyphicon glyphicon-search"></span> Consulta <i class="fa fa-caret-down pull-right" aria-hidden="true"></i></li>
						<li class="header sub-header list-block list-consulta"> Relatório</li>
						<li class="list-block list-consulta buttonClass"><a href="#" onClick="ldy('financeiro/relatorio-contrato.php','.conteudo')"><i class="fas fa-newspaper"></i> <span>Relatorio Contrato</span></a></li>
						<li class="list-block list-consulta buttonClass"><a href="#" onClick="ldy('almoxarifado/consulta-equipamentos.php','.conteudo')"><i class="fas fa-truck-pickup"></i> <span>Equipamentos</span></a></li>
						
				<!-- GESTOR -->
					<?php if ($gestor_array == $id_usuario_logado) { ?>
					<li class="header button-header" onClick="clickFunction4();"><i class="fa fa-user-secret" aria-hidden="true"></i>&nbsp;&nbsp;Gestor <i class="fa fa-caret-down pull-right" aria-hidden="true"></i></li>
					<li class="header sub-header list-block list-gestor">RH</li>
						<li class="list-block list-gestor buttonClass"><a href="#" onclick="ldy('gestor/consulta-usuarios.php','.conteudo')"><i class="fa fa-users" aria-hidden="true"></i> <span>Usuários</span></a></li>
					<li class="header sub-header list-block list-gestor">Consulta</li>
						<li class="list-block list-gestor buttonClass"><a href="#" onclick="ldy('gestor/consulta-categoria-equip.php','.conteudo')"><i class="fas fa-car"></i> <span>Categoria Equipamentos</span></a></li>
						<li class="list-block list-gestor buttonClass"><a href="#" onclick="ldy('gestor/consulta-situacao-equip.php','.conteudo')"><i class="fas fa-clipboard-check"></i> <span>Situação Equipamentos</span></a></li>
						<li class="list-block list-gestor buttonClass"><a href="#" onClick="ldy('financeiro/consulta-itens.php','.conteudo')"><i class="fas fa-boxes"></i> <span>Itens</span></a></li>
					<?php } ?>
				</ul>
        </div>
        
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
			<div class="content-wrapper conteudo" style="margin:10px">
				<div class="container-fluid">
					<div class="col-xs-12 col-md-8">
						<section style="margin:0px; padding:5px; border-bottom:1px solid #ccc; margin-bottom:20px;">
							<h4 style="font-family: 'Oswald', sans-serif; letter-spacing:1.5px;">Calendário<small> </small></h4>
						</section>
						<div id='calendar-container'>
							<div id='calendar'></div>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<section style="margin:0px; padding:5px; border-bottom:1px solid #ccc; margin-bottom:20px;">
							<h4 style="font-family: 'Oswald', sans-serif; letter-spacing:1.5px;">Equipamentos Disponiveis<small> </small></h4>
						</section>
						<?php
							echo '<div class="table-dash-box">
								<table id="resultadoConsulta" class="box box-widget table table-condensed table-dash" style="font-size:12px">
								<thead>
									<tr>
										<th style="text-align:center"><i class="fa fa-list-alt" aria-hidden="true"></i></th>
										<th style="text-align:center">Descrição:</th>
										<th style="text-align:center">Total</th>
									</tr>
								</thead> 
							<tbody>';
						// Verifica a estrutura da tabela notas_cat_sub
						$colunas_cat_sub = [];
						try {
							$stmt = $con->query("SHOW COLUMNS FROM notas_cat_sub");
							$colunas_cat_sub = $stmt->fetchAll(PDO::FETCH_COLUMN);
						} catch(PDOException $e) {
							// Se houver erro, usamos fallback seguro
							$colunas_cat_sub = ['id']; // Coluna mínima que deve existir
						}

						// Define a coluna para ordenação (verifica se 'descricao' existe, senão usa 'nome' ou 'id')
						$coluna_ordenacao = 'id'; // Fallback padrão
						if (in_array('descricao', $colunas_cat_sub)) {
							$coluna_ordenacao = 'descricao';
						} elseif (in_array('nome', $colunas_cat_sub)) {
							$coluna_ordenacao = 'nome';
						}
						// QUERY MODIFICADA PARA USAR CONDIÇÕES DINÂMICAS (MELHORIA ADICIONADA)
						$query_equipamentos = "SELECT *, 
							(SELECT COUNT(*) 
							 FROM notas_equipamentos 
							 WHERE $condicoes_equipamentos)
							 AS total_equipamentos 
							 FROM notas_cat_sub 
							 WHERE oculto = '0' 
							 ORDER BY descricao ASC";
						try {
								$stm2 = $con->query($query_equipamentos);
								$se2 = 0;
								$total_equipamentos_g = 0;
								
								while($c = $stm2->fetch()) {
									if($c['total_equipamentos'] != 0) {
										$se2 += 1;
										echo '<tr>';
										echo '<td style="text-align:center">'.$se2.'</td>';
										echo '<td>'.(isset($c['descricao']) ? $c['descricao'] : (isset($c['nome']) ? $c['nome'] : 'Item '.$c['id'])).'</td>';
										echo '<td style="text-align:center">'.$c['total_equipamentos'].'</td>';
										echo '</tr>';
										$total_equipamentos_g += $c['total_equipamentos'];
									}
								}
								
								// Adiciona este trecho para debug (pode remover depois)
								error_log("Total de equipamentos calculado: ".$total_equipamentos_g);
								
							} catch(PDOException $e) {
								echo '<tr><td colspan="3">Erro ao carregar equipamentos: '.$e->getMessage().'</td></tr>';
								$total_equipamentos_g = 0; // Garante que a variável existe mesmo em caso de erro
							}

							// Verifica se o total está sendo exibido
							echo '<tfoot>';
							echo '<tr class="active"><td colspan="2"><b>Total</b></td><td style="text-align:center"><b>'.$total_equipamentos_g.'</b></td></tr>';
							echo '</tfoot>';
					?>
					</div>
				</div>
			</div>
			
			<!-- ELEMENTOS DE LOADING E RODAPÉ ORIGINAIS INTACTOS -->
			<div id="loading" class="hidden-print" style="width:100%; height:100%; display:none; position:fixed; top:0; left:0; background: rgba(255, 255, 255, 0.5); z-index:9999;">
				<div style="position:relative; top: 40%; text-align:center;">
					<img src="../style/img/loading.svg"  alt="" width="40px" />
					<h4 style="font-family: 'Lobster', sans-serif; font-size:15px; color: rgba(0, 0, 0, 0.5);">Carregando...</h4>
				</div>
			</div>
			<div id="loadingstart" style="position: absolute; height: 100%; width: 100%; top:0; left: 0; background: #FFF; z-index:9999; 
			font-size: 30px; text-align: center; padding-top: 10px; color: #666;">
				<img src="../style/img/loading.gif" alt="" width="120px"/>
				<h4 style="font-family: 'Lobster', sans-serif; font-size:15px; color: rgba(0, 0, 0, 0.5);">Carregando...</h4>
			</div>
			<footer class="main-footer hidden-print" style="padding:20px 10px; background:#F5F5F5">
				<div>
					<strong>Copyright &copy; 2025</strong> Todos direitos reservados. <br/> <small>Desenvolvido por: Lucas Samuel</small>
				</div>
			</footer>
		</div>
    </div>
	
	<!-- SCRIPTS ORIGINAIS (now loaded after jQuery and Bootstrap) -->
	<script src="../style/js/app.min.js"></script>
	<script src="../style/js/bootstrap-combobox.js"></script>
	<script src="../plugins/datatables/jquery.dataTables.js?v3"></script>
	<script src="../plugins/datatables/dataTables.bootstrap.js"></script>
	<script src="../plugins/iCheck/icheck.min.js"></script>
	<script src="../plugins/autocomplete/jquery-ui.min.js"></script>
	<script src="../plugins/autocomplete/jquery.select-to-autocomplete.js"></script>
	<script src="../plugins/input-mask/jquery.maskedinput.js"></script>
	<script src="../plugins/jquery.uploadfile.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js"></script>
	<script src="../plugins/jquery.slimscroll.min.js"></script>
	<script src="../plugins/jquery.multiple.js"></script>
	<script src="../plugins/bootstrap-select.js"></script>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script src="../plugins/jquery.printElement.js"></script>
	
	<!-- FUNÇÕES JAVASCRIPT ORIGINAIS INTACTAS -->
	<script>
	// Função para carregar conteúdo dinamicamente
	function ldy(url, target) {
		fetch(url)
			.then(response => {
				if (!response.ok) throw new Error('Erro ao carregar: ' + response.status);
				return response.text();
			})
			.then(html => {
				const container = document.querySelector(target);
				if (container) {
					container.innerHTML = html;
				} else {
					console.error('Elemento alvo não encontrado:', target);
				}
			})
			.catch(error => {
				console.error('Erro:', error);
				alert('Erro ao carregar o conteúdo: ' + error.message);
			});
	}

	// Funções para mostrar/ocultar menus
	function clickFunction1() {
		toggleMenu('.list-finac');
	}

	function clickFunction2() {
		toggleMenu('.list-comp');
	}

	function clickFunction3() {
		toggleMenu('.list-consulta');
	}

	function clickFunction4() {
		toggleMenu('.list-gestor');
	}

	// Função genérica para alternar menus
	function toggleMenu(selector) {
		const elements = document.querySelectorAll(selector);
		elements.forEach(el => {
			el.style.display = el.style.display === 'none' ? 'block' : 'none';
		});
	}

	// Inicializa os menus ao carregar a página
	document.addEventListener('DOMContentLoaded', function() {
		// Oculta todos os submenus inicialmente
		document.querySelectorAll('.list-block').forEach(el => {
			el.style.display = 'none';
		});
		
		// Adiciona eventos de clique alternativo
		document.querySelectorAll('.button-header').forEach(header => {
			header.addEventListener('click', function() {
				const icon = this.querySelector('.fa-caret-down');
				if (icon) {
					icon.classList.toggle('fa-rotate-180');
				}
			});
		});
	});
	</script>
	
	<script>
	     // MELHORIA 4: FUNÇÃO PARA O MENU ADMIN (ADICIONADA NO FINAL)
        function clickFunctionAdmin() {
            $('.list-admin').toggle();
        }
		// Verificação de carregamento
		document.addEventListener('DOMContentLoaded', function() {
			console.log('Funções disponíveis:', {
				ldy: typeof ldy,
				clickFunction1: typeof clickFunction1,
				jQuery: typeof jQuery
			});
			
			// Inicializa menus ocultos
			document.querySelectorAll('.list-block').forEach(el => {
				el.style.display = 'none';
			});
		});
	</script>
</body>
</html>