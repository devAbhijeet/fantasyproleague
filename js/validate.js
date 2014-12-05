(function(window,document){

	var defaults = {
		messages : {
			required      : "The %s field is required",
			valid_email   : "The %s field is invalid",
			alph_num_dash : "The %s field must be alphabet,numbers and underscores",
			max_length    : "The %s field must be max of %s characters",
			min_length    : "The %s field must be min of %s characters",
			matches       : "The password field are not matching" 
		}
	};

	var err = [];

	var ruleRegex     = /^(.+?)\[(.+)\]$/,
		numericRegex = /^[0-9]+$/,
		emailRegex    = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.(?:[A-Z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|asia|jobs|museum)$/i,
		alph_num_dash =  /^[a-z0-9_\-]+$/i;

	var FormValidate = function(formElementOrNode,fields,callback){
		this.callback  = callback;
		this.form      = this._formElementOrNode(formElementOrNode) || {};
		this.fields    = {}; 
		this.messages  = {};
		this.errors    = [];

		for(var i = 0,fieldsLength = fields.length;i<fieldsLength;i++){
			
			var field = fields[i];

			if((!field.names && !field.name) || !field.rules){
				continue;
			}

			if(field.names){
				for(var j = 0,fieldNameLength = field.name.length;j<fieldNameLength;j++){
					console.log(field.name[j]+" "+field);
				}
			}else{
				this._addField(field,field.name);
			}
		}

		var _onsubmit = this.form.onsubmit;
		this.form.onsubmit = (function(that){
			return function(evt){
				try{
					return that._validateForm(evt) && (_onsubmit === undefined|| _onsubmit());
				}catch(e){}
			};
		})(this);
	};

	FormValidate.prototype._formElementOrNode = function(elem){
		return (typeof elem === "object") ? elem : document.forms[elem];
	};

	FormValidate.prototype._addField = function(field,nameValue){
		this.fields[nameValue] = {
			name  : nameValue,
			display: field.display || nameValue,
			rules : field.rules 
		};
	};

	FormValidate.prototype._validateForm = function(evt){
		for(var prop in this.fields){
			
			if(this.fields.hasOwnProperty(prop)){
				var field   = this.fields[prop] || {},
					element = this.form[field.name];

				if(element && element !== undefined){
					field.id = element.id;
					field.element = element;
					field.type    = (element.length > 0) ? element[0].type : element.type;
					field.value   = element.value;

					this._validateField(field);
				}
			}
		}

		if(typeof this.callback === "function"){
			this.callback(this.errors);
		}

		if(this.errors.length > 0){
			for(var i = 0,len = this.errors.length;i<len;i++){
				this.form[this.errors[i].id].nextElementSibling.innerHTML = this.errors[i].id;
			}
		}

		return true;
	};

	FormValidate.prototype._validateField = function(field){
		var rules 	  = field.rules.split("|");
			
			for(var i = 0,rulesLength = rules.length;i<rulesLength;i++){
				var method = rules[i],
					param  = null,
					failed = false,
					parts  = ruleRegex.exec(method);

					if(parts){
						method = parts[1];
						param  = parts[2];
					} 

				if(typeof this._hook[method] === "function"){
					if(!this._hook[method].apply(this,[field,param])){
						failed = true;
					}
				}

				if(failed){
					var src = this.messages[field.name+"."+method] || this.messages[method] || defaults.messages[method],
						message = " Erro in "+field.display+" field";

						if(src){
							message = src.replace("%s",field.display);
							if(param){
								message = message.replace("%s",param); 
							}
						}

					this.errors.push({
						id      : field.id,
						element : field.element,
						name    : field.name,
						message : message,
						rule    : method 
					});

				break;   
				}
			} 
	};

	FormValidate.prototype._hook = {
		required 	 : function(field){
			return (field.value !== null && field.value !== "");
		},

		valid_email  : function(field){
			return emailRegex.test(field.value);
		},

		alph_num_dash : function(field){
			return alph_num_dash.test(field.value);
		},

		max_length    : function(field,param){
			if(!numericRegex.test(param)){
				return false;
			}
			return (field.value.length <= parseInt(param,10));
		},

		min_length    : function(field,param){
			if(!numericRegex.test(param)){
				return false;
			}

			return (field.value.length >= parseInt(param,10));
		},

		matches       : function(field,matchName){
			var el = this.form[matchName];
			if(el){
				return field.value === el.value;
			}
			return false;
		}

	};

	var form = new FormValidate("form-register",[{
			name  : "email",
			rules : "valid_email|required"
		},{
			name  : "username",
			display : "username", 
			rules :  "required|alph_num_dash|min_length[3]|max_length[15]" 
		},{
			name  : "password", 
			rules : "required|min_length[6]" 
		},{
			name  : "confirmpassword",
			rules : "required|matches[password]" 
		}],function(errors){
			if(errors.length > 0){
				for(var i = 0;i<errors.length;i++){
				 	//console.log(errors[i].message);  
				}
			}
		}); 

})(window,document);