<?php

    require_once '../../Libraries/ShopwaveConnect.php';
    
    use ShopwaveConnect\ShopwaveConnectManager;
    
    /* Your Shopwave ClientIdentifier (e.g. js7woa9ro028djsnakf778sn3wiam3ond274knao) */
    $clientIdentifier = "SHOPWAVE_CLIENT_IDENTIFIER";
    
    /* Your Shopwave ClientSecret (e.g. 76h4389732ru2039r20ruju023r9u2309jk8sna0) */
    $clientSecret = "SHOPWAVE_CLIENT_SECRET";
    
    /* Your Shopwave RedirectUri (e.g. http://my.app) */
    $redirectUrl = "SHOPWAVE_REDIRECT_URI";
    
    /* Your Shopwave Scope. Please request scopes that are absolute essential for your app */
    $scopes = array("user","application", "merchant","store", "product", "category", "basket", "promotion", "log", "supplierStore", "supplier", "invoice", "stock");
    
    session_start();
    
    $shopwaveConnect = new ShopwaveConnectManager($clientIdentifier, $clientSecret, $redirectUrl, $scopes);
    
    if(isset($_GET['redirect']))
    {
        header('Location: ' . $shopwaveConnect->getAuthoriseApplicationUri()); //redirect to auth uri
    }
    else if(isset($_GET['logout']))
    {
        session_destroy();
        header('Location: ' . $shopwaveConnect->getLogoutUri()); //redirect to logout uri on shopwave auth side
    }
    else
    {
        /* Check for existing session */
        if(isset($_GET['code']) && strlen($_GET['code']) > 0)
        {
            if(!isset($_SESSION["token"]))
            {
                $shopwaveConnect->authCode = $_GET['code'];
                
                /* Fetch access and refresh token */
                $token = $shopwaveConnect->makeTokenCall();
                
                /* Store the token within the user session */ 
                /* Please do not expose user token variables on client code */
                $_SESSION['token'] = $token;
                
                $uriParts = explode('?', $_SERVER['REQUEST_URI'], 2);
                //redirect to previous page
                header("Location: http://".$_SERVER['HTTP_HOST'].$uriParts[0]);
                
            }
        }
        
        if(isset($_SESSION['token']))
        {
            $headers = array(
                "x-accept-version" => "0.4"
            );
            
            $user = $shopwaveConnect->makeShopwaveApiCall('user', $_SESSION['token'], "GET", $headers);
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>PAGE TITLE</title>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
        
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <a href="" class="navbar-brand">YOUR APP NAME</a>
                </div>
            </div>
        </div>
        <div class="container body-content">
            <div class="jumbotron" style="margin:0px auto; text-align:center; background:#ffffff;">
                <h1>YOUR APP LOGO</h1>
                <?php if(!isset($_SESSION["token"])) { ?>
                    <p class="lead">Click to login with Shopwave.</p>

                    <a href="?redirect=true">
                        <img src="images/LoginWithShopwaveSmall.png" alt="Shopwave Connect" width="134" />
                    </a>
                <?php } else { ?>
                    <p class="lead">Logged in <a href="?logout=true" class="btn btn-success btn-sm">Logout</a></p>

                    <ul style="list-style:none">
                        <li><strong>Name: </strong><?php echo $user["body"]->user->firstName ?> <?php echo $user["body"]->user->lastName ?></li>
                        <li><strong>Merchant Id: </strong><?php echo $user["body"]->user->employee->merchantId ?></li>
                    </ul>
                <?php } ?>
            </div>
            <hr/>
            <footer>
                <p>&copy; <?php echo date('Y') ?> - YOUR COMPANY NAME</p>
            </footer>
        </div>
    </body>
</html>
