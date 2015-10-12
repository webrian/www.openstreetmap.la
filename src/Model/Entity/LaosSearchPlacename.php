<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * LaosSearchPlacename Entity.
 *
 * @property int $osm_id
 * @property \App\Model\Entity\Osm $osm
 * @property string $way
 * @property float $lon
 * @property float $lat
 * @property string $name
 * @property string $feature
 * @property string $is_in_province_en
 * @property string $is_in_province_lo
 * @property string $closest_village_en
 * @property string $closest_village_lo
 * @property int $closest_village_dist
 */
class LaosSearchPlacename extends Entity
{

}
