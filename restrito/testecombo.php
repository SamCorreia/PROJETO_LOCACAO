<?php
	require_once('../config.php');
	require_once('../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
?>
<link rel="stylesheet" href="../style/css/combobox.css"/>

<script>
	$(function() {
		$("#combobox").combobox();
		$("#combobox2").combobox();
	});
		</script>
<div class="container-fluid">
  <div class="ui-widget">
    <label>Your preferred programming language:</label>
    <br />
    <select id="combobox">
      <option value="">Select one...</option>
      <option value="ActionScript" disabled="">ActionScript</option>
      <option value="AppleScript">AppleScript</option>
      <option value="Asp">Asp</option>
      <option value="BASIC">BASIC</option>
      <option value="C">C</option>
      <option value="C++">C++</option>
      <option value="Clojure">Clojure</option>
      <option value="COBOL">COBOL</option>
      <option value="ColdFusion">ColdFusion</option>
      <option value="Erlang">Erlang</option>
      <option value="Fortran">Fortran</option>
      <option value="Groovy">Groovy</option>
      <option value="Haskell">Haskell</option>
      <option value="Java">Java</option>
      <option value="JavaScript">JavaScript</option>
      <option value="Lisp">Lisp</option>
      <option value="Perl">Perl</option>
      <option value="PHP">PHP</option>
      <option value="Python">Python</option>
      <option value="Ruby">Ruby</option>
      <option value="Scala">Scala</option>
      <option value="Scheme">Scheme</option>
    </select>
  </div>
</div>

<div class="container-fluid">
  <div class="ui-widget">
    <label>Your preferred programming language:</label>
    <br />
    <select id="combobox2">
      <option value="">Select one...</option>
      <option value="ActionScript" disabled="">ActionScript</option>
      <option value="AppleScript">AppleScript</option>
      <option value="Asp">Asp</option>
    </select>
  </div>
</div>
