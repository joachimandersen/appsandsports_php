var rankingsorter = {
    options: {
        rankings: [],
        width: '33%',
        saverankingorderurl: null,
        savebuttonselector: null,
        displaysuccesscallback: null,
        successheader: '',
        successmessage: '',
        errorheader: '',
        errormessage: ''
    },
    _create: function() {
        this._buildList();
        var self = this;
        jQuery(this.options.savebuttonselector).click(function() {
            self._saveRankings();
        });
    },
    _getList: function() {
        return this.element.find('ul');
    },
    _buildList: function() {
        var self = this;
        jQuery(this.options.rankings).each(function(index, ranking){
            var li = jQuery('<li>');
            li.addClass('ui-state-default').data('ranking', ranking);
            var span = jQuery('<span>');
            span.addClass('icon ui-icon ui-icon-arrowthick-2-n-s');
            li.append(span);
            var name = jQuery('<span />').html(ranking.name);
            var rank = jQuery('<span class="ranking" />').html(' ('+ranking.ranking+')');
            li.append(name);
            li.append(rank);
            self._getList().append(li);
        });
        this._getList().css({
            width: this.options.width,
            cursor: 'move'
        });
        this._getList().sortable();
    },
    _saveRankings: function() {
        var self = this;
        jQuery.ajax({
            type: 'POST',
            url: this.options.saverankingorderurl,
            data: JSON.stringify({rankings: this._getOrderedRankings()}),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            success: function (msg) {
                if (msg.status == 'ok') {
                    self.options.displaysuccesscallback(
                        self.options.successheader,
                        self.options.successmessage,
                        2000
                    );
                    self._updateRankings();
                }
            },
            error: function (req, status, ex) {
                self.options.displaysuccesscallback(
                    self.options.errorheader,
                    self.options.errormessage,
                    2000
                );
            }
        });
        return false;
    },
    _updateRankings: function() {
        this._getList().find('li').each(function(index, li) {
            var ranking = jQuery(li).data('ranking');
            var item = jQuery(li);
            var idx = index+1;
            item.data('ranking', { id: ranking.id, index: idx });
            item.find('span:last').html(' (' + idx + ')')
        });
    },
    _getOrderedRankings: function() {
        var ids = [];
        this._getList().find('li').each(function(index, li) {
            var ranking = jQuery(li).data('ranking');
            ids.push({id: ranking.id, index: index + 1});
        });
        return ids;
    },
    destroy: function() {
        this._getList().sortable('destroy');
        this.element.html();
    }
};

jQuery.widget('ui.rankingsorter', rankingsorter);