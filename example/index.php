<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mads
 * Date: 02-12-12
 * Time: 09:25
 * To change this template use File | Settings | File Templates.
 */
$title = "DB_DataObject_FormBuilder_Frontend - examples";
require_once 'top.php';
?>
<h1>What?</h1>
    <p>DB_DataObject_FormBuilder_Frontend is a package to help making frontends,
    using DB_DataObject and DB_DataObject_FormBuilder.</p>

    <p>It is configured using XML-files, from where you can set the tables to be edited,
    and it will take care of mostly everything: Display a list of these tables,
    display a list with the contents/rows of these tables, and showing the form from
    which to edit or add rows.</p>

    <p>To use it, you will need some knowledge of both DB_DataObject and DB_DataObject_FormBuilder</p>


<h2>Examples</h2>

    <dl>
        <dt><a href="/examples/simple/">Simple</a></dt>
        <dd>A simple usage of the frontend</dd>
    </dl>

<?php
require_once 'foot.php';
