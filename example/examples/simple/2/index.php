<?php

require_once 'setup.php';
require_once 'DB/DataObject/FormBuilder/Frontend.php';
require_once 'DB/DataObject/FormBuilder/Frontend/Renderer.php';


$frontend = new DB_DataObject_FormBuilder_Frontend('./frontend-simple.xml', true);

$renderer = new DB_DataObject_FormBuilder_Frontend_Renderer($frontend);
$renderer->run();
