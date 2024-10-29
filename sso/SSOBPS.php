<?php


namespace app\sso;


require 'openid_php53/vendor/autoload.php';
use InoOicClient\Flow\Basic;

class SSOBPS{

    private $provider;
    private $token;
    private $protocol;
    private $redirectUri;
    private $config;
    private $state; // return param from sso.bps.go.id
    private $code; // return param from sso.bps.go.id
    private $session_state; // return param from sso.bps.go.id

    // CONSTRUCTOR
    function __construct(){
        if(!isset($_SESSION))  session_start();
    }

    // DESTRUCTOR
    function __destruct() {

    }

    function setCredential($clientId, $clientSecret, $protocol='https://'){
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->protocol = $protocol;
        $this->redirectUri = $protocol.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
    }

    function setRedirectUri($uri){
        $this->redirectUri = $uri;
    }

    function getLogin(){

        $this->config = array(
            'client_info' => array(
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectUri,

                'authorization_endpoint' => 'https://sso.bps.go.id/auth/realms/pegawai-bps/protocol/openid-connect/auth',
                'token_endpoint' => 'https://sso.bps.go.id/auth/realms/pegawai-bps/protocol/openid-connect/token',
                'user_info_endpoint' => 'https://sso.bps.go.id/auth/realms/pegawai-bps/protocol/openid-connect/userinfo',

                'authentication_info' => array(
                    'method' => 'client_secret_post',
                    'params' => array(
                        'client_secret' => $this->clientSecret,
                    ),
                )
            )
        );

        $this->provider = new Basic($this->config);



        if (!isset($_GET['code'])) {
            $this->getAuthorizationCode();
            // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            $this->getAuthorizationCode();
        } else {
            // Try to get an access token (using the authorization coe grant)
            try {
                $this->token = $this->provider->getAccessToken($_GET['code']);


            } catch (Exception $e) {
                //
                if($e->getMessage() == "Error response from server 'invalid_grant' (Code not valid)"){
                    $this->getAuthorizationCode();
                }else{
                    echo $e->getMessage();
                }

            }

            if(!$this->token || $this->token==''){
                unset($_SESSION['oauth2state']);
                $this->getAuthorizationCode();
            }

            return array(
                'token'=>$this->token,
                'user'=>$this->getLoginInformation(),
                'logout_url'=>'https://sso.bps.go.id/auth/realms/pegawai-bps/protocol/openid-connect/logout?redirect_uri='.$this->redirectUri
            );
        }
    }

    function getLoginAsJSON(){
        header('Content-Type: application/json');
        $data = $this->getLogin();
        echo json_encode($data);
    }

    private function getLoginInformation(){
        $user_ = null;
        try {


            $user = $this->provider->getUserInfo($this->token);

            $user_ = array(
                'nama' => $user['name'] ?: null,
                'email' => $user['username']."@bps.go.id" ?: null,
                'username' => $user['username'] ?: null,
                'nip' => $user['nip-lama'] ?: null,
                'nip_baru' => $user['nip'] ?: null,
                'kode_organisasi' => $user['organisasi'] ?: null,
                'kode_provinsi' => substr($user['organisasi'],0,2) ?: null,
                'kode_kabupaten' => substr($user['organisasi'],2,2) ?: null,
                'alamat_kantor' => $user['alamat-kantor'] ?: null,
                'provinsi' => $user['provinsi'] ?: null,
                'kabupaten' => $user['kabupaten'] ?: null,
                'golongan' => $user['golongan'] ?: null,
                'jabatan' => $user['jabatan'] ?: null,
                'foto' => $user['foto'] ?: null,
                'eselon' => $user['eselon'] ?: null,
            );





        } catch (Exception $e) {
            //exit('Gagal Mendapatkan Data Pengguna: '.$e->getMessage());
            exit("Exception during user info request: ".get_class($e)." ".$e->getMessage());
        }

        return $user_;
    }

    private function getAuthorizationCode(){
        // If we don't have an authorization code then get one
        $authUrl =  $this->provider->getAuthorizationRequestUri('profile-pegawai,email');

        $param = parse_url( $authUrl, $component = -1 );
        $query = explode('&', $param['query']);
        $query_final = array();
        foreach($query as $key => $value) {
            $temp = explode('=', $value);
            $query_final[$temp[0]] = $temp[1];
        }


        $_SESSION['oauth2state'] = $query_final['state'];
        \Yii::$app->response->redirect($authUrl);
    }

}
