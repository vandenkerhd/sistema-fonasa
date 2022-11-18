<?php 

    $select = "id_hospital,nombre";
    $surcursales = TemplateModel::mdlTableSelect("hospitales", $select, null, null, "id_hospital", "ASC");

?>

<div class="container">
    <div class="row align-items-center vh-100">
        <div class="col-6 mx-auto">
            <div class="card shadow border">
                <div class="card-body d-flex flex-column align-items-center">
                    <img src="<?=TemplateController::path()?>assets/images/fonasa.jpg" class="rounded mx-auto d-block" alt="fonasa" style="width:30%;">  
                    <br>
                    <select class="form-select" id="surcursal">
                        <option value="">Elegir una surcursal</option>
                        <?php foreach ($surcursales as $key => $value): ?>
                            <option value="<?= $value["id_hospital"] ?>"><?= $value["nombre"] ?></option>
                        <?php endforeach ?>
                    </select>
                    <br>
                    <button type="button" class="btn btn-primary" id="enviarSurcursal">SELECCIONAR</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?=TemplateController::path()?>assets/js/surcursal.js" ></script>
