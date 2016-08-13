/*!
 * UI development toolkit for HTML5 (OpenUI5)
 * (c) Copyright 2009-2015 SAP SE or an SAP affiliate company.
 * Licensed under the Apache License, Version 2.0 - see LICENSE.txt.
 */

//Provides the base implementation for all model implementations
sap.ui.define([
'jquery.sap.global', 'sap/ui/model/SimpleType', 'sap/ui/model/FormatException', 'sap/ui/model/ParseException', 'sap/ui/model/ValidateException', 'sap/ui/model/type/String'],
function(jQuery, SimpleType, FormatException, ParseException, ValidateException, String) {
	"use strict";


	/**
	 * Constructor for a ZString type.
	 *
	 * @class
	 * This class represents string simple types.
	 *
	 * @extends sap.ui.model.type.String
	 *
	 * @author Maurício Lauffer
	 * @version 1.0
	 *
	 * @constructor
	 * @public
	 * @param {object}  [oFormatOptions] formatting options. String doesn't support any formatting options
	 * @param {object}  [oConstraints] value constraints. All given constraints must be fulfilled by a value to be valid  
	 * @param {boolean} [oConstraints.email] maximum length (in characters) that a string of this value may have  
	 * @param {boolean} [oConstraints.url] maximum length (in characters) that a string of this value may have  
	 * @alias com.mlauffer.gotmoneyappui5.controller.ZString
	 */
	var StringType = String.extend("com.mlauffer.gotmoneyappui5.controller.ZString", /** @lends com.mlauffer.gotmoneyappui5.controller.ZString.prototype */ {
		constructor : function () {
			SimpleType.apply(this, arguments);
			this.sName = "ZString";
		}
	});

	/**
	 * @see sap.ui.model.SimpleType.prototype.validateValue
	 */
	StringType.prototype.validateValue = function(sValue) {
		var oString = new String(this.oFormatOptions, this.oConstraints);
		oString.validateValue(sValue);

		if (this.oConstraints) {
			var oBundle = sap.ui.getCore().getLibraryResourceBundle(),
			aViolatedConstraints = [],
			aMessages = [];
			jQuery.each(this.oConstraints, function(sName, oContent) {
				switch (sName) {
				case "email":  // expects boolean
					if (oContent && sValue.length > 0) {
						// Copyright (c) Jörn Zaefferer, MIT licensed
						// https://github.com/jzaefferer/jquery-validation
						var sRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
						if (!sRegex.test(sValue)) {
							aViolatedConstraints.push("search");
							aMessages.push(oBundle.getText("String.Search", 'E-mail'));
						}
					}
					break;

				case "url": // expects boolean
					if (oContent && sValue.length > 0) {
						// Copyright (c) Jörn Zaefferer, MIT licensed
						// https://github.com/jzaefferer/jquery-validation
						var sRegex = /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i;
						if (!sRegex.test(sValue)) {
							aViolatedConstraints.push("search");
							aMessages.push(oBundle.getText("String.Search", 'URL'));
						}
					}
					break;
				}
			});
			if (aViolatedConstraints.length > 0) {
				throw new ValidateException(aMessages.join(" "), aViolatedConstraints);
			}
		}
	};


	return StringType;

});
