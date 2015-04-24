function fw_poi_map_initialize(){
    
    if (typeof MapCenter == 'undefined'){
        var MapCenter= new google.maps.LatLng(53.5463333, 9.9932418);
    }
    
    var mapProp = {
        center: MapCenter,
        zoom: 5,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    FW_POI_MAP[MapID] = new google.maps.Map(document.getElementById(MapID), mapProp);

    var marker = new google.maps.Marker({
        position: MapCenter,
    });

    marker.setMap(FW_POI_MAP[MapID]);
}