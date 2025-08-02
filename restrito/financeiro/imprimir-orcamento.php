<?php
	require_once('../../config.php');
	require_once('../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	$stm = $con->query("SELECT *, (select razao_social from localferramentas_cadastroempresa where id = orcamento_dados.empresa) as razao_social, (select cnpj from localferramentas_cadastroempresa where id = orcamento_dados.empresa) as cnpj FROM orcamento_dados WHERE id = '$id_orcamento'");
	while($b = $stm->fetch()){ extract ($b); }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title> Litoral Rent</title>
	<link rel="icon" href="../../style/img/logo.ico" type="image/x-icon"/>
	<link rel="shortcut icon" href="../../style/img/imagens/logo.ico" type="image/x-icon"/>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
	<link rel="stylesheet" href="../../style/css/bootstrap.min.css"/>
	<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'/>
	<link href='https://fonts.googleapis.com/css?family=Oswald:300' rel='stylesheet' type='text/css'/>
	<style>
		*{
			font-size:12px;
		}
		@page {
			size: A4;
		}
		table, tr, th, td {
			border:1px solid #000;
		}
		
	</style>
</head>
<script>
window.onload = function() { 
	window.print(); 
}
</script>
<body>
		<div class="col-xs-12 hidden-print" style="margin:0 auto; text-align:center">
			<a href="#" class="btn btn-warning btn-xs hidden-print" style="width:60%; margin-top:10px; padding:10px; font-size:15px; font-weight:bold; margin-bottom:30px;" onclick="javascript:window.close()">Fechar</a>
		</div>
		<div class="container-fluid">
			<div class="col-xs-12" style="text-align:center; padding-bottom:20px; margin-bottom:20px;"> 
				<center>
					<img src="../../style/img/litoralrent-logo.png" alt="logo" width="15%" class="img-responsive"/>
				</center>
			</div>
			<div class="col-xs-12"> 
				<p>São Vicente, <?=$dia_view?> DE <?=$mes_nome?> DE <?= $ano_view ?></p>
				<p>À</p>
				<p><b><?= strtoupper($razao_social)?></b></p>
				<p>CNPJ: <b><?= strtoupper($cnpj)?></b></p>
				<p>Assunto: <?= $assunto?></p>
			</div>
			
			<div class="col-xs-12" style="text-align:center"> 
				<h3>ORÇAMENTO</h3>
			</div>
			<div class="col-xs-12" style="text-align:center">
				<p> <u>Prezado Senhores</u>,<br/> Pelo presente, apresentamos e submetemos à apreciação de V.Sa., nossa Proposta referente a locação dos seguintes equipamentos, para sua análise dos itens abaixo decriminados.
				</p>
			</div>
			<div class="col-xs-12">
				<table class="table table-condensed" border="0">
						<tr>
							<th style="border:1px solid #000; text-align:center">ITEM</th>
							<th style="border:1px solid #000; text-align:center">DESCRIÇÃO</th>
							<th style="border:1px solid #000; text-align:center">QTDE</th>
							<th style="border:1px solid #000; text-align:center">V.UNIT</th>
							<th style="border:1px solid #000; text-align:center">TOTAL</th>
						</tr>
					<?php 
					$stm = $con->prepare("SELECT *, (SELECT descricao FROM notas_itens WHERE id = orcamento_itens.id_item) as descricao FROM orcamento_itens WHERE id_orcamento = ? ");
					$stm->execute(array($id_orcamento));
					$se = 0;
					$total_item_geral = 0;
					while($s = $stm->fetch())
					{
						$se += 1;
						$total_item = 0;
						echo '<tr>';
						echo '<td>'.$se.'</td>';
						echo '<td>'.strtoupper($s['descricao']).'</td>';
						echo '<td align="center">'.$s['qtd'].'</td>';
						$stm2 = $con->query("SELECT * FROM notas_itens WHERE id = '".$s['id_item']."'");
						$valor_array = $stm2->fetch(PDO::FETCH_ASSOC);
						switch($medicao){
							case '30':
								$valor_un = $valor_array['valor30'];
							break;
							case '15':
								$valor_un = $valor_array['valor15'];
							break;
							case '7':
								$valor_un = $valor_array['valor07'];
							break;
							case '3':
								$valor_un = $valor_array['valor03'];
							break;
						}
						$valor_total = ($valor_un - $s['desconto_vlr']) + $s['acres_vlr'];
						echo '<td align="center">R$ '.number_format($valor_total,2,",",".").'</td>';
						$total_item = $s['qtd'] * $valor_total;
						echo '<td align="center">R$ '.number_format($total_item,2,",",".").'</td>';
					echo '</tr>';
						$total_item_geral += $total_item;
					}
					
					echo '<tr><td colspan="4" align="right"><b>Valor Total </b>&nbsp;&nbsp;&nbsp;</td><td align="center"> <b> R$ '.number_format($total_item_geral,2,",",".").'</b></td>';
					?>
				</table>
			</div>
			<?php
				if($frete == '0') {
					$fretePrint = 'Gratis';
				}else if($frete == '1'){
					$fretePrint = 'Embutido';
				}
				
				switch($pagamento){
					case '0': $pagamentoPrint = 'A VISTA'; break;
					case '1': $pagamentoPrint = '10 (dez) dias após emissão da nota fiscal'; break;
					case '2': $pagamentoPrint = '20 (vinte) dias após emissão da nota fiscal'; break;
					case '3': $pagamentoPrint = '30 (trinta) dias após emissão da nota fiscal'; break;
					case '4': $pagamentoPrint = '40 (quarenta) dias após emissão da nota fiscal'; break;
				}
			?>
			<div class="col-xs-12">
				<p>Frete no Litoral Sul – <?= $fretePrint ?></p>
				<p>Período de Medição <?= $medicao ?> dias.</p>
				<p>Pagamento <?= $pagamentoPrint ?>.</p>
				<p>Prazo de locação a combinar.</p>
				<p>Validade desta Proposta é de 15 (quinze) dias.</p>
			</div>
			<div class="col-xs-12">
				<br/>
				<p style="padding-left:20px">Atenciosamente.</p>
			</div>
			<div class="col-xs-12" style="text-align:center">
				<br/>
				<br/>
				<br/>
				<p>________________________________<br/><?= $con->query("SELECT nome FROM usuarios WHERE id = '$user'")->fetchColumn() ?><br/><?= $con->query("SELECT cargo FROM usuarios WHERE id = '$user'")->fetchColumn() ?><br/>Tel. <?= $con->query("SELECT telefone FROM usuarios WHERE id = '$user'")->fetchColumn() ?></p>
				<br/>
				<br/>
				<br/>
			</div>
			
			<div class="col-xs-12" style="text-align:center">
				<p style="border-top:1px solid #000; padding:5px; margin:0px 50px">www.litoralrent.com.br&nbsp;&nbsp;&nbsp;&nbsp;Telefone: (13) 3043-4211&nbsp;&nbsp;&nbsp;&nbsp;E-mail: comercial@litoralrent.com.br<br/>Av. Antônio Emmerick, 723 - Bairro: Jardim Guassu - São Vicente/SP   CEP 11370-001				</p>
			</div>
		</div>
</body>
</html>