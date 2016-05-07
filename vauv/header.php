<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<?php $fav = get_bloginfo( 'template_directory' ) . '/assets/images/' . rand( 1, 45 ) . '.ico'; ?>
	<link rel="icon" href="<?php echo $fav; ?>">
	<title>
		<?php wp_title( ' | ', true, 'right' ); ?>
		<?php bloginfo( 'name' ); ?>
	</title>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> >

<?php if ( ! is_user_logged_in() ) : ?>
<div class="modal fade" id="loginModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
						aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Вход для сотрудников отдела ВАУВ</h4>
			</div>
			<div class="modal-body">
				<?php
				$args = array(
					'redirect'       => get_option( 'siteurl' ),
					'form_id'        => 'loginform-custom',
					'label_username' => 'Логин',
					'label_password' => 'Пароль',
					'label_remember' => 'Запомнить на 14 дней',
					'label_log_in'   => 'Вход',
					'remember'       => true
				);
				wp_login_form( $args );
				?>
			</div>
			<div class="modal-footer">
				<a href="/wp-login.php?action=lostpassword">Я забыл пароль</a>
			</div>
		</div>
	</div>
</div>
<!-- /.modal #loginModal -->
<?php endif; ?>