<h3>Дополнительные права</h3>

<?php
$vauv_plugins = get_option('vauv_plugins');

echo '<table class="form-table">';
foreach ( $vauv_plugins as $vauv_plugin ) {
	echo '<tr>';
	echo '<th>' . $vauv_plugin['name'] . '</th>';
	echo '<td>';
	foreach ( $vauv_plugin['capabilities'] as $key => $value ) {

		echo '<input type="checkbox" name="' . $key . '" ';

		if ( array_key_exists( $key, $user->allcaps ) ) {
			echo 'value="1" checked disabled>&nbsp;';
		} else {
			echo 'value="0" disabled>&nbsp;';
		}
		echo $value . '<br>';
	}
	echo '</td>';
	echo '</tr>';
}
echo '</table>';