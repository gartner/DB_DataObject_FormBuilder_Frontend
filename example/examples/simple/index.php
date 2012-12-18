<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mads
 * Date: 11-12-12
 * Time: 09:08
 * To change this template use File | Settings | File Templates.
 */
require_once 'setup.php';
require_once 'top.php';
?>

<h1>Simple examples</h1>
<p>This is some examples of the most simple way to use the
DB_DataObject_FormBuilder_Frontend-package.</p>

<h3><a href="1/">Example 1</a></h3>
<p>The most basic way to use the frontend. Using the Renderer to manage it.</p>
<p>It sets up some default values, and defines which columns from the table to
display in the table list.</p>
<p>
    [ <a href="/examples/showExampleSource.php?example=/simple/1/&amp;type=xml">Show configfile</a> ]
    [ <a href="/examples/showExampleSource.php?example=/simple/1/&amp;type=php">Show source</a> ]
</p>

<h3><a href="2/">Example 2</a></h3>
<p>Just like the above example, except, this one will include a css-file in the
generated page. This makes the page look so much nicer.</p>

<p>
    [ <a href="/examples/showExampleSource.php?example=/simple/2/&amp;type=xml">Show configfile</a> ]
    [ <a href="/examples/showExampleSource.php?example=/simple/2/&amp;type=php">Show source</a> ]
</p>

<h3><a href="3/">Example 3</a></h3>
<p>Like example 2, but instead of using the Renderer, use the Frontend more direct.</p>

<script type="syntaxhighlighter" class="brush: php"><![CDATA[
    &lt;?php
    $frontend = new DB_DataObject_FormBuilder_Frontend('config.xml', true);

    $output = $frontend->process();

    if (!empty($frontend->css){
        echo '<link rel="stylesheet" type="text/css" href="' . $frontend->css} . '"/>';
    }

    echo $frontend->getToolbar();

    echo $output;
    ?>
]]></script>

<p>When doing that, you have to do several things. First, check if there is a css-file from the config that should be added to the page. (Line 6-8)</p>
<p>Then print the toolbar (->getToolbar()) The toolbar is the one containing the 'add new record'-link and the pager. (Line 10)</p>
<p>Finally, output whatever is returned from ->process() (Line 12). But, since ->process() might do a http-redirect, it needs to be called before outputting anything. (Line 4)</p>
<p>
    [ <a href="/examples/showExampleSource.php?example=/simple/3/&amp;type=xml">Show configfile</a> ]
    [ <a href="/examples/showExampleSource.php?example=/simple/3/&amp;type=php">Show source</a> ]
</p>
<?php
require_once 'foot.php';
?>