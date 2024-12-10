<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Laporan;

class LaporanSearch extends Laporan
{
    public function rules()
    {
        return [
            [['id_laporan'], 'integer'],
            [['laporan', 'dokumentasi'], 'safe'],
        ];
    }
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Laporan::find();
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
            'id_laporan' => $this->id_laporan,
        ]);
        $query->andFilterWhere(['like', 'laporan', $this->laporan])
            ->andFilterWhere(['like', 'dokumentasi', $this->dokumentasi]);
        return $dataProvider;
    }
}
