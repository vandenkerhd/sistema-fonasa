// * Referencias globales en el HTML
const rutaOculta = $("#rutaOculta").val();
const btnCambiarSurcursal = document.querySelector('#cambiarSurcursal');

// * Funciones globales
delete_cookie = (name) => {
    document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
};

get_cookie = (name) => {
	let cookie = {};
	document.cookie.split(';').forEach(function(el) {
		let [key,value] = el.split('=');
		cookie[key.trim()] = value;
	})

	return cookie[name];
}

// * Eventos globales
btnCambiarSurcursal.addEventListener('click', () => {
	fncSweetAlert("confirm", "question", "Confirme para cambiar de surcursal", 7000).then(resp=>{
		if (resp) {
			delete_cookie("surcursal");
			window.location.href = '/';
		}
	});
});

if (rutaOculta == "") {

	// * Referencias en el HTML
	const btnAtenderPaciente = document.querySelector('#btnAtenderPaciente');
	const btnReiniciarEjercicio = document.querySelector('#btnReiniciarEjercicio');
	const btnLiberarConsultas = document.querySelector('#btnLiberarConsultas');
	const btnOptimizarAtencion = document.querySelector('#btnOptimizarAtencion');

	//Funciones
	fncMostrarConsultas = () => {

		let addSalaConsultas='';

		let datos = new FormData();
		datos.append("mostrar_consultas", true);

		$.ajax({
			url:"controllers/template.controller.php",
			method: "POST",
			"data": datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function(respuesta){ 
				
				$("#SalaConsultas > tbody").empty();
				respuesta.map((consultorio) => {					
					let paciente = "-";
					
					if (consultorio.id_paciente_consulta != null) {

						let datos = new FormData();
						datos.append("ctr_consulta_datos_paciente", consultorio.id_paciente_consulta);

						$.ajax({
							url:"controllers/template.controller.php",
							method: "POST",
							"data": datos,
							cache: false,
							contentType: false,
							processData: false,
							dataType: "json",
							success: function(respuesta){ 

								let colorTag = (consultorio.estado == "ocupado") ? 'bg-success' : 'bg-secondary';
								let colorButton = (consultorio.estado == "ocupado") ? 'danger' : 'secondary';
								let disabled = (consultorio.estado == "desocupado") ? 'disabled' : '';
								
								let c = `<tr>
										<td scope="row"><b>${consultorio.tipo_consulta}</b></td>
										<td>${consultorio.nombre_especialista}</td><td>${consultorio.cantidad_pacientes}</td>
										<td><span class="badge ${colorTag}">${consultorio.estado}</span></td>
										<td>${respuesta.nombre}</td>
										<td><button type="button" class="btn btn-${colorButton} liberarConsulta" updateItem = '${consultorio.id_consulta}' updateItem2 = '${respuesta.id_paciente}' nameItem = '${consultorio.tipo_consulta}' ${disabled}>Liberar</button></td>
									</tr>`;

								$("#SalaConsultas > tbody").append(c);

								addSalaConsultas += c;
								
							}

						});

					} else {

						let c = `<tr>
									<td scope="row"><b>${consultorio.tipo_consulta}</b></td>
									<td>${consultorio.nombre_especialista}</td><td>${consultorio.cantidad_pacientes}</td>
									<td><span class="badge bg-secondary">${consultorio.estado}</span></td>
									<td>${paciente}</td>
									<td><button type="button" class="btn btn-secondary">Liberar</button></td>
								</tr>`;

						$("#SalaConsultas > tbody").append(c);

						addSalaConsultas += c;

					}
					
				});

			}

		});

	}

	fncMostrarPacientes = () => {

		let addSalaPendiente='';
		let addSalaEspera='';
		let optimizado = (localStorage.getItem("optimizar")) ? localStorage.getItem("optimizar") : 0;

		let datos = new FormData();
		datos.append("mostrar_pacientes", true);
		datos.append("ctr_mostrar_optimizado", optimizado);

		$.ajax({
			url:"controllers/template.controller.php",
			method: "POST",
			"data": datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function(respuesta){ 
				$("#salaEspera > tbody").empty();
				$("#salaPendientes > tbody").empty();


				respuesta.map((paciente) => {
					
					if (paciente.estado == 0) {

						let p = `<tr><td>${paciente.nombre}</td><td>${paciente.edad}</td></tr>`;
						$("#salaPendientes > tbody").append(p);

						addSalaPendiente += p;  


					} else if (paciente.estado == 1) {

						let p = `<tr><td>${paciente.nombre}</td><td>${paciente.edad}</td></tr>`;
						$("#salaEspera > tbody").append(p);

						addSalaEspera += p;  

					}

				});

			}

		});

		if (!localStorage.getItem("optimizar")) {

			btnOptimizarAtencion.classList.remove("btn-success");
			btnOptimizarAtencion.classList.add("btn-danger");

		} else {

			btnOptimizarAtencion.classList.remove("btn-danger");
			btnOptimizarAtencion.classList.add("btn-success");

		}

	}

	fncListaPacientes = () => {

		return new Promise( ( resolve, reject ) =>{

			let datos = new FormData();
			datos.append("mostrar_pacientes", true);

			$.ajax({
				url:"controllers/template.controller.php",
				method: "POST",
				"data": datos,
				cache: false,
				contentType: false,
				processData: false,
				dataType: "json",
				success: function(respuesta){ 

					resolve(respuesta);

				}

			});

		});
		
	}

	fncListaConsultas = () => {

		return new Promise( ( resolve, reject ) =>{

			let datos = new FormData();
			datos.append("mostrar_consultas", true);

			$.ajax({
				url:"controllers/template.controller.php",
				method: "POST",
				"data": datos,
				cache: false,
				contentType: false,
				processData: false,
				dataType: "json",
				success: function(respuesta){ 

					resolve(respuesta);

				}

			});

		});

	}


	fncAtenderPacientes = (type) => {

		fncListaConsultas().then((resp2) => {

			fncListaPacientes().then((resp) => {

				const listaConsultas = resp2;
				const listaPacientes = resp;

				let lista_espera = [];
				let lista_pendiente = [];

				for (let paciente of listaPacientes) {

					if (paciente.estado == 1) {

						lista_espera.push(paciente);

					} else if (paciente.estado == 0) {

						lista_pendiente.push(paciente);

					}

				}

				listaConsultas.map( (consulta) => {
										
					if (consulta.estado == "desocupado" || consulta.estado == "en espera") {

						if (consulta.tipo_consulta == "urgencia") {

							if (lista_espera.length > 0) {

								for (i in lista_espera) {

									if (lista_espera[i].prioridad >= 4) {

										let ids = {

											id_consulta : consulta.id_consulta,
											id_paciente : lista_espera[i].id_paciente,
											estado_paciente : 2

										};

										let datos = new FormData();
										datos.append("ctr_actualizar_estados", JSON.stringify(ids));

										$.ajax({
											url:"controllers/template.controller.php",
											method: "POST",
											"data": datos,
											cache: false,
											contentType: false,
											processData: false,
											dataType: "json",
											success: function(respuesta){ 
												
												console.log("respuesta", respuesta);
												
												

											}

										});

										lista_espera.splice(i, 1);
										break;

									} else if (lista_pendiente.length > 0) {

										for (i in lista_pendiente) {

											if (lista_pendiente[i].prioridad >= 4) {

												let ids = {

													id_consulta : consulta.id_consulta,
													id_paciente : lista_pendiente[i].id_paciente,
													estado_paciente : 2

												};

												let datos = new FormData();
												datos.append("ctr_actualizar_estados", JSON.stringify(ids));

												$.ajax({
													url:"controllers/template.controller.php",
													method: "POST",
													"data": datos,
													cache: false,
													contentType: false,
													processData: false,
													dataType: "json",
													success: function(respuesta){ 

														console.log("respuesta", respuesta);



													}

												});
												lista_pendiente.splice(i, 1);
												break;

											}
										}


									}

								}

							} else {

								for (i in lista_pendiente) {

									if (lista_pendiente[i].prioridad >= 4) {

										let ids = {

											id_consulta : consulta.id_consulta,
											id_paciente : lista_pendiente[i].id_paciente,
											estado_paciente : 2

										};

										let datos = new FormData();
										datos.append("ctr_actualizar_estados", JSON.stringify(ids));

										$.ajax({
											url:"controllers/template.controller.php",
											method: "POST",
											"data": datos,
											cache: false,
											contentType: false,
											processData: false,
											dataType: "json",
											success: function(respuesta){ 

												console.log("respuesta", respuesta);



											}

										});
										lista_pendiente.splice(i, 1);
										break;

									}
								}

							}

						}

						if (consulta.tipo_consulta == "cgi") {
							
							if (lista_espera.length > 0) {

								for (i in lista_espera) {

									if (lista_espera[i].edad >= 16) {

										let ids = {

											id_consulta : consulta.id_consulta,
											id_paciente : lista_espera[i].id_paciente,
											estado_paciente : 2

										};

										let datos = new FormData();
										datos.append("ctr_actualizar_estados", JSON.stringify(ids));

										$.ajax({
											url:"controllers/template.controller.php",
											method: "POST",
											"data": datos,
											cache: false,
											contentType: false,
											processData: false,
											dataType: "json",
											success: function(respuesta){ 
												
												console.log("respuesta", respuesta);
												
												

											}

										});
										lista_espera.splice(i, 1);
										break;

									} else if (lista_pendiente.length > 0) {

										for (i in lista_pendiente) {
											console.log("i2", i);
											if (lista_pendiente[i].edad >= 16) {

												let ids = {

													id_consulta : consulta.id_consulta,
													id_paciente : lista_pendiente[i].id_paciente,
													estado_paciente : 2

												};

												let datos = new FormData();
												datos.append("ctr_actualizar_estados", JSON.stringify(ids));

												$.ajax({
													url:"controllers/template.controller.php",
													method: "POST",
													"data": datos,
													cache: false,
													contentType: false,
													processData: false,
													dataType: "json",
													success: function(respuesta){ 
														
														console.log("respuesta", respuesta);
														
														

													}

												});
												lista_pendiente.splice(i, 1);
												break;

											}
										}


									}

								}

							} else {

								for (i in lista_pendiente) {

									if (lista_pendiente[i].edad >= 16) {

										let ids = {

											id_consulta : consulta.id_consulta,
											id_paciente : lista_pendiente[i].id_paciente,
											estado_paciente : 2

										};

										let datos = new FormData();
										datos.append("ctr_actualizar_estados", JSON.stringify(ids));

										$.ajax({
											url:"controllers/template.controller.php",
											method: "POST",
											"data": datos,
											cache: false,
											contentType: false,
											processData: false,
											dataType: "json",
											success: function(respuesta){ 

												console.log("respuesta", respuesta);



											}

										});
										lista_pendiente.splice(i, 1);
										break;

									}
								}

							}

						}

						if (consulta.tipo_consulta == "pediatria") {

								if (lista_espera.length > 0) {

									for (i in lista_espera) {

										if (lista_espera[i].edad <= 15) {

											let ids = {

												id_consulta : consulta.id_consulta,
												id_paciente : lista_espera[i].id_paciente

											};

											let datos = new FormData();
											datos.append("ctr_actualizar_estados", JSON.stringify(ids));

											$.ajax({
												url:"controllers/template.controller.php",
												method: "POST",
												"data": datos,
												cache: false,
												contentType: false,
												processData: false,
												dataType: "json",
												success: function(respuesta){ 
													
													console.log("respuesta", respuesta);
													
													

												}

											});

											break;

										} else if (lista_pendiente.length > 0) {

											for (i in lista_pendiente) {

												if (lista_pendiente[i].edad <= 15) {

													let ids = {

														id_consulta : consulta.id_consulta,
														id_paciente : lista_pendiente[i].id_paciente,
														estado_paciente : 2

													};

													let datos = new FormData();
													datos.append("ctr_actualizar_estados", JSON.stringify(ids));

													$.ajax({
														url:"controllers/template.controller.php",
														method: "POST",
														"data": datos,
														cache: false,
														contentType: false,
														processData: false,
														dataType: "json",
														success: function(respuesta){ 
															
															console.log("respuesta", respuesta);
															
															

														}

													});
													lista_pendiente.splice(i, 1);
													break;

												}
											
											}

										} 

									} 

								} else {

									for (i in lista_pendiente) {

										if (lista_pendiente[i].edad <= 15) {

											let ids = {

												id_consulta : consulta.id_consulta,
												id_paciente : lista_pendiente[i].id_paciente,
												estado_paciente : 2

											};

											let datos = new FormData();
											datos.append("ctr_actualizar_estados", JSON.stringify(ids));

											$.ajax({
												url:"controllers/template.controller.php",
												method: "POST",
												"data": datos,
												cache: false,
												contentType: false,
												processData: false,
												dataType: "json",
												success: function(respuesta){ 
													
													console.log("respuesta", respuesta);
													
													

												}

											});
											lista_pendiente.splice(i, 1);
											break;

										}
										
									}

								}

						} 

					}

					if (consulta.estado == "ocupado") {

						if (localStorage.getItem("optimizar")) {

							lista_pendiente.sort((a, b) => a.riesgo - b.riesgo);

							for (i in lista_pendiente) {

								let ids = {

									id_consulta : 0,
									id_paciente : lista_pendiente[i].id_paciente,
									estado_paciente : 1

								};

								let datos = new FormData();
								datos.append("ctr_actualizar_estados", JSON.stringify(ids));

								$.ajax({
									url:"controllers/template.controller.php",
									method: "POST",
									"data": datos,
									cache: false,
									contentType: false,
									processData: false,
									dataType: "json",
									success: function(respuesta){ 

										console.log("respuesta", respuesta);

									}

								});
								lista_pendiente.splice(i, 1);
								break;

							}

						} else {

							if (consulta.tipo_consulta == "urgencia") {

								if (lista_pendiente.length > 0) {

									for (i in lista_pendiente) {

										if (lista_pendiente[i].prioridad >= 4) {

											let ids = {

												id_consulta : 0,
												id_paciente : lista_pendiente[i].id_paciente,
												estado_paciente : 1

											};

											let datos = new FormData();
											datos.append("ctr_actualizar_estados", JSON.stringify(ids));

											$.ajax({
												url:"controllers/template.controller.php",
												method: "POST",
												"data": datos,
												cache: false,
												contentType: false,
												processData: false,
												dataType: "json",
												success: function(respuesta){ 

													console.log("respuesta", respuesta);

												}

											});
											lista_pendiente.splice(i, 1);
											break;

										}
									}

								}

							}

							if (consulta.tipo_consulta == "cgi") {
								
								if (lista_pendiente.length > 0) {

									for (i in lista_pendiente) {
										console.log("i2", i);
										if (lista_pendiente[i].edad >= 16) {

											let ids = {

												id_consulta : 0,
												id_paciente : lista_pendiente[i].id_paciente,
												estado_paciente : 1

											};

											let datos = new FormData();
											datos.append("ctr_actualizar_estados", JSON.stringify(ids));

											$.ajax({
												url:"controllers/template.controller.php",
												method: "POST",
												"data": datos,
												cache: false,
												contentType: false,
												processData: false,
												dataType: "json",
												success: function(respuesta){ 

													console.log("respuesta", respuesta);

												}

											});
											lista_pendiente.splice(i, 1);
											break;

										}
									}


								} 

							}

							if (consulta.tipo_consulta == "pediatria") {

								if (lista_pendiente.length > 0) {

									for (i in lista_pendiente) {

										if (lista_pendiente[i].edad <= 15) {

											let ids = {

												id_consulta : 0,
												id_paciente : lista_pendiente[i].id_paciente,
												estado_paciente : 1

											};

											let datos = new FormData();
											datos.append("ctr_actualizar_estados", JSON.stringify(ids));

											$.ajax({
												url:"controllers/template.controller.php",
												method: "POST",
												"data": datos,
												cache: false,
												contentType: false,
												processData: false,
												dataType: "json",
												success: function(respuesta){ 

													console.log("respuesta", respuesta);



												}

											});
											lista_pendiente.splice(i, 1);
											break;

										}

									}

								} 

							}
						}

					}
					
				})

				let frase = (type == "liberar-atender") ? "Consultas liberadas, atendiendo" : "Atendiendo";

				fncSweetAlert("notify-reload", "success", frase, 1000);
				btnAtenderPaciente.disabled = true;

			});

		});

	}

	fncReiniciarEjercicio = () => {

		let datos = new FormData();
		datos.append("ctr_reiniciar_ejercicio", true);

		$.ajax({
			url:"controllers/template.controller.php",
			method: "POST",
			"data": datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function(respuesta){ 

				fncSweetAlert("notify-reload", "success", "Empezamos de nuevo", 1000);
				btnReiniciarEjercicio.disabled = true;		

			}

		});


	}

	fncLiberarConsultas = () => {

		let datos = new FormData();
		datos.append("ctr_liberar_consultas", true);

		$.ajax({
			url:"controllers/template.controller.php",
			method: "POST",
			"data": datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function(respuesta){ 

				btnLiberarConsultas.disabled = true;		

				fncAtenderPacientes('liberar-atender');

			}

		});

	}

	fncMostrarPacientes();
	fncMostrarConsultas();

	btnAtenderPaciente.addEventListener('click', () => { fncAtenderPacientes('atender'); });

	btnReiniciarEjercicio.addEventListener('click', () => { fncReiniciarEjercicio(); });

	setTimeout(() => {

		const btnLiberarConsulta = document.querySelectorAll('.liberarConsulta');

		btnLiberarConsulta.forEach((e) => {
		e.addEventListener('click', (event) => {
			
				let id_consulta = event.target.getAttribute('updateitem');
				let id_paciente = event.target.getAttribute('updateitem2');
				let nombre = event.target.getAttribute('nameItem');
				
				fncSweetAlert("confirm", "question", "Desea liberar consultorio: "+nombre+"?", null).then(resp=>{
					
					if (resp) {
						
						let ids = {

							id_consulta, 
							id_paciente

						};

						let datos = new FormData();
						datos.append("ctr_liberar_consultorio", JSON.stringify(ids));


						$.ajax({
							url:"controllers/template.controller.php",
							method: "POST",
							data: datos,
							cache: false,
							contentType: false,
							processData: false,
							dataType: "json",
							success: function(respuesta){ 
								console.log("respuesta", respuesta);

								if (respuesta == "ok") 
									fncSweetAlert("notify-reload", "success", "Consultorio: "+nombre+" liberado", 2000);

							}

						});

					}

				});

			});

		});

	}, 500);

	btnLiberarConsultas.addEventListener('click', () => { fncLiberarConsultas(); });

	btnOptimizarAtencion.addEventListener('click', () => { 

		if (!localStorage.getItem("optimizar")) {

			localStorage.setItem("optimizar",  1);
			fncSweetAlert("notify-reload", "success", "Optimizador activado", 1000);

		} else {

			localStorage.removeItem("optimizar");
			fncSweetAlert("notify-reload", "success", "Optimizador desactivado", 1000);
			

		}

	});
	
}

if (rutaOculta == "pacientes") {

	// * Referencias en el HTML
	const btnAddPaciente = document.querySelector('#btnAddPaciente');
	const btnGuardarPaciente = document.querySelector('#btnGuardarPaciente');
	const btnEditarPaciente = document.querySelectorAll('.btnEditarPaciente');
	const btnEliminarPaciente = document.querySelectorAll('.btnEliminarPaciente');
	const btnActualizarPaciente = document.querySelector('#btnActualizarPaciente');
	const edadPacienteFormAdd = document.querySelector('#agregarEdad');
	const edadPacienteFormEdt = document.querySelector('#editarEdad');
	const esFumadorFormAdd = document.querySelector('#agregarEsFumador');
	const esFumadorFormEdt = document.querySelector('#editarEsFumador');

	// * Funciones

	fncTipoPaciente = (edad) =>{

		$('.ResetEnNuevaEdad').val('');

		if (edad >= 1 && edad <= 15) {
			$(".relPesoEstatura").show();
			$(".esFumador").hide();
			$(".aniosFumando").hide();
			$(".tieneDieta").hide();

		} else if (edad >= 16 && edad <= 40) {
			$(".relPesoEstatura").hide();
			$(".esFumador").show();
			$(".aniosFumando").hide();
			$(".tieneDieta").hide();

		} else if (edad >= 41) {
			$(".relPesoEstatura").hide();
			$(".esFumador").hide();
			$(".aniosFumando").hide();
			$(".tieneDieta").show();

		} else {
			$(".relPesoEstatura").hide();
			$(".esFumador").hide();
			$(".aniosFumando").hide();
			$(".tieneDieta").hide();
		
		}

	}

	fncEsFumador = (val) =>{

		if (val == 1) 
			$(".aniosFumando").show();
		else
			$(".aniosFumando").hide();

	}
	
	// * Eventos

	btnAddPaciente.addEventListener('click', () => { document.getElementById("formAddPaciente").reset(); fncTipoPaciente(0); });

	edadPacienteFormAdd.addEventListener('blur', () => { fncTipoPaciente(edadPacienteFormAdd.value); });

	edadPacienteFormEdt.addEventListener('blur', () => { fncTipoPaciente(edadPacienteFormEdt.value); });

	esFumadorFormAdd.addEventListener('change', () => { fncEsFumador(esFumadorFormAdd.value); });

	esFumadorFormEdt.addEventListener('change', () => { fncEsFumador(esFumadorFormEdt.value); });

	btnGuardarPaciente.addEventListener('click', () => { 
		
		let nroHistClinica = $("#agregarNroHistoriaClinica").val();
		let nombre = $("#agregarNombre").val();
		let edad = $("#agregarEdad").val();
		let esFumador = $("#agregarEsFumador").val();
		let aniosFumando = $("#agregarAniosFumando").val(); 
		let tieneDieta = $("#agregarTieneDieta").val();
		let prioridad = 0;
		let relPesoEstatura = $("#agregarRelPesoEstatura").val();
		let riesgo = 0;

		edad = (edad != "") ? parseInt(edad) : "";
		aniosFumando = (aniosFumando != "") ? parseInt(aniosFumando) : 0;
		relPesoEstatura = (relPesoEstatura != "") ? parseInt(relPesoEstatura) : 0;

		// * calcular prioridad por edad
		if (edad <= 15) {

			prioridad = (edad <= 5) ? relPesoEstatura + 3 : 
						(edad >= 6 && edad <= 12) ? relPesoEstatura + 2 :
						(edad >= 13 && edad <= 15) ? relPesoEstatura + 1 : 0; 


		} else if (edad >= 16 && edad <= 40) {

			if (esFumador == 1) {

				prioridad = (aniosFumando / 4) + 2;

			} else {

				prioridad = 2;

			}

			
		} else if (edad >= 41) {

			if (tieneDieta == 1 && edad >= 60) {

				prioridad = (edad/20) + 4;

			} else {

				prioridad = (edad/30) + 3;

			}


		}

		riesgo = (edad <= 40) ? (edad * prioridad) / 100 : ((edad * prioridad)/100) + 5.3;

		if (edad == "" || nombre == "")
			return fncSweetAlert("notify", "error", "Todos los campos con * son obligatorios", 4000);
		if (edad <= 15 && relPesoEstatura == "") 
			return fncSweetAlert("notify", "error", "Debe indicar la relacion peso-estatura", 4000);
		if (edad <= 15 && relPesoEstatura > 4) 
			return fncSweetAlert("notify", "warning", "la relacion peso-estatura debe ser de 1 a 4", 4000);
		if (edad >= 16 && edad <= 40 && esFumador == "") 
			return fncSweetAlert("notify", "error", "Debe seleccionar si es fumador", 4000);
		if (edad >= 16 && esFumador == 1 && aniosFumando == "") 
			return fncSweetAlert("notify", "error", "Debe indicar los años que ha fumado", 4000);
		if (edad >= 41 && tieneDieta == "") 
			return fncSweetAlert("notify", "error", "Debe seleccionar si tiene una dieta", 4000);
		if (aniosFumando > edad) 
			return fncSweetAlert("notify", "error", "No puede fumar mas años de los que vive", 4000);


		let datos_paciente = {

			id_hospital: get_cookie('surcursal'),
			nro_historia_clinica: nroHistClinica,
			nombre,
			edad,
			es_fumador: esFumador,
			anios_fumando: aniosFumando,
			tiene_dieta: tieneDieta,
			prioridad,
			rel_peso_estatura: relPesoEstatura,
			riesgo

		};


		let datos = new FormData();
		datos.append("ctr_agregar_datos_paciente", JSON.stringify(datos_paciente));

		$.ajax({
			url:"controllers/template.controller.php",
			method: "POST",
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function(respuesta){ 
				
				if (respuesta == "ok") {
					fncSweetAlert("notify-reload", "success", "Paciente registrado", 2000);
					btnGuardarPaciente.disabled = true;
				}

			}

		});

	});

	btnEditarPaciente.forEach((e) => {
		e.addEventListener('click', (event) => {

			document.getElementById("formEdtPaciente").reset(); 
			fncTipoPaciente(0);
	    	
	    	id_paciente = event.target.getAttribute('editItem');

	    	let datos = new FormData();
			datos.append("ctr_consulta_datos_paciente", id_paciente);

			$.ajax({
				url:"controllers/template.controller.php",
				method: "POST",
				data: datos,
				cache: false,
				contentType: false,
				processData: false,
				dataType: "json",
				success: function(respuesta){ 
						
					$("#editarNroHistoriaClinica").val(respuesta.nro_historia_clinica);
					$("#editarNombre").val(respuesta.nombre);
					$("#editarEdad").val(respuesta.edad);
					if (respuesta.rel_peso_estatura != null) {
						$("#editarRelPesoEstatura").val(respuesta.rel_peso_estatura);
						$(".relPesoEstatura").show();
					}
					
					if (respuesta.es_fumador != null) {
						$("#editarEsFumador").val(respuesta.es_fumador);
						
						$(".esFumador").show();
						if (respuesta.anios_fumando != 0){
							$(".aniosFumando").show();
							$("#editarAniosFumando").val(respuesta.anios_fumando);4
						}

					}
										
					if (respuesta.tiene_dieta != null) {
						$("#editarTieneDieta").val(respuesta.tiene_dieta);
						$(".tieneDieta").show();
					}
					
					$("#idPacienteHidden").val(id_paciente);				
					
				}

			});

	    	
	    });

	});

	btnActualizarPaciente.addEventListener('click', () => { 
		
		let id = $("#idPacienteHidden").val();
		let nombre = $("#editarNombre").val();
		let edad = $("#editarEdad").val();
		let esFumador = $("#editarEsFumador").val();
		let aniosFumando = $("#editarAniosFumando").val(); 
		let tieneDieta = $("#editarTieneDieta").val();
		let prioridad = 0;
		let relPesoEstatura = $("#editarRelPesoEstatura").val();
		let riesgo = 0;

		edad = (edad != "") ? parseInt(edad) : "";
		aniosFumando = (aniosFumando != "") ? parseInt(aniosFumando) : 0;
		relPesoEstatura = (relPesoEstatura != "") ? parseInt(relPesoEstatura) : 0;

		// * calcular prioridad por edad
		if (edad <= 15) {

			prioridad = (edad <= 5) ? relPesoEstatura + 3 : 
						(edad >= 6 && edad <= 12) ? relPesoEstatura + 2 :
						(edad >= 13 && edad <= 15) ? relPesoEstatura + 1 : 0; 


		} else if (edad >= 16 && edad <= 40) {

			if (esFumador == 1) {

				prioridad = (aniosFumando / 4) + 2;

			} else {

				prioridad = 2;

			}

			
		} else if (edad >= 41) {

			if (tieneDieta == 1 && edad >= 60) {

				prioridad = (edad/20) + 4;

			} else {

				prioridad = (edad/30) + 3;

			}


		}

		riesgo = (edad <= 40) ? (edad * prioridad) / 100 : ((edad * prioridad)/100) + 5.3;

		if (edad == "" || nombre == "")
			return fncSweetAlert("notify", "error", "Todos los campos con * son obligatorios", 4000);
		if (edad <= 15 && relPesoEstatura == "") 
			return fncSweetAlert("notify", "error", "Debe indicar la relacion peso-estatura", 4000);
		if (edad <= 15 && relPesoEstatura > 4) 
			return fncSweetAlert("notify", "warning", "la relacion peso-estatura debe ser de 1 a 4", 4000);
		if (edad >= 16 && edad <= 40 && esFumador == "") 
			return fncSweetAlert("notify", "error", "Debe seleccionar si es fumador", 4000);
		if (edad >= 16 && esFumador == 1 && aniosFumando == "") 
			return fncSweetAlert("notify", "error", "Debe indicar los años que ha fumado", 4000);
		if (edad >= 41 && tieneDieta == "") 
			return fncSweetAlert("notify", "error", "Debe seleccionar si tiene una dieta", 4000);
		if (aniosFumando > edad) 
			return fncSweetAlert("notify", "error", "No puede fumar mas años de los que vive", 4000);


		let datos_paciente = {

			id_paciente: id,
			nombre,
			edad,
			es_fumador: esFumador,
			anios_fumando: aniosFumando,
			tiene_dieta: tieneDieta,
			prioridad,
			rel_peso_estatura: relPesoEstatura,
			riesgo

		};


		let datos = new FormData();
		datos.append("ctr_actualizar_datos_paciente", JSON.stringify(datos_paciente));

		$.ajax({
			url:"controllers/template.controller.php",
			method: "POST",
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function(respuesta){ 
				console.log("respuesta", respuesta);
				
				if (respuesta == "ok") {
					fncSweetAlert("notify-reload", "success", "Paciente actualizado", 2000);
					btnActualizarPaciente.disabled = true;
				}

			}

		});

	});

	btnEliminarPaciente.forEach((e) => {
		e.addEventListener('click', (event) => {

			let id_paciente = event.target.getAttribute('removeItem');
			let nombre = event.target.getAttribute('nameItem');

			fncSweetAlert("confirm", "question", "Desea eliminar a paciente: "+nombre+"?", 7000).then(resp=>{
				
				if (resp) {
					
					let datos = new FormData();
					datos.append("ctr_eliminar_datos_paciente", id_paciente);

					$.ajax({
						url:"controllers/template.controller.php",
						method: "POST",
						data: datos,
						cache: false,
						contentType: false,
						processData: false,
						dataType: "json",
						success: function(respuesta){ 

							if (respuesta == "ok") 
								fncSweetAlert("notify-reload", "success", "Paciente: "+nombre+" eliminado", 2000);

						}

					});

				}

			});

		});

	});

}

