<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Projectmember;

class ProjectmemberSearch extends Projectmember
{
    public $teame, $tahun;
    public function rules()
    {
        return [
            [['id_projectmember',  'member_status'], 'integer'],
            [['pegawai', 'fk_project', 'teame', 'tahun'], 'safe'],
        ];
    }
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Projectmember::find();
        $query->joinWith(['projecte', 'penggunae', 'teame']);
        $query->where('aktif = 1');
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            // 'sort' => ['defaultOrder' => ['fk_project' => SORT_ASC]]
            'sort' => [
                'attributes' => [
                    'tahun' => [
                        'asc' => ['tahun' => SORT_ASC],
                        'desc' => ['tahun' => SORT_DESC],
                        'label' => 'tahun', // Use the original attribute name for sorting link labels
                    ],
                    'fk_project'
                ],
                'defaultOrder' => [
                    'tahun' => SORT_DESC,
                    'fk_project' => SORT_ASC,
                ],
            ],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id_projectmember' => $this->id_projectmember,
            'project.tahun' => $this->tahun,
            'member_status' => $this->member_status,
        ]);
        $query
            ->andFilterWhere([
                'or',
                ['like', 'team.nama_team', $this->teame],
                ['like', 'team.panggilan_team', $this->teame],
            ])
            ->andFilterWhere([
                'or',
                ['like', 'project.nama_project', $this->fk_project],
                ['like', 'project.panggilan_project', $this->fk_project],
            ])
            ->andFilterWhere([
                'or',
                ['like', 'pengguna.nama', $this->pegawai],
                ['like', 'pegawai', $this->pegawai],
            ]);
        return $dataProvider;
    }
}
