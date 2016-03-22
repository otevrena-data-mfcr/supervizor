/**
 * This is a custom function for Raphael elements, and is designed
 * to be used with properties added and defined in Raphael.styles
 *
 * @author      Terry Young <terryyounghk [at] gmail.com>
 * @license     WTFPL Version 2 ( http://en.wikipedia.org/wiki/WTFPL )
 */
Raphael.el.style = function (state, style, aniOptions)
{
    if (!this.class)
    {
        this.class = style ? style : 'default';
        this.aniOptions = aniOptions ? aniOptions : null;
 
        // start assigning some basic behaviors
        this.mouseover(function () { this.style('hover'); });
        this.mouseout(function () { this.style('base'); });
        this.mousedown(function () { this.style('mousedown'); });
        this.mouseup(function () { this.style('hover'); });
    }
 
    style = this.class ? this.class : style;
    state = state ? state : 'base';
    aniOptions = this.aniOptions ? this.aniOptions : null;
 
    // The structure of Raphael.styles is " type --> style --> state "
    if (aniOptions)
    {
        this.animate(Raphael.styles[this.type][style][state], 
                     aniOptions.duration, aniOptions.easing, aniOptions.callback);
    }
    else
    {
        this.attr(Raphael.styles[this.type][style][state]);
    }
 
return this; // chaining, e.g. shape.attr({ stroke: '#fff'}).style('dragging').toFront();
};
 
 
/**
 * Same API as Raphael.el.style for Raphael Sets
 *
 * @author      Terry Young <terryyounghk [at] gmail.com>
 * @license     WTFPL Version 2 ( http://en.wikipedia.org/wiki/WTFPL )
 */
Raphael.st.style = function (state, style, animated)
{
    for (var i = 0, j = this.items.length; i < j; i++)
    {
        var item = this.items[i];
        item.style(state, style, animated);
    }
 
return this; // chaining, e.g. set.attr({ stroke: '#fff'}).style('dragging').toFront();
};

/**
 * This is a method to add more style sets at runtime
 *
 * @author      Terry Young <terryyounghk [at] gmail.com>
 * @license     WTFPL Version 2 ( http://en.wikipedia.org/wiki/WTFPL )
 */
Raphael.setStyles = function (styles)
{
Raphael.styles = $.extend(true, {}, styles);
};