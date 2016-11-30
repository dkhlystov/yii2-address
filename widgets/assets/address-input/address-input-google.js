function initAddressInputMap() {
	$('.address-map-container').each(function() {
		var $container = $(this),
			$input = $container.parent().find('>.input-group input'),
			$latitude = $container.find('.address-map-latitude'), latitude = $latitude.val(),
			$longitude = $container.find('.address-map-longitude'), longitude = $longitude.val(),
			$map = $container.find('.address-map'),
			map, placemark;

		$container.parent().find('.address-map-search').click(searchAddress);

		$container.parent().find('.address-map-remove').click(removeAddress);

		if (latitude == '' || longitude == '') {
			$.post({
				url: 'https://www.googleapis.com/geolocation/v1/geolocate?key=' + $container.data('key'),
				dataType: 'json',
				success: function(data) {
					initMap(data.location);
				},
				error: function(xhr, status, error) {
					initMap({lat: 55.75583, lng: 37.61778);
				}
			});
		} else {
			var latLng = {lat: parseFloat(latitude), lng: parseFloat(longitude)};
			initMap(latLng);
			initPlacemark(latLng);
		};

		function initMap(latLng)
		{
			map = new google.maps.Map($map[0], {
				center: latLng,
				zoom: 16,
				mapTypeControl: false,
				streetViewControl: false
			});

			map.addListener('click', function(e) {
				setPlacemark(e.latLng);
			});
		};

		function initPlacemark(latLng)
		{
			placemark = new google.maps.Marker({
				position: latLng,
				map: map,
				draggable: true
			});

			placemark.addListener('dragend', function(e) {
				setGeocode(e.latLng);
			});
		};

		function searchAddress()
		{
			var val = $input.val();

			if (val == '')
				return;

			var geocoder = new google.maps.Geocoder();
			geocoder.geocode({
				address: val
			}, function(results, status) {
				if (status === google.maps.GeocoderStatus.OK) {
					setPlacemark(results[0].geometry.location);
				} else {
					console.log(status);
				}
			});
		};

		function removeAddress()
		{
			if (placemark !== undefined) {
				placemark.setMap(null);
				placemark = undefined;
				setGeocode({lat: '', lng: ''});
			}
		};

		function setPlacemark(latLng)
		{
			map.setCenter(latLng);

			if (placemark === undefined) {
				initPlacemark(latLng);
			} else {
				placemark.setPosition(latLng);
			}

			setGeocode(latLng);
		};

		function setGeocode(latLng)
		{
			$latitude.val(latLng.lat);
			$longitude.val(latLng.lng);
		};

	});
}
