var matchscore = {
    _create: function() {
        var usevalidation = typeof(this.options.validator) === 'function';
        var self = this;
        this._numberOfRows = 0;
	if (this.options.initialScore.length > 0) {
	    for (var i = 0; i<this.options.initialScore.length; i++) {
                this._getBody().append(this._createRow(i+1, this.options.initialScore[i]));
            }
        }
	else {
            for (var i = 1; i<=this.options.numberofsetstodraw; i++) {
                this._getBody().append(this._createRow(i));
            }
	}
        this._addAppendSetButton();
        this.element.bind('matchscore:row:created', function() {
            if (self._numberOfRows == self.options.numberofsetspermatch) {
                self._addSetButton.attr('disabled', true);
            }
            else {
                self._addSetButton.removeAttr('disabled');
            }
        });
        this.element.bind('matchscore:row:deleted', function() {
            if (self._valid) {
                self._addSetButton.attr('disabled', true);
            }
            else {
                self._addSetButton.removeAttr('disabled');
            }
        });
        if (usevalidation) {
            this._saveButton.attr('disabled', 'true');
            this.element.bind('scorevalidator:validation:success', function(e, json, home) {
                self._valid = true;
                self._saveButton.removeAttr('disabled');
                self._addSetButton.attr('disabled', true);
                self._setJson(json);
                self._announceWinner(home);
            });
            this.element.bind('scorevalidator:validation:failure', function(e) {
                self._valid = false;
                self._saveButton.attr('disabled', true);
                if (self._numberOfRows != self.options.numberofsetspermatch) {
                    self._addSetButton.removeAttr('disabled');
                }
                self._setJson('');
                self._resetWinner();
            });
            this._valid = false;
            this.options.validator(this.options.initialScore);
        }
    },
    _resetWinner: function() {
        jQuery(this.element).find(this.options.challengerselector).removeAttr('style');
        jQuery(this.element).find(this.options.challengedselector).removeAttr('style');
    },
    _announceWinner: function(home) {
        var winner = null;
        var looser = null;
        if (home) {
            winner = jQuery(this.element).find(this.options.challengerselector);
            looser = jQuery(this.element).find(this.options.challengedselector);
        }
        else {
            winner = jQuery(this.element).find(this.options.challengedselector);
            looser = jQuery(this.element).find(this.options.challengerselector);
        }
        winner.css({fontWeight: 'strong', color: '#000000'});
        looser.css({color: '#999999'});
    },
    _getBody: function() {
        return jQuery(this.element).find('tbody');
    },
    _getFooter: function(){
        return jQuery(this.element).find('tfoot');
    },
    _addAppendSetButton: function() {
        var self = this;
        var addbutton = jQuery('<button />');
        this._addSetButton = addbutton;
        addbutton.addClass('btn');
        addbutton.text(this.options.addsetbuttoncaption);
        addbutton.click(function() {
            self.addRow();
            return false;
        });
        var tr = jQuery('<tr />');
        var col1 = jQuery('<td />');
        col1.append(addbutton);
        tr.append(col1);
        var savebutton = jQuery('<button type="submit" />');
        this._saveButton = savebutton;
        savebutton.click(function() {
            return true;
        });
        savebutton.addClass('btn');
        savebutton.text(this.options.savebuttoncaption);
        var col2 = jQuery('<td />');
        col2.append(savebutton);
        tr.append(col2);
        var col3 = jQuery('<td />');
        this._resultColumn = col3;
        col3.attr('id', 'js_result');
        tr.append(col3);
        this._getFooter().append(tr);
    },
    _createRow: function(no, set) {
        var self = this;
        this._numberOfRows++;
        var tr = jQuery('<tr />');
        var col1 = jQuery('<td />');
        col1.addClass('no');
        col1.html(no+'.');
        var col2 = jQuery('<td />');
	var homeScore = set !== undefined ? set.home : '';
        var home = this._createInput('home', no - 1, homeScore)
        col2.append(home);
        var col3 = jQuery('<td />');
	var awayScore = set !== undefined ? set.away : '';
        var away = this._createInput('away', no - 1, awayScore);
        col3.append(away);
        this._addKeyUpEvent(home, away);
        var col4 = jQuery('<td class="valid" />');
        var col5 = jQuery('<td />');
        var button = jQuery('<button />');
        button.text(this.options.deletebuttoncaption);
        button.addClass('btn');
        button.click(function(){
            self.deleteRow(this);
            return false;
        });
        col5.append(button);
        tr.append(col1);
        tr.append(col2);
        tr.append(col3);
        tr.append(col4);
        tr.append(col5);
        this._trigger(':row:created');
        return tr;
    },
    _addKeyUpEvent: function(home, away) {
        var self = this;
        home.keyup(function(event) {
            var index = jQuery(this).data('index');
            self._keyUpEvent(home, away, index);
        });
        away.keyup(function(event) {
            var index = jQuery(this).data('index');
            self._keyUpEvent(home, away, index);
        });
    },
    _keyUpEvent: function (home, away, index) {
        var icon = home.parent().parent().find('td.valid');
        this._trigger(':validate', 0, [home, away, icon, index]);
    },
    _createInput: function(cssclass, index, points) {
        var self = this;
        var input = jQuery('<input />');
        input.numeric({
            decimal: false, 
            negative: false
            },
            function() {
                this.value = '';
                this.focus();
            }
        );
        input.attr('type', 'text');
        input.addClass('required');
        input.addClass('number');
        input.addClass(cssclass);
        input.data('index', index);
	input.val(points);
        return input;
    },
    addRow: function() {
        var row = this._createRow(this._numberOfRows+1);
        this._getBody().append(row);
    },
    deleteRow: function(btn) {
        this._numberOfRows--;
        var index = jQuery(btn).parent().parent().find('input.home').data('index');
        this._trigger(':row:deleted', 0, index);
        jQuery(btn).parent().parent().remove();
        this._updateRowNumbers();
    },
    _updateRowNumbers: function() {
        jQuery(this.element).find('td.no').each(function(key, element) {
            var no = key + 1;
            jQuery(element).html(no+'.');
        });
        jQuery(this.element).find('input.home').each(function(key, element) {
            jQuery(element).data('index', key)
        });
        jQuery(this.element).find('input.away').each(function(key, element) {
            jQuery(element).data('index', key)
        });
    },
    _setJson: function(json) {
        jQuery(this.options.resultcontainerselector).val(json);
    },
    _numberOfRows: null,
    _resultColumn: null,
    _saveButton: null,
    _addSetButton: null,
    _valid: true,
    destroy: function() {
        this.element.unbind('matchscore:row:created');
        this.element.unbind('matchscore:row:deleted');
        this.element.unbind('scorevalidator:validation:success');
        this.element.unbind('scorevalidator:validation:failure');
        jQuery(this.options.resultcontainerselector).val('');
        this._getBody().html('');
        this._getFooter().html('');
        if (typeof(this.options.validator) === 'function') {
            this.options.validator(false);
        }
        jQuery.Widget.prototype.destroy.call(this);
    },
    options: {
        numberofsetstodraw: 3,
        numberofsetspermatch: 5,
        deletebuttoncaption: 'Slet',
        addsetbuttoncaption: 'Tilføj sæt',
        savebuttoncaption: 'Gem',
        challengerselector: null,
        challengedselector: null,
        resultcontainerselector: null,
        validator: null,
	initialScore: []
    }
};

jQuery.widget('ui.matchscore', matchscore);
