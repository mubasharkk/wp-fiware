(function (){

//var query_url = "http://193.190.127.198/poi_dp/" + "radial_search?" +
//  "lat=" + 1 + "&lon=" + 1 + "&radius=" + 10 + "&component=fw_core";
var query_url = "http://193.190.127.198/poi_dp/get_all_pois.php?poi_id";

console.log(query_url);

 poi_xhr = new XMLHttpRequest();
 poi_xhr.onreadystatechange = function () {
   if(poi_xhr.readyState === 4) {
     if(poi_xhr.status  === 200) { 
       var resp_data = JSON.parse(poi_xhr.responseText);
       console.log(resp_data);
       process_response(resp_data);
     }
     else { 
       console.log("failed: " + poi_xhr.responseText);
     }
   }
 }

 poi_xhr.onerror = function (e) {
   log("failed to get POIs");
 };

 poi_xhr.open("GET", query_url, true);
 //set_accept_languages(poi_xhr, languages);
 poi_xhr.send();

function process_response( data ) {

       var counter = 0, jsonData, poiData, pos, i, uuid, pois,
           contents, locations, location, searchPoint, poiCore,
           poiXxx;

       if (!(data && data.pois)) {
           return;
       }

       pois = data['pois'];

       /* process pois */
	var myCenter=new google.maps.LatLng(53.5463333,9.9932418);


       for ( uuid in pois ) {
           poiData = pois[uuid];
           /*
              process the components of the POI
              e.g. fw_core component containing category, name,
              location etc.
              Taking local copies of the data can speed up later 
              processing.
           */
           poiCore = poiData.fw_core;
           if (poiCore) {
             /* fw_core data is used here */
		
		var latitude = poiCore.location.wgs84.latitude;
		var longitude = poiCore.location.wgs84.longitude;
		var category = poiCore.category;

		console.log(latitude + ", "+ longitude);
		var mapProp = {
		  center:myCenter,
		  zoom:5,
		  mapTypeId:google.maps.MapTypeId.ROADMAP
		  };

		var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);

		var marker=new google.maps.Marker({
		  position:myCenter,
		  });

		marker.setMap(map);

		// apply other location markers
	
		marker = new google.maps.Marker({
		position: new google.maps.LatLng(latitude , longitude),
		map: map,
		title: category
		});

	

           }
           /* Possible other components */
           //poiXxx = poiData.xxx;
           //if (poiXxx) {
              /* xxx data is used here */

           //}
       }
   }



}());