<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Linkapp;
use Yii;

class LinkappSearch extends Linkapp
{
    public function rules()
    {
        return [
            [['id_linkapp', 'views'], 'integer'],
            [['judul', 'link', 'keyword'], 'safe'],
        ];
    }
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Linkapp::find();
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['views' => SORT_DESC]]
        ]);
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->level != 0) { //cuma admin yang bisa lihat
            $dataProvider->query->where('active = 1');
        }
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id_linkapp' => $this->id_linkapp,
            'views' => $this->views,
        ]);
        $query->andFilterWhere(['like', 'judul', $this->judul])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'keyword', $this->keyword]);
        return $dataProvider;
    }
}
