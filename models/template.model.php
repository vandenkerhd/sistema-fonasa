<?php 
require_once "conexion.php";

class TemplateModel{

	static public function mdlTableSelect($table, $select, $item, $value, $orderBy, $orderMode){

		$select = ($select == null) ? '*' : $select ;

		$sql = ($item == null) ? "SELECT $select FROM $table" : "SELECT $select FROM $table WHERE $item = :$item ORDER BY $orderBy $orderMode";

		$stmt = Conexion::conectar()->prepare($sql);
		
		if ($item != null)
			$stmt->bindParam(":".$item, $value, PDO::PARAM_INT);
	
		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt-> close();

		$stmt = null;

	}

	static public function mdlTableSelectSearchSingle($table, $search, $value, $select){

		$select = ($select == null) ? '*' : $select ;

		$stmt = Conexion::conectar()->prepare("SELECT $select FROM $table WHERE $search = :$search");
		$stmt->bindParam(":".$search, $value, PDO::PARAM_INT);
		
		$stmt -> execute();

		return $stmt -> fetch();

		$stmt-> close();

		$stmt = null;

	}

	static public function mdlTableSelectOrderBy($table, $select, $item, $val, $orderBy, $orderMode){

		$itemToArray = explode(",", $item);
		$valueToArray = explode(",", $val);
		$linkToText = "";
		
		if(count($itemToArray) > 1){

			foreach ($itemToArray as $key => $value) {
				
				if($key > 0){

					$linkToText .= "AND ".$value." = :".$value." ";
				}

			}

		}
		
		$select = ($select == null) ? '*' : $select ;

		$sql = "SELECT $select FROM $table WHERE $itemToArray[0] = :$itemToArray[0] $linkToText ORDER BY $orderBy $orderMode";

		$stmt = Conexion::conectar()->prepare($sql);
		
		foreach ($itemToArray as $key => $value) {
			
			$stmt->bindParam(":".$value, $valueToArray[$key], PDO::PARAM_STR);
			
		}
	
		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt-> close();

		$stmt = null;

	}

	static public function mdlDinamicDelete($table, $item, $id){

		$stmt =  Conexion::conectar()->prepare("DELETE FROM $table WHERE $item = :$item");

		$stmt -> bindParam(":".$item, $id, PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";

		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;
	}

	static public function mdlValorMaximo($table, $column){

		$stmt = Conexion::conectar()->prepare("SELECT MAX($column) AS ultimoValor FROM $table");
		
		$stmt -> execute();

		return $stmt -> fetch();

		$stmt-> close();

		$stmt = null;

	}

	static public function mdlAgregarPaciente($datos){

		$link = Conexion::conectar();

		$stmt = $link->prepare("INSERT INTO pacientes(id_hospital, nombre, edad, nro_historia_clinica, prioridad, riesgo) VALUES (:id_hospital, :nombre, :edad, :nro_historia_clinica, :prioridad, :riesgo)");

		$stmt->bindParam(":id_hospital", $datos["id_hospital"], PDO::PARAM_INT);
		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":edad", $datos["edad"], PDO::PARAM_INT);
		$stmt->bindParam(":nro_historia_clinica", $datos["nro_historia_clinica"], PDO::PARAM_INT);
		$stmt->bindParam(":prioridad", $datos["prioridad"], PDO::PARAM_STR);
		$stmt->bindParam(":riesgo", $datos["riesgo"], PDO::PARAM_STR);
	
		if($stmt->execute()){

			$id_paciente = $link->lastInsertId();

			if($datos["edad"] <= 15){

				$stmt2 = $link->prepare("INSERT INTO pacientes_ninios(id_paciente, rel_peso_estatura) VALUES (:id_paciente, :rel_peso_estatura)");
				$stmt2->bindParam(":id_paciente", $id_paciente, PDO::PARAM_INT);
				$stmt2->bindParam(":rel_peso_estatura", $datos["rel_peso_estatura"], PDO::PARAM_INT);

				if($stmt2->execute()){

					return "ok";	

				}else{

					return "error_add_extra_paciente";
				
				}

			} else if($datos["edad"] >= 16 && $datos["edad"] <= 40){

				$stmt2 = $link->prepare("INSERT INTO pacientes_jovenes(id_paciente, es_fumador, anios_fumando) VALUES (:id_paciente, :es_fumador, :anios_fumando)");
				$stmt2->bindParam(":id_paciente", $id_paciente, PDO::PARAM_INT);
				$stmt2->bindParam(":es_fumador", $datos["es_fumador"], PDO::PARAM_INT);
				$stmt2->bindParam(":anios_fumando", $datos["anios_fumando"], PDO::PARAM_INT);

					if($stmt2->execute()){

					return "ok";	

				}else{

					return "error_add_extra_paciente";
				
				}

			} else if($datos["edad"] >= 41){

				$stmt2 = $link->prepare("INSERT INTO pacientes_ancianos(id_paciente, tiene_dieta) VALUES (:id_paciente, :tiene_dieta)");
				$stmt2->bindParam(":id_paciente", $id_paciente, PDO::PARAM_INT);
				$stmt2->bindParam(":tiene_dieta", $datos["tiene_dieta"], PDO::PARAM_INT);

					if($stmt2->execute()){

					return "ok";	

				}else{

					return "error_add_extra_paciente";
				
				}

			} else {

				return "ok";
			}

			

		}else{

			return $link->errorInfo();
		
		}

		$stmt->close();
		
		$stmt = null;

	}


	static public function mdlConsultaPaciente($id, $select){

		$select = ($select == null) ? '*' : $select ;

		$stmt = Conexion::conectar()->prepare("SELECT p.*, pn.rel_peso_estatura, pj.es_fumador, pj.anios_fumando, pa.tiene_dieta FROM pacientes p LEFT JOIN pacientes_ancianos pa ON pa.id_paciente = p.id_paciente LEFT JOIN pacientes_jovenes pj ON pj.id_paciente = p.id_paciente LEFT JOIN pacientes_ninios pn ON pn.id_paciente = p.id_paciente WHERE p.id_paciente = :id_paciente");
		$stmt->bindParam(":id_paciente", $id, PDO::PARAM_INT);
		
		$stmt -> execute();

		return $stmt -> fetch();

		$stmt-> close();

		$stmt = null;

	}

	static public function mdlActualizarPaciente($datos){

		$link = Conexion::conectar();

		$stmt = $link->prepare("UPDATE pacientes SET nombre = :nombre, edad = :edad, prioridad = :prioridad, riesgo = :riesgo WHERE id_paciente = :id_paciente");

		$stmt->bindParam(":id_paciente", $datos["id_paciente"], PDO::PARAM_INT);
		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":edad", $datos["edad"], PDO::PARAM_INT);
		$stmt->bindParam(":prioridad", $datos["prioridad"], PDO::PARAM_STR);
		$stmt->bindParam(":riesgo", $datos["riesgo"], PDO::PARAM_STR);
	
		if($stmt->execute()){

			if($datos["edad"] <= 15){
				
				$stmt2 = $link->prepare("UPDATE pacientes_ninios SET rel_peso_estatura = :rel_peso_estatura WHERE id_paciente = :id_paciente");
				$stmt2->bindParam(":id_paciente", $datos["id_paciente"], PDO::PARAM_INT);
				$stmt2->bindParam(":rel_peso_estatura", $datos["rel_peso_estatura"], PDO::PARAM_INT);

				if($stmt2->execute()){

					return "ok";	

				}else{

					return "error_add_extra_paciente";
				
				}

			} else if($datos["edad"] >= 16 && $datos["edad"] <= 40){

				$stmt2 = $link->prepare("UPDATE pacientes_jovenes SET es_fumador = :es_fumador, anios_fumando = :anios_fumando WHERE id_paciente = :id_paciente");
				$stmt2->bindParam(":id_paciente", $datos["id_paciente"], PDO::PARAM_INT);
				$stmt2->bindParam(":es_fumador", $datos["es_fumador"], PDO::PARAM_INT);
				$stmt2->bindParam(":anios_fumando", $datos["anios_fumando"], PDO::PARAM_INT);

					if($stmt2->execute()){

					return "ok";	

				}else{

					return "error_add_extra_paciente";
				
				}

			} else if($datos["edad"] >= 41){

				$stmt2 = $link->prepare("UPDATE pacientes_ancianos SET tiene_dieta = :tiene_dieta WHERE id_paciente = :id_paciente");
				$stmt2->bindParam(":id_paciente", $datos["id_paciente"], PDO::PARAM_INT);
				$stmt2->bindParam(":tiene_dieta", $datos["tiene_dieta"], PDO::PARAM_INT);

					if($stmt2->execute()){

					return "ok";	

				}else{

					return "error_add_extra_paciente";
				
				}

			} else {

				return "ok";
			}

			

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;

	}

	static public function mdlResetDinamic($table, $item, $value){
		
		$link = Conexion::conectar();
		
		$stmt = $link->prepare("UPDATE $table SET $item = :$item WHERE 1");
				
		$stmt->bindParam(":".$item, $value, PDO::PARAM_STR);

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";

		}

		$stmt->close();
		
		$stmt = null;
	}

	static public function mdlActualizarConsulta($id_consulta, $id_paciente){
		
		$link = Conexion::conectar();
		
		$stmt = $link->prepare("UPDATE consultas SET estado = 'ocupado', id_paciente_consulta = :id_paciente_consulta WHERE id_consulta = :id_consulta");
				
		$stmt->bindParam(":id_consulta", $id_consulta, PDO::PARAM_INT);
		$stmt->bindParam(":id_paciente_consulta", $id_paciente, PDO::PARAM_INT);

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";

		}

		$stmt->close();
		
		$stmt = null;
	}

	static public function mdlActualizarDinamic($table, $item, $value, $item2, $value2){
		
		$link = Conexion::conectar();
		
		$stmt = $link->prepare("UPDATE $table SET $item = :$item WHERE $item2 = :$item2");
				
		$stmt->bindParam(":".$item, $value, PDO::PARAM_STR);
		$stmt->bindParam(":".$item2, $value2, PDO::PARAM_STR);

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";

		}

		$stmt->close();
		
		$stmt = null;
	}

	static public function mdlConsultaPacienteFumadorUrgente($item, $val, $orderBy, $orderMode){


		$itemToArray = explode(",", $item);
		$valueToArray = explode(",", $val);
		$linkToText = "";
		
		if(count($itemToArray) > 1){

			foreach ($itemToArray as $key => $value) {
				
				if($key > 0){

					$linkToText .= "AND ".$value." = :".$value." ";
				}

			}

		}

		$sql = "SELECT p.*, pj.es_fumador, pj.anios_fumando FROM pacientes p INNER JOIN pacientes_jovenes pj ON pj.id_paciente = p.id_paciente WHERE $itemToArray[0] = :$itemToArray[0] $linkToText ORDER BY $orderBy $orderMode";

		$stmt = Conexion::conectar()->prepare($sql);
		
		foreach ($itemToArray as $key => $value) {
			
			$stmt->bindParam(":".$value, $valueToArray[$key], PDO::PARAM_STR);
			
		}
		
		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt-> close();

		$stmt = null;

	}

}

