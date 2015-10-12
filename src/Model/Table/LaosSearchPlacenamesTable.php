<?php
namespace App\Model\Table;

use App\Model\Entity\LaosSearchPlacename;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LaosSearchPlacenames Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Osms
 */
class LaosSearchPlacenamesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('laos_search_placenames');
        $this->displayField('name');

        $this->belongsTo('Osms', [
            'foreignKey' => 'osm_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmpty('way');

        $validator
            ->add('lon', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('lon');

        $validator
            ->add('lat', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('lat');

        $validator
            ->allowEmpty('name');

        $validator
            ->allowEmpty('feature');

        $validator
            ->allowEmpty('is_in_province_en');

        $validator
            ->allowEmpty('is_in_province_lo');

        $validator
            ->allowEmpty('closest_village_en');

        $validator
            ->allowEmpty('closest_village_lo');

        $validator
            ->add('closest_village_dist', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('closest_village_dist');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['osm_id'], 'Osms'));
        return $rules;
    }
}
