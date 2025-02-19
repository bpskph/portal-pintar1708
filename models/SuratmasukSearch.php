<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Suratmasuk;
use DateTime;
use Yii;

class SuratmasukSearch extends Suratmasuk
{
    public function rules()
    {
        return [
            [['id_suratmasuk', 'sifat', 'deleted'], 'integer'],
            [['pengirim_suratmasuk', 'perihal_suratmasuk', 'tanggal_diterima', 'nomor_suratmasuk', 'tanggal_suratmasuk', 'timestamp', 'timestamp_lastupdate', 'reporter', 'pemberidisposisi', 'penerimadisposisi'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Suratmasuk::find();
        $query->joinWith(['reportere', 'suratmasukdisposisie']);

        $penerima_disposisi =  Suratmasukdisposisi::find()->joinWith('suratmasuke')->select(['tujuan_disposisi_pegawai'])->where(['not', ['suratmasuk.sifat' => 0]])->column();
        $penginput_surat = Suratmasukdisposisi::find()->joinWith('suratmasuke')->select(['reporter'])->where(['not', ['suratmasuk.sifat' => 0]])->column();
        // $tes = in_array(Yii::$app->user->identity->username, $penerima_disposisi);
        // die(var_dump($tes));
        if (
            Yii::$app->user->identity->issuratmasukpejabat
            || in_array(Yii::$app->user->identity->username, $penerima_disposisi)
            || in_array(Yii::$app->user->identity->username, $penginput_surat)
        )
            $query->where(['suratmasuk.deleted' => 0]);
        else
            $query->where(['suratmasuk.deleted' => 0, 'suratmasuk.sifat' => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => ['defaultOrder' => ['timestamp_lastupdate' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // filter tanggal_suratmasuk based on waktu range
        if (!empty($this->tanggal_suratmasuk)) {
            $waktuRange = explode(' - ', $this->tanggal_suratmasuk);
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
                        ['>=', 'tanggal_suratmasuk', $waktumulaiFormatted],
                        ['<=', 'tanggal_suratmasuk', $waktuselesaiFormatted],
                    ]);
            }
        }

        // filter tanggal_suratmasuk based on waktu range
        if (!empty($this->tanggal_diterima)) {
            $waktuRange = explode(' - ', $this->tanggal_diterima);
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
                        ['>=', 'tanggal_diterima', $waktumulaiFormatted],
                        ['<=', 'tanggal_diterima', $waktuselesaiFormatted],
                    ]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_suratmasuk' => $this->id_suratmasuk,
            'sifat' => $this->sifat,
            'deleted' => $this->deleted,
            'timestamp' => $this->timestamp,
            'timestamp_lastupdate' => $this->timestamp_lastupdate,
        ]);

        $query->andFilterWhere(['like', 'pengirim_suratmasuk', $this->pengirim_suratmasuk])
            ->andFilterWhere(['like', 'perihal_suratmasuk', $this->perihal_suratmasuk])
            ->andFilterWhere(['like', 'nomor_suratmasuk', $this->nomor_suratmasuk])
            ->andFilterWhere(['like', 'suratmasukdisposisi.pemberi_disposisi', $this->pemberidisposisi])
            ->andFilterWhere(['like', 'suratmasukdisposisi.tujuan_disposisi_pegawai', $this->penerimadisposisi])
            ->andFilterWhere([
                'or',
                ['like', 'reporter', $this->reporter],
                ['like', 'pengguna.nama', $this->reporter],
            ]);

        return $dataProvider;
    }
}
