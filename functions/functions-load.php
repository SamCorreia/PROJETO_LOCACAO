<?php
	require_once('../config.php');
	require_once('../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
?>
<script>
	$('#autoComplete1').selectToAutocomplete();
	$('.sel').multiselect({
		buttonClass: 'btn btn-sm', 
		numberDisplayed: 1,
		maxHeight: 500,
		includeSelectAllOption: true,
		selectAllText: "Selecionar todos",
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		selectAllValue: 'multiselect-all',
		buttonWidth: '100%'
	}); 
</script>
<?php
	//Categoria Equipamento
	if($atu == 'cadastroEmpresa'){ 
		if($tipo_empresa == '3'){?>
					<div class="form-group">
						<label>CPF:</label>
						<input type="text" name="cnpj" onblur="$('#autoco').load('financeiro/cadastro-empresa.php?up=busca-rgi&busca=' + $(this).val() + '');" onfocus="$(this).mask('999.999.999-99')" placeholder="___.___.___-__" class="juridica form-control input-sm"  required />
						<div id="autoco"></div>
					</div>
					<div class="form-group">
						<label>Nome Completo:</label>
						<input type="text" name="razao_social" id="razao_social" placeholder="Nome da Empresa" size="80" class="todosInput form-control input-sm" required />
					</div>
		<?php
		}else{
		?>
			<div class="form-group">
						<label>CNPJ:</label>
						<input type="text" name="cnpj" onblur="$('#autoco').load('financeiro/cadastro-empresa.php?up=busca-rgi&busca=' + $(this).val() + '');" onfocus="$(this).mask('99.999.999/9999-99')" placeholder="__.___.___/____-__" class="juridica form-control input-sm"  required />
						<div id="autoco"></div>
					</div>
					<div class="form-group">
						<label>Razão Social:</label>
						<input type="text" name="razao_social" id="razao_social" placeholder="Nome da Empresa" size="80" class="todosInput form-control input-sm" required />
					</div>
		<?php
		}
		exit;
	}
	if($atu == 'tipo_contrato'){
		echo '<label><small>Selecione o Equipamento:</small></label>
			<select name="equipamentoInput" id="autoComplete1" class="form-control input-sm" required>
			<option value="" selected>Selecione um item</option>';
			$stm = $con->query("SELECT * FROM notas_equipamentos WHERE controle = '0' AND status = '0' ORDER BY patrimonio ASC");
			while($b = $stm->fetch()){
				echo '<option value="'.$b['id'].'">'.$b['patrimonio'].' - '.$con->query("SELECT descricao FROM notas_cat_e WHERE id = '".$b['categoria']."' ")->fetchColumn().' '.$con->query("SELECT descricao FROM notas_cat_sub WHERE id = '".$b['sub_categoria']."' ")->fetchColumn().'</option>';
			}
		echo '</select>';
		exit;
	}
	if($atu == 'tipo_contrato2'){
		echo '<label><small>Selecione o Equipamento:</small></label>
		<select name="equipamentoInput" id="autoComplete1" class="form-control input-sm" required>
		<option value="" selected>Selecione um item</option>';
		$query2 = $con->query("SELECT * FROM contrato_itens WHERE id_contrato = '$id_contrato'");
		while($a = $query2->fetch()){
			$stm = $con->query("SELECT * FROM notas_equipamentos WHERE controle = '1' AND id = '".$a['equipamento']."' ORDER BY patrimonio ASC");
			while($b = $stm->fetch()){
				echo '<option value="'.$b['id'].'">'.$b['patrimonio'].' - '.$con->query("SELECT descricao FROM notas_cat_e WHERE id = '".$b['categoria']."' ")->fetchColumn().' '.$con->query("SELECT descricao FROM notas_cat_sub WHERE id = '".$b['sub_categoria']."' ")->fetchColumn().'</option>';
			}
		}
		echo '</select>';
		exit;
	}
	if($atu == 'categoria'){
		if($control=='1'){
			echo '<label style="width:100%"><small>Sub-Categoria</small><select name="sub_categoria" style="width:100%" class="form-control input-sm">';
			$stms = $con->query("select * from notas_cat_sub where associada in($categoria) order by descricao asc");
			while($l = $stms->fetch()){
				echo '<option value="'.$l['id'].'">'.$l['descricao'].'</option>';
			}
			echo '</select></label>';
		}else{
			echo '<label style="width:100%"><small>Sub-Categoria:</small><br/>
				<select name="sub[]" style="width:100%" id="itens" class="sel" multiple="multiple" class="form-control input-sm" required>';
				$stms = $con->query("select * from notas_cat_sub where associada in($categoria) order by descricao asc");
				while($l = $stms->fetch()){
					echo '<option value="'.$l['id'].'" selected>'.$l['descricao'].'</option>';
				}
				echo '</select>';
			echo '</label>';
		}
		exit;
	}

?>
