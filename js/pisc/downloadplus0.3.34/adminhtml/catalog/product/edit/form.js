/**
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * Downloadplus Form Scripts
 *
 * @author     Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.0
 */

downloadplusAdminForm = Class.create();
downloadplusAdminForm.prototype = {

	initialize: function(){
	},

    bindFieldsChange : function(dataElements){
        for(var i=0; i<dataElements.length;i++){
            Event.observe(dataElements[i], 'change', dataElements[i].setHasChanges.bind(dataElements[i]));
        }
    },

    getDataElements:function (){
		var elements = $$('*[id^="product_downloadplus_detail"]');
		return elements;
    }

}
