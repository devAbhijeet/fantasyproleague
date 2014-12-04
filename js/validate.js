(function(window,document){

	var defaults = {
		"messages" : {
			"required" : "The %s field is required"
		}
	};

	var FormValidate = function(formElementOrNode,fields){
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
			rules : field.rules 
		};
	};

	FormValidate.prototype._validateForm = function(evt){
		this.errors = [];

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

		if(this.errors.length > 0){
			for(var i = 0,len = this.errors.length;i<len;i++){
				console.log(this.errors[i].message);
			}
		}

		return true;
	};

	FormValidate.prototype._validateField = function(field){
		var rules 	  = field.rules,
			failed    = false;

			if(typeof this._hook[rules] === "function"){
				if(!this._hook[rules].apply(this,[field])){
					failed = true;
				}
			}

			if(failed){
				var message = "There was a failure in "+field.name+" field";
				//console.log(message);
				this.errors.push({
					id: field.id,
					element : field.element,
					name : field.name,
					message : message,
					rule : rules 
				});
			} 
	};

	FormValidate.prototype._hook = {
		required : function(field){
			return (field.value !== null && field.value !== "");
		}
	};

	var form = new FormValidate("form-register",[{
			"name"  : "username",
			"rules" : "required"
		},{
			"name"  : "email",
			"rules" : "required"
		}]);


})(window,document);