<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Suratrepoeks;
use DateTime;
use yii\db\Expression;
class SuratrepoeksSearch extends Suratrepoeks
{
    public function rules()
    {
        return [
            [['id_suratrepoeks', 'fk_agenda', 'fk_suratsubkode'], 'integer'],
            [['penerima_suratrepoeks', 'tanggal_suratrepoeks', 'perihal_suratrepoeks', 'nomor_suratrepoeks', 'owner', 'timestamp', 'timestamp_suratrepoeks_lastupdate', 'jenis', 'sifat'], 'safe'],
        ];
    }
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function search($params)
    {
        // Retrieve the data query from your model or wherever you have defined it
        $query = Suratrepoeks::find()->select([
            '*', // Select all columns from the table
            'sorted_nomor_suratrepoeks' => new Expression("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(nomor_suratrepoeks, '/', 1), '-', -1) AS UNSIGNED)"), // Extract numeric part and cast as integer
        ]);
        $query->joinWith(['ownere']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'tanggal_suratrepoeks',
                    'sorted_nomor_suratrepoeks' => [
                        'asc' => ['sorted_nomor_suratrepoeks' => SORT_ASC],
                        'desc' => ['sorted_nomor_suratrepoeks' => SORT_DESC],
                        'label' => 'nomor_suratrepoeks', // Use the original attribute name for sorting link labels
                    ],
                ],
                'defaultOrder' => [
                    'tanggal_suratrepoeks' => SORT_DESC,
                    'sorted_nomor_suratrepoeks' => SORT_DESC,
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
        if (!empty($this->tanggal_suratrepoeks)) {
            $waktuRange = explode(' - ', $this->tanggal_suratrepoeks);
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
                        ['>=', 'tanggal_suratrepoeks', $waktumulaiFormatted],
                        ['<=', 'tanggal_suratrepoeks', $waktuselesaiFormatted],
                    ]);
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id_suratrepoeks' => $this->id_suratrepoeks,
            'fk_agenda' => $this->fk_agenda,
            'sifat' => $this->sifat,
            'jenis' => $this->jenis,
            'fk_suratsubkode' => $this->fk_suratsubkode,
            'timestamp' => $this->timestamp,
            'timestamp_suratrepoeks_lastupdate' => $this->timestamp_suratrepoeks_lastupdate,
        ]);
        $query->andFilterWhere(['like', 'penerima_suratrepoeks', $this->penerima_suratrepoeks])
            ->andFilterWhere(['like', 'perihal_suratrepoeks', $this->perihal_suratrepoeks])
            ->andFilterWhere(['like', 'nomor_suratrepoeks', $this->nomor_suratrepoeks])
            // ->andFilterWhere(['like', 'owner', $this->owner]);
            ->andFilterWhere([
                'or',
                ['like', 'owner', $this->owner],
                ['like', 'pengguna.nama', $this->owner],
            ]);
        return $dataProvider;
    }
}
