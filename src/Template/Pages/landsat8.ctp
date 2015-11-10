<html>
    <head>
        <?php echo $this->Html->css("/lib/leaflet-0.7.5/leaflet.css") . "\n"; ?>
        <?php echo $this->Html->script("/lib/leaflet-0.7.5/leaflet.js"). "\n"; ?>
        <style type="text/css">
            #map { right: 10px; top: 10px; left: 10px; bottom: 10px; position: absolute; }
        </style>
        <?php echo $this->element("/cookieconsent"); ?>
    </head>
    <body>
        <div id="map"></div>

        <script type="text/javascript">

        var map = L.map('map').setView([18, 102], 6);
        L.tileLayer("<?php echo $this->Url->build('/tms/1.0.0/landsat8/{z}/{x}/{y}.jpeg'); ?>",
        {
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © Landsat 8',
    maxZoom: 14,
    tms: true
}).addTo(map);
        </script>

    </body>
</html>
