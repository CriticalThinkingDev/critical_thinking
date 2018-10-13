/**
 * Created by JetBrains PhpStorm.
 * User: pp
 * Date: 12-3-30
 * Time: 17:31
 */
function UnirgyStoreLocator(config) {
    var map;
    var geocoder;
    var mapMarkers = [];
    var infoWindows = [];
    var lastSearch;
    var current_tags = {};
    var current_locations = {};
    var directionsDisplay;
    var directionsService = new google.maps.DirectionsService();
    var config = config;
    var searched = false;
    var searchCallback = function (data) {
        var xml = data.responseXML;
        var entries = xml.documentElement.getElementsByTagName('marker');

        clearAll(false);

        var sidebar = config.sidebarEl;
        if (entries.length == 0) {
            sidebar.innerHTML = 'No results found.';
            map.setCenter(new google.maps.LatLng(40, -100), 4);
            return;
        }
        searched = true;
        var bounds = new google.maps.LatLngBounds();
        for (var i = 0; i < entries.length; i++) {
            var entry = {};
            for (var j = 0, l = entries[i].attributes.length; j < l; j++) {
                entry[entries[i].attributes[j].nodeName] = entries[i].attributes[j].nodeValue;
            }
            ;
            var point = new google.maps.LatLng(parseFloat(entry.latitude), parseFloat(entry.longitude));
            bounds.extend(point);
            var marker = createMarker(entry, point);
            var sidebarEntry = createSidebarEntry(entry, marker);
            sidebar.appendChild(sidebarEntry);
            parseTags(entry);
            current_locations[entry.location_id] = entry;
        }
        fitMap(bounds);
        createTagsRow();
        prepareDirections();
    };

    function searchLocationsNear(params, georesults) {

        var searchUrl = config.searchUrl;
        lastSearch = georesults.geometry.location; // this is potential start point for directions
        params.lat = georesults.geometry.location.lat();
        params.lng = georesults.geometry.location.lng();
        params.loc_type = georesults.types[0];
        params.long_name = georesults.address_components[0].long_name;
        params.short_name = georesults.address_components[0].short_name;

        postSearch(searchUrl, params, searchCallback);
    }
    
    function clearAll(del) {
        clearMarkers(del);
        current_tags = {};
        current_locations = {};
        clearDirections();
        config.sidebarEl.innerHTML = '';
    }

    function fitMap(bounds) {
        map.fitBounds(bounds);
        var zoom = map.getZoom();
        if (map.getZoom() > config.min_zoom) {
            map.setZoom(config.min_zoom);
        }
    }

    function postSearch(url, params, callback) {
        new Ajax.Request(url, {method:'post', parameters:params, onSuccess:callback});
    }

    /**
     * Clear markers from map. Optionally delete them
     * @param del
     */
    function clearMarkers(del) {
        if (mapMarkers.length) {
            for (var i = 0, j = mapMarkers.length; i < j; i++) {
                var marker = mapMarkers[i];
                if (!marker) {
                    continue;
                }
                marker.setMap(null);
                if (del) {
                    mapMarkers[i] = null;
                }
            }
        }
    }

    function disableMarker(loc_id) {
        for (var i = 0, j = mapMarkers.length; i < j; i++) {
            var marker = mapMarkers[i];
            if (marker.config.location_id == loc_id) {
                marker.setMap(null);
            }
        }
    }

    function enableMarker(loc_id) {
        for (var i = 0, j = mapMarkers.length; i < j; i++) {
            var marker = mapMarkers[i];
            if (marker && marker.config.location_id == loc_id) {
                marker.setMap(map);
            }
        }
    }

    function clearInfoWindows(window) {
        if (infoWindows.length) {
            for (var i = 0, j = infoWindows.length; i < j; i++) {
                var info = infoWindows[i];
                if (window == info) {
                    continue;
                }
                info.close()
            }
        }
    }

    function clearDirections() {
        directionsDisplay.setMap(null);
        $$('.step-directions').invoke('hide');
    }

    function escapeUserText(text) {
        if (text === undefined) {
            return null;
        }
        text = text.replace(/@/, "@@");
        text = text.replace(/\\/, "@\\");
        text = text.replace(/'/, "@'");
        text = text.replace(/\[/, "@[");
        text = text.replace(/\]/, "@]");
        return encodeURIComponent(text);
    }

    ;

    function createLabeledMarkerIcon(m) {
        var image;
        var addStar = (m.is_featured === true) || false;
        var width = (addStar) ? 23 : 21;
        var height = (addStar) ? 39 : 34;
        var iconSize = new google.maps.Size(width, height);
        var scaleSize = null;
        if (!m.icon) {
            var opts = config.iconOpts || {};

            var primaryColor = opts.primaryColor || "DA7187";//e5e5e5
            var starColor = opts.starPrimaryColor || "FFFF00";
            var label = escapeUserText(opts.label) || "";
            var labelColor = opts.labelColor || "000000";
            var pinProgram = (addStar) ? "pin_star" : "pin";
            var baseUrl = 'http://chart.apis.google.com/chart?chst=d_map_xpin_letter&chld='
            var iconUrl = baseUrl + pinProgram + "|";
            if (m.marker_label) {
                iconUrl += m.marker_label;
            }
            iconUrl += "|" + primaryColor.replace("#", "") + "|" + labelColor.replace("#", "") + "|" + starColor.replace("#", "")
            image = iconUrl;
        } else {
            image = m.icon;
            width = m.icon_width ? m.icon_width : width;
            height = m.icon_height ? m.icon_height : height;
            if (config.scale_icon) {
                var s_ratio = iconSize.height / height;
                var s_height = height * s_ratio;
                var s_width = width * s_ratio;
                iconSize = new google.maps.Size(s_width, s_height); // scale icon proportions
                scaleSize = new google.maps.Size(iconSize.width, iconSize.height); // scale image to icon size
            } else {
                iconSize = new google.maps.Size(width, height);
            }
        }
        var icon = new google.maps.MarkerImage(image, iconSize, null, null, scaleSize);
        return icon;
    }

    function createMarker(m, point) {
        var icon = createLabeledMarkerIcon(m);
        var options = {
            map:map,
            icon:icon,
            position:point
        };
        var marker = new google.maps.Marker(options);
        marker.config = m;
        mapMarkers.push(marker);
        google.maps.event.addListener(marker, 'click', function () {
            var infoWindow = marker.infoWindow;
            if (!infoWindow) {
                infoWindow = new google.maps.InfoWindow({
                    content:config.generateMarkerHtml(marker.config)
                });
                marker.infoWindow = infoWindow;
                infoWindows.push(infoWindow);
            }
            if (marker.config.zoom) {
                var zoom = Math.abs(parseInt(marker.config.zoom));
                map.setZoom(zoom);
            }
            clearInfoWindows(infoWindow);
            infoWindow.open(map, marker);
        });
        return marker;
    }

    function createSidebarEntry(m, marker) {
        var div = document.createElement('div');
        div.className = "sidebar-entry-container"
        div.id = "sidebar-entry-container-" + m.location_id;
        var a = document.createElement('a');
        a.href = "javascript:void(0)";
        var icon = createLabeledMarkerIcon(m);
        var addStar = (m.is_featured === true) || false;
        var width = (addStar) ? 23 : 21;
        var height = (addStar) ? 39 : 34;
//        var html = '<img src="' + icon.url + '" style="float:left" width="'+ width + '" height="' + height + '"/>';
        var html = '<img src="' + icon.url + '" style="float:left"/>';
        html += config.generateSidebarHtml(m);
        a.innerHTML = html;
        google.maps.event.addDomListener(a, 'click', function () {
            google.maps.event.trigger(marker, 'click');
        });
        div.appendChild(a);
        positionImage(div);
        return div;
    }

    function createDirectionsEntry(m) {
        var div = $('sidebar-entry-container-' + m.location_id);
        if(!div) {
            return;
        }
        var directionsDiv = config.generateDirectionsElement(m);
        div.appendChild(directionsDiv);
    }

    function positionImage(el) {
        el = $(el);
        if (el) {
            var img = el.down('img');
            if (img.complete) {
                posI(img);
            } else {
                img.observe('load', function (e) {
                    posI(this);
                });
            }
        }
        function posI(img) {
            var width = img.width;
            if (width > 50) {
                $(img).setStyle({'float':'none'});
            }
        }
    }

    function parseTags(entry) {
        var tagsString = entry.product_types;
        var tags = tagsString.split(',');
        for (var i = 0; i < tags.length; i++) {
            var tag = tags[i].toLowerCase();
            if (current_tags[tag] == undefined) {
                current_tags[tag] = [entry.location_id];
            } else {
                current_tags[tag].push(entry.location_id);
            }
        }
    }

    function mapCenter() {
        var bounds = null;
        for (var i = 0; i < mapMarkers.length; i++) {
            if (!mapMarkers[i] || mapMarkers[i].getMap() == null) {
                continue;
            }
            if (!bounds) {
                bounds = new google.maps.LatLngBounds();
            }
            var pos = mapMarkers[i].getPosition();
            if (!bounds.contains(pos)) {
                bounds.extend(pos);
            }
        }
        if (bounds) {
            map.panTo(bounds.getCenter());
            fitMap(bounds);
        }
//        map.fitBounds(bounds);
    }

    function createTagsRow() {
        if (current_tags.length == 0) {
            return;
        }
        var tagsRow = $('tag-row').update("<a id='show-all-locations' class='show-all' href='#'>Show all</a> | ");
        var fontSize = 9;
        var tagTpl = new Template("<span class='loc-tag'><a id='#{id}' class='tag-btn' href='#'>#{name}</a></span>&nbsp;");
        for (var tag in current_tags) {
            var tagHtml = tagTpl.evaluate({id:'tag-' + tag, name:tag});
            tagsRow.insert(tagHtml);
        }
        $('show-all-locations').observe('click', function(e){
            Event.stop(e);
            clearInfoWindows();
            clearMarkers(false);
            clearDirections();
            $H(current_locations).each(function(el){
                enableMarker(el.key);
            });
            $$(".sidebar-entry-container").invoke('show');
            mapCenter();
        });
        tagsRow.select('.tag-btn').each(function (a) {
            // add click listener
            var btn = $(a);
            btn.observe('click', function (e) {
                Event.stop(e);
                var tag = this.identify().replace('tag-', '');
                if (!current_tags[tag]) {
                    return;
                }
                clearInfoWindows();
                clearMarkers(false);
                clearDirections();
                $$(".sidebar-entry-container").invoke('hide');
                var ids = current_tags[tag];
                var sideEntryId = "sidebar-entry-container-";
                for (var i = 0; i < ids.length; i++) {
                    $(sideEntryId + ids[i]).show();
                    enableMarker(ids[i]);
                }
                mapCenter();
                return false;
            });
        });
    }

    function prepareDirections() {
        if (!searched){
            return;
        }
        
        $H(current_locations).each(function(el){
            createDirectionsEntry(el.value);
        });
        
        var directions = $$('.directions');
        directions.each(function (div) {
            var dir = $(div);
            var dir_btn = div.down('.dir-btn');
            dir_btn.stopObserving();
            var dir_span = div.down('.get-dir-btn-container');
            var dir_panel = div.down('.step-directions');
            dir_btn.observe('click', function (e) {
                Event.stop(e);
                var btn = $(this);
                if (!lastSearch) {
                    //todo add search field under btn for user to add search origin
                    alert(config.route_not_found);
                    return false;
                }
                var id = btn.identify().replace('get-dir-', '');
                if (!id) {
                    alert(config.route_not_found);
                    return false;
                }
                var entry = current_locations[id];
                if (!entry) {
                    alert(config.route_not_found);
                    return false;
                }
                $$('.step-directions').invoke('hide');
                var origin = lastSearch;
                var destination = new google.maps.LatLng(parseFloat(entry.latitude), parseFloat(entry.longitude));
                var request = {
                    origin:origin,
                    destination:destination,
                    provideRouteAlternatives:false,
                    travelMode:google.maps.TravelMode.DRIVING, // could be also WALKING or BICYCLING
                    unitSystem:config.units == 'mi' ? google.maps.UnitSystem.IMPERIAL : google.maps.UnitSystem.METRIC
                };

                directionsService.route(request, function (result, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        directionsDisplay.setMap(map);
                        directionsDisplay.setPanel(dir_panel);
                        directionsDisplay.setDirections(result);
                        dir_panel.show();
                    } else {
                        alert(config.route_not_found);
                    }
                });
                return false;
            });
        });
    }
    
    function initLocations() {
        var bounds = new google.maps.LatLngBounds();
        current_tags = {};
        current_locations = {};
        clearAll(true);
        var defaultLoc = false;

        for (var i = 0, c = config.initial_locations.length; i < c; i++) {
            var entry = config.initial_locations[i];
            parseTags(entry);
            var point = new google.maps.LatLng(parseFloat(entry.latitude), parseFloat(entry.longitude));
            bounds.extend(point);
            var marker = createMarker(entry, point);
            var sidebarEntry = createSidebarEntry(entry, marker);
            config.sidebarEl.appendChild(sidebarEntry);
            current_locations[entry.location_id] = entry;
            if (entry.location_id == config.defaultLocation) {
                defaultLoc = marker;
            }
        }
        fitMap(bounds);
        if (defaultLoc) {
            setTimeout(function(){
                google.maps.event.trigger(defaultLoc, 'click');
            }, 1000);
        }
    }

    return {
        load:function (options) {
            var myOptions = {
                center: new google.maps.LatLng(40, -100),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                zoom: parseInt(config.min_zoom)
            };
            map = new google.maps.Map(config.mapEl, myOptions);
            directionsDisplay = new google.maps.DirectionsRenderer();
            geocoder = new google.maps.Geocoder();
            initLocations();
            createTagsRow();
        },
        reset: function (){
            initLocations();
        },
        search:function (address, params) {
            directionsDisplay.setMap(null);
            geocoder.geocode({address:address}, function (result, status) {
                params.address = address;
                if (status != google.maps.GeocoderStatus.OK) {
                    alert('The address is not valid: ' + address);
                } else {
                    searchLocationsNear(params, result[0]); // send req params plus first result
                }
            });
        }
    };
}