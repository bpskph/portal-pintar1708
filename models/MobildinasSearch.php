<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Mobildinas;
use DateTime;

class MobildinasSearch extends Mobildinas
{
    public function rules()
    {
        return [
            [['id_mobildinas', 'approval', 'deleted'], 'integer'],
            [['mulai', 'selesai', 'keperluan', 'borrower', 'timestamp', 'timestamp_lastupdate'], 'safe'],
        ];
    }
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Mobildinas::find();
        $query->where(['deleted' => 0]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['mulai' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

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
                $query
                    ->andWhere(['>=', 'mulai', $waktumulaiFormatted])
                    ->andWhere(['<=', 'selesai', $waktuselesaiFormatted]);
            }
        }

        $query->andFilterWhere([
            'id_mobildinas' => $this->id_mobildinas,
            'approval' => $this->approval,
            'timestamp' => $this->timestamp,
            'timestamp_lastupdate' => $this->timestamp_lastupdate,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'keperluan', $this->keperluan])
            ->andFilterWhere(['like', 'borrower', $this->borrower]);

        return $dataProvider;
    }
}
