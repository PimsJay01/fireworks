/**
 * jQueryUI widget for create embded calendar in jQueryMobile
 *
 * @author Jérémy Gobet <jeremy.gobet.72@gmail.com>
 * @version "1.0.0"
 */
(function(jQuery) {
jQuery.widget('std72.calendar', {

    /**
    * object to store widget default options
    */
    options: {
		today: null,
		date: null,
		weekdays: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
		month: ['January','February','March','April','May','June','July','August','September','October','November','December']
    },

    /**
    * object to store widget global variables (elements x3dom)
    */
    vars: {
		table: null,
		size: 0
    },

    /* Widget internal functions */

    /**
    * Widget constructor function : create and add calendar to the DOM
    * @constructor
    * @private
    */
    _create: function() {
		// Enregistre la date du jour
		if(this.options.today == null) {
			this.options.today = new Date();
		}
		
		// Enregistrement du mois à visualiser
		this.options.date = new Date(this.options.today.getFullYear(), this.options.today.getMonth(), 1);
    },

    /**
    * Widget init function : set options received on start of the widget
    * @private
    */
    _init: function() {
		this._initCalendar();
    },

    /**
    * Widget destructor function : remove widget from DOM
    * @private
    */
    _destroy: function() {
		this.element.empty();
    },

    /* Public functions */
	
	addEvent: function(options) {
		if((options.date !== undefined) && (options.color !== undefined)) {
			var eventDay = this._container.find('#' + options.date);
			
			if(eventDay.length) {
				var number = eventDay.html();				
				var parent = eventDay.parent()
				eventDay.remove();
				
				var table = $('<table></table>')
					.css('width','100%')
					.css('height','100%')
					.appendTo(parent);
				
				var tr = $('<tr></tr>')
					.appendTo(table);
				
				var td = $('<td></td>')
					.css('cursor','pointer')
					.css('width','90%')
					.css('height','90%')
					.css('border-radius','50%')
					.css('background-color',options.color)
					.click(options.click)
					.appendTo(tr);
					
				$('<p></p>')
					.css('margin','0')
					.css('color',options.fontColor)
					.css('text-align','center')
					.html(number)
					.appendTo(td);
			}
		}
	},
    
    refresh: function() {
        this._initCalendar();
    },

    /* Private functions */
	
	_initCalendar: function() {
		if(this._container != null) {
			this._container.remove();
		}
		
		this._container = $('<ul></ul>')
			.attr('class','ui-listview ui-listview-inset ui-corner-all ui-shadow ui-group-theme-c')
			.appendTo(this.element);
            
        this._container
            .css('max-width','700px')
            .css('margin','auto');
		
	    this._createTitle();
		
		this._createWeekdaysName();
		
		this._createCalendar(); 
		
		var self = this;
		// Définit la taille du calendrier verticalement
		var getSize = function() {
			var width = Math.min($('.ui-content').width(),700);
			self.vars.size = Math.round((width / 7) * 6);
			self.vars.table.height(self.vars.size);
		};
		
		if($('.ui-content').width() == 0) {
			$(window).load(getSize);
		}
		else {
			getSize();
		}
	},

    /**
    * Create calendar title
    * @private
    * @return {Object}
    */
    _createTitle: function() {
		var container = $('<li></li>')
			.attr('class','ui-li-divider ui-bar-b ui-first-child');
    
		var table = $('<table></table>')
			.css('width','100%')
			.appendTo(container);
			
		var tr = $('<tr></tr>')
			.appendTo(table);
			
		var self = this;
		this._createBtnNavigation('arrow-l',function() {
			var month = self.options.date.getMonth();
			self.options.date.setMonth(month - 1);
			self._initCalendar();
		}).appendTo(tr);
			
		this._createMonthTitle()
			.appendTo(tr);
		
		this._createBtnNavigation('arrow-r',function() {
			var month = self.options.date.getMonth();
			self.options.date.setMonth(month + 1);
			self._initCalendar();
		}).appendTo(tr);	
		
		container.appendTo(this._container);
	},

    /**
    * 
    * @private
	* @return {Object}
    */
    _createBtnNavigation: function(button, callback) {
		var td = $('<td></td>')
			.css('width','20px')
			.css('cursor','pointer')
			.click(callback);
			
		$('<a></a>')
			.attr('class','ui-btn ui-icon-' + button + ' ui-btn-icon-notext ui-corner-all ui-shadow')
            .css('margin','0px')
			.appendTo(td);
			
		return td;
    },
	
	/**
    * 
    * @private
	* @return {Object}
    */
    _createMonthTitle: function() {
		var td = $('<td></td>')
			
		$('<h3></h3>')
			.css('margin','0px')
			.css('text-align','center')
			.html(this.options.month[this.options.date.getMonth()] + " " + this.options.date.getFullYear())
			.appendTo(td);
			
		return td;
    },
	
	/**
    * 
    * @private
	* @return {Object}
    */
	_createWeekdaysName: function() {
		var container = $('<li></li>')
			.css('width','100%')
            .css('class','ui-li-static ui-body-c')
			.css('border-bottom','solid 1px #DDDDDD');
    
		var table = $('<table></table>')
			.css('width','100%')
			.appendTo(container);
			
		var tr = $('<tr></tr>')
			.appendTo(table);
		
		for(var i=0; i<7; i++) {
			var td = $('<td></td>')
				.attr('width','14.2857%')
				.appendTo(tr);
				
			$('<p></p>')
				.css('margin','2px 0')
				.css('text-align','center')
				.html(this.options.weekdays[i])
				.appendTo(td);
		}
		
		container.appendTo(this._container);
	},
	
	/**
    * 
    * @private
	* @return {Object}
    */
	_createCalendar: function() {
		var container = $('<li></li>')
            .css('class','ui-last-child')
			.css('width','100%');
    
		this.vars.table = $('<table></table>')
			.css('width','100%')
			.appendTo(container);
			
		var daysTab = this._createDaysTab();
		var month = this.options.date.getMonth();
		// Requête à la base de données
		
		var start = false;
		for(var i=0; i<6; i++) {
			
			var tr = $('<tr></tr>')
				.attr('height','16.6667%')
				.appendTo(this.vars.table);
			
			for(var j=0; j<7; j++) {
				var td = $('<td></td>').attr('width','14.2857%');
				
				// Identifie le premier jour du mois en cours
				if(daysTab[i][j].substr(8,10) == '01') {
					start = !start;
				}
				
				// Si c'est la date d'aujourd'hui
				if(daysTab[i][j] == this._trmtDate(this.options.today)) {
					td.attr('class','ui-btn ui-btn-b ui-btn-active');
                    td.css('cursor','auto');
                    td.css('display','table-cell');
                    td.css('margin','0px');
                    td.css('padding','0px');
				}
				else
				// Si c'est un jour du weekend
				if(j >=5) {
					td.css('background-color','#EEEEEE');
				}
				
				// Numéros des jours en gris pour le mois précédent et suivant
				if(!start) {
					td.css('color','grey');
				}
				// Numéros en gras pour les jour du mois en cours
				else {
					td.css('font-weight','bold');
				}
				td.appendTo(tr);
				
				$('<p></p>')
					.attr('id',daysTab[i][j])
					.css('margin','0')
					.css('text-align','center')
					.html(daysTab[i][j].substr(8,10))
					.appendTo(td);
			}
		}
		
		container.appendTo(this._container);

		// Envoi un trigger avec la date de début et de fin visualisable sur le calendrier
		this._container.trigger('dateChange',{
			start: daysTab[0][0],
			end: daysTab[5][6]
		});
	},
	
	_createDaysTab: function() {
		var tab = new Array(6);
		
		// Réccupère le premier jour du mois
		var start = new Date(this.options.date.getFullYear(),this.options.date.getMonth(),1);
		var day = (start.getDay() == 0) ? 6 : (start.getDay() - 1);
		tab[0] = new Array(7);
		tab[0][day] = 1;
		
		// Réccupère la date du premier lundi de la première semaine du mois
		var date = start.getDate();
		start.setDate(date - day);
		date = start.getDate();
		// Ajoute réccursivement les derniers jours du mois précédent
		var i=0;
		while(tab[0][i] != 1)
			tab[0][i++] = this._trmtDate(start,date++);
			
		// Prépare le tableau pour la suite
		for(var j=1; j<6; j++)
			tab[j] = new Array(7);
		
		// Calcul le dernier jour du mois en cours
		var cal = new Date(this.options.date.getFullYear(),this.options.date.getMonth(),1);
		var month = cal.getMonth();
		cal.setMonth(month + 1);
		date = cal.getDate();
		cal.setDate(date - 1);
		date = cal.getDate();
		// Ajouter réccursivement tout les numéros de jours du mois
		var num = 1;
		while(num <= date) {
			tab[Math.floor(i/7)][i%7] = this._trmtDate(cal,num);
			i++;
			num++;
		}
		
		// Ajoute réccursivement les numéros du mois suivant pour complèter le calendrier
		var end = new Date(this.options.date.getFullYear(),this.options.date.getMonth(),1);
		var month = end.getMonth();
		end.setMonth(month + 1);
		num = 1;
		while(i < 42) {
			tab[Math.floor(i/7)][i%7] = this._trmtDate(end,num);
			i++;
			num++;
		}
		
		return tab;
	},
	
	_trmtDate: function(date, day) {
		if(day == null) {
			return date.getFullYear() + '-' + this._trmtValue((date.getMonth()+1)) + '-' + this._trmtValue(date.getDate())
		}
		return date.getFullYear() + '-' + this._trmtValue((date.getMonth()+1)) + '-' + this._trmtValue(day);
	},
	
	_trmtValue: function(value) {
		if(value < 10) {
			return '0' + value;
		}
		return value;
	},

    /* Widget options */

    /**
    * Manage options received from the init function
    * @private
    */
    _setOption: function(key, value) {}
  });
})(jQuery);
