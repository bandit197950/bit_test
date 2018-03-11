<h1>Profile information: <?php echo $first_name . " " . $last_name; ?></h1>
<div class="menu"><a href="<?php echo $logout_action; ?>">Logout</a></div>
<br />
<form action="<?php echo $action; ?>" method="post">
	<table>
		<?php if (!empty($error)) { ?>
		<tr>
			<td colspan="2" class="error">
				<?php foreach ($error as $err) {
					echo $err; ?><br />
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td>First name:</td>
			<td><?php echo $first_name; ?></td>
		</tr>
		<tr>
			<td>Last name:</td>
			<td><?php echo $last_name; ?></td>
		</tr>
		<tr>
			<td>Email:</td>
			<td><?php echo $email; ?></td>
		</tr>
		<tr>
			<td>Balance</td>
			<td><?php echo $balance; ?></td>
		</tr>
		<tr>
			<td>Write off money</td>
			<td><input type="text" name="write_off_amount" value="" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" name="write_off" value="<?php echo $submit_button; ?>" /></td>
		</tr>
	</table>
	<?php if(!empty($balance_history)) { ?>
	<h4>Balance history</h4>
	<table border="1">
		<tr>
			<th>Balance before</th>
			<th>Writed off amount</th>
			<th>Balance after</th>
		</tr>
        <?php foreach($balance_history as $balance_item) { ?>
        <tr>
            <td><?php echo $balance_item['balance_before']; ?></td>
            <td>-<?php echo $balance_item['write_off_amount']; ?></td>
            <td><?php echo round(floatval($balance_item['balance_before'] - $balance_item['write_off_amount']), 2); ?></td>
        </tr>
        <?php } ?>
	</table>
	<?php } ?>
</form>