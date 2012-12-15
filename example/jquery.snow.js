/**
 * jquery.snow - jQuery Snow Effect Plugin
 *
 * Available under MIT licence
 *
 * @version 1 (21. Jan 2012)
 * @author Ivan Lazarevic
 * @requires jQuery
 * @see http://workshop.rs
 *
 * @params minSize - min size of snowflake, 10 by default
 * @params maxSize - max size of snowflake, 20 by default
 * @params newOn - frequency in ms of appearing of new snowflake, 500 by default
 * @params flakeColor - color of snowflake, #FFFFFF by default
 * @example $.fn.snow({ maxSize: 200, newOn: 1000 });
 */
(function($){

    $.fn.snow = function(options){

        var offset          = $('#image').offset(),
            $flake 			= $('<div id="flake" />').css({'position': 'absolute', 'top': offset.top + 'px'}).html('&#10052;'),
            documentHeight 	= $('#image').height(),
            documentWidth	= $('#image').width(),
            defaults		= {
                minSize		: 10,
                maxSize		: 20,
                newOn		: 500,
                flakeColor	: "#FFFFFF"
            },
            options			= $.extend({}, defaults, options);


        var interval		= setInterval( function(){
            var startPositionLeft 	= offset.left + Math.random() * documentWidth - 10,
                startOpacity		= 0.5 + Math.random(),
                sizeFlake			= options.minSize + Math.random() * options.maxSize,
                endPositionTop		= offset.top + documentHeight - 40,
                endPositionLeft		= startPositionLeft - 10 + Math.random() * 200,
                durationFall		= documentHeight * 10 + Math.random() * 5000;
            $flake
                .clone()
                .appendTo('#image')
                .css(
                {
                    left: startPositionLeft,
                    opacity: startOpacity,
                    'font-size': sizeFlake,
                    color: options.flakeColor
                }
            )
                .animate(
                {
                    top: endPositionTop,
                    left: endPositionLeft,
                    opacity: 0.2
                },
                durationFall,
                'linear',
                function() {
                    $(this).remove()
                }
            );
        }, options.newOn);

    };

})(jQuery);