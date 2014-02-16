(function( $, undefined ) {

// prevent duplicate loading
// this is only a problem because we proxy existing functions
// and we don't want to double proxy them
$.faucon = $.faucon || {};
if ( $.faucon.version ) {
	return;
}

$.extend($.faucon, {
    version: '0.1',
    showSuccessDialog: function(h, msg, delay) {
        var modal = jQuery('<div />')
            .addClass('modal')
            .addClass('fade')
            .addClass('hide');
        var header = jQuery('<div />')
            .addClass('modal-header');
        var button = jQuery('<button />')
            .addClass('close')
            .attr('data-dismiss', 'modal')
            .html('x');
        header.append(button).append(msg);
        header.append(jQuery('<h3 />').html(h));
        var body = jQuery('<div />')
            .addClass('modal-body')
            .append(jQuery('<p />').html(msg));
        modal.append(header).append(body);
        jQuery('body').append(modal);
        modal.modal({});
        modal.modal('show');
        setTimeout(function() { modal.modal('hide'); modal.remove(); }, delay);
    },
    isValidEmail: function(value) {
        // contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
        return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(value);
    }
});

/*var proxiedajax = jQuery.ajax;
jQuery.ajax = function() {
    console.log(arguments);
    return proxiedajax.apply(this, arguments);
};*/


})(jQuery);