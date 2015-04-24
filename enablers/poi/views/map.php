<?php $id = $id ? $id : "fw-poi-" . uniqid(); ?>

<div class="fw-poi-gmap" id="<?php echo $id; ?>" style="min-height: 500px; width: 100%;">
    GMap loading...
</div>


<script>
    var MapID = "<?php echo $id; ?>";
    
    <?php if (!empty($center)):?>
    var MapCenter = var MapCenter= new google.maps.LatLng(<?php echo $center?>);
    <?php endif;?>
    var FW_POI_MAP = {};
    FW_POI_MAP[MapID] = null;
    
    
    jQuery(document).ready(function () {
        google.maps.event.addDomListener(window, 'load', fw_poi_map_initialize);
    });
    
</script>