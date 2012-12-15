<?php

require_once 'setup.php';
require_once 'DB/DataObject/FormBuilder/Frontend.php';
require_once 'DB/DataObject/FormBuilder/Frontend/Renderer.php';

$frontend = new DB_DataObject_FormBuilder_Frontend('./frontend-simple.xml', true);

$output = $frontend->process();

/*
 * see if any css-file is specified in the configfile.
 * If it is, add it to the page.
 */
if ($css = $frontend->css) {
    $_css = '<link rel="stylesheet" type="text/css" href="' . $css .'"/>';
}

require_once 'top.php';
?>

<h1><?php echo htmlentities($frontend->displayName); ?></h1>
<p><?php echo htmlentities($frontend->description); ?></p>

<?php
echo $frontend->getToolbar();

echo $output;
?>
<p>
[ <a href="<?php echo $frontend->getListTablesUrl(); ?>">Show tables</a> ]
<?php if ($frontend->getMode() != DB_DataObject_FormBuilder_Frontend::LISTTABLES): ?>
 [ <a href="<?php echo $frontend->getListTableUrl(); ?>">Show table:
    <?php echo $frontend->displayName; ?></a> ]
<?php endif; ?>
</p>
<?php
require_once 'foot.php';

