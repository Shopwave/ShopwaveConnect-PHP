ShopwaveConnect-PHP
============================

<p>A PHP library for ShopwaveConnect with an accompanying PHP example project.</p>

<h2>Required Class Libraries</h2>

<p>Each of the following libraries must be included in your PHP implementation file. An example of this can be found in <strong> Examples/StandardPHPApplication/index.php </strong>.</p>

```PHP
ShopwaveConnectManager
```

<h2>Required Parameters</h2>

<p>Each of the following parameters will have to be supplied in the code in order to communicate with the ShopwaveConnect API. An example of this can be found in <strong>Examples/StandardPHPApplication/index.php</strong>.</p>

```PHP
/* Your Shopwave ClientIdentifier (e.g. js7woa9ro028djsnakf778sn3wiam3ond274knao) */
$clientIdentifier = "SHOPWAVE_CLIENT_IDENTIFIER";
    
/* Your Shopwave ClientSecret (e.g. 76h4389732ru2039r20ruju023r9u2309jk8sna0) */
$clientSecret = "SHOPWAVE_CLIENT_SECRET";
    
/* Your Shopwave RedirectUri (e.g. http://my.app) */
$redirectUrl = "SHOPWAVE_REDIRECT_URI";
    
/* Your Shopwave Scope. Please request scopes that are absolute essential for your app */
$scopes = array("user","application", "merchant","store", "product", "category", "basket", "promotion", "log", "supplierStore", "supplier", "invoice", "stock");

```
<h2>Using the Library</h2>

<p>Each of the following code snipets can be found in <strong>Examples/StandardPHPApplication/index.php</strong>.

<h3>Initialisation</h3>

```PHP
$shopwaveConnect = new ShopwaveConnectManager($clientIdentifier, $clientSecret, $redirectUrl, $scopes);
```

<h3>Authorise</h3>

<h4>PHP</h4>

```PHP
header('Location: ' . $shopwaveConnect->getAuthoriseApplicationUri()); //redirect to auth uri
```

<h3>Fetch Token</h3>

```PHP
$token = $shopwaveConnect->makeTokenCall();
```

<h3>Make API Call</h3>

```PHP
$endpoint = $shopwaveConnect->makeShopwaveApiCall("API_ENDPOINT", "OAUTH2_TOKEN", "METHOD", "HEADERS_DICTIONARY", "POST_BODY_JSON")
$user = $shopwaveConnect->makeShopwaveApiCall('user', $_SESSION['token'], "GET", $headers);
```
