// Custom map
(function($){
        var var_location = new google.maps.LatLng(37.682289, -97.333280);
    
        var var_mapoptions = {
            center: var_location,
            zoom: 15,
            styles: [
                {
                    "featureType": "administrative",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "on"
                        }
                    ]
                }
            ]
        };
    
        var var_map = new google.maps.Map(document.getElementById('contact-map-container'),
            var_mapoptions);
    
        var var_marker = new google.maps.Marker({
            position: var_location,
            map: var_map,
            title: "Wichita Work Release Facility"
        });

})(jQuery);