ymaps.ready(function() {
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
			ymaps.geolocation.get({
				provider: 'yandex'
			}).then(function (res) {
				initMap(res.geoObjects.position);
			}, function (err) {
				initMap([55.75583, 37.61778]);
			});
		} else {
			initMap([latitude, longitude]);
			initPlacemark([latitude, longitude]);
		};

		function initMap(pos)
		{
			map = new ymaps.Map($map[0], {
				center: pos,
				zoom: 16,
				controls: ['zoomControl']
			});

			map.events.add('click', function(e) {
				setPlacemark(e.get('coords'));
			});
		};

		function initPlacemark(pos)
		{
			placemark = new ymaps.Placemark(pos, {}, {
				draggable: true
			});

			placemark.events.add('dragend', function(e) {
				setGeocode(e.get('target').geometry.getCoordinates());
			});

			map.geoObjects.add(placemark);
		};

		function searchAddress()
		{
			var val = $input.val();

			if (val == '')
				return;

			ymaps.geocode(val, {
				results: 1,
				json: true
			}).then(function (res) {
				var objects = res.GeoObjectCollection.featureMember;
				if (objects.length) {
					var a = objects[0].GeoObject.Point.pos.split(' ');

					if (a.length == 2)
						setPlacemark([parseFloat(a[1]), parseFloat(a[0])]);
				}
			}, function (err) {
				console.log(err);
			});
		};

		function removeAddress()
		{
			if (placemark !== undefined) {
				map.geoObjects.remove(placemark);
				placemark = undefined;
				setGeocode(['', '']);
			}
		};

		function setPlacemark(pos)
		{
			map.setCenter(pos);

			if (placemark === undefined) {
				initPlacemark(pos);
			} else {
				placemark.geometry.setCoordinates(pos);
			}

			setGeocode(pos);
		};

		function setGeocode(pos)
		{
			$latitude.val(pos[0]);
			$longitude.val(pos[1]);
		};

	});
});
