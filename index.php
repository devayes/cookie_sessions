<?php

require('Session.class.php');

if (isset($_POST['action'])) {
    
    switch ($_POST['action']) {
        case 'add':
            Session::setData($_POST['key'], $_POST['value']); // NOTE: always scrub inout.
        break;
        
        case 'delete':
            Session::unsetData($_POST['key']);
        break;
    }
    
    header('location: '.$_SERVER['PHP_SELF']);
    exit;
}

?>

<div style="width:700px; margin:0 40px; overflow:auto;">
<div style="margin:40px 0;">
    <h1>Cookie Session Class</h1>
    <p>This class maintains client side "stateless" sessions. The obvious benefit being scalability, not having to store sessions on a server.</p>
    <p>For security sake, you should consider encrypting the cookie with a reversable algorithm.</p>
</div>
<div style="margin:40px 0; background:#ffe; padding:10px 15px;">
    <p><strong>Add record to Cookie Session</strong></p>
    <form action="" method="post">
        <label for="key">Key:</label><input id="key" type="text" name="key" value="foo" />&nbsp;&nbsp;
        <label for="value">Value:</label><input id="value" type="text" name="value" value="bar" />
        <input type="hidden" name="action" value="add" />
        <input type="submit" value="Set value" />
    </form>
</div>
<?php if($_SESSION){ ?>
<div style="margin:40px 0; background:#ffe; padding:10px 15px;">
    <p><strong>Remove record from Cookie Session</strong></p>
    <form action="" method="post">
        <label for="key">Key:</label>
        <select id="key" name="key">
        <?php 
        foreach(Session::getData() as $k=>$v) { 
            echo '<option value="'.$k.'">'.$k.'</option>';
        }
        ?>
        </select>
        <input type="hidden" name="action" value="delete" />
        <input type="submit" value="Remove value" />
    </form>
</div>
<?php
}
?>

<?php
echo '<h1>$_COOKIE</h1>';
print('<xmp>'.print_r($_COOKIE,true).'</xmp>');
echo '<h1>$_SESSION</h1>';
print('<xmp>'.print_r($_SESSION,true).'</xmp>');
echo '<h1>Methods</h1>';
print('<xmp>'.print_r(get_class_methods('Session'),true).'</xmp>');
?>
</div>
