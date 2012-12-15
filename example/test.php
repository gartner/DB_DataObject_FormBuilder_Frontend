<?php
require_once 'setup.php';

$conn = mysqli_connect('localhost', $db['mysql']['user'], $db['mysql']['password'], $db['mysql']['database']);

$res = mysqli_query($conn, 'DESCRIBE pages');

$i = 0;
while ($r = mysqli_fetch_object($res)) {
    //var_dump($r);
}

$res = mysqli_query($conn, 'SELECT * FROM pages');

for ($i = 0; $i<4; $i++) {
    $fieldInfo = mysqli_fetch_field_direct($res, $i);
    var_dump($fieldInfo);
    var_dump($fieldInfo->flags & MYSQLI_BINARY_FLAG);
}

var_dump(mysqli_character_set_name($conn));

exit;

$xmlData = <<<DOC
<?xml version="1.0" encoding="utf-8" standalone="no" ?>
<!DOCTYPE frontend
    SYSTEM "http://cms.palustris.dk/MLJ_DB_DataObject_FormBuilder_Frontend-v1.dtd">
<frontend>
</frontend>
DOC;

$doc = new DOMDocument();
$doc->loadXML($xmlData);
var_dump($doc->doctype->publicId);
var_dump($doc->doctype->systemId);
var_dump($doc->doctype->name);
var_dump($doc->doctype->entities);
var_dump($doc->doctype->notations);
var_dump(basename($doc->doctype->systemId));
exit;

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Demo</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script src="jquery.snow.js"></script>
    <script type="text/javascript">
        $(document).ready( function(){
            $('#image').snow({flakeColor: '#7EB3FF'});
        });
    </script>
</head>
<body style="background-color: #4444ff;  overflow-x: hidden; ">
<a href="http://jquery.com/">jQuery</a>
<br/><br/><br/><br/><br/>
<h1>Test</h1>
<div id="image" style="float: right;width: 700px; height: 300px; border: 1px solid red; overflow: hidden;">
    <h1>Test</h1>
</div>
</body>
</html>