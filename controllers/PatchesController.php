<?php

namespace app\controllers;

use app\models\Patches;
use app\models\PatchesSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\controllers\AgendaController;

/**
 * PatchesController implements the CRUD actions for Patches model.
 */
class PatchesController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => \yii\filters\AccessControl::className(),
                    'rules' => [
                        [
                            'actions' => ['error', 'index'],
                            'allow' => true,
                        ],
                        [
                            'actions' => ['create', 'wa_blast'],
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                return !\Yii::$app->user->isGuest && (\Yii::$app->user->identity->username === 'nofriani');
                            },
                        ],
                        [
                            'actions' => ['view'], // add all actions to take guest to login page
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }
    public function actionIndex()
    {
        $searchModel = new PatchesSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }
    public function actionView($id)
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $this->findModel($id),
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }
    public function actionCreate()
    {
        $model = new Patches();
        if ($this->request->isPost) {
            $model->load($this->request->post());
            if ($model->save()) {
                if ($model->is_notification == true) {
                    $users = \app\models\Pengguna::find()->where('level <> 2')->all();
                    foreach ($users as $user) {
                        $userId = $user->username;
                        \app\models\Notification::createNotification($userId, 'Terdapat update/menu baru di Sistem Portal Pintar, yaitu: <strong>' . $model->title . '</strong> dengan rincian sebagai berikut: <br/>' . $model->description, Yii::$app->controller->id, $model->id_patches);
                    }
                }

                return $this->redirect(['index']);
            }
        } else {
            $model->loadDefaultValues();
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    protected function findModel($id_patches)
    {
        if (($model = Patches::findOne(['id_patches' => $id_patches])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    protected function convertHtmlToWhatsapp($html)
    {
        // Convert special HTML tags to WhatsApp-compatible text formatting
        $search = [
            '/<strong>(.*?)<\/strong>/i',  // Bold
            '/<b>(.*?)<\/b>/i',            // Bold
            '/<em>(.*?)<\/em>/i',          // Italic
            '/<i>(.*?)<\/i>/i',            // Italic
            '/<u>(.*?)<\/u>/i',            // Underline
            '/<br\s*\/?>/i',               // Line breaks
            '/<li>(.*?)<\/li>/i',          // List items
            '/<p>(.*?)<\/p>/i',            // Paragraphs
            '/<ol>(.*?)<\/ol>/is',         // Ordered lists
            '/<ul>(.*?)<\/ul>/is',         // Unordered lists
        ];

        $replace = [
            '*$1*',                        // Bold
            '*$1*',                        // Bold
            '_$1_',                        // Italic
            '_$1_',                        // Italic
            '~$1~',                        // Strikethrough as underline is not supported
            "\n",                          // New line for <br>
            "• $1\n",                      // Bullet for list items
            "$1\n\n",                      // Paragraphs with double new line
            "$1",                          // Keep list items intact
            "$1",                          // Keep list items intact
        ];

        // Convert HTML to plain text
        $text = preg_replace($search, $replace, $html);

        // Remove any remaining HTML tags
        $text = strip_tags($text);

        // Trim any unnecessary spaces or line breaks
        $text = trim($text);

        return $text;
    }

    public function actionWa_blast($id)
    {
        $model = $this->findModel($id);

        if ($model->is_notification != 1) {
            Yii::$app->session->setFlash('warning', "WA Blast hanya disediakan untuk patch yang bersifat -notifikasi-.");
            return $this->redirect(['index']);
        }

        $htmlContent = $model->description;
        // $nomor_tujuan = '6285267246910-1431003500@g.us';
        $nomor_tujuan = '6285664991937-1407296320@g.us';
        // $whatsappText = $this->convertHtmlToWhatsapp($htmlContent);
        $whatsappText = '
        Ykh.
Pengguna Portal Pintar 2.0

Bersama ini kami sampaikan terdapat update fitur baru pada Sistem Portal Pintar, yaitu;
WhatsApp Notification Blast
Notifikasi tersedia untuk fitur Agenda, Surat Eksternal dan Mobil Dinas, dengan ketentuan sbb:

1. Nomor ini digunakan untuk keperluan WhatsApp Blast Notification Portal Pintar 2.0 dan di-manage oleh Tim Pengolahan, TI dan Metodologi.
2. Nomor WA Bapak-Ibu telah kami input secara manual ke Sistem Portal Pintar dan dapat dikonfirmasi di menu User > Profil Saya (kanan atas halaman Portal Pintar). Jika terdapat kesalahan atau perubahan nomor, maka harap mengajukan perubahan data ke kami.
3. Jika Bapak/Ibu mengisi agenda baru (yang direncanakan), sistem tidak langsung mengirimkan notifikasi undangan ke para peserta yang terdaftar di agenda tersebut. Fitur untuk mengirimkan undangan via WA tersedia di beranda Agenda, kolom Aksi, tombol WhatsApp. 
4. Sistem akan mengirimkan notifikasi WA ke pemilik Surat Eksternal jika telah disetujui.
5. Sistem akan mengirimkan notifikasi WA ke Bagian Umum (PJK Pengelolaan Kendaraan Dinas) jika ada pengajuan peminjaman mobil dinas masuk ke sistem.
6. Sistem akan mengirimkan notifikasi WA ke peminjam mobil dinas jika peminjaman disetujui, ditolak atau persetujuannya dibatalkan.
7. Nomor Portal Pintar ini menyediakan fitur Auto-Reply (masih dalam tahap pengembangan).
8. Konsultasi terkait sistem layanan TI dari Tim PTIM masih dilakukan melalui Halocik.
9. Karena fitur baru dan masih dikembangkan, apabila Bapak-Ibu menemukan error pada sistem, mohon untuk mengirimkan notifikasi ke kami.

Demikian disampaikan.
Atas perhatiannya diucapkan terima kasih.

Hormat Kami
Tim Pengolahan, TI dan Metodologi

Tembusan: Ketua Tim PTIM';

        $response = AgendaController::wa_engine($nomor_tujuan, $whatsappText);

        Yii::info($response, 'wa_blast'); // Log the response instead of outputting it

        if (strpos($response, 'Error:') !== false) {
            Yii::$app->session->setFlash('error', "Failed to send WA Blast. Error: " . $response);
        } else {
            Yii::$app->session->setFlash('success', "WA Blast berhasil dikirim. Terima kasih." . $response);
        }

        return $this->redirect(['index']);
    }
}
