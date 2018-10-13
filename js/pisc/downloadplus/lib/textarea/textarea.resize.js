/**
 * DownloadplusMagazine Textarea Resize for Prototype
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_DownloadplusMagazine
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

var TextareaResize = Class.create();
TextareaResize.prototype = {

    initialize: function(element, options) {
        this.element = $(element);
        this.options = Object.extend({maxRows: 50}, options || {} );
        this.options.origHeight = this.element.offsetHeight;

        Event.observe(this.element, 'keyup', this.onKey.bindAsEventListener(this));
        this.onKey();
    },

    onKey: function() {
    	if (this.element.scrollHeight > this.element.offsetHeight) {
            while (this.element.scrollHeight > this.element.offsetHeight && this.element.rows < this.options.maxRows) {
                this.element.rows = this.element.rows + 1;
            }
            this.element.rows = this.element.rows + 2;
        	this.element.setStyle({overflow:'hidden'});
    	}
    	if (this.element.scrollHeight < this.element.offsetHeight) {
            while (this.element.scrollHeight < this.element.offsetHeight && this.element.offsetHeight > this.options.origHeight) {
                this.element.rows = this.element.rows - 1;
            }
        	this.element.setStyle({overflow:'hidden'});
    	}
        
        if (this.element.rows > this.options.maxRows) {
        	this.element.setStyle({overflow:'auto'});
        }
    }

};
