<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Patches;

class PatchesSearch extends Patches
{
    public function rules()
    {
        return [
            [['id_patches'], 'integer'],
            [['timestamp', 'description'], 'safe'],
        ];
    }
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Patches::find();
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['timestamp' => SORT_DESC]]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id_patches' => $this->id_patches,
            'timestamp' => $this->timestamp,
        ]);
        $query->andFilterWhere(['like', 'description', $this->description]);
        return $dataProvider;
    }
}
