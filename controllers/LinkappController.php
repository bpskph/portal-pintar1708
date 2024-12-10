<?php
namespace app\controllers;
use app\models\Linkapp;
use app\models\LinkappSearch;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class LinkappController extends BaseController
{
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
                            'actions' => ['error', 'index', 'indexgrid', 'indextile', 'updateviews'],
                            'allow' => true,
                        ],
                        [
                            'actions' => ['view', 'create', 'update', 'delete', 'aktifkanlagi'],
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                return !\Yii::$app->user->isGuest && (\Yii::$app->user->identity->level === 0);
                            },
                        ],
                    ],
                ],
            ]
        );
    }
    public function actionIndex()
    {
        $searchModel = new LinkappSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        // $keywords = ['pengolahan', 'sakernas', 'susenas'];
        // Get all the keywords from the database
        $allKeywords = Linkapp::find()->select('keyword')->column();
        // Count the frequency of each keyword
        $keywordCounts = array_count_values(array_map('trim', explode(',', implode(',', $allKeywords))));
        // Sort the keywords by frequency in descending order
        arsort($keywordCounts);
        // Take the top 10 most frequent keywords
        $mostFrequentKeywords = array_slice(array_keys($keywordCounts), 0, 10);
        // Convert the most frequent keywords to an array of key-value pairs for use in checkboxList
        $keywords = array_combine($mostFrequentKeywords, $mostFrequentKeywords);
        if (Yii::$app->request->get('LinkappSearch')) {
            $searchModel->keyword = Yii::$app->request->get('LinkappSearch')['keyword'];
            $dataProvider->query->andFilterWhere(['like', 'keyword', $searchModel->keyword]);
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'keywords' => $keywords
        ]);
    }
    public function actionIndexgrid()
    {
        $searchModel = new LinkappSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('indexgrid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionCreate()
    {
        $model = new Linkapp();
        Yii::$app->params['uploadPath'] = Yii::getAlias("@app") . '/images/linkapp';
        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->owner = Yii::$app->user->identity->username;
            $model->screenshot = UploadedFile::getInstance($model, 'screenshot');
            $query = new Query();
            $query->select('id_linkapp')->from('linkapp')->orderBy(['id_linkapp' => SORT_DESC])->limit(1);
            $latestId = $query->scalar();
            $path = Yii::$app->params['uploadPath'] . '/' . ($latestId + 1) . '.' . $model->screenshot->extension;
            if ($model->save()) {
                if (isset($model->screenshot))
                    $model->screenshot->saveAs($path);
                Yii::$app->session->setFlash('success', "Link berhasil ditambahkan. Terima kasih.");
                return $this->redirect(['indexgrid']);
            }
        } else {
            $model->loadDefaultValues();
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        Yii::$app->params['uploadPath'] = Yii::getAlias("@app") . '/images/linkapp';
        if ($this->request->isPost && $model->load($this->request->post())) {
            $screenshot = UploadedFile::getInstance($model, 'screenshot');
            date_default_timezone_set('Asia/Jakarta');
            $model->timestamp_lastupdate = date('Y-m-d H:i:s');
            if ($screenshot) {
                // The user has uploaded a new file, so update the file attribute
                $model->screenshot = $screenshot;
                $path = Yii::$app->params['uploadPath'] . '/' . $model->id_linkapp . '.' . $model->screenshot->extension;
            } else {
                // The user has not uploaded a new file, so keep the existing file attribute
                $model->screenshot = $model->getOldAttribute('screenshot');
            }
            if ($model->save()) {
                if (isset($model->screenshot))
                    $model->screenshot->saveAs($path);
                Yii::$app->session->setFlash('success', "Link berhasil dimutakhirkan. Jika Anda menambahkan screenshot baru dan tidak ada perubahan pada sistem, mohon lakukan clear cache atau akses dengan moda penyamaran. Terima kasih.");
                return $this->redirect(['indexgrid']);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }
    public function actionDelete($id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $affected_rows = Linkapp::updateAll(['active' => 0, 'timestamp_lastupdate' => date('Y-m-d H:i:s')], 'id_linkapp = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['indexgrid']);
        } else {
            Yii::$app->session->setFlash('success', "Link berhasil di-nonaktifkan. Terima kasih.");
            return $this->redirect(['indexgrid']);
        }
    }
    public function actionAktifkanlagi($id)
    {
        $model = $this->findModel($id);
        $affected_rows = Linkapp::updateAll(['active' => 1], 'id_linkapp = "' . $id . '"');
        if ($affected_rows == 0) {
            Yii::$app->session->setFlash('warning', "Gagal. Mohon hubungi Admin.");
            return $this->redirect(['indexgrid']);
        } else {
            Yii::$app->session->setFlash('success', "Link berhasil diaktifkan kembali. Terima kasih.");
            return $this->redirect(['indexgrid']);
        }
    }
    protected function findModel($id_linkapp)
    {
        if (($model = Linkapp::findOne(['id_linkapp' => $id_linkapp])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionUpdateviews($id)
    {
        $model = Linkapp::findOne($id);
        $model->views++;
        $model->save(false); // don't validate to save time
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['success' => true];
    }
}
