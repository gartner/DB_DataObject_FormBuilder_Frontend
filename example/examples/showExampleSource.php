<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mads
 * Date: 13-12-12
 * Time: 08:12
 * To change this template use File | Settings | File Templates.
 */

$_style = <<<CSS
#left {
    display: none;
}
#right {
    width: 95%;
}
CSS;

require_once 'setup.php';
require_once 'top.php';

$basedir =  $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'examples' . DIRECTORY_SEPARATOR;
//ini_set('open_basedir', $basedir);

$example = str_replace('..' . DIRECTORY_SEPARATOR, '', $_REQUEST['example']);
if (substr($example, -1, 1) !== DIRECTORY_SEPARATOR) {
    $example .= DIRECTORY_SEPARATOR;
}
if (substr($example, 0, 1) != DIRECTORY_SEPARATOR) {
    $example = DIRECTORY_SEPARATOR . $example;
}
$type = strtolower($_REQUEST['type']);

if (! in_array($type, array('xml', 'php'))) { ?>
    <h1>Not allowed</h1>
    <p>I refuse to show the filetype <?php echo $type; ?></p>
<?php
} else {
    try {
        $dir = new DirectoryIterator($basedir . $example);
        foreach ($dir as $fileinfo) {
            if (strtolower($fileinfo->getExtension()) == $type) {
                echo "<h5>File: /examples$example{$fileinfo->getFilename()}</h5>\n";
                echo '<script type="syntaxhighlighter" class="brush: '. $type . '"><![CDATA[' ."\n";
                echo str_replace('</script', '&lt;script', file_get_contents($fileinfo->getPathname()));
                echo "]]></script>";
            }
        }
    } catch (UnexpectedValueException $e) {
        echo <<<HTML
    <h1>Cannot show source</h1>
    <p>The source for $example cannot be shown</p>
HTML;

    }
}

require_once 'foot.php';

