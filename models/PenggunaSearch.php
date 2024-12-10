<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pengguna;
use Yii;

class PenggunaSearch extends Pengguna
{
    public function rules()
    {
        return [
            [['username', 'password', 'nama', 'email', 'tgl_daftar', 'nip'], 'safe'],
            [['level', 'theme'], 'integer'],
        ];
    }
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Pengguna::find();
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['nipbaru' => SORT_ASC]]
        ]);
        $level = Yii::$app->user->identity->level;
        if ($level != 0) { //cuma admin yang bisa lihat
            $dataProvider->query->where('level <> 2');
        }
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            // 'nip' => $this->nip,
            'tgl_daftar' => $this->tgl_daftar,
            'level' => $this->level,
            'theme' => $this->theme,
        ]);
        $query
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere([
                'or',
                ['like', 'nip', $this->nip],
                ['like', 'nipbaru', $this->nip],
            ])
            ->andFilterWhere(['like', 'nama', $this->nama]);
        return $dataProvider;
    }
}
