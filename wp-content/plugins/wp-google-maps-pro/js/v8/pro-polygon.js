/**
 * @namespace WPGMZA
 * @module ProPolygon
 * @requires WPGMZA.Polygon
 */
jQuery(function($) {
	
	var Parent;
	
	WPGMZA.ProPolygon = function(row, enginePolygon)
	{
		var self = this;
		
		Parent.call(this, row, enginePolygon);
		
		this.on("mouseover", function(event) {
			self.onMouseOver(event);
		});
		
		this.on("mouseout", function(event) {
			self.onMouseOut(event);
		});

		this.on("click", function(event) {
			self.onClick(event);
		});

		this.initPolygonLabels();

	}
	
	Parent = WPGMZA.Polygon;
	
	WPGMZA.ProPolygon.prototype = Object.create(Parent.prototype);
	WPGMZA.ProPolygon.prototype.constructor = WPGMZA.ProPolygon;
	
	Object.defineProperty(WPGMZA.ProPolygon.prototype, "hoverFillColor", {
		enumerable: true,
		
		"get": function()
		{
			if(!this.ohfillcolor || !this.ohfillcolor.length)
				return "#ff0000";
			
			return "#" + this.ohfillcolor.replace(/^#/, "");
		},
		"set": function(a){
			this.ohfillcolor = a;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProPolygon.prototype, "hoverStrokeColor", {
		enumerable: true,
		
		"get": function()
		{
			if(!this.ohlinecolor || !this.ohlinecolor.length)
				return "#ff0000";
			
			return  "#" + this.ohlinecolor.replace(/^#/, "");
		},
		"set": function(a){
			this.ohlinecolor = a;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProPolygon.prototype, "hoverOpacity", {
		enumerable: true,
		
		"get": function()
		{
			if(!this.ohopacity){
				return 0.6;
			}
			
			return this.ohopacity;
		},
		"set": function(a){
			this.ohopacity = a;
		}
		
	});
	
	/*
	 * Adapted from, and with thanks to https://github.com/mapbox/polylabel
	 */
	WPGMZA.ProPolygon.getLabelPosition = function(geojson, precision, debug)
	{
		var polygon = geojson;
		
		precision = precision || 1.0;

		// find the bounding box of the outer ring
		var minX, minY, maxX, maxY;
		for (var i = 0; i < polygon[0].length; i++) {
			var p = polygon[0][i];
			if (!i || p[0] < minX) minX = p[0];
			if (!i || p[1] < minY) minY = p[1];
			if (!i || p[0] > maxX) maxX = p[0];
			if (!i || p[1] > maxY) maxY = p[1];
		}

		var width = maxX - minX;
		var height = maxY - minY;
		var cellSize = Math.min(width, height);
		var h = cellSize / 2;

		if (cellSize === 0) return [minX, minY];

		// a priority queue of cells in order of their "potential" (max distance to polygon)
		var cellQueue = new WPGMZA.Queue(null, compareMax);

		// cover polygon with initial cells
		for (var x = minX; x < maxX; x += cellSize) {
			for (var y = minY; y < maxY; y += cellSize) {
				cellQueue.push(new Cell(x + h, y + h, h, polygon));
			}
		}

		// take centroid as the first best guess
		var bestCell = getCentroidCell(polygon);

		// special case for rectangular polygons
		var bboxCell = new Cell(minX + width / 2, minY + height / 2, 0, polygon);
		if (bboxCell.d > bestCell.d) bestCell = bboxCell;

		var numProbes = cellQueue.length;

		while (cellQueue.length) {
			// pick the most promising cell from the queue
			var cell = cellQueue.pop();

			// update the best cell if we found a better one
			if (cell.d > bestCell.d) {
				bestCell = cell;
				if (debug) console.log('found best %d after %d probes', Math.round(1e4 * cell.d) / 1e4, numProbes);
			}

			// do not drill down further if there's no chance of a better solution
			if (cell.max - bestCell.d <= precision) continue;

			// split the cell into four cells
			h = cell.h / 2;
			cellQueue.push(new Cell(cell.x - h, cell.y - h, h, polygon));
			cellQueue.push(new Cell(cell.x + h, cell.y - h, h, polygon));
			cellQueue.push(new Cell(cell.x - h, cell.y + h, h, polygon));
			cellQueue.push(new Cell(cell.x + h, cell.y + h, h, polygon));
			numProbes += 4;
		}

		if (debug) {
			console.log('num probes: ' + numProbes);
			console.log('best distance: ' + bestCell.d);
		}

		return [bestCell.x, bestCell.y];
	}
	
	function compareMax(a, b) {
		return b.max - a.max;
	}

	function Cell(x, y, h, polygon) {
		this.x = x; // cell center x
		this.y = y; // cell center y
		this.h = h; // half the cell size
		this.d = pointToPolygonDist(x, y, polygon); // distance from cell center to polygon
		this.max = this.d + this.h * Math.SQRT2; // max distance to polygon within a cell
	}

	// signed distance from point to polygon outline (negative if point is outside)
	function pointToPolygonDist(x, y, polygon) {
		var inside = false;
		var minDistSq = Infinity;

		for (var k = 0; k < polygon.length; k++) {
			var ring = polygon[k];

			for (var i = 0, len = ring.length, j = len - 1; i < len; j = i++) {
				var a = ring[i];
				var b = ring[j];

				if ((a[1] > y !== b[1] > y) &&
					(x < (b[0] - a[0]) * (y - a[1]) / (b[1] - a[1]) + a[0])) inside = !inside;

				minDistSq = Math.min(minDistSq, getSegDistSq(x, y, a, b));
			}
		}

		return (inside ? 1 : -1) * Math.sqrt(minDistSq);
	}

	// get polygon centroid
	function getCentroidCell(polygon) {
		var area = 0;
		var x = 0;
		var y = 0;
		var points = polygon[0];

		for (var i = 0, len = points.length, j = len - 1; i < len; j = i++) {
			var a = points[i];
			var b = points[j];
			var f = a[0] * b[1] - b[0] * a[1];
			x += (a[0] + b[0]) * f;
			y += (a[1] + b[1]) * f;
			area += f * 3;
		}
		if (area === 0) return new Cell(points[0][0], points[0][1], 0, polygon);
		return new Cell(x / area, y / area, 0, polygon);
	}

	// get squared distance from a point to a segment
	function getSegDistSq(px, py, a, b) {

		var x = a[0];
		var y = a[1];
		var dx = b[0] - x;
		var dy = b[1] - y;

		if (dx !== 0 || dy !== 0) {

			var t = ((px - x) * dx + (py - y) * dy) / (dx * dx + dy * dy);

			if (t > 1) {
				x = b[0];
				y = b[1];

			} else if (t > 0) {
				x += dx * t;
				y += dy * t;
			}
		}

		dx = px - x;
		dy = py - y;

		return dx * dx + dy * dy;
	}
	
	/**
	 * Called when the user hovers their cursor over the polygon
	 * @return void
	 */
	WPGMZA.ProPolygon.prototype.onMouseOver = function(event)
	{
		this.revertOptions = this.getScalarProperties();

		var options = {
			fillColor:		this.hoverFillColor,
			strokeColor:	this.hoverStrokeColor,
			fillOpacity:	this.hoverOpacity
		};

		this.setOptions(options);
	}
	
	/**
	 * Called when the user hovers their cursor over the polygon
	 * @return void
	 */
	WPGMZA.ProPolygon.prototype.onMouseOut = function(event)
	{
		var options = {
			fillColor:		this.fillColor,
			strokeColor:	this.strokeColor,
			fillOpacity:	this.fillOpacity
		};

		if(this.revertOptions){
			options =  this.revertOptions;
			this.revertOptions = false;
		}
		
		this.setOptions(options);
	}


	WPGMZA.ProPolygon.prototype.onClick = function(event){
		if(this.map.settings.disable_polygon_info_windows){
			return;
		}

		this.openInfoWindow();
	}

	WPGMZA.ProPolygon.prototype.getPosition = function(){
		return this.getCentroid();
	}

	WPGMZA.ProPolygon.prototype.openInfoWindow = function() {
		if(!this.map) {
			console.warn("Cannot open infowindow for polygon with no map");
			return;
		}
		
		if(this.map.lastInteractedMarker){
			this.map.lastInteractedMarker.infoWindow.close();
		}

		this.map.lastInteractedMarker = this;
		
		this.initInfoWindow();

		this.pic = "";
		this.infoWindow.open(this.map, this);

		//Switched to centroid 2021-01-05 so that it is better aligned
		//this.centroid = this.getCenterApprox();
		
		this.centroid = this.getCentroid();
		
		this.infoWindow.setPosition(this.centroid);

		this.infoWindow.element.classList.add('ol-info-window-polygon');

		if(this.map.settings.click_open_link == 1 && this.link && this.link.length){
			if(WPGMZA.settings.wpgmza_settings_infowindow_links == "yes"){
				window.open(this.link);
			}else{
				window.open(this.link, '_self');
			}
		}
	}

	WPGMZA.ProPolygon.prototype.initInfoWindow = function(){
		if(this.infoWindow)
			return;
		
		this.infoWindow = WPGMZA.InfoWindow.createInstance();
	}

	WPGMZA.ProPolygon.prototype.getCentroid = function(){
		var geojson = [[]];

		for(var i in this.polydata){
			geojson[0].push([
				parseFloat(this.polydata[i].lat),
				parseFloat(this.polydata[i].lng)
			]);
		}

		var latLng = WPGMZA.ProPolygon.getLabelPosition(geojson);
		return new WPGMZA.LatLng({
			lat: latLng[0],
			lng: latLng[1]
		});
	}

	WPGMZA.ProPolygon.prototype.getCenterApprox = function(){
		/** 
		 * This function is less advanced that the centroid alternative, 
		 * Centroid will focus on finding the center of the area, where as this focuses on an average center points
		 * 
		 * May lead to strange placements with odd shapes
		 *
		 * We should use centroid, but at the time of building this, it was un-usable
		*/
		var pos = {
			lat : 0,
			lng : 0
		};

	    var n = this.polydata.length;

	    for(var i in this.polydata){
	    	pos.lat += parseFloat(this.polydata[i].lat);
	    	pos.lng += parseFloat(this.polydata[i].lng);
	    }

		return new WPGMZA.LatLng(pos.lat / n, pos.lng / n);
	}

	WPGMZA.ProPolygon.prototype.initPolygonLabels = function(){
		if(WPGMZA.getMapByID(this.map_id)){
			var settings = WPGMZA.getMapByID(this.map_id).settings;
			if(settings && settings.polygon_labels){
				if(this.title){
					var text = WPGMZA.Text.createInstance({
						text: this.title,
						map: WPGMZA.getMapByID(this.map_id),
						position: this.getCentroid()
					});
				}
			}
		}
	}


});