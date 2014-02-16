var MatchModel = function(sets) {
    var self = this;
    self.sets = ko.observableArray(sets);

    self.addSet = function() {
        var next = sets.length + 1;
        self.sets.push({
            no: next+'.',
            home: '',
            away: ''
        });
    };

    self.removeSet = function(set) {
        self.sets.remove(set);
    };

    self.save = function(form) {
        jQuery('input#score').val(ko.utils.stringifyJson(self.sets));
        return true;
    };
};

jQuery(document).ready(function() {
    jQuery('input.number').live('focusin', function(e) {
        jQuery(this).numeric({decimal: false, negative: false}, function() {this.value = "";this.focus();});
    });
    jQuery('input.number').live('focusout', function(e) {
        jQuery(this).removeNumeric();
    });
    jQuery('input.setscore').live('keyup', function() {
        var home = [];
        var away = [];
        jQuery.each(jQuery('input.setscore'), function(key, element) {
            if (jQuery(element).hasClass('home')) {
                home.push(jQuery(element).val());
            }
            else {
                away.push(jQuery(element).val());
            }
        });
        var sets = [];
        jQuery.each(home, function(key, val) {
            sets.push(val+'/'+away[key]);
        });
        var result = sets.join(' - ');
        jQuery('div#js_preview').html(result);
    })
});