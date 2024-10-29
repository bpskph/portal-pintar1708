<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Dl;

/**
 * DlSearch represents the model behind the search form of `app\models\Dl`.
 */
class DlSearch extends Dl
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_dl', 'deleted'], 'integer'],
            [['pegawai', 'tanggal_mulai', 'tanggal_selesai', 'fk_tujuan', 'tugas', 'tim', 'reporter', 'timestamp', 'timestamp_lastupdate'], 'safe'],
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
        $query = Dl::find();
        $query->where(['deleted' => 0]);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['tanggal_mulai' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_dl' => $this->id_dl,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'deleted' => $this->deleted,
            'timestamp' => $this->timestamp,
            'timestamp_lastupdate' => $this->timestamp_lastupdate,
        ]);

        $query->andFilterWhere(['like', 'pegawai', $this->pegawai])
            ->andFilterWhere(['like', 'fk_tujuan', $this->fk_tujuan])
            ->andFilterWhere(['like', 'tugas', $this->tugas])
            ->andFilterWhere(['like', 'tim', $this->tim])
            ->andFilterWhere(['like', 'reporter', $this->reporter]);

        return $dataProvider;
    }
}
