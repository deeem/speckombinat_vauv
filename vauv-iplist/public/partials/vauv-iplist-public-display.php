<?php //$this->iplist_db->import(dirname(__FILE__) . '/../../test.csv'); ?>
<div class="container">

	<div class="row">
		<div class="col-md-3">
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-addon"><span class="glyphicon glyphicon-search"></span>&nbsp;&nbsp;поиск
					</div>
					<input type="text" id="search_bar" class="form-control" placeholder="название или фамилия">
				</div>
			</div>
		</div>
		<div class="col-md-2 col-md-offset-2">
			<select id="subnets" class="form-control"></select>
		</div>
	</div>

	<div id="iplist" class="row"></div>

	<div class="modal fade" id="iplist-modal" tabindex="-1" role="dialog" aria-labelledby="iplist-modal">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
							aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="iplist-modal-label">&nbsp;</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal">
						<div class="form-group">
							<label for="iplist-form-ip" class="col-sm-3 control-label">IP</label>

							<div class="col-sm-9">
								<input type="text" id="iplist-form-ip" name="ip" value="" class="form-control"
								       placeholder="enter ip address" disabled/>
							</div>
						</div>
						<div class="form-group">
							<label for="iplist-form-name" class="col-sm-3 control-label">Название</label>

							<div class="col-sm-9">
								<input type="text" id="iplist-form-name" name="name" value="" class="form-control"
								       placeholder="enter device name"/>
							</div>
						</div>
						<div class="form-group">
							<label for="iplist-form-user" class="col-sm-3 control-label">Пользователь</label>

							<div class="col-sm-9">
								<input type="text" id="iplist-form-user" name="user" value="" class="form-control"
								       placeholder="enter user name"/>
							</div>
						</div>
						<div class="form-group">
							<label for="iplist-form-phone" class="col-sm-3 control-label">Телефон</label>

							<div class="col-sm-9">
								<input type="text" id="iplist-form-phone" name="phone" value="" class="form-control"
								       placeholder="enter phone"/>
							</div>
						</div>
						<?php if ( current_user_can( 'iplist_edit' ) ): ?>
							<hr/>
							<div class="form-group">
								<button type="button" name="delete" class="btn btn-danger col-sm-offset-1">Удалить
								</button>
								<button type="button" name="save" class="btn btn-primary col-sm-offset-7">Закрыть
								</button>
							</div>
						<?php endif; ?>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="subnet-modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
							aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="subnet-modal-label">&nbsp;</h4>
				</div>
				<div class="modal-body">
					<form class="form-inline">
						<div class="form-group">
							<label for="new_subnet">Подсеть</label>
							<input type="text" id="new_subnet" class="form-control" placeholder="10.0.8">
							<span class="help-block" id="new_message"></span>
						</div>
						<button type="button" class="btn btn-default">Добавить</button>
					</form>
				</div>
			</div>
		</div>
	</div>

</div>