var pyramidefitter = {
    _create: function() {
        var self = this;
        if (jQuery(this.options.pyramideSelector).length == 0) {
            return;
        }
        jQuery(this.options.pyramideSelector).css({
            width: this.options.maxWidth + 'px',
            fontSize: this.options.initialFontSize + 'px'
        });
        jQuery(this.element).data('text', this._findLongestText(this.options.initialFontSize));
        jQuery(window).resize(function () {
            self._performResize();
        });
        this._performResize();
    },
    _performResize: function() {
        var referencewidth = this.options.useParentAsReference ?
            jQuery(this.element).width() : jQuery(window).width();
        var maxwidth = referencewidth / this.options.maxItemsPerLayer;
        var div = jQuery(this.options.pyramideSelector+':first');
        var newwidth = maxwidth-10;
        var size = div.css('font-size').match(/\d+/g)[0];
        if (this.options.maxWidth < newwidth) {
            newwidth = this.options.maxWidth;
        }
        var text = jQuery(this.element).data('text');
        var newsize = this._findOptimalFontSize(newwidth, text, size);
        jQuery(this.options.pyramideSelector)
            .css('font-size', newsize + 'px');
        jQuery(this.options.pyramideSelector)
            .css('width', newwidth);
    },
    _findLongestText: function(fontsize) {
        var width = 0;
        var text = '';
        var span = jQuery('<span />');
        span.attr('id', 'js_pyramide_fitter_text_measure');
        jQuery('body').append(span);
        jQuery(this.options.pyramideSelector).each(function(key, value) {
            var textwidth = span
                .css({ 
                    fontSize: fontsize + 'px',
                    display: 'none'
                })
                .html(jQuery(value).html())
                .width();
            if (textwidth > width) {
                width = textwidth;
                text = jQuery(value).html();
            }
        });
        span.remove();
        return text;
    },
    _findOptimalFontSize: function(maxwidth, text, fontsize) {
        var span = jQuery('<span />');
        span.attr('id', 'js_pyramide_fitter_text_measure')
        jQuery('body').append(span);
        var textwidth = span
            .css({ 
                fontSize: fontsize + 'px',
                display: 'none'
            })
            .html(text)
            .width();
        if (textwidth > maxwidth) {
            while (textwidth - maxwidth > 15) {
                fontsize--;
                textwidth = span
                    .css({fontSize: fontsize + 'px'})
                    .html(text)
                    .width();
            }
        }
        else {
            while (maxwidth - textwidth > 15) {
                if (fontsize >= this.options.initialFontSize) {
                    return this.options.initialFontSize;
                }
                fontsize++;
                textwidth = span
                    .css({fontSize: fontsize + 'px'})
                    .html(text)
                    .width();
            }
        }
        jQuery('#js_pyramide_fitter_text_measure').remove();
        return fontsize;
    },
    destroy: function() {
    },
    options: {
        maxWidth: 140,
        initialFontSize: 13,
        useParentAsReference: true,
        maxItemsPerLayer: 3,
        pyramideSelector: '.pyramid',
        onResizeEvent: null
    }
};

jQuery.widget('ui.pyramidefitter', pyramidefitter);