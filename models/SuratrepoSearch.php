<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Suratrepo;
use DateTime;
use yii\db\Expression;
/**
 * SuratrepoSearch represents the model behind the search form of `app\models\Suratrepo`.
 */
class SuratrepoSearch extends Suratrepo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_suratrepo', 'fk_agenda', 'fk_suratsubkode'], 'integer'],
            [['penerima_suratrepo', 'tanggal_suratrepo', 'perihal_suratrepo', 'nomor_suratrepo', 'owner', 'timestamp', 'timestamp_suratrepo_lastupdate', 'jenis'], 'safe'],
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
        // Retrieve the data query from your model or wherever you have defined it
        $query = suratrepo::find()->select([
            '*', // Select all columns from the table
            'sorted_nomor_suratrepo' => new Expression("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(nomor_suratrepo, '/', 1), '-', -1) AS UNSIGNED)"), // Extract numeric part and cast as integer
        ]);
        $query->joinWith(['ownere']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'tanggal_suratrepo',
                    'sorted_nomor_suratrepo' => [
                        'asc' => ['sorted_nomor_suratrepo' => SORT_ASC],
                        'desc' => ['sorted_nomor_suratrepo' => SORT_DESC],
                        'label' => 'nomor_suratrepo', // Use the original attribute name for sorting link labels
                    ],
                ],
                'defaultOrder' => [
                    'tanggal_suratrepo' => SORT_DESC,
                    'sorted_nomor_suratrepo' => SORT_DESC,
                ],
            ],
        ]);
        $dataProvider->query->where(['deleted' => '0']);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // filter waktumulai and waktuselesai based on waktu range
        if (!empty($this->tanggal_suratrepo)) {
            $waktuRange = explode(' - ', $this->tanggal_suratrepo);
            if (count($waktuRange) == 2) {
                $waktumulai = $waktuRange[0];
                $waktuselesai = $waktuRange[1];
                $month_map = [
                    'Januari' => 'January',
                    'Februari' => 'February',
                    'Maret' => 'March',
                    'April' => 'April',
                    'Mei' => 'May',
                    'Juni' => 'June',
                    'Juli' => 'July',
                    'Agustus' => 'August',
                    'September' => 'September',
                    'Oktober' => 'October',
                    'November' => 'November',
                    'Desember' => 'December',
                    'Jan' => 'January',
                    'Feb' => 'February',
                    'Mar' => 'March',
                    'Apr' => 'April',
                    'Mei' => 'May',
                    'Jun' => 'June',
                    'Jul' => 'July',
                    'Agt' => 'August',
                    'Sep' => 'September',
                    'Okt' => 'October',
                    'Nov' => 'November',
                    'Des' => 'December',
                ];
                $date_arr = explode(' ', $waktumulai);
                $month = $month_map[$date_arr[1]];
                $date_string = $date_arr[0] . ' ' . $month . ' ' . $date_arr[2];
                $date = DateTime::createFromFormat('d F Y', $date_string);
                $waktumulaiFormatted = $date->format('Y-m-d 00:00:00');
                $date_arr = explode(' ', $waktuselesai);
                $month = $month_map[$date_arr[1]];
                $date_string = $date_arr[0] . ' ' . $month . ' ' . $date_arr[2];
                $date = DateTime::createFromFormat('d F Y', $date_string);
                $waktuselesaiFormatted = $date->format('Y-m-d 23:59:00');
                $query
                    ->andWhere([
                        'and',
                        ['>=', 'tanggal_suratrepo', $waktumulaiFormatted],
                        ['<=', 'tanggal_suratrepo', $waktuselesaiFormatted],
                    ]);
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id_suratrepo' => $this->id_suratrepo,
            'fk_agenda' => $this->fk_agenda,
            'jenis' => $this->jenis,
            'fk_suratsubkode' => $this->fk_suratsubkode,
            'timestamp' => $this->timestamp,
            'timestamp_suratrepo_lastupdate' => $this->timestamp_suratrepo_lastupdate,
        ]);
        $query->andFilterWhere(['like', 'penerima_suratrepo', $this->penerima_suratrepo])
            ->andFilterWhere(['like', 'perihal_suratrepo', $this->perihal_suratrepo])
            ->andFilterWhere(['like', 'nomor_suratrepo', $this->nomor_suratrepo])
            // ->andFilterWhere(['like', 'owner', $this->owner]);
            ->andFilterWhere([
                'or',
                ['like', 'owner', $this->owner],
                ['like', 'pengguna.nama', $this->owner],
            ]);
        return $dataProvider;
    }
}
