<?php 

$pacientes = TemplateController::ctrMostrar("todos", "pacientes", "*", "id_hospital", $_COOKIE["surcursal"], "id_paciente", "ASC");

$ultimoNroHistMed = TemplateController::ctrValorMaximo("pacientes", "nro_historia_clinica")[0] + 1;

?>

<button type="button" class="btn btn-success" data-bs-toggle="modal" id="btnAddPaciente" data-bs-target="#agregarPaciente">Agregar paciente</button>
<hr>
<br>

<!-- Tabla de pacientes-->

<table class="table">
  <thead>
    <tr>
      <th scope="col">N° Historia Clinica</th>
      <th scope="col">Nombre</th>
      <th scope="col">Edad</th>
      <th></th>
    </tr>
  </thead>
  <tbody>

    <?php if (count($pacientes) > 0): ?>

      <?php foreach ($pacientes as $key => $value): ?>

        <tr>
          <th scope="row"><?=$value["nro_historia_clinica"]?></th>
          <td><?=$value["nombre"]?></td>
          <td><?=$value["edad"]?></td>
          <td>
            <button type='button' class='btn btn-warning btn-sm mr-1 rounded-circle btnEditarPaciente' editItem='<?=$value["id_paciente"]?>' data-bs-toggle="modal" data-bs-target="#editarPaciente">
              <i class='fas fa-pencil-alt' editItem='<?=$value["id_paciente"]?>'></i>
            </button>&nbsp;
            <button type='button' class='btn btn-danger btn-sm rounded-circle btnEliminarPaciente' removeItem='<?=$value["id_paciente"]?>' nameItem='<?=$value["nombre"]?>'>
              <i class='fas fa-trash'  removeItem='<?=$value["id_paciente"]?>'nameItem='<?=$value["nombre"]?>'></i>
            </button>
          </td>
        </tr>
        
      <?php endforeach ?>

    <?php else: ?>

      <tr>
        <th colspan="4" style="text-align: center;">Sin pacientes</th>
      </tr>
      
    <?php endif ?>

    

  </tbody>
</table>

<!-- Modal Agregar Paciente-->
<div class="modal fade" id="agregarPaciente" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="agregarPacienteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formAddPaciente">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="agregarPacienteLabel">Agregar Paciente</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body add-paciente">

          <div class="mb-3">
            <label for="agregarNroHistoriaClinica" class="form-label">Nro. Historia Clinica</label>
            <input type="number" class="form-control" id="agregarNroHistoriaClinica" value="<?= $ultimoNroHistMed ?>" readonly="">
          </div>
          <div class="mb-3">
            <label for="agregarNombre" class="form-label">Nombre <span style="color: red;">*</span></label>
            <input type="text" class="form-control" id="agregarNombre">
          </div>
          <div class="mb-3">
            <label for="agregarEdad" class="form-label">Edad <span style="color: red;">*</span></label>
            <input type="number" class="form-control" id="agregarEdad">
          </div>

          <div class="mb-3 relPesoEstatura" style="display: none;">
            <label for="agregarRelPesoEstatura" class="form-label">Relacion peso estatura (1 a 4)</label>
            <input type="number" class="form-control ResetEnNuevaEdad" id="agregarRelPesoEstatura">
          </div>

          <div class="mb-3 esFumador" style="display: none;">
            <label for="agregarEsFumador" class="form-label">Es fumador?</label>
            <select class="form-select ResetEnNuevaEdad" id="agregarEsFumador">
              <option value="">Seleccione una opcion</option>
              <option value="1">Si</option>
              <option value="0">No</option>
            </select>
          </div>

          <div class="mb-3 aniosFumando" style="display: none;">
            <label for="agregarAniosFumando" class="form-label">Años Fumando</label>
            <input type="number" class="form-control ResetEnNuevaEdad" id="agregarAniosFumando">
          </div>

          <div class="mb-3 tieneDieta" style="display: none;">
            <label for="agregarTieneDieta" class="form-label">Tiene dieta?</label>
            <select class="form-select ResetEnNuevaEdad" id="agregarTieneDieta">
              <option value="">Seleccione una opcion</option>
              <option value="1">Si</option>
              <option value="0">No</option>
            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="btnGuardarPaciente">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Editar Paciente-->
<div class="modal fade" id="editarPaciente" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editarPacienteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formEdtPaciente">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="editarPacienteLabel">Editar Paciente</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <div class="mb-3">
            <label for="editarNroHistoriaClinica" class="form-label">Nro. Historia Clinica</label>
            <input type="text" class="form-control" id="editarNroHistoriaClinica" readonly="">
          </div>
          <div class="mb-3">
            <label for="editarNombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="editarNombre">
          </div>
          <div class="mb-3">
            <label for="editarEdad" class="form-label">Edad</label>
            <input type="text" class="form-control" id="editarEdad">
          </div>

          <div class="mb-3 relPesoEstatura" style="display: none;">
            <label for="editarRelPesoEstatura" class="form-label">Relacion peso estatura (1 a 4)</label>
            <input type="number" class="form-control ResetEnNuevaEdad" id="editarRelPesoEstatura">
          </div>

          <div class="mb-3 esFumador" style="display: none;">
            <label for="editarEsFumador" class="form-label">Es fumador?</label>
            <select class="form-select ResetEnNuevaEdad" id="editarEsFumador">
              <option value="">Seleccione una opcion</option>
              <option value="1">Si</option>
              <option value="0">No</option>
            </select>
          </div>

          <div class="mb-3 aniosFumando" style="display: none;">
            <label for="editarAniosFumando" class="form-label">Años Fumando</label>
            <input type="text" class="form-control ResetEnNuevaEdad" id="editarAniosFumando">
          </div>

          <div class="mb-3 tieneDieta" style="display: none;">
            <label for="editarTieneDieta" class="form-label">Tiene dieta?</label>
            <select class="form-select ResetEnNuevaEdad" id="editarTieneDieta">
              <option value="">Seleccione una opcion</option>
              <option value="1">Si</option>
              <option value="0">No</option>
            </select>
          </div>

          <input type="hidden" class="form-control" id="idPacienteHidden">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="btnActualizarPaciente">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>