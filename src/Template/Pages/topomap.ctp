<?php
// Set the doctype to xhtml 1.0 strict
echo $this->Html->doctype("xhtml-strict") . "\n";
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
<?php
// Add meta tags
echo $this->element("/metatags");
// Add title tag
echo $this->Html->tag("title", __('OpenStreetMap Laos')) . "\n";
// Append Leaflet stylesheet
echo $this->Html->css("/lib/leaflet-0.7.5/leaflet.css") . "\n";
// Add Leaflet JavaScript
echo $this->Html->script("/lib/leaflet-0.7.5/leaflet.js",
                        ["type" => "text/javascript"]) . "\n";
?>
        <style type="text/css">
            #map { right: 10px; top: 10px; left: 10px; bottom: 10px; position: absolute; }
        </style>
        <?php echo $this->element("/cookieconsent"); ?>
    </head>
    <body>
        <div id="map"></div>
        <script type="text/javascript">
        //<![CDATA[
        var map = L.map('map').setView([18, 102], 6);
        L.tileLayer("<?php echo $this->Url->build('/tms/1.0.0/topomap/{z}/{x}/{y}.jpeg'); ?>",
        {
    attribution: 'Elevation model &copy; <a href="http://www2.jpl.nasa.gov/srtm/">SRTM</a>, Maps &copy; US Army Map Service, Series 7015',
    maxZoom: 16,
    tms: true
}).addTo(map);
        //]]>
        </script>
    </body>
</html>
