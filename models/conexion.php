<?php

class Conexion{

	static public function informacion(){

		$infoBD = array(

			'database' => "root", 
			'user' => "",
			'pass' => "",

		);

		return $infoBD;

	}

	static public function conectar(){

		try {

			$link = new PDO(

				"mysql:host=localhost;dbname=".Conexion::informacion()["database"],
				Conexion::informacion()["user"],
				Conexion::informacion()["pass"]

			);

			$link ->exec("set names utf8");

			
		} catch (PDOException $e) {

			die("Error: ".$e->getMessage());
			
		}

		return $link;

	}

}