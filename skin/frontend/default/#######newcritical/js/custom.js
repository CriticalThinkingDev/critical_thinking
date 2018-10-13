var kv = jQuery.noConflict();



function criticalrs(){
var vkt = kv(window).width();


if (vkt < 767) {
// console.log(vkt);

kv('.subheader').insertBefore('.header');
kv('.company_info_links h3').addClass('waves-effect waves-light');
kv('.mob-nav').addClass('side-nav');

kv('.menu-collapse').sideNav();
kv('.mob-nav').css('transform', '-100%');
//kv('ul#nav, ul#nav1').addClass('collapsible');
kv('.level0 .level-top').addClass('cble-headeri waves-effect waves-light');
kv('.level0 .level-top').next('ul.level0').addClass('collapsible-body');
kv('.level0.arrow .level-top').unbind('click');
kv('.level0.arrow .level-top').click(function(e){ e.preventDefault();});
kv('ul.tabs').tabs('select_tab', 'tab_id');
kv('.homepage_shopbyneed1, .top_shopby').insertAfter('.mb-shop-by');
kv('.homepage_shopbyneed2, .top_shopby').insertAfter('.mb-shop-by');
kv('.left_search_box1').insertAfter('.mb-search-by');
kv('#my-orders-table').addClass('responsive-table');
//kv('.collapsible-header').click(function(e){kv(this).next().slideToggle();});
kv('.filter_dropbox .s6').unbind('click');
kv('.filter_dropbox .s6').click(function(e){ kv(this).next('div').toggle(); kv(this).toggleClass('active'); kv('.trans_bg').toggle();  });
kv('.cble-header').unbind('click');
kv('.cble-header').click(function(e){kv(this).next('.collapsible-body').slideToggle(); kv(this).parent().toggleClass('active'); 
kv(this).parent().siblings().removeClass('active').children().next().slideUp();
 //return false;
});
kv('.cble-headeri').click(function(e){kv(this).next('.collapsible-body').slideToggle(); kv(this).parent().toggleClass('active'); 
kv(this).parent().siblings().removeClass('active').children().next().slideUp();
 //return false;
});
kv('.customer-account-create .menu-collapse, .customer-account-create .filter_dropbox .s6').unbind('click');

}else if (vkt < 980){

kv('.subheader').insertAfter('.header');
kv('.top_shopby').insertAfter('.header-container');
	
kv('.mob-nav').addClass('side-nav');

kv('.menu-collapse').sideNav();

kv('.mob-nav').css('transform', '-100%');

//kv('ul#nav, ul#nav1').addClass('collapsible');
kv('.level0 .level-top').addClass('cble-headeri waves-effect waves-light');
kv('.level0 .level-top').next('ul.level0').addClass('collapsible-body');
kv('.level0.arrow .level-top').unbind('click');
kv('.level0.arrow .level-top').click(function(e){ e.preventDefault();});
kv('ul.tabs').tabs('select_tab', 'tab_id');
kv('.cble-header').unbind('click');
kv('.cble-header').click(function(e){kv(this).next('.collapsible-body').slideToggle(); kv(this).parent().toggleClass('active'); 
kv(this).parent().siblings().removeClass('active').children().next().slideUp();
 //return false;
});
kv('.cble-headeri').click(function(e){kv(this).next('.collapsible-body').slideToggle(); kv(this).parent().toggleClass('active'); 
kv(this).parent().siblings().removeClass('active').children().next().slideUp();
 //return false;
});
kv('.customer-account-create .menu-collapse, .customer-account-create .filter_dropbox .s6').unbind('click');
}

if (vkt > 768) {

	kv('.menu-collapse').sideNav('hide');

	kv( ".menu-close" ).click();
}
if (vkt > 980) {
kv('.subheader').insertAfter('.header');
kv('.left_search_box1').insertBefore('#nav-mobile');
kv('.left_search_box1').removeAttr("style");
kv('#imgdiv').insertAfter('#d_banner_cat_txt');
kv('#imgdiv').removeAttr("style");
kv('.menu-collapse').sideNav('hide');


kv('.mob-nav').css('transform', '0');
kv('.mob-nav').removeClass('side-nav');
		kv('.mob-nav').removeAttr("style");
		



	}else{
kv('.mob-nav').addClass('side-nav');
kv('.mob-nav').css('transform', '-100%');
	}


};    

// criticalrs();
kv(document).ready(criticalrs);
//kv(document).resize(criticalrs);
       kv( window ).resize(criticalrs);


kv(window).bind("pageshow", function(event) {
    if (event.originalEvent.persisted) {
		
        window.location.reload() 
    }
});


