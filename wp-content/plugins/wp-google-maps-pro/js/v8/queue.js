/**
 * @namespace WPGMZA
 * @module Queue
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/*
	 * Adapted from, and with thanks to https://github.com/mourner/tinyqueue
	 */
	
	function defaultCompare(a, b) {
		return a < b ? -1 : a > b ? 1 : 0;
	}

	WPGMZA.Queue = function(data, compare)
	{
		if(!data)
			data = [];
		
		if(!compare)
			compare = defaultCompare;
		
		this.data = data;
		this.length = this.data.length;
		this.compare = compare;
		
		if(this.lenght > 0)
			for(var i = (this.length >> 1) - 1; i >= 0; i--)
				this._down(i);
	}

	WPGMZA.Queue.prototype.push = function(item)
	{
		this.data.push(item);
		this.length++;
		this._up(this.length - 1);
	}

	WPGMZA.Queue.prototype.pop = function()
	{
		if(this.length === 0)
			return undefined;
		
		var top = this.data[0];
		var bottom = this.data.pop();
		this.length--;
		
		if(this.length > 0)
		{
			this.data[0] = bottom;
			this._down(0);
		}
		
		return top;
	}

	WPGMZA.Queue.prototype.peek = function()
	{
		return this.data[0];
	}

	WPGMZA.Queue.prototype._up = function(pos)
	{
		var data = this.data;
		var compare = this.compare;
		var item = data[pos];
		
		while(pos > 0)
		{
			var parent = (pos - 1) >> 1;
			var current = data[parent];
			
			if(compare(item, current) >= 0)
				break;
			
			data[pos] = current;
			pos = parent;
		}
		
		data[pos] = item;
	}

	WPGMZA.Queue.prototype._down = function(pos)
	{
		var data = this.data;
		var compare = this.compare;
		var halfLength = this.length >> 1;
		var item = data[pos];
		
		while(pos < halfLength)
		{
			var left = (pos << 1) + 1;
			var best = data[left];
			var right = left + 1;
			
			if(right < this.length && compare(data[right], best) < 0)
			{
				left = right;
				best = data[right];
			}
			
			if(compare(best, item) >= 0)
				break;
			
			data[pos] = best;
			pos = left;
		}
		
		data[pos] = item;
	}
	
});