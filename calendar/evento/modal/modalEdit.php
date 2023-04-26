<div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			<form class="form-horizontal" method="POST" action="evento/action/eventoEdit.php" onsubmit="return validaForm(this);">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Evento</h4>
				
			  </div>
			  <div class="modal-body">
				
				  <div class="form-group">
					<label for="titulo" class="col-sm-2 control-label">Titulo</label>
					<div class="col-sm-10">
					  <input type="text" name="titulo" class="form-control" id="titulo" placeholder="Titulo" required>
					</div>
				  </div>

				  <div class="form-group">
					<label for="descricao" class="col-sm-2 control-label">Descrição</label>
					<div class="col-sm-10">
					  <textarea type="text" name="descricao" class="form-control" id="descricao" placeholder="Descrição"></textarea>
					</div>
				  </div>

				  <div class="form-group">
					<label for="inicio" class="col-sm-2 control-label">Inicio</label>
					<div class="col-sm-10">
					  <input type="text" name="inicio" class="form-control" id="inicio" required>
					</div>
				  </div>
				  <div class="form-group">
					<label for="termino" class="col-sm-2 control-label">Termino</label>
					<div class="col-sm-10">
					  <input type="text" name="termino" class="form-control" id="termino" required>
					</div>
				  </div>
                    
                    <!-- Deletar Evento -->
				    <div class="form-group"> 
						<div class="col-sm-offset-2 col-sm-10">
						  <div class="checkbox">
							<label class="text-danger"><input type="checkbox"  name="delete"> Deletar Evento</label>
						  </div>
						</div>
					</div>
				  
				  <input type="hidden" name="id_evento" class="form-control" id="id_evento">
				
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
				<button type="submit" class="btn btn-primary">Salvar</button>
			  </div>
			</form>
			</div>
		  </div>
		</div>