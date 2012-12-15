<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mads
 * Date: 02-12-12
 * Time: 09:23
 * To change this template use File | Settings | File Templates.
 */
?>
<!-- Begin footer -->
</div>

<div class="cleaner"></div>

<div id="footer">
    &copy; 2008 - <?php echo date('Y'); ?> <a href="mailto:mads@gartneriet.dk">Mads Lie Jensen</a>
</div>


</div>

</div>
<!-- Finally, to actually run the highlighter, you need to include this JS on your page -->
<script type="text/javascript">
SyntaxHighlighter.autoloader(
        'js jscript javascript  /js/syntaxHighlighter/shBrushJScript.js',
        'php                    /js/syntaxHighlighter/shBrushPhp.js',
        'html htm xml xhtml     /js/syntaxHighlighter/shBrushXml.js'
);
SyntaxHighlighter.all()
</script>
</body>
</html>
