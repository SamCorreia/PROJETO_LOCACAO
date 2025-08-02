<?php
require_once('../../config.php');
require_once('../../functions.php');
$con = new DBConnection();

$categorias = $con->query("SELECT * FROM notas_cat_e WHERE oculto = '0' ORDER BY descricao ASC");
while($l = $categorias->fetch()){
    echo '<option value="'.$l['id'].'">'.$l['descricao'].'</option>'; 
}
?>