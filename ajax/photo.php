<?php 

if(isset($_POST)){

    ############ Edit settings ##############
//     $ThumbSquareSize        = 250; //Thumbnail will be 175x250
    $nailWidth              = 175;
    $nailHeight             = 250;
    $BigImageMaxSize        = 500; //Image Maximum height or width
    $ThumbPrefix            = "small_"; //Normal thumb Prefix
    $PictureDirectory       = 'inc/photos/'; 
    $DestinationDirectory   = '../inc/photos/';
    $Quality                = 90; //jpeg quality
    ##########################################
    
    //check if this is an ajax request
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
        die();
    }
    
    // check $_FILES['image_file'] not empty
    if(!isset($_FILES['image_file']) || !is_uploaded_file($_FILES['image_file']['tmp_name']))
    {
        die('Il manque des données pour importer la photo!'); // output error when above checks fail.
    }
    
    // Random number will be added after image name
    $RandomNumber   = rand(0, 9999999999); 

    $ImageName      = str_replace(' ','-',strtolower($_FILES['image_file']['name'])); //get image name
    $ImageSize      = $_FILES['image_file']['size']; // get original image size
    $TempSrc        = $_FILES['image_file']['tmp_name']; // Temp name of image file stored in PHP tmp folder
    $ImageType      = $_FILES['image_file']['type']; //get file type, returns "image/png", image/jpeg, text/plain etc.

    //Let's check allowed $ImageType, we use PHP SWITCH statement here
    switch(strtolower($ImageType))
    {
        case 'image/png':
            //Create a new image from file 
            $CreatedImage =  imagecreatefrompng($_FILES['image_file']['tmp_name']);
            break;
        case 'image/gif':
            $CreatedImage =  imagecreatefromgif($_FILES['image_file']['tmp_name']);
            break;          
        case 'image/jpeg':
        case 'image/pjpeg':
            $CreatedImage = imagecreatefromjpeg($_FILES['image_file']['tmp_name']);
            break;
        default:
            die('Le type de fichier n\'est pas supporté!'); //output error and exit
    }
    
    //PHP getimagesize() function returns height/width from image file stored in PHP tmp folder.
    //Get first two values from image, width and height. 
    //list assign svalues to $CurWidth,$CurHeight
    list($CurWidth,$CurHeight)=getimagesize($TempSrc);
    
    //Get file extension from Image name, this will be added after random name
    $ImageExt = substr($ImageName, strrpos($ImageName, '.'));
    $ImageExt = str_replace('.','',$ImageExt);
    
    //remove extension from filename
    $ImageName      = preg_replace("/\\.[^.\\s]{3,4}$/", "", $ImageName); 
    
    //Construct a new name with random number and extension.
    $NewImageName = $ImageName.'-'.$RandomNumber.'.'.$ImageExt;
    
    //set the Destination Image
    $thumb_DestRandImageName    = $DestinationDirectory.$ThumbPrefix.$NewImageName; //Thumbnail name with destination directory
    $DestRandImageName          = $DestinationDirectory.$NewImageName; // Image with destination directory
    
    //Resize image to Specified Size by calling resizeImage function.
    if(resizeImage($CurWidth,$CurHeight,$BigImageMaxSize,$DestRandImageName,$CreatedImage,$Quality,$ImageType))
    {
        //Create a square Thumbnail right after, this time we are using cropImage() function
        if(!cropImage($CurWidth,$CurHeight,$nailWidth,$nailHeight,$thumb_DestRandImageName,$CreatedImage,$Quality,$ImageType))
        {
            echo 'Impossible de créer le miniature!';
        }
        /*
        We have succesfully resized and created thumbnail image
        We can now output image to user's browser or store information in the database
        */
        echo '<table width="100%" border="0" cellpadding="4" cellspacing="0">';
        echo '<tr>';
        echo '<td align="center"><img src="'.$PictureDirectory.$ThumbPrefix.$NewImageName.'" alt="Thumbnail"></td>';
        echo '</tr><tr>';
        echo '<td align="center"><img src="'.$PictureDirectory.$NewImageName.'" alt="Resized Image"></td>';
        echo '</tr>';
        echo '</table>';

        /*
        // Insert info into database table!
        mysql_query("INSERT INTO myImageTable (ImageName, ThumbName, ImgPath)
        VALUES ($DestRandImageName, $thumb_DestRandImageName, 'uploads/')");
        */

    }else{
        die('Impossible de redimensionner la photo!'); //output error
    }
}

// This function will proportionally resize image 
function resizeImage($CurWidth,$CurHeight,$MaxSize,$DestFolder,$SrcImage,$Quality,$ImageType)
{
    //Check Image size is not 0
    if($CurWidth <= 0 || $CurHeight <= 0) 
    {
        return false;
    }
    
    //Construct a proportional size of new image
    $ImageScale         = min($MaxSize/$CurWidth, $MaxSize/$CurHeight); 
    $NewWidth           = ceil($ImageScale*$CurWidth);
    $NewHeight          = ceil($ImageScale*$CurHeight);
    $NewCanves          = imagecreatetruecolor($NewWidth, $NewHeight);
    
    // Resize Image
    if(imagecopyresampled($NewCanves, $SrcImage,0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight))
    {
        switch(strtolower($ImageType))
        {
            case 'image/png': imagepng($NewCanves,$DestFolder);
                break;
            case 'image/gif': imagegif($NewCanves,$DestFolder);
                break;          
            case 'image/jpeg':
            case 'image/pjpeg': imagejpeg($NewCanves,$DestFolder,$Quality);
                break;
            default:
                return false;
        }
    //Destroy image, frees memory   
    if(is_resource($NewCanves)) {imagedestroy($NewCanves);} 
        return true;
    }

}

//This function corps image to create exact square images, no matter what its original size!
function cropImage($CurWidth,$CurHeight,$nailWidth,$nailHeight,$DestFolder,$SrcImage,$Quality,$ImageType)
{    
    //Check Image size is not 0
    if($CurWidth <= 0 || $CurHeight <= 0) 
    {
        return false;
    }
    
    //abeautifulsite.net has excellent article about "Cropping an Image to Make Square bit.ly/1gTwXW9
    if($CurWidth>$CurHeight)
    {
        $y_offset = 0;
        $x_offset = ($CurWidth - $CurHeight) / 2;
        $square_size    = $CurWidth - ($x_offset * 2);
    }else{
        $x_offset = 0;
        $y_offset = ($CurHeight - $CurWidth) / 2;
        $square_size = $CurHeight - ($y_offset * 2);
    }
    
    $NewCanves  = imagecreatetruecolor($nailWidth, $nailHeight); 
    if(imagecopyresampled($NewCanves, $SrcImage, 0, 0, $x_offset, $y_offset, $nailWidth, $nailHeight, $square_size, $square_size))
    {
        switch(strtolower($ImageType))
        {
            case 'image/png': imagepng($NewCanves,$DestFolder);
                break;
            case 'image/gif': imagegif($NewCanves,$DestFolder);
                break;          
            case 'image/jpeg':
            case 'image/pjpeg': imagejpeg($NewCanves,$DestFolder,$Quality);
                break;
            default:
                return false;
        }
        
        //Destroy image, frees memory   
        if(is_resource($NewCanves)) {
            imagedestroy($NewCanves);
        } 
        return true;
    }
} ?>