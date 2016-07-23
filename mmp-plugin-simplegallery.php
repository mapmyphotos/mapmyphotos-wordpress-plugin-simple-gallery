<?php
/**
Plugin Name: MapMyPhotos Simple Gallery Plugin
Description: A simple gallery view that displays your photos from your MapMyPhotos photo map gallery
Version: 1.0
Author: Lachlan Pearce
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Register the menu.
add_action( "admin_menu", "getSimpleGalleryMenu" );

function getSimpleGalleryMenu() {
   add_submenu_page(    "options-general.php",                      // Which menu parent
                        "MapMyPhotos Simple Gallery",               // Page title
                        "MapMyPhotos Simple Gallery",               // Menu title
                        "manage_options",                           // Minimum capability (manage_options is an easy way to target administrators)
                        "mmp-simplegallery",                        // Menu slug
                        "getPluginOptions"                         // Callback that prints the markup
               );
}

// Print the markup for the page
function getPluginOptions() {
   if ( !current_user_can( "manage_options" ) )  {
      wp_die( __( "You do not have sufficient permissions to access this page." ) );
   }
   
    ?>
        <form method="post" action="<?php echo admin_url( 'admin-post.php'); ?>">

            <input type="hidden" name="action" value="update_mmpsimplegallery_settings" />

            <div style="background-color:#428bca;padding:10px;margin:10px;margin-left:0px;">
                <div style="display:block;float:left;margin-right:30px;">
                    <img src="https://www.mapmyphotos.net/img/mmpmainlogo.png" />
                </div>
                <div style="display:block;float:left;color:white!important;line-height:52px;font-size:20px;">
                    <?php _e("Simple Gallery Settings", "mmp-simple-gallery"); ?>
                </div>
                <div style="clear:both;"></div>
            </div>
            <p>
                <label><?php _e("MapMyPhotos Username:", "github-api"); ?></label>
                <input class="" type="text" name="mmp-simple-gallery-username" value="<?php echo get_option('mmp-simple-gallery-username'); ?>" />
            </p>

            <p>
                <label><?php _e("MapMyPhotos Password:", "mmp-simple-gallery"); ?></label>
                <input class="" type="password" name="mmp-simple-gallery-password" value="<?php echo get_option('mmp-simple-gallery-password'); ?>" />
            </p>

            <p>
                <label><?php _e("Display Profile Information:", "mmp-simple-gallery"); ?></label>
                <input type="checkbox" id="mmp-simple-gallery-displayprofile" name="mmp-simple-gallery-displayprofile" />
                <script>
                    var bDisplayProfile = <?php echo get_option('mmp-simple-gallery-displayprofile'); ?>;
                    var ele = document.getElementById("mmp-simple-gallery-displayprofile");

                    if(bDisplayProfile != true)
                    {
                        ele.removeAttribute('checked');
                    }
                    else
                    {
                        ele.setAttribute('checked', 'checked');
                    }
                </script>
            </p>

            <p>
                <label><?php _e("Display Photo Details:", "mmp-simple-gallery"); ?></label>
                <input type="checkbox" id="mmp-simple-gallery-displayphotodetails" name="mmp-simple-gallery-displayphotodetails" />
                <script>
                    var bDisplayPhotoDetails = <?php echo get_option('mmp-simple-gallery-displayphotodetails'); ?>;
                    var ele = document.getElementById("mmp-simple-gallery-displayphotodetails");

                    if(bDisplayPhotoDetails != true)
                    {
                        ele.removeAttribute('checked');
                    }
                    else
                    {
                        ele.setAttribute('checked', 'checked');
                    }
                </script>
            </p>

            <p>
                After successful configuration, add your photos to any Page or Blog Post by adding the following tag to the post contents:<br/>
                <pre>[mapmyphotos_simplegallery]</pre>
            </p>

            <input class="button button-primary" type="submit" value="<?php _e("Save", "mmp-simple-gallery"); ?>" />
            
            <?php
                if ( isset($_GET['status']) && $_GET['status']=='success') { 
            ?>
                    <div id="message" class="updated notice is-dismissible">
                        <p><?php _e("Settings updated and MapMyPhotos account connected!", "mmp-simple-gallery"); ?></p>
                        <button type="button" class="notice-dismiss">
                            <span class="screen-reader-text"><?php _e("Dismiss this notice.", "mmp-simple-gallery"); ?></span>
                        </button>
                    </div>
            <?php
                }
            ?>

            <?php
                if ( isset($_GET['status']) && $_GET['status']=='invalidcredentials') { 
            ?>
                    <div id="message" class="error notice is-dismissible">
                        <p><?php _e("Invalid username and/or password, please try again. <a target='_blank' href='https://www.mapmyphotos.net/#/needpasswordreset'>Forgot password?</a>", "mmp-simple-gallery"); ?></p>
                        <button type="button" class="notice-dismiss">
                            <span class="screen-reader-text"><?php _e("Dismiss this notice.", "mmp-simple-gallery"); ?></span>
                        </button>
                    </div>
            <?php
                }
            ?>

            <?php
                if ( isset($_GET['status']) && $_GET['status']=='failure') { 
            ?>
                    <div id="message" class="error notice is-dismissible">
                        <p><?php _e("Failed to save settings.", "mmp-simple-gallery"); ?></p>
                        <button type="button" class="notice-dismiss">
                            <span class="screen-reader-text"><?php _e("Dismiss this notice.", "mmp-simple-gallery"); ?></span>
                        </button>
                    </div>
            <?php
                }
            ?>

        </form>
    <?php

}

function update_mmpsimplegallery_settings() {

    $validationStatus = 'failure';

    // Get the options that were sent
    $UserName = (!empty($_POST["mmp-simple-gallery-username"])) ? $_POST["mmp-simple-gallery-username"] : NULL;
    $Password = (!empty($_POST["mmp-simple-gallery-password"])) ? $_POST["mmp-simple-gallery-password"] : NULL;
    $DisplayProfile = (!empty($_POST["mmp-simple-gallery-displayprofile"])) ? $_POST["mmp-simple-gallery-displayprofile"] : NULL;
    $DisplayPhotoDetails = (!empty($_POST["mmp-simple-gallery-displayphotodetails"])) ? $_POST["mmp-simple-gallery-displayphotodetails"] : NULL; 


    // Validation would go here

    $loginCheckObj = do_mapmyphotos_login($UserName, $Password);

    if(isset($loginCheckObj) && $loginCheckObj->Succeeded)
    {
        $validationStatus = 'success';
    }
    else
    {
        $validationStatus = 'invalidcredentials';
    }

    if($validationStatus == 'success'){
        // Update the values
        update_option(  "mmp-simple-gallery-username"           , $UserName         , TRUE);
        update_option(  "mmp-simple-gallery-password"           , $Password         , TRUE);
        update_option(  "mmp-simple-gallery-displayprofile"     , $DisplayProfile == "on" ? true : false   , TRUE);
        update_option(  "mmp-simple-gallery-displayphotodetails", $DisplayPhotoDetails == "on" ? true : false   , TRUE);
    }

    // Redirect back to settings page
    // The ?page=github corresponds to the "slug" 
    // set in the fourth parameter of add_submenu_page() above.
    $redirect_url = get_bloginfo("url") . "/wp-admin/options-general.php?page=mmp-simplegallery&status=".$validationStatus;
    header("Location: ".$redirect_url);
    exit;
}

function do_mapmyphotos_login($mmpUsernameOrEmail, $mmpPassword){
    /*******************
        MAKE AN API CALL TO LOGIN AND RETRIEVE THE AUTH TOKEN FROM THE RESPONSE
    *******************/
    $baseAPIUrl                 = "https://guty1ov3l8.execute-api.ap-southeast-2.amazonaws.com/mapmyphotos_syd/";
    $apiRequestContentType      = "application/json";
    $apiRequestMethod           = "POST";

    $apiEndpoint_SubmitLogin    = "/login/submit";
    $apiEndpoint_SearchPhotos  = "/photos/get/gallerybyuserid";

    $requestData = array
    (
        "UserName" => $mmpUsernameOrEmail, 
        "Password" => $mmpPassword
    );   

    $dataJsonString = json_encode($requestData);                                                                                   
                                                                                                                        
    $ch = curl_init($baseAPIUrl.$apiEndpoint_SubmitLogin);                                                                      
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $apiRequestMethod);                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJsonString);                                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
    curl_setopt($ch, CURLOPT_HTTPHEADER, array
        (                                                                          
            'Content-Type: '.$apiRequestContentType,                                                                                
            'Content-Length: '.strlen($dataJsonString)
        )                                                                       
    );                  
                                                                                                                    
    $submitLoginResponse = curl_exec($ch);
    
    $submitLoginObjResponse = json_decode($submitLoginResponse);
        
    // close cURL resource, and free up system resources
    curl_close($ch);

    return $submitLoginObjResponse;
}

function getHtmlContent( $atts ) {
    $HTMLOutput = "";

    $apiAuthToken = null;

    $arryPhotos = array();
    $userObj = null;

    try{

        $mmpUsernameOrEmail         = get_option('mmp-simple-gallery-username');
        $mmpPassword                = get_option('mmp-simple-gallery-password');
        $bDisplayProfile            = get_option("mmp-simple-gallery-displayprofile");
        $bDisplayPhotoDetails       = get_option("mmp-simple-gallery-displayphotodetails");

        $baseAPIUrl                 = "https://guty1ov3l8.execute-api.ap-southeast-2.amazonaws.com/mapmyphotos_syd/";
        $apiRequestContentType      = "application/json";
        $apiRequestMethod           = "POST";

        $apiEndpoint_SubmitLogin    = "/login/submit";
        $apiEndpoint_SearchPhotos  = "/photos/get/gallerybyuserid";

        $submitLoginObjResponse = do_mapmyphotos_login($mmpUsernameOrEmail, $mmpPassword);
            
        if
        (
            isset($submitLoginObjResponse) && 
            $submitLoginObjResponse->Succeeded
        )
        {
            //Login Success
            $userObj = $submitLoginObjResponse->Data;
            $apiAuthToken = $submitLoginObjResponse->AuthToken;
        }
        else
        {
            //Login Failure
            return "<span style='color:red;'>Login to MapMyPhotos failed: ".$submitLoginObjResponse->ErrorMessage."</span>";
        }

        /*******************
            MAKE AN API CALL TO OBTAIN PHOTOS
        *******************/
        $requestData = array
        (
            "Skip" => "0",
            "Take" => "3000",
            "UserId" => $userObj->_id,
            "FilterSortData" => 
                array(
                    "IsSearchHidden" => false,
                    "Caption" => "",
                    "DateFrom" => "",
                    "DateFromInternal" => "1800-01-01T00:00:00+00:00",
                    "DateTo" => "",
                    "DateToInternal" => "3100-01-01T00:00:00+00:00",
                    "Coords_HasThem" => true,
                    "Coords_DoesNotHaveThem" => false,
                    "Sort" => array(
                        "Field" => "DateTaken",
                        "Direction" => "DESC"
                    )
                ),
            "AuthToken" => $apiAuthToken
        );   

        $dataJsonString = json_encode($requestData);                                                                                   
                                                                                                                            
        $ch = curl_init($baseAPIUrl.$apiEndpoint_SearchPhotos);                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $apiRequestMethod);                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJsonString);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array
            (                                                                          
                'Content-Type: '.$apiRequestContentType,                                                                                
                'Content-Length: '.strlen($dataJsonString)
            )                                                                       
        );                                                                                                   
                                                                                                                            
        $photosResponse         = curl_exec($ch);
        $photosObjResponse      = json_decode($photosResponse);
        
        // close cURL resource, and free up system resources
        curl_close($ch);

        if
        (
            isset($photosObjResponse) && 
            $photosObjResponse->Succeeded
        )
        {
            //Get Popular Photos Success
            $arryPhotos = $photosObjResponse->Data->Photos;
        }
        else
        {
            //Get Popular Photos Failure
            return "<span style='color:red;'>Request MapMyPhotos failed: ".$popPhotosObjResponse->ErrorMessage."</span>";
        }
    }
    catch(Exception $ex)
    {
        return "<span style='color:red;'>Exception caught: ".$ex->getMessage()."</span>";
    }

    //RENDER OUT THE HTML

    $HTMLOutput .= "";

    if($userObj != null && count($arryPhotos) > 0)
    {
        //PROFILE INFORMATION
        if($bDisplayProfile){
            $HTMLOutput .= "<div style='display:block;float:left;'><img style='width:100px;height:100px;' src='".$userObj->ProfilePicture."' /></div>";
            $HTMLOutput .= "<div style='display:block;float:left;margin-left:20px;'><h4>".$userObj->FirstName."'s photos ".
                    "</h4><br/><a target='_blank' href='https://www.mapmyphotos.net/#/view-gallery/".$userObj->UserName."' >View on MapMyPhotos</a></div><div style='clear:both;'></div>";
        }
        //POPULAR PHOTOS
        foreach($arryPhotos as $thisPhoto)
        {
            $dtDateTaken = new DateTime($thisPhoto->DateTaken);

            $HTMLOutput .= "<div style='display:block;float:left;width:150px;min-height:".($bDisplayPhotoDetails ? "250" : "150")."px;padding:5px;'>".
                                "<center>".
                                    "<a target='_blank' href='https://www.mapmyphotos.net/#/view-gallery/id/".$userObj->_id."/".$thisPhoto->_id."'>".
                                        "<img style='width:150px;height:150px;' src='".$thisPhoto->ThmSrc."' />".
                                    "</a>";
            if($bDisplayPhotoDetails){
                $HTMLOutput .= "<span style='line-height:1.1!important;font-size:11px!important;'>".
                                    $thisPhoto->Caption."<br/>".
                                    $thisPhoto->City."<br/>".
                                    ($dtDateTaken->format('jS F Y g:i A'))."<br/></span>";
            }
            $HTMLOutput .=  "</center></div>";
        }

        $HTMLOutput .= "<div style='clear:both;'></div>";
    }
    else
    {
        return "<span style='color:red;'>Something went wrong.</span>";
    }

    return $HTMLOutput;
}

add_action( 'admin_post_update_mmpsimplegallery_settings', 'update_mmpsimplegallery_settings' );

add_shortcode( "mapmyphotos_simplegallery", "getHtmlContent" );
?>