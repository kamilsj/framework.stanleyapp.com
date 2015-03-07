<?php
require_once('libs/posts.php');

if($a == 'category'){

    if(isset($what_id)) $cat = $what_id; else $cat = 0;

}else $cat = 0;

$posts       = new posts();
$posts->db   = $db;
$posts->mem  = $mem;
$posts->lang = $lang;
$posts->path = $path;
						
if(strlen(@$user['img'])>0)	$pic = $user['img']; else $pic = $path.'gfx/faces/23.png';
if(strlen(@$user['fname'])>0 && strlen($user['sname'])>0) $name = $user['fname'].' '.$user['sname']; else $name = $user['zhname'];
if(defined('dbchooser')){ if(dbchooser != 'CHANGE_ME'){ $dbchooser = dbchooser; $posts->dbchooser = $dbchooser; }}

$layout->name  = $name;
$layout->pic   = $pic;

$layout->baner();

?>
<div class="mainArea native margin">
	<div class="inside">	
		<div class="sp1">

		<?php
			echo '<ul style="list-style: none; padding-left: 10px;">
			      <li><div class="box"></div><a class="big" href="/files">'.$lang->main->files.'</a></li>
			      <li><span></span></li>
			      <li><div class="box"></div><a class="big" href="/">'.$lang->main->all.'</a></li>';

            $resp = $db->query("SELECT * FROM categories");
			while($tmp = $resp->fetch_assoc())
			{
				echo '<li><div class="box" style="background:'.$tmp['color'].'"></div><a href="/category/'.$tmp['id'].'" class="big">'.ucfirst($tmp['name']).'</a></li>';
				if($tmp['id'] == $cat) $posts->cat = $cat;
			}
			echo '</ul>';
			
			unset($resp, $tmp);
		?>
		</div>
		<div class="sp2">
			<div id="startMainArea">
			<?php 
				
									
				$posts->name  = $name;
				$posts->pic   = $pic;
				$posts->create();
				
			?>
			</div>
		</div>
		<div class="sp3">
            <?php



            ?>
		</div>	
	</div>
</div>

<script type="text/javascript">
$(function() {
    $('#file_upload').uploadifive({
        //'auto'         : false,
        'debug'        : true,
        'simUploadLimit' : 1,
        'fileSizeLimit' : '4096KB',
        'uploadLimit' : 6,
        'queueID'      : 'queue',
        'uploadScript' : '../libs/uploadifive-image-target.php?target=<?php echo $what; ?>',
        'onDrop'       : function(file, fileDropCount) {},
        'onUploadComplete' : function(file, res)
        {
            var obj = jQuery.parseJSON(res);
            //alert(obj);
            if(typeof obj == 'object')
            {
                if(obj.stat == 'OK')
                {
                    if(obj.file == 1)
                    {

                    }
                    else { $('#preview').append('<img src="'+obj.url+'" width="40" height="40"  /> '); }
                }
            }
        }       
     });
});
</script>

<?php if(isset($dbchooser)) echo '<script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs"  data-app-key="'.$dbchooser.'"></script>'; ?>

<script type="text/javascript" src="<?php echo $path; ?>js/posts.js"></script>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.userProfilePhoto').each(function() {
            Tipped.create(this,'../ajax/userProfileTip.php', {
                ajax: { data: $(this).data('querystring'), type: 'post' }
            });
        });
    });
</script>