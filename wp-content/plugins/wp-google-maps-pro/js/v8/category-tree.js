/**
 * @namespace WPGMZA
 * @module CategoryTree
 * @requires WPGMZA.CategoryTreeNode
 */
jQuery(function($) {
	
	WPGMZA.CategoryTree = function(options)
	{
		WPGMZA.CategoryTreeNode.call(this, options);
	}
	
	WPGMZA.extend(WPGMZA.CategoryTree, WPGMZA.CategoryTreeNode);
	
	WPGMZA.CategoryTree.createInstance = function(options)
	{
		return new WPGMZA.CategoryTree(options);
	}
	
	WPGMZA.CategoryTree.prototype.getCategoryByID = function(id)
	{
		return this.getChildByID(id);
	}
	
	if(WPGMZA.categoryTreeData)
	{
		WPGMZA.categories = WPGMZA.CategoryTree.createInstance(WPGMZA.categoryTreeData);
		
		// Delete the data, we require that the user interacts with the interface, not the raw data
		delete WPGMZA.categoryTreeData;
	}
	
});