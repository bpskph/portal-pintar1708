<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Zooms;

/**
 * ZoomsSearch represents the model behind the search form of `app\models\Zooms`.
 */
class ZoomsSearch extends Zooms
{
    /**
     * {@inheritdoc}
     */
    
    public function rules()
    {
        return [
            [['id_zooms', 'fk_agenda', 'jenis_zoom', 'jenis_surat', 'fk_surat', 'deleted'], 'integer'],
            [['proposer', 'timestamp', 'timestamp_lastupdate'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Zooms::find()->select('*, agenda.waktumulai as waktumulai')
        ->joinWith(['agendae']);
        $query->where(['zooms.deleted' => 0]);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['waktumulai' => SORT_DESC, 'fk_agenda' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['waktumulai'] = [
            // The tables are the ones our relation are configured to
            'asc' => ['agenda.waktumulai' => SORT_ASC],
            'desc' => ['agenda.waktumulai' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_zooms' => $this->id_zooms,
            'fk_agenda' => $this->fk_agenda,
            'jenis_zoom' => $this->jenis_zoom,
            'jenis_surat' => $this->jenis_surat,
            'fk_surat' => $this->fk_surat,
            'deleted' => $this->deleted,
            'timestamp' => $this->timestamp,
            'timestamp_lastupdate' => $this->timestamp_lastupdate,
        ]);

        $query->andFilterWhere(['like', 'proposer', $this->proposer]);

        return $dataProvider;
    }
}
