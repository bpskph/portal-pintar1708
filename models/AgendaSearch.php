<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Agenda;
use DateTime;
use DateTimeZone;
use IntlDateFormatter;
/**
 * AgendaSearch represents the model behind the search form of `app\models\Agenda`.
 */
class AgendaSearch extends Agenda
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_agenda', 'metode', 'progress', 'id_lanjutan'], 'integer'],
            [['kegiatan', 'waktumulai', 'waktuselesai', 'pelaksana', 'tempat', 'peserta', 'reporter', 'pemimpin', 'timestamp', 'timestamp_lastupdate', 'waktu', 'metode', 'fk_kategori'], 'safe'],
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
        $query = Agenda::find();
        $query->joinWith(['reportere', 'zoomsnya']);
        $query->where(['agenda.deleted' => 0]);
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['waktumulai' => SORT_DESC, 'id_agenda' => SORT_DESC]]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // filter waktumulai and waktuselesai based on waktu range
        if (!empty($this->waktu)) {
            $waktuRange = explode(' - ', $this->waktu);
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
                if ($this->waktumulai_tunda != NULL && $this->waktuselesai_tunda != NULL) {
                    $query
                        ->andWhere([
                            'or',
                            ['>=', 'waktumulai', $waktumulaiFormatted],
                            ['>=', 'waktumulai_tunda', $waktumulaiFormatted],
                        ])
                        ->andWhere([
                            'or',
                            ['<=', 'waktuselesai', $waktumulaiFormatted],
                            ['<=', 'waktuselesai_tunda', $waktumulaiFormatted],
                        ]);
                } else {
                    $query
                        ->andWhere(['>=', 'waktumulai', $waktumulaiFormatted])
                        ->andWhere(['<=', 'waktuselesai', $waktuselesaiFormatted]);
                }
                // $query->andWhere(['>=', 'waktumulai', $waktumulaiFormatted])
                //     ->andWhere(['<=', 'waktuselesai', $waktuselesaiFormatted]);
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id_agenda' => $this->id_agenda,
            'waktumulai' => $this->waktumulai,
            'waktuselesai' => $this->waktuselesai,
            'metode' => $this->metode,
            'progress' => $this->progress,
            'fk_kategori' => $this->fk_kategori,
            'id_lanjutan' => $this->id_lanjutan,
            'timestamp' => $this->timestamp,
            'timestamp_lastupdate' => $this->timestamp_lastupdate,
        ]);
        $query->andFilterWhere(['like', 'kegiatan', $this->kegiatan])
            ->andFilterWhere(['like', 'peserta', $this->peserta])
            ->andFilterWhere(['like', 'pemimpin', $this->pemimpin])
            ->andFilterWhere([
                'or',
                ['like', 'reporter', $this->reporter],
                ['like', 'pengguna.nama', $this->reporter],
            ]);
        if ($this->tempat == 'other') {
            $subquery = Rooms::find()->select('id_rooms')->column();
            $query->andWhere(['or', ['tempat' => null], ['not in', 'tempat', $subquery]]);
        } else {
            $query->andFilterWhere([
                'tempat' => $this->tempat
            ]);
        }
        if ($this->pelaksana == 'other') {
            $subquery = Project::find()->select('id_project')->column();
            $query->andWhere(['or', ['pelaksana' => null], ['not in', 'pelaksana', $subquery]]);
        } else {
            // $query->andFilterWhere(['like', 'pelaksana', $this->pelaksana]);
            $query->andFilterWhere([
                'pelaksana' => $this->pelaksana
            ]);
        }
        return $dataProvider;
    }
}
