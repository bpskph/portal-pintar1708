<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AccessLogs;

/**
 * AccessLogsSearch represents the model behind the search form of `app\models\AccessLogs`.
 */
class AccessLogsSearch extends AccessLogs
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['controller', 'action', 'user_id', 'user_ip', 'user_agent', 'timestamp'], 'safe'],
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
        $query = AccessLogs::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 200,                
            ],
            'sort' => ['defaultOrder' => ['timestamp' => SORT_DESC, 'id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'timestamp' => $this->timestamp,
        ]);

        $query->andFilterWhere(['like', 'controller', $this->controller])
            ->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'user_ip', $this->user_ip])
            ->andFilterWhere(['like', 'user_agent', $this->user_agent]);

        return $dataProvider;
    }
}
