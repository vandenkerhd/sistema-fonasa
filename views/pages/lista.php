<?php 
if ($routesArray[1] == 'mayor-riesgo'){									
	$lista = TemplateController::ctrMostrar('listado', 'pacientes', 'nombre,edad,riesgo', "id_hospital,atendido", $_COOKIE["surcursal"].',0', 'riesgo', 'DESC');
	$title = "Pacientes con mayor riesgo";
} else if ($routesArray[1] == 'fumadores-urgentes') {
	$lista = TemplateController::ctrMostrar('fumador-urgente', null, null, "id_hospital,atendido,es_fumador", $_COOKIE["surcursal"].',0,1', 'riesgo', 'DESC');
	$title = "Fumadores Urgentes";
} else if ($routesArray[1] == 'mas-atendidos') {
	$lista = TemplateController::ctrMostrar('listado', 'consultas', 'tipo_consulta,cantidad_pacientes', "id_hospital", $_COOKIE["surcursal"], 'cantidad_pacientes', 'DESC');
	$title = "Consultorios mas atendidos";
} else if ($routesArray[1] == "mas-ancianos") {
	$lista = TemplateController::ctrMostrar('listado', 'pacientes', 'nombre,edad', "id_hospital,atendido", $_COOKIE["surcursal"].',0', 'edad', 'DESC');
	$title = "Pacientes mas ancianos";
}

?>
<h1 class="title" style="text-align: center;"><?=$title?></h1>

<table class="table">
  <thead>
<tr>

  	<?php if ($routesArray[1] == 'mayor-riesgo'): ?>
	  	<th scope="col">Nombre</th>
	    <th scope="col">Edad</th>
	    <th scope="col">Riesgo</th>
	<?php elseif ($routesArray[1] == 'fumadores-urgentes'): ?>
	    <th scope="col">Nombre</th>
	    <th scope="col">Edad</th>
	    <th scope="col">Años fumando</th>
	    <th scope="col">Riesgo</th>
    <?php elseif ($routesArray[1] == 'mas-atendidos'): ?>
	    <th scope="col">#</th>
	    <th scope="col">Consultorios</th>
	    <th scope="col">Pacientes Atendidos</th>
	<?php else: ?>
		<th scope="col">Nombre</th>
	    <th scope="col">Edad</th>
  	<?php endif ?>	
         
    </tr>
  </thead>
  <tbody>   
  	
  		<?php 

  		if ($routesArray[1] == 'mayor-riesgo'){

  			foreach ($lista as $key => $value) {
  				echo "
  					<tr>
  						<td> ".$value["nombre"]." </td>
  						<td> ".$value["edad"]." </td>
  						<td> ".round($value["riesgo"], 2)." </td>
  					</tr>";
  			}


  		} else if ($routesArray[1] == 'fumadores-urgentes'){

  			foreach ($lista as $key => $value) {
  				echo "
  					<tr>
  						<td> ".$value["nombre"]." </td>
  						<td> ".$value["edad"]." </td>
  						<td> ".$value["anios_fumando"]." </td>
  						<td> ".round($value["riesgo"], 2)." </td>
  					</tr>";
  			}


  		} else if ($routesArray[1] == 'mas-ancianos'){

  			foreach ($lista as $key => $value) {
  				echo "
  					<tr>
  						<td> ".$value["nombre"]." </td>
  						<td> ".$value["edad"]." </td>
  					</tr>";
  			}


  		} 

  		else if ($routesArray[1] == 'mas-atendidos'){

  			foreach ($lista as $key => $value) {
  				$i = $key+1;
  				echo "
  					<tr>
  						<td> ".$i." </td>
  						<td> ".$value["tipo_consulta"]." </td>
  						<td> ".$value["cantidad_pacientes"]." </td>
  					</tr>";
  			}


  		} 



  		?>
  	
  </tbody>
</table>