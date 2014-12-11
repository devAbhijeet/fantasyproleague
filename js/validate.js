(function(window,document){

	var defaults = {
		message : {
			required     : "The %s field is required",
			validEmail   : "The %s field is invalid",
			alphaNDash   : "The %s field must be alphabet,numbers and underscores",
			maxLength    : "The %s field must be max of %s characters",
			minLength    : "The %s field must be min of %s characters",
			matches      : "The password field are not matching" 
		}
	};

	var ruleRegex     = /^(.+?)\[(.+)\]$/,  
		numericRegex  = /^[0-9]+$/,
		emailRegex    = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.(?:[A-Z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|asia|jobs|museum)$/i,
		alphaNumDash  =  /^[a-z0-9_\-]+$/i; 


	var formValidate  = function(formField,fields){
			this.message = {};
			this.error   = {};
			this.field   = {};
			this.form    = this.getFormElement(formField);
			
			for(var i=0,fieldLength=fields.length;i<fieldLength;i++){
				var field = fields[i];
				
				if((!field.name || !field.rules)){
					continue;
				}
				this.addField(field,field.name);
			}

			var onkeyup = this.form.onkeyup;
			this.form.onkeyup = (function(that){
				return function(e){
					that.validateForm(e);
				};
			})(this);
	};

	formValidate.prototype.getFormElement = function(elem){ 
		return (typeof elem === "object") ? elem : document.forms[elem];
	};

	formValidate.prototype.addField = function(field,fieldName){
		this.field[fieldName] = field;
	};

	formValidate.prototype.validateForm = function(e){
		var currentField = e.target.name; 
 		if(this.field.hasOwnProperty(currentField)){
 			var field = this.field[currentField] || {},
 				element = this.form[currentField];
 				
 				if(element && element !== undefined){
					field.id      = element.id;
					field.element = element;
					field.type    = (element.length > 0) ? element[0].type : element.type;
					field.value   = element.value;

					this.validateField(field,element);
				}
		}

		
		if(this.error.hasOwnProperty(field.name)){
			
			var errorElement = this.form[this.error[field.name].name];

			if(errorElement){
				errorElement.nextElementSibling.innerHTML = (this.error[field.name].message !== "") ? 
															this.error[field.name].message : "";
			}

		}

		return true;
	};

	formValidate.prototype.validateField = function(field,element){
		var rules = field.rules.split("|");

		for(var i=0,rulesLength=rules.length;i<rulesLength;i++){
			var method = rules[i],
				param  = null,
				failed = false,
				parts  = ruleRegex.exec(method);

				if(parts){
					method = parts[1];
					param  = parts[2];
				}

				if(typeof this.hook[method] === "function"){
					if(!this.hook[method].apply(this,[field,param])){
						failed = true;
					}
				}

				if(failed){
					var src = this.message[field.name+"."+method] || this.message[method] || defaults.message[method],
						message = " Error in "+field.display+" field";
						
					if(src){
						message = src.replace("%s",field.display);
						if(param){
							message = message.replace("%s",param);
						}
					}

					this.error[field.name] = {
						id      : field.id,
						element : field.element,
						name    : field.name,
						message : message,
						rule    : method
					};

				}else{
					this.error[field.name] = {
						id      : field.id,
						element : field.element,
						name    : field.name,
						message : "",
						rule    : method
					};
				}

		}

	};

	formValidate.prototype.hook = {
		required     : function(field){
			return (field.value !== null || field.value !=="");
		},

		validEmail   : function(field){
			return emailRegex.test(field.value);
		},

		alphaNDash   : function(field){
			return alphaNumDash.test(field.value);
		},

		maxLength    : function(field,param){
			if(!numericRegex.test(param)){
				return false;
			}
			return (field.value.length <= parseInt(param,10));
		},

		minLength    : function(field,param){
			if(!numericRegex.test(param)){
				return false;
			}
			return (field.value.length >= parseInt(param,10));
		},

		matches      : function(field,param){
			var el = this.form[param];
			if(el){
				return field.value === el.value;
			}
			return false;
		}

	};

	 var form = new formValidate("form-register",[{
	 	name    : "email",
	 	display : "email",
	 	rules   : "validEmail"
	 },{
	 	name    : "username",
		display : "username", 
		rules   :  "minLength[3]|maxLength[5]"
	 },{
	 	name  	: "password",
	 	display : "password",  
		rules 	: "minLength[6]"
	 },{
	 	name  	: "confirmpassword",
	 	display : "confirmpassword", 
		rules   : "matches[password]" 
	 }]);

})(window,document);