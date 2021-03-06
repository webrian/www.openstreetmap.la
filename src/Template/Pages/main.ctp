<?php
use Cake\Core\Configure;
?>
<?php
// Set the doctype to xhtml 1.0 strict
echo $this->Html->docType("xhtml-strict") . "\n";
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head profile="http://a9.com/-/spec/opensearch/1.1/">
<?php
// Add meta tags
echo $this->element("/metatags");
?>
        <title><?php echo __('OpenStreetMap Laos'); ?></title>
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <!-- Link to the OpenSearch description document -->
        <link rel="search" type="application/opensearchdescription+xml"
              href="<?php echo $this->Url->build("/places.xml", true); ?>" title="OpenStreetMap.la Places" />
        <!-- ** CSS ** -->
        <!-- base library -->
        <?php
        echo $this->Html->css("/lib/ext-3.3.0/resources/css/ext-all-notheme.css");
        echo $this->Html->css("/lib/ext-3.3.0/resources/css/xtheme-gray.css");
        echo $this->Html->css("/lib/leaflet-0.7.5/leaflet.css");
        echo $this->Html->css("/style.css");
        ?>

        <!-- ** Javascript ** -->
        <!-- ExtJS library: base/adapter -->
        <?php echo $this->Html->script("/lib/ext-3.3.0/adapter/ext/ext-base.js",
                                       ['type' => 'text/javascript']); ?>

        <!-- ExtJS library: all widgets -->
        <?php
        echo $this->Html->script("/lib/ext-3.3.0/ext-all-osmla.js",
                                 ['type' => 'text/javascript']);
        echo $this->Html->script("/lib/leaflet-0.7.5/leaflet.js",
                                 ['type' => 'text/javascript']);
        echo $this->Html->script("/lang.js?_dc=" . time(),
                                 ['type' => 'text/javascript']); 
        ?>

        <!-- page specific -->
        <script type="text/javascript">
<?php
echo "var initLang='$lang';";

echo "var initCenter=new L.LatLng($lat, $lng);";
if (!empty($mlat) && !empty($mlng)) {
    echo "var initMarker=new L.LatLng($mlat, $mlng);";
} else {
    echo "var initMarker=null;";
}
echo "var initZoom=$zoom;";


if (!empty($startCoords)) {
    echo "var initStart=new L.LatLng($startCoords[0], $startCoords[1]);";
} else {
    echo "var initStart=null;";
}
if (!empty($destCoords)) {
    echo "var initDest=new L.LatLng($destCoords[0], $destCoords[1]);";
} else {
    echo "var initDest=null;";
}
if (!empty($viaCoords) && count($viaCoords) > 0) {
    echo "var initVias=[";
    for ($i = 0; $i < count($viaCoords); $i++) {
        $viaCoord = $viaCoords[$i];
        echo "new L.LatLng($viaCoord[0], $viaCoord[1])";
        if ($i < (count($viaCoords) - 1)) {
            echo ",";
        }
    }
    echo "];";
} else {
    echo "var initVias=null;";
}
echo "\n";
echo "Ext.namespace('Ext.ux');";
echo "Ext.ux.activeTab = '$tab';\n";
// Create a MixedCollection which holds the current
// service endpoints to make the application portable
echo "Ext.ux.services = new Ext.util.MixedCollection();";
echo "Ext.ux.services.addAll({'redirect': '" . $this->Url->build("/redirect/") . "',";
echo "'places': '" . $this->Url->build("/places") . "',";
echo "'base': '" . $this->Url->build("/") . "',";
echo "'directions': '" . $this->Url->build("/directions") . "'});";
?>
        </script>
<?php
    if (Configure::read("debug") == 0) {
        echo $this->Html->script("/main.js",
                                 ['type' => 'text/javascript']);
    } else {
        $date = date_create();
        echo $this->Html->script("/main-devel.js?_dc="
                                 . date_timestamp_get($date),
                                 ['type' => 'text/javascript']);
    }
?>
<?php echo $this->element("/cookieconsent"); ?>
    </head>
    <body>
        <div id="sidepanel-header" style="text-align: center; padding: 5px; padding-top: 10px;">
            <?php echo $this->Html->image('/img/osmla.png', ['alt' => __('OpenStreetMap Laos'), 'width' => 118, 'height' => 118]); ?><br/>
            <h1>OpenStreetMap.la</h1>
            <div style="padding: 10px 0px 10px;">
                <?php echo __('The free wiki world map'); ?>
            </div>
        </div>
        <div id="route-summary-panel" style="padding: 5px;">
            <!-- placeholder -->
        </div>
        <div id="route-result-panel" style="padding: 5px">
            <!-- placeholder -->
        </div>

        <div id="map" style="width: 100%; height: 100%">

        </div>
        <div id="edit-tab" class="x-panel-mc">
            <div class="main-tab">
                <h1><?php echo __("Edit the map"); ?></h1>
                <?php
                // Include the text about how to edit the map
                echo $this->element("Pages/" . Configure::read('Config.language') . "/edit");
                ?>
                <h2><?php echo __("Topographic Map TMS"); ?></h2>
                <div class="content">
                    <?php echo __('Support in mapping geographical features provides the <a href="http://wiki.osgeo.org/wiki/Tile_Map_Service_Specification" class="external">Tile Map Service</a>, which serves topographic maps for northern Laos. These topographic maps from the 1960ies were made by the US Army Map Service in the scales of 1:200000 and 1:50000. Meanwhile these maps are released to the public domain and available at the <a href="http://www.lib.utexas.edu/maps/laos.html" class="external">University of Texas Libraries</a>.'); ?>
                </div>
                <div class="content">
                    <?php echo __("The tile map service is accessible at:"); ?><br/>
                    <a href="<?php echo $this->Url->build("/tms/", true); ?>"><?php echo $this->Url->build("/tms/", true); ?></a>
                </div>
                <h3>Add topographic maps in JOSM</h3>
                <div class="content">
                    <?php echo __('To use these topographic maps in <a href="http://josm.openstreetmap.de/" class="external">JOSM</a> open the "Imagery Preferences" tab in the preferences dialog and add a new TMS entry.'); ?>
                </div>
                <div class="content">
                    <?php echo __('Use the following settings:'); ?>
                    <ul>
                        <li>Menu Name: <pre>Topomap Laos</pre></li>
                        <li>TMS URL: <pre>http://www.openstreetmap.la/tms/1.0.0/topomap/{zoom}/{x}/{-y}.png</pre></li>
                    </ul>
                </div>
                <div class="content">
                    <?php echo $this->Html->image('/img/addTmsDialog.png', ['alt' => 'Add imagery URL dialog in JOSM', 'width' => 350, 'height' => 193]); ?>
                </div>
                <div class="content">
                    <?php echo __("More detailed instructions on how to add custom imagery is available at"); ?>
                    <a class="external" href="http://josm.openstreetmap.de/wiki/Help/Preferences/Imagery#AddcustomTMSimagery">http://josm.openstreetmap.de/wiki/Help/Preferences/Imagery#AddcustomTMSimagery</a>.
                </div>
            </div>
        </div>
        <div id="downloads-tab" class="x-panel-mc">
            <div class="main-tab">
                <h1><?php echo __('Downloads'); ?></h1>
                <?php
                    // Include the introductory text for the downloads
                    echo $this->element("Pages/" . Configure::read('Config.language') . "/downloads");

                    // Laos files and download table
                    $laos_files = array(
                        array("laos.osm.pbf", "OSM Protobuf", __("Complete database")),
                        array("laos.osm.bz2", "OSM XML", __("Complete database")),
                        array("amenities.shp.zip", "ESRI Shapefile", __("Amenities")),
                        array("buildings.shp.zip", "ESRI Shapefile", __("Buildings")),
                        array("country.shp.zip", "ESRI Shapefile", __("Country")),
                        array("national_parks.shp.zip", "ESRI Shapefile", __("National Parks")),
                        array("places.shp.zip", "ESRI Shapefile", __("Places")),
                        array("provinces.shp.zip", "ESRI Shapefile", __("Provinces")),
                        array("roads.shp.zip", "ESRI Shapefile", __("Roads")),
                        array("waterway_lines.shp.zip", "ESRI  Shapefile", __("Waterways")),
                        array("waterway_polygons.shp.zip", "ESRI Shapefile", __("Waterbodies")),
                        array("gmapsupp.img.zip", "<a class=\"external\" href=\"http://wiki.openstreetmap.org/wiki/OSM_Map_On_Garmin#Installing_the_map_onto_your_GPS\">Garmin Map</a>", __("Routable GPS map")),
                        array("pointsofinterest.kmz", "KMZ Google Earth", __("Points of Interest")),
                        array("pointsofinterest.gpx.zip", "GPX", __("Points of Interest")));

                    fileList($this, __('Laos'), $laos_files, 'Laos');

                    // Cambodia files and download table
                    $cambodia_files = array(
                        array("cambodia.osm.pbf", "OSM Protobuf", __("Complete database")),
                        array("cambodia.osm.bz2", "OSM XML", __("Complete database")),
                        array("amenities.shp.zip", "ESRI Shapefile", __("Amenities")),
                        array("buildings.shp.zip", "ESRI Shapefile", __("Buildings")),
                        array("country.shp.zip", "ESRI Shapefile", __("Country")),
                        array("national_parks.shp.zip", "ESRI Shapefile", __("National Parks")),
                        array("places.shp.zip", "ESRI Shapefile", __("Places")),
                        array("provinces.shp.zip", "ESRI Shapefile", __("Provinces")),
                        array("roads.shp.zip", "ESRI Shapefile", __("Roads")),
                        array("waterway_lines.shp.zip", "ESRI  Shapefile", __("Waterways")),
                        array("waterway_polygons.shp.zip", "ESRI Shapefile", __("Waterbodies")),
                        array("gmapsupp.img.zip", "<a class=\"external\" href=\"http://wiki.openstreetmap.org/wiki/OSM_Map_On_Garmin#Installing_the_map_onto_your_GPS\">Garmin Map</a>", __("Routable GPS map")),
                        array("pointsofinterest.kmz", "KMZ Google Earth", __("Points of Interest")),
                        array("pointsofinterest.gpx.zip", "GPX", __("Points of Interest")));

                    fileList($this, __('Cambodia'), $cambodia_files, 'Cambodia');

                    function fileList($view, $country, $files, $downloadDirectory) {
                        echo " <div class=\"content\"><h2>$country</h2><table width=\"100%\">";
                        echo "<tr><td>" . __('Content') . "</td><td>Format</td><td>" . __('Download Size') . "</td></tr>\n";

                        foreach ($files as $f) {
                            $url = $view->Url->build("/downloads/"
                                                     . strtolower($downloadDirectory)
                                                     . "/$f[0]");

                            echo "<tr><td><a href=\"$url\">$f[2]</a></td><td>$f[1]</td><td>";
                            //$path = dirname(APP) . DS . "Data" . DS . $downloadDirectory;
                            $path = Configure::read("DataDirectory") . DS . $downloadDirectory;
                            echo formatBytes(@filesize($path . DS . $f[0])) . "</td></tr>\n";
                        }

                        echo "</table></div>";

                        echo "<div class=\"content\">" . __('Last data update') . ": ";
                        echo lastModified($path . DS . $files[0][0]);
                        echo "</div>";
                    }

                    function formatBytes($bytes) {
                        if ($bytes < 1024)
                            return $bytes . ' B';
                        elseif ($bytes < 1048576)
                            return round($bytes / 1024, 2) . ' KB';
                        else
                            return round($bytes / 1048576, 2) . ' MB';
                    }

                    function lastModified($file) {
                        if (file_exists($file)) {
                            return date("j. M Y", filemtime($file));
                        } else {
                            return __("unknown");
                        }
                    }
                ?>
                </div>
            </div>
            <div id="about-tab" class="x-panel-mc">
                <div class="main-tab">
                    <h1><?php echo __("About OpenStreetMap.la"); ?></h1>
                <?php
                    // Include the about text
                    echo $this->element("Pages/" . Configure::read('Config.language') . "/about");
                ?>
                <div class="content">
                    <a href="http://validator.w3.org/check?uri=referer">
                        <img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Strict" height="31" width="88" />
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
