<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../services/AuthService.php';


use League\OAuth2\Client\Provider\Facebook;

class FacebookController
{
    private $provider;
    private $authService;

    public function __construct()
    {
        $this->provider = new Facebook([
            'clientId'     => '1350876316239418',  
            'clientSecret' => '48f741c23e842e1cd8802bdf93ea9fdf',  
            'redirectUri'  => 'http://localhost:81/fbcallback',
            'graphApiVersion' => 'v22.0',
        ]);
        $this->authService = new AuthService();

    }

    public function login()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        // session_start();
        $authUrl = $this->provider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $this->provider->getState();
        
        header('Location: ' . $authUrl);
        exit;
    }

    public function callback()
    {
//         error_reporting(E_ALL);
// ini_set('display_errors', 1);

        // session_start();

        if (!isset($_GET['code'])) {
            exit('Lỗi: Không có mã code từ Facebook.');
        }

        try {
            $token = $this->provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            $user = $this->provider->getResourceOwner($token);

            echo "<h1>Xin chào, " . htmlspecialchars($user->getName()) . "!</h1>";
            echo "<p>Email: " . htmlspecialchars($user->getEmail()) . "</p>";
            echo "<p>ID Facebook: " . htmlspecialchars($user->getId()) . "</p>";
            

           $res = $this->authService->loginWithFB($user->getEmail(),$user->getId());
           if (! $res->isSuccess()) {
            $_SESSION['errors']    = $res->getErrors();
           
            header('Location: /login');
            exit;
        }
        $_SESSION['account'] = $res->getData();

        echo 'Dang nhap thanh cong roi day nhung chua xu li phan chuyen trang :)';


        } catch (\Exception $e) {
            exit('Lỗi lấy Access Token: ' . $e->getMessage());
        }
    }
}
?>