<?php
use Cake\Core\Configure;
?>

<!-- Begin cookie consent plugin -->
<script type="text/javascript">
    window.cookieconsent_options = {"message":"This website uses cookies to ensure you get the best experience.","dismiss":"I accept","learnMore":"Read more »","link":"<?php echo $this->Url->build("/privacy"); ?>",
 <?php
    if (Configure::read("debug") == 0) {
        echo '"theme":"light-floating.min"';
    } else {
        $date = date_create();
        echo "\"theme\":\"lib/light-floating.css?" . date_timestamp_get($date) . "\"";
    }
?>           
        };
</script>

<?php
    if (Configure::read("debug") == 0) {
        echo $this->Html->script("/lib/cookieconsent.latest.min.js",
                                 ['type' => 'text/javascript']);
    } else {
        $date = date_create();
        echo $this->Html->script("/lib/cookieconsent.latest.js?_dc="
                                 . date_timestamp_get($date),
                                 ['type' => 'text/javascript']);
    }
?>
<!-- End cookie consent plugin -->
