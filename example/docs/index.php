<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mads
 * Date: 14-12-12
 * Time: 21:24
 * To change this template use File | Settings | File Templates.
 */
require_once 'setup.php';
require_once 'top.php';

?>
<h1>Documentation</h1>

<h3>Plugins</h3>
    <h5>Using plugins</h5>
<p>The plugins that are included in this package, are automatically added. That
    means you don't have to specify any &lt;loader&gt;-tag in the config to use
    these. You can en- or disable them by just using the &lt;plugin&gt;-tag.</p>
<p>TODO: List included plugins</p>
<h5>Writing plugins</h5>
<p>Plugins has to inherit from DB_DataObject_FormBuilder_Frontend_Plugin</p>

<p>For the internal automatic loading of plugin-files to work, the file has to
    be named the same as the basename of the class. So, if the class containing
    your plugin is My_Frontend_Plugin_CoolPlugin it should be in a file named
    CoolPlugin.php (watch case!)</p>
<p>Where the file containing your plugin is, doesn't really matter. To use it,
    add
    this to the config:</p>
<script type="syntaxhighlighter" class="brush: xml"><![CDATA[
    <frontend>
        <plugins>
            <plugin name="CoolPlugin">
                <loader>
                    <className>My_Frontend_Plugin_CoolPlugin</className>
                    <path>my/plugins/</path>
                </loader>
            </plugin>
        </plugins>
    </frontend>
    ]]>
</script>
<p>The example above would requre that somewhere in the includepath was a file
    my/plugins/CoolPlugin.php containing a class named
    My_Frontend_Plugin_CoolPlugin</p>

<?php
require_once 'foot.php';
?>