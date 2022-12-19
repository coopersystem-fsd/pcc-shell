/**
 * @namespace WPGMZA
 * @module CategoryTreeNode
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.CategoryTreeNode = function(options)
	{
		this.children = [];
		
		for(var name in options)
		{
			switch(name)
			{
				case "children":
				
					for(var i = 0; i < options.children.length; i++)
					{
						var child = WPGMZA.CategoryTreeNode.createInstance(options.children[i]);
						child.parent = this;
						this.children.push(child);
					}
					
					break;
				
				default:
				
					this[name] = options[name];
					
					break;
			}
		}
	}
	
	WPGMZA.extend(WPGMZA.CategoryTreeNode, WPGMZA.EventDispatcher);
	
	WPGMZA.CategoryTreeNode.createInstance = function(options)
	{
		return new WPGMZA.CategoryTreeNode(options);
	}
	
	WPGMZA.CategoryTreeNode.prototype.getChildByID = function(id)
	{
		if(this.id == id)
			return this;
		
		for(var i = 0; i < this.children.length; i++)
		{
			var result = this.children[i].getChildByID(id);
			
			if(result)
				return result;
		}
		
		return null;
	}
	
});