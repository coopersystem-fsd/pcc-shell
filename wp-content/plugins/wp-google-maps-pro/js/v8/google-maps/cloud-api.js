/**
 * @namespace WPGMZA
 * @module CloudAPI
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.CloudAPI = function()
	{
		
	}
	
	WPGMZA.CloudAPI.createInstance = function()
	{
		return new WPGMZA.CloudAPI();
	}
	
	Object.defineProperty(WPGMZA.CloudAPI, "url", {
		value:		"https://www.wpgmaps.com/cloud/public",
		writable:	false
	});
	
	Object.defineProperty(WPGMZA.CloudAPI, "isBeingUsed", {
		get: function() {
			return /^wpgmza[a-f0-9]+$/.test(WPGMZA.settings.wpgmza_google_maps_api_key);
		}
	});
		
	Object.defineProperty(WPGMZA.CloudAPI, "key", {
		get: function() {
			return WPGMZA.settings.wpgmza_google_maps_api_key;
		}
	});
	
	var nativeCallFunction = WPGMZA.CloudAPI.call;
	WPGMZA.CloudAPI.call = function()
	{
		console.warn("WPGMZA.CloudAPI.call was called statically, did you mean to call the function on WPGMZA.cloudAPI?");
		
		nativeCallFunction.apply(this, arguments);
	}
	
	WPGMZA.CloudAPI.prototype.call = function(url, options)
	{
		if(!options)
			options				= {};
		
		if(!options.data)
			options.data		= {};
		
		var sessionToken;
		var language 			= WPGMZA.locale.substr(0, 2);
		
		if(options.data.sessiontoken)
		{
			sessionToken = options.data.sessiontoken;
			delete options.data.sessiontoken;
		}
		
		if(WPGMZA.locale == "he_IL")
			language = "iw";
		
		options.url				= WPGMZA.CloudAPI.url + url;
		options.beforeSend		= function(xhr) {
			xhr.setRequestHeader('X-WPGMZA-CLOUD-API-KEY', WPGMZA.CloudAPI.key);
			
			if(sessionToken)
				xhr.setRequestHeader('X-WPGMZA-CLOUD-API-SESSION-TOKEN', sessionToken);
		};
		
		options.data.language	= language;
		
		$.ajax(options);
	}
	
});