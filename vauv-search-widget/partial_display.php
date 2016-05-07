<?php if ( is_user_logged_in() ): ?>
	<div class="container">
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-search"></span>&nbsp;&nbsp;поиск
						</div>
						<input type="text" id="vauv_search" class="form-control" placeholder="фамилия, телефон или устройство">
					</div>
				</div>
			</div>
		</div>
		<div class="row" id="vauv_search_results"></div>
		<hr>
	</div>
<?php endif; ?>