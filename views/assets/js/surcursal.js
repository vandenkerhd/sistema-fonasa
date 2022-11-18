// * Referencias en el HTML
const btnEnviarSurcursal = document.querySelector('#enviarSurcursal');
const selectSurcursal = document.querySelector('#surcursal');

// * Eventos
btnEnviarSurcursal.addEventListener('click', () => {
	if (selectSurcursal.value != "") {
		document.cookie = `surcursal=${selectSurcursal.value}`;
		location.reload();
	} else {
		fncSweetAlert("notify", "warning", "Debe seleccionar una surcursal", 7000);
	}
});