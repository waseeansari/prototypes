<?php 
//Create fb Object 
function createFbOject()
{
    require_once get_home_path() . 'vendor/autoload.php';
    $creds = array(
        'app_id' => 'app_id', //Get app id from from facebook dev
        'app_secret' => 'app_secret', //Get app secret id from from facebook dev
        'default_graph_version' => 'default_graph_version', //set suitable version 
        'persistent_data_handler' => 'session'
    );
    return new Facebook\Facebook( $creds ); // create facebook object
}

//Create login url to get facebook pages parmission
function fbLoginUrl($permissions)
{
    $facebook = createFbOject();
    $helper = $facebook->getRedirectLoginHelper(); // helper
    $oAuth2Client = $facebook->getOAuth2Client(); // oauth object
    return $helper->getLoginUrl( get_option('redirect_url'), $permissions ); // display login url    
}

//Create login url to get facebook pages parmission and saving platform to user meta data
function fb_login_url()
{
    //setting permission for pages
    $permissions = [
        'user_photos',
        'email', 
        'public_profile', 
        'pages_manage_posts', 
        'pages_read_engagement',
        'manage_pages',
        'ads_management',
        'business_management',
        'publish_actions'
    ];
    
     //setting permission for Groups
     $permissions = [
        'user_photos',
        'manage_pages',
        'email', 
        'public_profile', 
        'groups_access_member_info', 
        'publish_to_groups',
        'publish_actions'
    ];

    $fbUrlLogon = fbLoginUrl($permissions);
    return $fbUrlLogon;
}
?>

<!--On click show facebook login page url and ask for permisson  -->
<a href="<?php echo $fb_login_url; ?>">Login To Facebook Page</a>

<!-- After login and allowing permission, facebook will redirect to giving page in facebook dev with url-->

<!-- 
    your-giving-domain/?code=CODE&state=STATE
 -->
<?php

//geting fb short lived auth key and exchaning it with longed lived key
function getFbAuthKey()
{
	$facebook = createFbOject();

	// helper
    $helper = $facebook->getRedirectLoginHelper();

    // oauth object
    $oAuth2Client = $facebook->getOAuth2Client();

	try {
        $accessToken = $helper->getAccessToken();
    } catch ( Facebook\Exceptions\FacebookResponseException $e ) { // graph error
        echo 'Graph returned an error ' . $e->getMessage;
    } catch ( Facebook\Exceptions\FacebookSDKException $e ) { // validation error
        echo 'Facebook SDK returned an error ' . $e->getMessage;
    }

    if ( !$accessToken->isLongLived() ) { // exchange short for long
        try {
            $accessToken = $oAuth2Client->getLongLivedAccessToken( $accessToken );
        } catch ( Facebook\Exceptions\FacebookSDKException $e ) {
            echo 'Error getting long lived access token ' . $e->getMessage();
        }
    }
    $accessToken = (string) $accessToken;

	return $accessToken; //save token to session for getting facbook pages
}
