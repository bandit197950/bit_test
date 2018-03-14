<H1><?php echo $title; ?></H1>

<form action="<?php echo $action; ?>" method="post">
    <table>
        <?php if (!empty($error)) { ?>
        <tr>
            <td colspan="2" class="error">
                <?php foreach ($error as $err) {
					echo $err; ?><br/>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td>Email:</td>
            <td><input type="text" name="email" value="<?php echo $email; ?>"/></td>
        </tr>
        <tr>
            <td>Password:</td>
            <td><input type="password" name="password"/></td>
        </tr>
        <tr>
            <td><input type="submit" value="LogIn"/></td>
        </tr>
    </table>
</form>