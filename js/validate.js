(function(window,document){

	var defaults = {
		messages : {
			required   : "The %s field is required",
			validEmail : "The %s field is invalid",
			minLength  : "The %s field must be a minimum of %s length",
			matches    : "The %s field does not matches the %s field" 
		}
	};

	var ruleRegex     = /^(.+?)\[(.+)\]$/,
		numericRegex  = /^[0-9]+$/,
		emailRegex    = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.(?:[A-Z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|asia|jobs|museum)$/i;

	var FormValidate = function(formElement,fields){
		var thisObj    = this;
		this.form       = this.getForm(formElement);
		this.toValidate = this.getToValidateField("validate-locally"); 
		this.fields = {};
			
		for(var i=0,fieldsLength=fields.length;i<fieldsLength;i++){
			var field = fields[i];

			this.addField(field,field.name);
		}

		for(var i=0,fieldsLength=this.toValidate.length;i<fieldsLength;i++){
			this.toValidate[i].onfocus = function(){
				var field = this;
				thisObj.setStyle.border("#15c",field);
			};

			this.toValidate[i].onblur = function(){
				var currentThis  = this;
				var currentField = this.name;
				if(thisObj.fields.hasOwnProperty(currentField)){
					var field   = thisObj.fields[currentField] || {},
						element = thisObj.form[currentField];

						if(element){
							field.id    = element.id;
							field.value = element.value;

							thisObj.validateField(field,currentThis);
						} 
				}
			};
		};
			
	};

	FormValidate.prototype.getForm = function(formElement){
		return (typeof formElement === "object") ? formElement : document.forms[formElement];
	};

	FormValidate.prototype.getToValidateField = function(field){
		return (typeof field === "object") ? field : document.getElementsByClassName(field);
	};

	FormValidate.prototype.addField = function(field,fieldName){
		this.fields[fieldName] = field;
	};

	FormValidate.prototype.setStyle = {
		border : function(color,field){
			field.style.border = "1px solid "+color;
		}
	};

	FormValidate.prototype.validateField = function(field,currentThis){
		var rules = field.rules.split("|");

		for(var i=0,rulesLength=rules.length;i<rulesLength;i++){
			var method   = rules[i],
				param    = null, 
				failed   = false,
				parts    = ruleRegex.exec(method),
				sibling  = this.form[field.name].nextElementSibling;
					
				if(parts){
					method = parts[1];
					param  = parts[2];
				}

				if(typeof this.hook[method] === "function"){
					if(!this.hook[method].apply(this,[field,param])){
						failed = true;
						this.setStyle.border("#dd4b39",currentThis);

						if(failed){
							var src     = defaults.messages[method],
								message = "Error in "+field.display+" field";

							if(src){
								message = src.replace("%s",field.display);
								if(param){
									message = message.replace("%s",param);
								}
							}
							sibling.innerHTML = message;
						}  
					}else{
						this.setStyle.border("#15c",currentThis);
						sibling.innerHTML = "";
					}
				}

		}
	};

	FormValidate.prototype.hook = {
		required   : function(field){
			return (field.value !== ""); 
		},

		validEmail : function(field){
			return emailRegex.test(field.value);
		},

		minLength  : function(field,param){
			if(!numericRegex.test(param)){
				return false;
			}
			return (field.value.length >= parseInt(param,10));
		},

		matches    : function(field,param){
			var el = this.form[param];
			if(el){
				return field.value === el.value;
			}
			return false;
		}

	};

	var validate = new FormValidate("form-register",[{
		name    : "email",
		display : "email", 
		rules   : "required|validEmail" 
	},{
		name    : "username",
		display : "username", 
		rules   : "required"
	},{
		name    : "password",
		display : "password", 
		rules   : "minLength[6]" 
	},{
		name    : "confirmpassword",
		display : "confirmpassword",
		rules   : "matches[password]"
	}]);

})(window,document); 