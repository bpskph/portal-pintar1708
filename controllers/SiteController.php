<?php

namespace app\controllers;

use app\models\Agenda;
use app\models\AgendaSearch;
use app\models\DlSearch;
use Yii;
use yii\bootstrap5\Html;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Linkapp;
use app\models\Linkmat;
use app\models\MobildinasSearch;
use app\models\Pengguna;
use app\models\Suratrepo;
use app\models\Suratrepoeks;
use app\models\User;
use app\sso\SSOBPS;
use DateTime;
use DateTimeZone;
use Exception;

class SiteController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'index'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['about', 'logout', 'theme', 'evaluasi', 'dashboard'], // add all actions to take guest to login page
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            'actionLog' => [
                'class' => \app\components\AccessLogsBehavior::class,
            ],
        ]);
    }
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'layout' => 'main-error',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    public function actionDashboard()
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('dashboard', []);
        } else {
            return $this->render('dashboard', []);
        }
    }
    public function actionIndex()
    {
        $this->layout = 'main-dashboard';
        $searchModel = new AgendaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $date = new DateTime("now", new DateTimeZone('Asia/Jakarta'));

        $searchModelMobil = new MobildinasSearch();
        $dataProviderMobil = $searchModelMobil->search($this->request->queryParams);
        $dataProviderMobil->query
            ->andWhere(['>=', 'mulai', date('Y-m-d H:i:s')])
            ->andWhere(['<=', 'mulai', date('Y-m-d H:i:s', strtotime('+2 weeks'))])
            ->andWhere(['deleted' => '0']);
        $dataProviderMobil->pagination = false;

        $searchModelDl = new DlSearch();
        $dataProviderDl = $searchModelDl->search($this->request->queryParams);
        $dataProviderDl->query
            ->andWhere(['<=', 'tanggal_mulai', date('Y-m-d')])
            ->andWhere(['>', 'tanggal_selesai', date('Y-m-d')])
            ->andWhere(['deleted' => '0']);
        $dataProviderDl->pagination =  false;

        $dataProvider->query
            ->andWhere([
                'and',
                ['progress' => 0],
                ['>=', 'waktuselesai', $date->format('Y-m-d H:i:s')]
            ])
            ->orWhere([
                'and',
                ['progress' => 2],
                ['>=', 'waktuselesai_tunda', $date->format('Y-m-d H:i:s')]
            ]);
        $dataProvider->sort->defaultOrder = ['waktumulai' => SORT_ASC];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderMobil' => $dataProviderMobil,
            'dataProviderDl' => $dataProviderDl,
        ]);
    }
    public function actionLogin()
    {
        $this->layout = 'main-login';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $sso = new SSOBPS();
        /* pakai OpenID*/
        $sso->setCredential(Yii::$app->params['ssoSatkerId'], Yii::$app->params['ssoSatkerKey']);
        $sso->setRedirectUri('http://localhost/portalpintar/site/login');
        $sso->setRedirectUri(Yii::$app->params['webhostingSatker'] . 'portalpintar/site/login');
        $model = new LoginForm();
        try {
            $post = Yii::$app->request->post();
            if (isset($post['loginsso']) || isset($_GET['code'])) { // login sso
                $login_sso = $sso->getLogin();
                if (($login_sso && isset($login_sso['user'])) || isset($_GET['code'])) {
                    $user = $login_sso['user'];
                    $loginurl = Yii::$app->request->hostInfo . '/portalpintar/' . Yii::$app->controller->id . '/login';
                    // untuk pedoman isi dari array $user
                    $user_login = User::findByUsername($user['username']); // di sini pakai contoh user bawaan yii2
                    // bisa juga pakai user yang ada di database, tinggal manfaatin aja variabel $user untuk login
                    if ($user_login) {
                        if ($user_login->level === 2) { //cek aktivasi
                            Yii::$app->session->setFlash('error', "Maaf, user ini sudah tidak aktif di database Portal Pintar. Silahkan hubungi Admin.");
                            return $this->redirect('https://sso.bps.go.id/auth/realms/pegawai-bps/protocol/openid-connect/logout?redirect_uri=' . $loginurl);
                        }
                        Yii::$app->user->login($user_login);
                        // Yii::$app->session['choicepegawai'] = true;
                        return $this->goBack();
                    } else {
                        Yii::$app->session->setFlash('error', "Maaf, Akun SSO tidak terdaftar dalam database Portal Pintar. Jika Anda menginginkan akses, silahkan input data Anda " .
                            Html::a('di sini', ['/pengguna/create'], ['class' => 'btn btn-light btn-sm text-dark text-decoration-none']));
                        // return $this->redirect(['pengguna/create']);
                        return $this->redirect('https://sso.bps.go.id/auth/realms/pegawai-bps/protocol/openid-connect/logout?redirect_uri=' . $loginurl);
                    }
                }
            } else if (isset($post['LoginForm']['username']) && isset($post['LoginForm']['password'])) { // login biasa
                // nanti di dalam ini, dimasukin logic dari login kalau pakai username atau password           
                if ($model->load(Yii::$app->request->post()) && $model->login()) {
                    // return $this->redirect('index'); //redirect ke beranda
                    return $this->goBack();
                    // print_r($model);
                } else {
                    // die('haa');
                }
            } else if (isset($post['LoginForm'])) {
                $model->addError('username', 'Username atau Password salah');
            }
        } catch (Exception $e) {
            $model->addError('username', 'Terjadi kesalahan pada SSO. Mohon coba beberapa saat lagi atau hubungi nofriani@bps.go.id.');
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
    public function actionTheme($choice)
    {
        if ($choice == 0)
            $affected_rows = Pengguna::updateAll(['theme' => 0], 'username = "' . Yii::$app->user->identity->username . '"');
        else
            $affected_rows = Pengguna::updateAll(['theme' => 1], 'username = "' . Yii::$app->user->identity->username . '"');
        $previousUrl = Yii::$app->request->referrer;
        if (!$previousUrl) {
            $previousUrl = ['index'];
        }
        return $this->redirect($previousUrl);
    }
    public function actionEvaluasi($year)
    {
        /* TAHUN BERJALAN ================================================================= */
        /* SNAPSHOT DATABASE INPUT */
        $jumlahagenda = Agenda::find()->select('*')
            ->where('deleted = 0')
            ->andWhere('YEAR(waktuselesai) = ' . $year)
            ->andWhere([
                'or',
                ['=', 'progress', 0],
                ['=', 'progress', 1],
            ])->count();
        $jumlahagendabatal = Agenda::find()->select('*')
            ->where('deleted = 0')
            ->andWhere([
                'and',
                ['=', 'progress', 2],
                ['=', 'YEAR(waktuselesai_tunda)', $year],
            ])
            ->orWhere([
                'and',
                ['=', 'progress', 3],
                ['=', 'YEAR(waktuselesai)', $year],
            ])->count();
        $jumlahsuratinternal = Suratrepo::find()->select('*')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepo) = ' . $year)
            ->count();
        $jumlahsurateksternal = Suratrepoeks::find()->select('*')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepoeks) = ' . $year)
            ->andWhere([
                'or',
                ['>', 'id_suratrepoeks', 826],
                ['<', 'id_suratrepoeks', 85],
            ])
            ->count();
        $jumlahaplikasi = Linkapp::find()->select('*')
            ->where('active = 1')
            ->andWhere('YEAR(timestamp) = ' . $year)
            ->count();
        $jumlahmateri = Linkmat::find()->select('*')
            ->where('active = 1')
            ->andWhere('YEAR(timestamp) = ' . $year)
            ->count();
        /* SNAPSHOT TOP CONTRIBUTORS */
        $topcontributoragenda = Agenda::find()->select('*, count(id_agenda) as jumlahinput')
            ->joinWith('reportere')
            ->where('deleted = 0')
            ->andWhere('YEAR(waktuselesai) = ' . $year)
            ->groupBy(['reporter'])->orderBy(['jumlahinput' => SORT_DESC])->asArray()->all();
        $topcontributorsuratinternal = Suratrepo::find()->select('*, count(id_suratrepo) as jumlahinput')
            ->joinWith('ownere')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepo) = ' . $year)
            ->groupBy(['owner'])->orderBy(['jumlahinput' => SORT_DESC])->asArray()->all();
        $topcontributorsurateksternal = Suratrepoeks::find()->select('*, count(id_suratrepoeks) as jumlahinput')
            ->joinWith('ownere')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepoeks) = ' . $year)
            ->andWhere([
                'or',
                ['>', 'id_suratrepoeks', 826],
                ['<', 'id_suratrepoeks', 85],
            ])
            ->groupBy(['owner'])->orderBy(['jumlahinput' => SORT_DESC])->asArray()->all();
        $topcontributoraplikasi = Linkapp::find()->select('*, count(id_linkapp) as jumlahinput')
            ->joinWith('ownere')
            ->where('active = 1')
            ->andWhere('YEAR(timestamp) = ' . $year)
            ->groupBy(['owner'])->orderBy(['jumlahinput' => SORT_DESC])->asArray()->all();
        $topcontributormateri = Linkmat::find()->select('*, count(id_linkmat) as jumlahinput')
            ->joinWith('ownere')
            ->where('active = 1')
            ->andWhere('YEAR(timestamp) = ' . $year)
            ->groupBy(['owner'])->orderBy(['jumlahinput' => SORT_DESC])->asArray()->all();
        /* AGENDA */
        // Step 1: Assuming "Agenda" represents the table with the "peserta" column
        $models = Agenda::find()
            ->select(['peserta'])
            ->andWhere('YEAR(waktuselesai) = ' . $year)
            ->all();
        // Step 2: Extract email addresses from the "peserta" column and count their occurrences
        $emailList = [];
        foreach ($models as $model) {
            $emails = explode(', ', $model->peserta); // Assuming emails are separated by ", " as shown in your example
            $emailList = array_merge($emailList, $emails);
        }
        $emailCounts = array_count_values($emailList);
        // Step 3: Sort email addresses based on their frequency in descending order
        arsort($emailCounts);
        // Step 4: Get the top 3 most frequent email addresses along with their total appearance
        $agendapesertatersering = array_slice($emailCounts, 0, 3, true);
        // Now $top3EmailsWithCounts contains the top 3 most frequent email addresses with their total appearance
        $agendatimtersering =   Agenda::find()->select('*, count(id_agenda) as jumlahinput')
            ->joinWith('projecte')
            ->where('deleted = 0')
            ->andWhere('YEAR(waktuselesai) = ' . $year)
            ->groupBy(['pelaksana'])->orderBy(['jumlahinput' => SORT_DESC])
            ->asArray()->all();
        $suratinternalcakupan = Suratrepo::find()->select('*, count(id_suratrepo) as jumlahinput')
            ->joinWith('suratkodee')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepo) = ' . $year)
            ->groupBy(['fk_suratkode'])->orderBy(['jumlahinput' => SORT_DESC])->asArray()->all();
        $surateksternalcakupan = Suratrepoeks::find()->select('*, count(id_suratrepoeks) as jumlahinput')
            ->joinWith('suratkodee')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepoeks) = ' . $year)
            ->andWhere([
                'or',
                ['>', 'id_suratrepoeks', 826],
                ['<', 'id_suratrepoeks', 85],
            ])
            ->groupBy(['fk_suratkode'])->orderBy(['jumlahinput' => SORT_DESC])->asArray()->all();
        /* GRAFIK AGENDA */
        $agendadata = Agenda::find()->select('*, count(id_agenda) as jumlahinput, MONTH(waktuselesai), MONTHNAME(waktuselesai)')
            ->where('deleted = 0')
            ->andWhere('YEAR(waktuselesai) = ' . $year)
            ->groupBy(['MONTH(waktuselesai)'])->orderBy(['MONTH(waktuselesai)' => SORT_ASC])
            ->asArray()->all();
        $graphagenda = [];
        $graphagendalabel = [];
        foreach ($agendadata as $value) {
            array_push($graphagenda, $value['jumlahinput']);
            array_push($graphagendalabel, $value['MONTHNAME(waktuselesai)']);
        }
        /* GRAFIK SURAT */
        $suratinternaldata = Suratrepo::find()->select('*, count(id_suratrepo) as jumlahinput, MONTH(tanggal_suratrepo), MONTHNAME(tanggal_suratrepo)')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepo) = ' . $year)
            ->groupBy(['MONTH(tanggal_suratrepo)'])->orderBy(['MONTH(tanggal_suratrepo)' => SORT_ASC])
            ->asArray()->all();
        $graphsuratinternal = [];
        $graphsuratinternallabel = [];
        foreach ($suratinternaldata as $value) {
            array_push($graphsuratinternal, $value['jumlahinput']);
            array_push($graphsuratinternallabel, $value['MONTHNAME(tanggal_suratrepo)']);
        }
        $surateksternaldata = Suratrepoeks::find()->select('*, count(id_suratrepoeks) as jumlahinput, MONTH(tanggal_suratrepoeks), MONTHNAME(tanggal_suratrepoeks)')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepoeks) = ' . $year)
            ->andWhere([
                'or',
                ['>', 'id_suratrepoeks', 826],
                ['<', 'id_suratrepoeks', 85],
            ])
            ->groupBy(['MONTH(tanggal_suratrepoeks)'])->orderBy(['MONTH(tanggal_suratrepoeks)' => SORT_ASC])
            ->asArray()->all();
        $graphsurateksternal = [];
        $graphsurateksternallabel = [];
        foreach ($surateksternaldata as $value) {
            array_push($graphsurateksternal, $value['jumlahinput']);
            array_push($graphsurateksternallabel, $value['MONTHNAME(tanggal_suratrepoeks)']);
        }

        /* TAHUN SEBELUMNYA ================================================================= */
        /* SNAPSHOT DATABASE INPUT */
        $jumlahagendabefore = Agenda::find()->select('*')
            ->where('deleted = 0')
            ->andWhere('YEAR(waktuselesai) = ' . ($year - 1))
            ->andWhere([
                'or',
                ['=', 'progress', 0],
                ['=', 'progress', 1],
            ])->count();
        $jumlahagendabatalbefore = Agenda::find()->select('*')
            ->where('deleted = 0')
            ->andWhere([
                'and',
                ['=', 'progress', 2],
                ['=', 'YEAR(waktuselesai_tunda)', $year],
            ])
            ->orWhere([
                'and',
                ['=', 'progress', 3],
                ['=', 'YEAR(waktuselesai)', $year],
            ])->count();
        $jumlahsuratinternalbefore = Suratrepo::find()->select('*')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepo) = ' . ($year - 1))
            ->count();
        $jumlahsurateksternalbefore = Suratrepoeks::find()->select('*')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepoeks) = ' . ($year - 1))
            ->andWhere([
                'or',
                ['>', 'id_suratrepoeks', 826],
                ['<', 'id_suratrepoeks', 85],
            ])
            ->count();
        $jumlahaplikasibefore = Linkapp::find()->select('*')
            ->where('active = 1')
            ->andWhere('YEAR(timestamp) = ' . ($year - 1))
            ->count();
        $jumlahmateribefore = Linkmat::find()->select('*')
            ->where('active = 1')
            ->andWhere('YEAR(timestamp) = ' . ($year - 1))
            ->count();
        /* SNAPSHOT TOP CONTRIBUTORS */
        $topcontributoragendabefore = Agenda::find()->select('*, count(id_agenda) as jumlahinput')
            ->joinWith('reportere')
            ->where('deleted = 0')
            ->andWhere('YEAR(waktuselesai) = ' . ($year - 1))
            ->groupBy(['reporter'])->orderBy(['jumlahinput' => SORT_DESC])->asArray()->all();
        $topcontributorsuratinternalbefore = Suratrepo::find()->select('*, count(id_suratrepo) as jumlahinput')
            ->joinWith('ownere')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepo) = ' . ($year - 1))
            ->groupBy(['owner'])->orderBy(['jumlahinput' => SORT_DESC])->asArray()->all();
        $topcontributorsurateksternalbefore = Suratrepoeks::find()->select('*, count(id_suratrepoeks) as jumlahinput')
            ->joinWith('ownere')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepoeks) = ' . ($year - 1))
            ->andWhere([
                'or',
                ['>', 'id_suratrepoeks', 826],
                ['<', 'id_suratrepoeks', 85],
            ])
            ->groupBy(['owner'])->orderBy(['jumlahinput' => SORT_DESC])->asArray()->all();
        $topcontributoraplikasibefore = Linkapp::find()->select('*, count(id_linkapp) as jumlahinput')
            ->joinWith('ownere')
            ->where('active = 1')
            ->andWhere('YEAR(timestamp) = ' . ($year - 1))
            ->groupBy(['owner'])->orderBy(['jumlahinput' => SORT_DESC])->asArray()->all();
        $topcontributormateribefore = Linkmat::find()->select('*, count(id_linkmat) as jumlahinput')
            ->joinWith('ownere')
            ->where('active = 1')
            ->andWhere('YEAR(timestamp) = ' . ($year - 1))
            ->groupBy(['owner'])->orderBy(['jumlahinput' => SORT_DESC])->asArray()->all();
        /* AGENDA */
        // Step 1: Assuming "Agenda" represents the table with the "peserta" column
        $modelsbefore = Agenda::find()
            ->select(['peserta'])
            ->andWhere('YEAR(waktuselesai) = ' . ($year - 1))
            ->all();
        // Step 2: Extract email addresses from the "peserta" column and count their occurrences
        $emailListbefore = [];
        foreach ($modelsbefore as $model) {
            $emails = explode(', ', $model->peserta); // Assuming emails are separated by ", " as shown in your example
            $emailListbefore = array_merge($emailListbefore, $emails);
        }
        $emailCountsbefore = array_count_values($emailListbefore);
        // Step 3: Sort email addresses based on their frequency in descending order
        arsort($emailCountsbefore);
        // Step 4: Get the top 3 most frequent email addresses along with their total appearance
        $agendapesertaterseringbefore = array_slice($emailCountsbefore, 0, 3, true);
        // Now $top3EmailsWithCounts contains the top 3 most frequent email addresses with their total appearance
        $agendatimterseringbefore =   Agenda::find()->select('*, count(id_agenda) as jumlahinput')
            ->joinWith('projecte')
            ->where('deleted = 0')
            ->andWhere('YEAR(waktuselesai) = ' . ($year - 1))
            ->groupBy(['pelaksana'])->orderBy(['jumlahinput' => SORT_DESC])
            ->asArray()->all();
        $suratinternalcakupanbefore = Suratrepo::find()->select('*, count(id_suratrepo) as jumlahinput')
            ->joinWith('suratkodee')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepo) = ' . ($year - 1))
            ->groupBy(['fk_suratkode'])->orderBy(['jumlahinput' => SORT_DESC])->asArray()->all();
        $surateksternalcakupanbefore = Suratrepoeks::find()->select('*, count(id_suratrepoeks) as jumlahinput')
            ->joinWith('suratkodee')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepoeks) = ' . ($year - 1))
            ->andWhere([
                'or',
                ['>', 'id_suratrepoeks', 826],
                ['<', 'id_suratrepoeks', 85],
            ])
            ->groupBy(['fk_suratkode'])->orderBy(['jumlahinput' => SORT_DESC])->asArray()->all();
        /* GRAFIK AGENDA */
        $agendadatabefore = Agenda::find()->select('*, count(id_agenda) as jumlahinput, MONTH(waktuselesai), MONTHNAME(waktuselesai)')
            ->where('deleted = 0')
            ->andWhere('YEAR(waktuselesai) = ' . ($year - 1))
            ->groupBy(['MONTH(waktuselesai)'])->orderBy(['MONTH(waktuselesai)' => SORT_ASC])
            ->asArray()->all();
        $graphagendabefore = [];
        $graphagendalabelbefore = [];
        foreach ($agendadatabefore as $value) {
            array_push($graphagendabefore, $value['jumlahinput']);
            array_push($graphagendalabelbefore, $value['MONTHNAME(waktuselesai)']);
        }
        /* GRAFIK SURAT */
        $suratinternaldatabefore = Suratrepo::find()->select('*, count(id_suratrepo) as jumlahinput, MONTH(tanggal_suratrepo), MONTHNAME(tanggal_suratrepo)')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepo) = ' . ($year - 1))
            ->groupBy(['MONTH(tanggal_suratrepo)'])->orderBy(['MONTH(tanggal_suratrepo)' => SORT_ASC])
            ->asArray()->all();
        $graphsuratinternalbefore = [];
        $graphsuratinternallabelbefore = [];
        foreach ($suratinternaldatabefore as $value) {
            array_push($graphsuratinternalbefore, $value['jumlahinput']);
            array_push($graphsuratinternallabelbefore, $value['MONTHNAME(tanggal_suratrepo)']);
        }
        $surateksternaldatabefore = Suratrepoeks::find()->select('*, count(id_suratrepoeks) as jumlahinput, MONTH(tanggal_suratrepoeks), MONTHNAME(tanggal_suratrepoeks)')
            ->where('deleted = 0')
            ->andWhere('YEAR(tanggal_suratrepoeks) = ' . ($year - 1))
            ->andWhere([
                'or',
                ['>', 'id_suratrepoeks', 826],
                ['<', 'id_suratrepoeks', 85],
            ])
            ->groupBy(['MONTH(tanggal_suratrepoeks)'])->orderBy(['MONTH(tanggal_suratrepoeks)' => SORT_ASC])
            ->asArray()->all();
        $graphsurateksternalbefore = [];
        $graphsurateksternallabelbefore = [];
        foreach ($surateksternaldatabefore as $value) {
            array_push($graphsurateksternalbefore, $value['jumlahinput']);
            array_push($graphsurateksternallabelbefore, $value['MONTHNAME(tanggal_suratrepoeks)']);
        }

        return $this->render('evaluasi', [
            'year' => $year,
            'jumlahagenda' => $jumlahagenda,
            'jumlahagendabatal' => $jumlahagendabatal,
            'jumlahsuratinternal' => $jumlahsuratinternal,
            'jumlahsurateksternal' => $jumlahsurateksternal,
            'jumlahaplikasi' => $jumlahaplikasi,
            'jumlahmateri' => $jumlahmateri,
            'topcontributoragenda' => $topcontributoragenda,
            'topcontributorsuratinternal' => $topcontributorsuratinternal,
            'topcontributorsurateksternal' => $topcontributorsurateksternal,
            'topcontributoraplikasi' => $topcontributoraplikasi,
            'topcontributormateri' => $topcontributormateri,
            'agendapesertatersering' => $agendapesertatersering,
            'agendatimtersering' => $agendatimtersering,
            'suratinternalcakupan' => $suratinternalcakupan,
            'surateksternalcakupan' => $surateksternalcakupan,
            'graphagenda' => $graphagenda,
            'graphagendalabel' => $graphagendalabel,
            'graphsuratinternal' => $graphsuratinternal,
            'graphsuratinternallabel' => $graphsuratinternallabel,
            'graphsurateksternal' => $graphsurateksternal,
            'graphsurateksternallabel' => $graphsurateksternallabel,
            'jumlahagendabefore' => $jumlahagendabefore,
            'jumlahagendabatalbefore' => $jumlahagendabatalbefore,
            'jumlahsuratinternalbefore' => $jumlahsuratinternalbefore,
            'jumlahsurateksternalbefore' => $jumlahsurateksternalbefore,
            'jumlahaplikasibefore' => $jumlahaplikasibefore,
            'jumlahmateribefore' => $jumlahmateribefore,
            'topcontributoragendabefore' => $topcontributoragendabefore,
            'topcontributorsuratinternalbefore' => $topcontributorsuratinternalbefore,
            'topcontributorsurateksternalbefore' => $topcontributorsurateksternalbefore,
            'topcontributoraplikasibefore' => $topcontributoraplikasibefore,
            'topcontributormateribefore' => $topcontributormateribefore,
            'agendapesertaterseringbefore' => $agendapesertaterseringbefore,
            'agendatimterseringbefore' => $agendatimterseringbefore,
            'suratinternalcakupanbefore' => $suratinternalcakupanbefore,
            'surateksternalcakupanbefore' => $surateksternalcakupanbefore,
            'graphagendabefore' => $graphagendabefore,
            'graphagendalabelbefore' => $graphagendalabelbefore,
            'graphsuratinternalbefore' => $graphsuratinternalbefore,
            'graphsuratinternallabelbefore' => $graphsuratinternallabelbefore,
            'graphsurateksternalbefore' => $graphsurateksternalbefore,
            'graphsurateksternallabelbefore' => $graphsurateksternallabelbefore,
        ]);
    }
}
