<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Apel;

class ApelSearch extends Apel
{
    public function rules()
    {
        return [
            [['id_apel', 'jenis_apel'], 'integer'],
            [['tanggal_apel', 'pembina_inspektur', 'pemimpin_komandan', 'perwira', 'mc', 'uud', 'korpri', 'doa', 'ajudan', 'operator', 'bendera', 'reporter', 'timestamp', 'timestamp_apel_lastupdate'], 'safe'],
        ];
    }
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Apel::find();
        // add conditions that should always apply here
        $query->where(['deleted' => 0]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['tanggal_apel' => SORT_DESC, 'id_apel' => SORT_DESC]]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id_apel' => $this->id_apel,
            'jenis_apel' => $this->jenis_apel,
            'tanggal_apel' => $this->tanggal_apel,
            'timestamp' => $this->timestamp,
            'timestamp_apel_lastupdate' => $this->timestamp_apel_lastupdate,
        ]);
        $query->andFilterWhere(['like', 'pembina_inspektur', $this->pembina_inspektur])
            ->andFilterWhere(['like', 'pemimpin_komandan', $this->pemimpin_komandan])
            ->andFilterWhere(['like', 'perwira', $this->perwira])
            ->andFilterWhere(['like', 'mc', $this->mc])
            ->andFilterWhere(['like', 'uud', $this->uud])
            ->andFilterWhere(['like', 'korpri', $this->korpri])
            ->andFilterWhere(['like', 'doa', $this->doa])
            ->andFilterWhere(['like', 'ajudan', $this->ajudan])
            ->andFilterWhere(['like', 'operator', $this->operator])
            ->andFilterWhere(['like', 'bendera', $this->bendera])
            ->andFilterWhere(['like', 'reporter', $this->reporter]);
        return $dataProvider;
    }
}
