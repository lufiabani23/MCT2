<?php
// ALERTS DO SISTEMA
if (isset($_GET['alert']) && $_GET['alert'] === "success") { ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Sucesso!</strong> Operação realizada com sucesso.
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
<?php } elseif (isset($_GET['alert']) && $_GET['alert'] === "danger") { ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Erro!</strong> A operação não foi realizada.
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
<?php } ?>
