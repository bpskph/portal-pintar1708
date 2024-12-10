<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Linkmat;
use Yii;

class LinkmatSearch extends Linkmat
{
    public function rules()
    {
        return [
            [['id_linkmat', 'views', 'active'], 'integer'],
            [['judul', 'link', 'keyword', 'owner', 'keterangan', 'timestamp', 'timestamp_lastupdate'], 'safe'],
        ];
    }
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Linkmat::find();
        $query->joinWith(['ownere']);
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['views' => SORT_DESC]]
        ]);
        if (Yii::$app->user->isGuest) { //cuma admin yang bisa lihat
            $dataProvider->query->where('active = 1');
        } elseif (!Yii::$app->user->isGuest && Yii::$app->user->identity->level != 0) { //cuma admin yang bisa lihat
            $dataProvider->query->where('active = 1 OR (active = 2 AND owner = "' . Yii::$app->user->identity->username . '") OR (active = 0 AND owner = "' . Yii::$app->user->identity->username . '")');
        }

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id_linkmat' => $this->id_linkmat,
            'views' => $this->views,
            'active' => $this->active,
            'timestamp' => $this->timestamp,
            'timestamp_lastupdate' => $this->timestamp_lastupdate,
        ]);
        $query
            // ->andFilterWhere(['like', 'judul', $this->judul])
            ->andFilterWhere([
                'or',
                ['like', 'judul', $this->judul],
                ['like', 'keterangan', $this->judul],
            ])
            ->andFilterWhere([
                'or',
                ['like', 'owner', $this->owner],
                ['like', 'pengguna.nama', $this->owner],
            ])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'keyword', $this->keyword])
            // ->andFilterWhere(['like', 'owner', $this->owner])
            ->andFilterWhere(['like', 'keterangan', $this->keterangan]);
        return $dataProvider;
    }
}
