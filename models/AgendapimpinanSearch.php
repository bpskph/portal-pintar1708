<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Agendapimpinan;
use DateTime;
/**
 * AgendapimpinanSearch represents the model behind the search form of `app\models\Agendapimpinan`.
 */
class AgendapimpinanSearch extends Agendapimpinan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_agendapimpinan'], 'integer'],
            [['waktumulai', 'waktuselesai', 'tempat', 'kegiatan', 'pendamping', 'pendamping_lain', 'reporter', 'timestamp', 'timestamp_agendapimpinan_lastupdate', 'waktu'], 'safe'],
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
        $query = Agendapimpinan::find();
        // add conditions that should always apply here
        $query->where(['deleted' => 0]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['waktumulai' => SORT_DESC, 'id_agendapimpinan' => SORT_DESC]]
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
                    'Apr' => 'April',
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
                    ->andWhere(['>=', 'waktumulai', $waktumulaiFormatted])
                    ->andWhere(['<=', 'waktuselesai', $waktuselesaiFormatted]);
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id_agendapimpinan' => $this->id_agendapimpinan,
            // 'waktumulai' => $this->waktumulai,
            // 'waktuselesai' => $this->waktuselesai,
            'timestamp' => $this->timestamp,
            'timestamp_agendapimpinan_lastupdate' => $this->timestamp_agendapimpinan_lastupdate,
        ]);
        $query->andFilterWhere(['like', 'tempat', $this->tempat])
            ->andFilterWhere(['like', 'kegiatan', $this->kegiatan])
            ->andFilterWhere(['like', 'pendamping', $this->pendamping])
            ->andFilterWhere(['like', 'pendamping_lain', $this->pendamping_lain])
            ->andFilterWhere(['like', 'reporter', $this->reporter]);
        return $dataProvider;
    }
}
