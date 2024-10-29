<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mobildinas".
 *
 * @property int $id_mobildinas
 * @property string $mulai
 * @property string $selesai
 * @property string $keperluan
 * @property string $borrower
 * @property int $approval
 * @property string $timestamp
 * @property string $timestamp_lastupdate
 * @property int $deleted
 */
class Mobildinas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $waktu;
    public static function tableName()
    {
        return 'mobildinas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mulai', 'selesai', 'timestamp', 'timestamp_lastupdate', 'alasan_tolak_batal'], 'safe'],
            [['keperluan', 'borrower'], 'required'],
            [['approval', 'deleted'], 'integer'],
            [['keperluan', 'keperluan_lainnya'], 'string', 'max' => 255],
            [['borrower'], 'string', 'max' => 50],
            ['mulai', 'validateTanggal'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_mobildinas' => 'Id Mobildinas',
            'mulai' => 'Mulai',
            'selesai' => 'Selesai',
            'keperluan' => 'Keperluan',
            'keperluan_lainnya' => 'Keperluan Lainnya',
            'borrower' => 'Peminjam/Penanggung Jawab',
            'approval' => 'Approval',
            'alasan_tolak_batal' => 'Alasan Penolakan/Pembatalan Persetujuan',
            'timestamp' => 'Timestamp',
            'timestamp_lastupdate' => 'Timestamp Lastupdate',
            'deleted' => 'Deleted',
        ];
    }

    public function getBorrowere()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'borrower']);
    }

    public function getKeperluane()
    {
        return $this->hasOne(Mobildinaskeperluan::className(), ['id_mobildinaskeperluan' => 'keperluan']);
    }

    public function validateTanggal()
    {
        $mulai = $this->mulai;
        $selesai = $this->selesai;
        
        // Create the base query
        $query = Mobildinas::find()
            ->joinWith('borrowere')
            ->andWhere(['approval' => '0'])
            ->andWhere(['deleted' => '0']);
    
        if (Yii::$app->controller->action->id == 'update') {
            $id = $this->id_mobildinas;
            $query->andWhere(['<>', 'id_mobildinas', $id]);
        }
    
        // Check for any overlap
        $query->andWhere([
            'or',
            ['between', 'mulai', $mulai, $selesai],
            ['between', 'selesai', $mulai, $selesai],
            [
                'and',
                ['<=', 'mulai', $mulai],
                ['>=', 'selesai', $selesai]
            ]
        ]);
    
        $mobil = $query->one();
    
        if ((Yii::$app->controller->action->id == 'update' || Yii::$app->controller->action->id == 'create') && $mobil !== null) {
            $this->addError('tempat', "Jadwal tersebut beririsan dengan jadwal yang diajukan oleh " . $mobil->borrowere->nama);
        }
    }
    
}
