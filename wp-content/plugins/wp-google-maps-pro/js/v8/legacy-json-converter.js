/**
 * @namespace WPGMZA
 * @module LegacyJSONConverter
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.LegacyJSONConverter = function()
	{
		
	}
	
	WPGMZA.LegacyJSONConverter.prototype.convert = function(json)
	{
		var markers = [];
		
		if(typeof json == "string")
			json = JSON.parse(json);
		
		for(var key in json)
		{
			
			function getField(name)
			{
				return json[name];
			}
			
			var data = {
				map_id:			getField("map_id"),
				marker_id:		getField("marker_id"),
				title:			getField("title"),
				address:		getField("address"),
				icon:			getField("icon"),
				pic:			getField("pic"),
				desc:			getField("desc"),
				linkd:			getField("linkd"),
				anim:			getField("anim"),
				retina:			getField("retina"),
				category:		getField("category"),
				lat:			getField("lat"),
				lng:			getField("lng"),
				infoopen:		getField("infoopen")
			};
			
			markers[data.marker_id] = data;
			
		}
		
		return markers;
	}
	
});