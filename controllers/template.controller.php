<?php 

if ( 	isset($_POST["ctr_agregar_datos_paciente"])  	|| 	
		isset($_POST["ctr_consulta_datos_paciente"]) 	|| 
		isset($_POST["ctr_actualizar_datos_paciente"]) 	||
		isset($_POST["ctr_eliminar_datos_paciente"])	||
		isset($_POST["mostrar_pacientes"])              ||
		isset($_POST["mostrar_consultas"]) 				||
		isset($_POST["ctr_actualizar_estados"])         ||
		isset($_POST["ctr_reiniciar_ejercicio"])        ||
		isset($_POST["ctr_liberar_consultorio"])        ||
		isset($_POST["ctr_liberar_consultas"])) {
		
	include "../models/template.model.php";

}


class TemplateController{

	// * Ubicacion de la ruta (url) para consultas externas

	static public function path(){

		return "views/";

	}

	// * Conexion principal con la plantilla

	public function index(){

		include "views/template.php";

	} 

	// * Determinar valor maximo de cualquier tabla

	static public function ctrValorMaximo($table, $column){
		
		return TemplateModel::mdlValorMaximo($table, $column);

	}

	/*========================================
	=            MODULO PACIENTES            =
	========================================*/

	// * Mostrar cualquier tabla dependiendo del requerimiento

	static public function ctrMostrar($type, $table, $select, $item, $value, $orderBy, $orderMode){

		switch ($type) {
			case 'todos':
				return TemplateModel::mdlTableSelect($table, $select, $item, $value, $orderBy, $orderMode);
			break;
			case 'individual':
				return TemplateModel::mdlTableSelectSearchSingle($table, $item, $value, $select);
			break;
			case 'listado':
				return TemplateModel::mdlTableSelectOrderBy($table, $select, $item, $value, $orderBy, $orderMode);
			break;
			case 'fumador-urgente':
				return TemplateModel::mdlConsultaPacienteFumadorUrgente($item, $value, $orderBy, $orderMode);
			break;
		}

	}

	/*===================================================
	=            Funciones de consultas ajax            =
	===================================================*/

	// * Agregar Paciente

	public $ctr_agregar_datos_paciente;

	public function ctrAgregarPaciente(){

		$datos = json_decode($this->ctr_agregar_datos_paciente, true);

		$respuesta = TemplateModel::mdlAgregarPaciente($datos);

		echo json_encode($respuesta);
	}

	// * consultar datos de paciente

	public $ctr_consulta_datos_paciente;

	public function ctrColsultaPaciente(){

		$id = $this->ctr_consulta_datos_paciente;

		$respuesta = TemplateModel::mdlConsultaPaciente($id, null);

		echo json_encode($respuesta);
	}

	// * Actualizar datos de paciente

	public $ctr_actualizar_datos_paciente;

	public function ctrActualizarPaciente(){

		$datos = json_decode($this->ctr_actualizar_datos_paciente, true);

		$respuesta = TemplateModel::mdlActualizarPaciente($datos);

		echo json_encode($respuesta);
	}

	// * Eliminar datos de paciente

	public $ctr_eliminar_datos_paciente;

	public function ctrEliminarPaciente(){

		$id = $this->ctr_eliminar_datos_paciente;

		$respuesta = TemplateModel::mdlDinamicDelete("pacientes", "id_paciente", $id);

		echo json_encode($respuesta);
	}

	// * Mostrar datos de pacientes

	public $ctr_mostrar_optimizado;
	public function ctrMostrarPacientes(){

		if ($this->ctr_mostrar_optimizado == 1) {
			$respuesta = TemplateModel::mdlTableSelectOrderBy("pacientes", "*", "id_hospital,atendido", $_COOKIE["surcursal"].','.$estado, "prioridad", "DESC");
		} else {
			$respuesta = TemplateModel::mdlTableSelectOrderBy("pacientes", "*", "id_hospital,atendido", $_COOKIE["surcursal"].','.$estado, "riesgo", "DESC");
		}

		echo json_encode($respuesta);

	}

	// * Mostrar datos de consultorios

	public function ctrMostrarConsultas(){

		$respuesta = TemplateController::ctrMostrar("todos", "consultas", "*", "id_hospital", $_COOKIE["surcursal"], "id_consulta", "ASC");
		echo json_encode($respuesta);

	}

	// * Actualizar estados para la atencion

	public $ctr_actualizar_estados;

	public function ctrActualizarEstados(){

		$datos = json_decode($this->ctr_actualizar_estados, true);

		if ($datos["id_consulta"] != 0)
			$respuesta = TemplateModel::mdlActualizarConsulta($datos["id_consulta"], $datos["id_paciente"]);

		$respuesta = TemplateModel::mdlActualizarDinamic("pacientes", "estado", $datos["estado_paciente"], "id_paciente", $datos["id_paciente"]);

		echo json_encode($respuesta);
	}

	// * Reiniciar el ejercicio para volver a usar

	public function ctrReiniciarEjercicio(){

		$estado_paciente = TemplateModel::mdlResetDinamic("pacientes", "estado", 0);
		$estado_paciente = TemplateModel::mdlResetDinamic("pacientes", "atendido", 0);
		$estado_consulta = TemplateModel::mdlResetDinamic("consultas", "estado", "desocupado");
		$cantidad_pacientes = TemplateModel::mdlResetDinamic("consultas", "cantidad_pacientes", 0);
		$paciente_consulta = TemplateModel::mdlResetDinamic("consultas", "id_paciente_consulta", null);

		echo json_encode($paciente_consulta);
	}

	// * Liberar Consultorio Individual

	public $ctr_liberar_consultorio;

	public function ctrLiberarConsulta(){

		$datos = json_decode($this->ctr_liberar_consultorio, true);

		$liberar_paciente = TemplateModel::mdlActualizarDinamic("pacientes", "estado", 3, "id_paciente", $datos["id_paciente"]);
		$liberar_paciente = TemplateModel::mdlActualizarDinamic("pacientes", "atendido", 1, "id_paciente", $datos["id_paciente"]);

		$actualizar_consultorio = TemplateModel::mdlActualizarDinamic("consultas", "estado", "en espera", "id_consulta", $datos["id_consulta"]);

		$cantidad_pacientes = TemplateModel::mdlTableSelectSearchSingle("consultas", "id_consulta", $datos["id_consulta"], "cantidad_pacientes");

		$actualizar_consultorio = TemplateModel::mdlActualizarDinamic("consultas", "cantidad_pacientes", $cantidad_pacientes[0]+1, "id_consulta", $datos["id_consulta"]);

		$actualizar_consultorio = TemplateModel::mdlActualizarDinamic("consultas", "id_paciente_consulta", null, "id_consulta", $datos["id_consulta"]);
			

		echo json_encode($liberar_paciente);
	}

	// * Liberar Consultorios
	
	public function ctrLiberarConsultas(){

		$consultas = TemplateController::ctrMostrar("todos", "consultas", "*", "id_hospital", $_COOKIE["surcursal"], "id_consulta", "ASC");

		foreach ($consultas as $key => $value) {
			if ($value["estado"] == "ocupado") {
				$actualizar_consulta = TemplateModel::mdlActualizarDinamic("consultas", "cantidad_pacientes", $value["cantidad_pacientes"]+1, "id_consulta", $value["id_consulta"]);
				$liberar_paciente = TemplateModel::mdlActualizarDinamic("pacientes", "estado", 3, "id_paciente", $value["id_paciente_consulta"]);
				$liberar_paciente = TemplateModel::mdlActualizarDinamic("pacientes", "atendido", 1, "id_paciente", $value["id_paciente_consulta"]);
				$actualizar_consulta = TemplateModel::mdlActualizarDinamic("consultas", "estado", "en espera", "id_consulta", $value["id_consulta"]);
				$actualizar_consulta = TemplateModel::mdlActualizarDinamic("consultas", "id_paciente_consulta", null, "id_consulta", $value["id_consulta"]);
			}
		}		

		echo json_encode("ok");
	}

}

/*========================================================================================
=            Hacer llamado de la funcion para retornar la informacion deseada            =
========================================================================================*/


if(isset($_POST["ctr_agregar_datos_paciente"])){

	$response = new TemplateController();
	$response -> ctr_agregar_datos_paciente = $_POST["ctr_agregar_datos_paciente"];
	$response -> ctrAgregarPaciente();

}

if(isset($_POST["ctr_consulta_datos_paciente"])){

	$response = new TemplateController();
	$response -> ctr_consulta_datos_paciente = $_POST["ctr_consulta_datos_paciente"];
	$response -> ctrColsultaPaciente();

}

if(isset($_POST["ctr_actualizar_datos_paciente"])){

	$response = new TemplateController();
	$response -> ctr_actualizar_datos_paciente = $_POST["ctr_actualizar_datos_paciente"];
	$response -> ctrActualizarPaciente();

}

if(isset($_POST["ctr_eliminar_datos_paciente"])){

	$response = new TemplateController();
	$response -> ctr_eliminar_datos_paciente = $_POST["ctr_eliminar_datos_paciente"];
	$response -> ctrEliminarPaciente();

}

if(isset($_POST["mostrar_pacientes"])){

	$response = new TemplateController();
	$response -> ctr_mostrar_optimizado = $_POST["ctr_mostrar_optimizado"];
	$response -> ctrMostrarPacientes();

}

if(isset($_POST["mostrar_consultas"])){

	$response = new TemplateController();
	$response -> ctrMostrarConsultas();

}


if(isset($_POST["ctr_actualizar_estados"])){

	$response = new TemplateController();
	$response -> ctr_actualizar_estados = $_POST["ctr_actualizar_estados"];
	$response -> ctrActualizarEstados();

}

if(isset($_POST["ctr_reiniciar_ejercicio"])){

	$response = new TemplateController();
	$response -> ctr_reiniciar_ejercicio = $_POST["ctr_reiniciar_ejercicio"];
	$response -> ctrReiniciarEjercicio();

}

if(isset($_POST["ctr_liberar_consultorio"])){

	$response = new TemplateController();
	$response -> ctr_liberar_consultorio = $_POST["ctr_liberar_consultorio"];
	$response -> ctrLiberarConsulta();

}

if(isset($_POST["ctr_liberar_consultas"])){

	$response = new TemplateController();
	$response -> ctrLiberarConsultas();

}