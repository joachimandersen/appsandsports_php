var scorevalidator = {
    _init: function () {
        
    },
    _create: function() {
        var self = this;
        jQuery(this.element).data('score', this.options.initialScore);
        this.element.bind('matchscore:validate', function(e, home, away, tr, index) {
            self._validate(home, away, tr, index);
        });
        this.element.bind('matchscore:row:deleted', function(e, index) {
            self._removeScore(index);
            self._validateScore();
        });
        this.element.bind('matchscore:row:created', function() {
            self._addScore();
        });
	if (this.options.initialScore.length > 0) {
            this._validateScore();
	}
    },
    _addScore: function() {
        var score = jQuery(this.element).data('score');
        score.push({home: 0, away: 0});
        jQuery(this.element).data('score', score);
    },
    _removeScore: function(index) {
        var score = jQuery(this.element).data('score');
        score.splice(index, 1);
        jQuery(this.element).data('score', score);
    },
    _setScore: function(home, away, index) {
        var score = jQuery(this.element).data('score');
        score[index].home = home;
        score[index].away = away;
        jQuery(this.element).data('score', score);
    },
    _isSetScoreValid: function(h, a) {
        var valid = true;
        if (isNaN(h) || isNaN(a)) {
            //console.log('isNaN was true');
            valid = false;
        }
        else if (Math.abs(h - a) < 2) {
            //console.log('Not at least 2 in diff');
            valid = false;
        }
        else if (Math.abs(h - a) > 2 && h > a && h != this.options.numberofpointstowinset) {
            //console.log('Not excactly 2 in diff where max different from 11 a', h, a);
            valid = false;
        }
        else if (Math.abs(h - a) > 2 && h < a && a != this.options.numberofpointstowinset) {
            //console.log('Not excactly 2 in diff where max different from 11 b', h, a);
            valid = false;
        }
        else if (h < this.options.numberofpointstowinset && a < this.options.numberofpointstowinset) {
            //console.log('Both less than 11');
            valid = false;
        }
	return valid;
    },
    _validate: function(home, away, tr, index) {
        var h = parseInt(home.val());
        var a = parseInt(away.val());
        this._setScore(h, a, index);
        var valid = this._isSetScoreValid(h, a);
        this._validateScore();
        this._updateErrorCss(tr, valid, home, away);
    },
    _updateErrorCss: function(tr, valid, home, away) {
        var icon = jQuery('<img />');
        if (!valid) {
            icon.attr('src', this.options.invalidiconsrc);
            icon.attr('alt', 'invalid');
            icon.attr('title', 'Fejl i indtastningen');
            tr.html(icon);
            home.removeAttr('style');
            away.removeAttr('style');
        }
        else {
            icon.attr('src', this.options.validiconsrc);
            icon.attr('alt', 'valid');
            icon.attr('title', 'Korrekt indtastet');
            tr.html(icon);
            var css = {
                color: '#000000',
                border: '2px solid black'
            };
            var h = parseInt(home.val());
            var a = parseInt(away.val());
            if (h > a) {
                home.css(css);
                away.removeAttr('style');
            }
            else {
                away.css(css);
                home.removeAttr('style');
            }
        }
    },
    _validateScore: function() {
        var home = 0;
        var away = 0;
        var score = jQuery(this.element).data('score');
        jQuery.each(score, function (key, set) {
            if (set.home > set.away) {
                home++;
            }
            else if (set.home < set.away) {
                away++;
            }
        });
	var areAllSetScoresValid = true;
	for (var i = 0; i < score.length; i++) {
            areAllSetScoresValid = areAllSetScoresValid && this._isSetScoreValid(score[i].home, score[i].away);
        }
        if ((home == this.options.setstowin || away == this.options.setstowin ) && areAllSetScoresValid) {
            var json = JSON.stringify(score);
            var h = home == this.options.setstowin;
            this._trigger(':validation:success', 0, [json, h]);
        }
        else {
            this._trigger(':validation:failure', score);
        }
    },
    destroy: function() {
        jQuery.removeData(this.element, 'score');
        this.element.unbind('matchscore:validate');
        this.element.unbind('matchscore:row:deleted');
        this.element.unbind('matchscore:row:created');
        jQuery.Widget.prototype.destroy.call(this);
    },
    options: {
        setstowin: 3,
        numberofpointstowinset: 11,
        validiconsrc: null,
        invalidiconsrc: null,
	initialScore: [{home: 0, away: 0},{home: 0, away: 0},{home: 0, away: 0}]
    }
};

jQuery.widget('ui.scorevalidator', scorevalidator);
