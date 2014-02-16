var listeditor = {
    options: {
        inputselector: null,
        listselector: null,
        addbuttonselector: null
    },
    _create: function() {
        var self = this;
        this.element.data('emails', []);
        jQuery(self.options.inputselector).keyup(function() {
            self._validateEmail();
        });
        jQuery(this.options.addbuttonselector).attr('disabled', 'disabled');
        jQuery(this.options.addbuttonselector).click(function() {
            self._addEmailToList(jQuery(self.options.inputselector).val());
            jQuery(self.options.addbuttonselector).attr('disabled', 'disabled');
        });
        var ol = jQuery('<ol />').addClass('ui-selectable').attr('id', 'selectable');
        this.element.find(this.options.listselector).append(ol);
        ol.selectable({
            cancel: '*'
        });
    },
    _validateEmail: function() {
        var input = jQuery(this.options.inputselector);
        if (jQuery.faucon.isValidEmail(input.val()) && this._canAdd(input.val())) {
            jQuery(this.options.addbuttonselector).removeAttr('disabled');
            input.removeAttr('style');
            return true;
        }
        var css = {
            borderColor: '#ff0000',
            borderStyle: 'dashed'
        };
        jQuery(this.options.addbuttonselector).attr('disabled', 'disabled');
        input.css(css);
        return false;
    },
    _getList: function() {
        return this.element.data('emails');
    },
    _addToList: function(email) {
        var emails = this._getList();
        emails.push(email);
        this._trigger(':emails', {}, { emails: emails });
    },
    _canAdd: function(email) {
        if (jQuery.inArray(email, this._getList()) !== -1) {
            return false;
        }
        return true;
    },
    _addEmailToList: function(email) {
        if (!this._validateEmail()) {
            return;
        }
        var ol = this.element.find(this.options.listselector).find('ol');
        var li = jQuery('<li />').html(email);
        li.addClass('ui-widget-content ui-selectee');
        var closeicon = jQuery('<i />').addClass('icon-remove')
            .css({
                'float': 'right',
                cursor: 'pointer'
            }).data('email', email);
        var self = this;
        closeicon.click(function() {
            var email = jQuery(this).data('email');
            jQuery(this).parent().remove();
            self._removeEmail(email);
        });
        li.append(closeicon);
        ol.append(li);
        this._addToList(email);
    },
    _removeEmail: function(email) {
        var emails = this._getList();
        var index = jQuery.inArray(email, emails);
        if (index !== -1) {
            emails.splice(index, 1);
        }
        this._validateEmail();
        this._trigger(':emails', {}, { emails: emails });
    },
    getEmails: function() {
        return this._getList();
    },
    clear: function() {
        jQuery(this.options.addbuttonselector).attr('disabled', 'disabled');
        var ol = this.element.find(this.options.listselector).find('ol');
        ol.html('');
        this.element.data('emails', []);
        this._trigger(':emails', {}, { emails: [] });
    }
};

jQuery.widget('ui.listeditor', listeditor);