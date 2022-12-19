/**
 * @namespace WPGMZA
 * @module ProAddressInput
 * @requires WPGMZA.AddressInput
 */
jQuery(function($) {
	
	WPGMZA.ProAddressInput = function(element, map)
	{
		WPGMZA.AddressInput.apply(this, arguments);
		
		this.useMyLocationButton = new WPGMZA.UseMyLocationButton(element);
		$(this.element).after(this.useMyLocationButton.element);
	}
	
	WPGMZA.extend(WPGMZA.ProAddressInput, WPGMZA.AddressInput);
	
	WPGMZA.AddressInput.createInstance = function(element, map)
	{
		return new WPGMZA.ProAddressInput(element, map);
	}
	
});