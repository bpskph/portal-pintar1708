<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sk;

class SkSearch extends Sk
{
    public function rules()
    {
        return [
            [['id_sk', 'deleted'], 'integer'],
            [['nomor_sk', 'tanggal_sk', 'tentang_sk', 'nama_dalam_sk', 'reporter', 'timestamp', 'timestamp_lastupdate'], 'safe'],
        ];
    }
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Sk::find();
        $query->where(['deleted' => 0]);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_sk' => $this->id_sk,
            'tanggal_sk' => $this->tanggal_sk,
            'nama_dalam_sk' => $this->nama_dalam_sk,
            'deleted' => $this->deleted,
            'timestamp' => $this->timestamp,
            'timestamp_lastupdate' => $this->timestamp_lastupdate,
        ]);

        $query->andFilterWhere(['like', 'nomor_sk', $this->nomor_sk])
            ->andFilterWhere(['like', 'tentang_sk', $this->tentang_sk])
            ->andFilterWhere(['like', 'reporter', $this->reporter]);

        return $dataProvider;
    }
}
