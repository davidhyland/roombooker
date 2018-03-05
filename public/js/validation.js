(function($) {

    var $thisForm;

    /*
    Validation Singleton
    */
    var Validation = function() {
        
        var rules = {
            
            email : {
               check: function(value) {
                   
                   if(value)
                       return testPattern(value,".+@.+\..+");
                   return true;
               },
               msg : "Enter a valid e-mail address. "
            },
            email_confirm : {

               check : function(value) {
                   return (value == $thisForm.find('input[validation*=email]').val()) ? true : false;
               },
               msg : "Emails do not match. "
            },
            url : {

               check : function(value) {

                   if(value)
                       return testPattern(value,"https?://(.+\.)+.{2,4}(/.*)?");
                   return true;
               },
               msg : "Enter a valid URL. "
            },
            phone : {

               check : function(value) {

                   if(value)
                       return testPatternMatch(value,/^\(?\d{3}\)?-? *\d{3}-? *-?\d{4}$/gi);
                   return true;
               },
               msg : "Phone number is not valid. "
            },
            number : {

               check : function(value) {

                   if(value)
                       return testPatternMatch(value,/^\d+$/gi);
                   return true;
               },
               msg : "This field must be a number "
            },
            zipcode : {

               check : function(value) {

                   if(value)
                       return testPatternMatch(value,/^\d{5}(?:[-\s]\d{4})?$/gi);
                   return true;
               },
               msg : "Zipcode is not valid. "
            },
            password : {

               check : function(value) {
                   return (value.length >= 6) ? true : false;
                   return true;
               },
               msg : "Password must be at least 6 characters "
            },
            password_confirm : {

               check : function(value) {
                   return (value == $thisForm.find('input[name=password]').val()) ? true : false;
               },
               msg : "Passwords do not match. "
            },
            required : {
                
               check: function(value) {

                   if(value)
                       return true;
                   else
                       return false;
               },
               msg : function(label) {
                  return (label != '') ? label.replace(/\W/g, ' ')+" is required. " : "This field is required. ";
               }
            }
        }
        var testPattern = function(value, pattern) {
            var regExp = new RegExp("^"+pattern+"$","");
            return regExp.test(value);
        }
        var testPatternMatch = function(value, pattern) {
            return value.match(pattern);
        }
        return {
            
            addRule : function(name, rule) {

                rules[name] = rule;
            },
            getRule : function(name) {

                return rules[name];
            }
        }
    }
    
    /* 
    Form factory 
    */
    var Form = function(form) {
        
        var fields = [];
        //form.find("input[validation], textarea[validation]").each(function() {
        form.find("input[validation], textarea[validation], select[validation]").each(function() {
           
            fields.push(new Field(this));
        });
        this.fields = fields;
    }
    Form.prototype = {
        validate : function() {

            for(field in this.fields) {
                
                this.fields[field].validate();
            }
        },
        isValid : function() {
            
            for(field in this.fields) {
                
                if(!this.fields[field].valid) {
            
                    this.fields[field].field.focus();
                    return false;
                }
            }
            return true;
        }
    }
    
    /* 
    Field factory 
    */
    var Field = function(field) {

        this.field = $(field);
        this.valid = false;
        this.attach("change");
    }
    Field.prototype = {
        
        attach : function(event) {
        
            var obj = this;
            if(event == "change") {
                obj.field.bind("change",function() {
                    return obj.validate();
                });
            }
            if(event == "keyup") {
                obj.field.bind("keyup",function(e) {
                    return obj.validate();
                });
            }
        },
        validate : function() {
            
            var obj = this,
                field = obj.field,
                errorClass = "errorlist",
                errorlist = $(document.createElement("div")).addClass(errorClass),
               // errorlist = $(document.createElement("ul")).addClass(errorClass),
                types = field.attr("validation").split(" "),
                container = field.parent(),
                errors = []; 
            
            if(!field.hasClass('custom')) field.next(".errorlist").remove();
            //field.next(".errorlist").remove();
            for (var type in types) {

                var rule = $.Validation.getRule(types[type]);
                if(!rule.check(field.val())) {
                    container.addClass("error");
                    if(types[type] == 'required')
                      errors.push(rule.msg(container.find('label').text()));
                    else
                      errors.push(rule.msg);
                    //console.log(field.attr('name') + ':' + field.val());
                }
            }
            if(errors.length) {

                obj.field.unbind("keyup")
                obj.attach("keyup");
                field.after(errorlist.empty());
                for(error in errors) {
                    errorlist.append(errors[error]); 
                    //errorlist.append("<li>"+ errors[error] +"</li>");
                }
                obj.valid = false;
            } 
            else {
                errorlist.remove();
                if(!field.hasClass('custom')) container.removeClass("error");
                obj.valid = true;
            }
        }
    }
    
    /* 
    Validation extends jQuery prototype
    */
    $.extend($.fn, {
        
        validation : function() {
            
            var validator = new Form($(this));
            $thisForm = $(this);
            $.data($(this)[0], 'validator', validator);
            
            $(this).bind("submit", function(e) {
                validator.validate();
                if(!validator.isValid()) {
                    e.preventDefault();
                }
            });
        },
        validate : function() {
            
            var validator = $.data($(this)[0], 'validator');
            validator.validate();
            return validator.isValid();
            
        },
        validform : function() {
            
            var validator = new Form($(this));
            $.data($(this)[0], 'validator', validator);
            
            validator.validate();
            if(validator.isValid()) {
               return true
            }
            else{
               return false
            }
            
        }
    });
    $.Validation = new Validation();
})(jQuery);