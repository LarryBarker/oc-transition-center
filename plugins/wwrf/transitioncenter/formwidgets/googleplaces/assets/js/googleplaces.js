// This example displays an address form, using the autocomplete feature
// of the Google Places API to help users fill in the information.

// This example requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

var placeSearch, autocomplete;
var placeID;
var componentForm = {
    street_number: 'short_name',
    route: 'long_name', //street
    locality: 'long_name', //city
    administrative_area_level_1: 'short_name', //state
    country: 'long_name',
    postal_code: 'short_name'
};

function initAutocomplete() {
    // Create the autocomplete object, restricting the search to geographical
    // location types.
    autocomplete = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('GooglePlaces-formCompany-input-company')),
        {types: ['establishment']});

    // When the user selects an address from the dropdown, populate the address
    // fields in the form.
    autocomplete.addListener('place_changed', fillInAddress);
}

function fillInAddress() {
    // Get the place details from the autocomplete object.
    var place = autocomplete.getPlace();
 
    placeID = place.place_id;

    var service = new google.maps.places.PlacesService(document.getElementById('GooglePlaces-formCompany-input-company'));

    service.getDetails({
        placeId: placeID
    }, function(place, status) {
        if (status === google.maps.places.PlacesServiceStatus.OK) {
            document.getElementById('GooglePlaces-formCompany-input-company').value = place.name;
            document.getElementById('Form-field-Post-phone').value = place.formatted_phone_number;
            document.getElementById('Form-field-Post-address').value = place.vicinity;
            document.getElementById('Form-field-Post-website').value = place.website;
        }
    });
}

// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function geolocate() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
        var geolocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
        };
        var circle = new google.maps.Circle({
            center: geolocation,
            radius: position.coords.accuracy
        });
        autocomplete.setBounds(circle.getBounds());
        });
    }
}