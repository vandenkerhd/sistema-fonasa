<?php

$routesArray = explode("/", $_SERVER['REQUEST_URI']);
$routesArray = array_filter($routesArray);

foreach ($routesArray as $key => $value) {

  $value = explode("?", $value)[0];

  $routesArray[$key] = $value;

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>FONASA :: ADMIN</title>
	<link rel="icon" href="<?=TemplateController::path()?>assets/images/icon/logo.png">
	<link rel="stylesheet" href="<?=TemplateController::path()?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?=TemplateController::path()?>assets/css/font-awesome.min.css">
	
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="<?=TemplateController::path()?>assets/js/head.js"></script>
	

</head>
<body>

	<?php 

	if (!isset($_COOKIE["surcursal"])) {

		include "views/pages/surcursal.php";
		echo '</body></head>';
		return;

	} else {

		
		$table  = "hospitales";
		$search = "id_hospital";
		$value	= $_COOKIE["surcursal"];
		$select = "nombre";
    	$hospital = TemplateModel::mdlTableSelectSearchSingle($table, $search, $value, $select);

	}

	?>

	<div class="container">
		
		<header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
			<a class="d-flex align-items-center col-md-3 mb-2 mb-md-0 text-dark text-decoration-none">
				<img src="<?=TemplateController::path()?>assets/images/fonasa.jpg" class="bi me-2" width="80" height="80">
			</a>


			<div class="col-md-6 text-end">
				<font size="6">
					Bienvenido a <b><?= $hospital["nombre"] ?></b>
					<button type="button" class="btn btn-secondary" id="cambiarSurcursal">Cambiar</button>
				</font>
				
			</div>
		</header>

		<div class="row">

			<div class="col-xs-12 col-md-3">

				<ul class="simple-list-menu list-group">
					
					<a class="list-group-item list-group-item-action <?php if($routesArray[1] == null) echo "active";?>" href="/">
						Inicio
					</a>

					<a class="list-group-item list-group-item-action <?php if($routesArray[1] == "pacientes") echo "active";?>" href="/pacientes">
						Pacientes
					</a>

					<a class="list-group-item list-group-item-action <?php if($routesArray[1] == "mayor-riesgo") echo "active";?>" href="/mayor-riesgo">
						Pacientes Mayor Riesgo 
					</a>

					<a class="list-group-item list-group-item-action <?php if($routesArray[1] == "fumadores-urgentes") echo "active";?>" href="/fumadores-urgentes">
						Pacientes Fumadores Urgentes
					</a>

					<a class="list-group-item list-group-item-action <?php if($routesArray[1] == "mas-atendidos") echo "active";?>" href="/mas-atendidos">
						Pacientes Mas atendidos
					</a>

					<a class="list-group-item list-group-item-action <?php if($routesArray[1] == "mas-ancianos") echo "active";?>" href="/mas-ancianos">
						Paciente mas anciano 
					</a>
	          
				</ul>

			</div>

			<div class="col-xs-12 col-md-9"> 

				<?php 
				/*=============================================
				LISTA BLANCA DE URL'S AMIGABLES
				=============================================*/

				if($routesArray[1] == null || $routesArray[1] == "pacientes" || $routesArray[1] == 'mayor-riesgo' || $routesArray[1] == 'fumadores-urgentes' || $routesArray[1] == 'mas-atendidos' || $routesArray[1] == "mas-ancianos"){						 

					 
					if ($routesArray[1] == null) {
						include 'pages/inicio.php';
					} else if ($routesArray[1] == 'mayor-riesgo' || 
						$routesArray[1] == 'fumadores-urgentes'  || 
						$routesArray[1] == 'mas-atendidos' 		 || 
						$routesArray[1] == "mas-ancianos") {
						
						include "pages/lista.php";
					} else {

						include "pages/".$routesArray[1].".php";

					}					

				} else {

					include "pages/404.php";

				}


				?>

			</div>

		</div>

	</div>
	<input type="hidden" id="rutaOculta" value="<?=$routesArray[1]?>">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
	<script src="<?=TemplateController::path()?>assets/js/main.js" ></script>
	<script src="<?=TemplateController::path()?>assets/js/bootstrap.bundle.min.js" ></script>
	

</body>
</html>