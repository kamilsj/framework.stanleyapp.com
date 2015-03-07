<?php
session_start();



require_once('config.php');
require_once('database.php');
require_once('ext/imagemap/WideImage.php');
require_once('ext/rackspace/rackspace.php');

function createRandomPassword() {
    $chars = "abcdefghijkmnopqrstuvwxyz0123456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;
    while ($i < 30) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    return $pass;
}

$target = $_REQUEST['target'];

if(empty($db))   $db         =  connect();
                 $id         =  $_SESSION['id'];
				 $login      =  $id;


$array = array('start', 'profile', 'messages', 'admin', 'files');
$json  = array();

if(in_array($target, $array))
{

    if($target == 'files')
    {
		/*
		
		*/




    }
    else
    {

        // Check if the file has a width and height
        $uploadDir = '../tmp/'.$login.'/';

        // Check if the file has a width and height
        function isImage($tempFile) {

            // Get the size of the image
            $size = getimagesize($tempFile);

            if (isset($size) && $size[0] && $size[1] && $size[0] *  $size[1] > 0) {
                return true;
            } else {
                return false;
            }

        }

        if (!empty($_FILES)) {

            $fileData = $_FILES['Filedata'];

            if ($fileData) {

                $tempFile     = $fileData['tmp_name'];

                $fn         = strtolower($fileData['name']);
                $fn         = preg_replace('/[^\w\._]+/', '', $fn);

                $error      = 0;

                if(!is_dir($uploadDir)) mkdir($uploadDir);

                // Validate the file type
                $fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // Allowed file extensions
                $fileParts = pathinfo($fileData['name']);

                $credentials = array(
                    'username' => cluser,
                    'apiKey' => clkey
                );
                $cloud  = new OpenCloud\Rackspace(RACKSPACE_US, $credentials);
                $server = $cloud->ObjectStore('cloudFiles','DFW','publicURL');
                $cont   = $server->Container();
                $cont->Create(array('name'=>clcon));
                $cont->PublishToCDN();


                // Validate the filetype
                if (in_array(strtolower($fileParts['extension']), $fileTypes) && filesize($tempFile) > 0 && isImage($tempFile)) {

                    list($fileName, $ext) = explode('.', $fn);
                    $fileName             = substr(createRandomPassword(), 0, 23);
                    $fn                   = $fileName.'.'.$ext;
                    $targetFile           = $uploadDir . $fn;

                    $imgmap               =  new WideImage();

                    // Save the file
                    if(move_uploaded_file($tempFile, $targetFile))
                    {

                        list($szer, $wys) = getimagesize($targetFile);

                        $json['add'] = 1;

                        if($szer>90 && $wys>90)
                        {
                            if($szer>$wys) $wspol=$wys/90; else $wspol=$szer/90;

                            $nszer=round($szer/$wspol, 0);
                            $nwys=round($wys/$wspol, 0);

                            $imgmap->load($targetFile)->resize($nszer, $nwys)->crop("center", "middle", 90, 90)->saveToFile($uploadDir.'_cube_'.$fn);
                        }
                        else
                        {
                            copy($targetFile, $uploadDir.'_cube_'.$fn);
                        }
                        if(($szer>125 && $wys>125) || ($szer>125 && $wys<125) || ($szer<125 && $wys>125))
                        {
                            if($szer>$wys) $wspol=$szer/125; else $wspol=$wys/125;

                            $nszer=round($szer/$wspol, 0);
                            $nwys=round($wys/$wspol, 0);

                            $imgmap->load($targetFile)->resize($nszer,$nwys)->saveToFile($uploadDir.'_mini_'.$fn);
                        }
                        else
                        {
                            copy($targetFile, $uploadDir.'_mini_'.$fn);
                        }

                        if(($szer>760 && $wys>760) || ($szer>760 && $wys<760) || ($szer<760 && $wys>760))
                        {
                            if($szer>$wys) $wspol=$szer/760; else $wspol=$wys/760;

                            $nszer=round($szer/$wspol, 0);
                            $nwys=round($wys/$wspol, 0);

                            $imgmap->load($targetFile)->resize($nszer,$nwys)->saveToFile($uploadDir.'_optim_'.$fn);
                        }
                        else
                        {
                            copy($targetFile, $uploadDir.'_optim_'.$fn);
                        }




                        if(file_exists($uploadDir.$fn))
                        {
                            $object = $cont->DataObject();
                            $object->Create(array('name'=>$target.'/'.$fn), $uploadDir.$fn);unset($object);
                            unlink($uploadDir.$fn);

                        }else $error = 1;
                        //upload optim
                        if(file_exists($uploadDir.'_optim_'.$fn))
                        {
                            $object = $cont->DataObject();
                            $object->Create(array('name'=>$target.'/_optim_'.$fn), $uploadDir.'_optim_'.$fn); unset($object);
                            unlink($uploadDir.'_optim_'.$fn);
                        }else $error = 1;
                        //upload mini
                        if(file_exists($uploadDir.'_mini_'.$fn))
                        {
                            $object = $cont->DataObject();
                            $object->Create(array('name'=>$target.'/_mini_'.$fn), $uploadDir.'_mini_'.$fn); unset($object);
                            unlink($uploadDir.'_mini_'.$fn);
                        }else $error = 1;
                        //upload cube
                        if(file_exists($uploadDir.'_cube_'.$fn))
                        {
                            $object = $cont->DataObject();
                            $object->Create(array('name'=>$target.'/_cube_'.$fn), $uploadDir.'_cube_'.$fn);
                            unset($url);
                            $url = $object->PublicURL();

                            unset($object);
                            unlink($uploadDir.'_cube_'.$fn);
                        }else $error = 1;

                        if($error == 0)
                        {

                            $json['stat'] = 'OK';

                            $db->query("INSERT INTO images SET uid='$id', target='$target', link='$fn', linkCube='$url'");

                            $json['id']     = $db->insert_id;
                            $json['url']    = $url;
                            $json['target'] = $target;



                            if(empty($_SESSION['tmpArray']))
                            {
                                $_SESSION['tmpArray'][0] = $json;
                            }
                            else
                            {
                                $tmpArray = $_SESSION['tmpArray']; unset($_SESSION['tmpArray']);
                                array_push($tmpArray, $json);
                                $_SESSION['tmpArray'] = $tmpArray;
                            }

                            if($target == 'profile')
                            {
                                $db->query("UPDATE users SET img='$url' WHERE id='$id'");
                            }

                        }else
                        {
                            $json['e'] = 10;
                        }



                    }

                } else {

                   /*
                    * adding files which are not image file using cloud
                    */

                    $targetFile = $uploadDir . $fn;

                    $fileTypes = array('doc', 'pdf', 'docx', 'mp3', 'mp4', 'mov', 'avi', 'odt', 'xls', 'xlsx');

                    if(in_array(strtolower($fileParts['extension']), $fileTypes))
                    {
                        if(move_uploaded_file($tempFile, $targetFile))
                        {
                            $target = 'files/'.$fn;
                            $object = $cont->DataObject();
                            $object->Create(array('name'=>$target), $targetFile);

                            $url    = $object->PublicURL();

                            unset($object);
                            unlink($targetFile);

                            if($db->query("INSERT INTO files SET uid='$id', name='$fn', link='$url', db='0'"))
                            {

                                $json['id']   = $db->insert_id;
                                $json['url']  = $url;
                                $json['name'] = $fn;

                                switch($fileParts['extension'])
                                {
                                    case 'doc':  { $json['ext'] = 'doc'; break; }
                                    case 'pdf':  { $json['ext'] = 'pdf'; break; }
                                    case 'docx': { $json['ext'] = 'doc'; break; }
                                    case 'mp3':  { $json['ext'] = 'mp3'; break; }
                                    case 'mp4':  { $json['ext'] = 'mp4'; break; }
                                    case 'mov':  { $json['ext'] = 'mov'; break; }
                                    case 'avi':  { $json['ext'] = 'avi'; break; }
                                    case 'odt':  { $json['ext'] = 'odt'; break; }
                                    case 'epub': { $json['ext'] = 'epub'; break; }
                                }

                                $json['file'] = 1;
                                $json['stat'] = 'OK';

                            }else { $json['e'] = 11; }

                        }

                        if(empty($_SESSION['tmpArrayFiles']))
                        {
                            $_SESSION['tmpArrayFiles'][0] = $json;
                        }
                        else
                        {
                            $tmpArray = $_SESSION['tmpArrayFiles']; unset($_SESSION['tmpArrayFiles']);
                            array_push($tmpArray, $json);
                            $_SESSION['tmpArrayFiles'] = $tmpArray;
                        }
                    }

                    $json['e'] = 9;

                }
            }
        }else $json['e'] = 8;
    }
}

unset($url, $url2, $object, $imgmap);
echo json_encode($json);

?>