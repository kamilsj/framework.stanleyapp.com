<table>
    <tr>
        <td><img id="addNewProfile" src="<?php echo $pic; ?>" width="90" height="90" border="0" /></td>
        <td>
            <div id="mainInfo">
                <table>
                    <tr>
                        <td style="width:400px">

                        </td>
                        <td style="width:200px;">
                            <?php

                                $tmp = $db->query("SELECT linkCube, id FROM images WHERE uid='$id' AND target='$what'");
                                while($tmp2 = $tmp->fetch_assoc())
                                {
                                    echo '<img class="imgUp" src="'.$tmp2['linkCube'].'" width="30" height="30" data-querystring="pid='.$tmp2['id'].'" onclick="changeAvatar('.$tmp2['id'].')" /> ';
                                }

                                unset($tmp, $tmp2);
                            ?>

                        </td>
                    </tr>
                </table>
            </div>
            <div id="uploadProfilePicture">
                <table>
                    <tr>
                        <td style="width: 120px">
                            <input id="file_upload_profile" name="file_upload_profile" type="file">
                        </td>
                        <td>
                            <div style="width: 100%" id="queue"></div>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>




<ul style="list-style: none; padding-left: 10px;">
    <li>
        <span class="title"><?php echo $lang->profile->chpwd; ?></span><br /><br /><br />
        <span class="stitle"><?php echo $lang->profile->opwd; ?></span><br />
        <input type="password" id="oldPwd" name="oldPwd" class="search" style="width: 250px" /><br />
        <span class="stitle"><?php echo $lang->profile->npwd; ?></span><br/>
        <input type="password" id="newPwd1" name="newPwd1" class="search" style="width: 250px" /><br />
        <span class="stitle"><?php echo $lang->profile->n2pwd; ?></span><br/>
        <input type="password" id="newPwd2" name="newPwd2" class="search" style="width: 250px" /><br />
        <input type="button" onclick="changePassword()" value="<?php ?>Change" />
    </li>
    <li>

    </li>
</ul>

<script type="text/javascript">
    $(function() {
        $('#file_upload_profile').uploadifive({
            'auto' : true,
            'simUploadLimit' : 1,
            'fileSizeLimit' : '4096KB',
            'queueID'      : 'queue',
            'uploadScript' : '../libs/uploadifive-image-target.php?target=<?php echo $what; ?>',
            'onUploadComplete' : function(file, res)
            {
                var obj = jQuery.parseJSON(res);

                if(typeof obj == 'object')
                {
                    if(obj.stat == 'OK')
                    {
                        $('#addNewProfile').attr('src',obj.url);
                        $('#banerPhoto').attr('src', obj.url);
                    }
                }
            }
        });
    });
</script>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // loop over each element and create a tooltip using the data-attribute
        $('.imgUp').each(function() {
            Tipped.create(this, "../ajax/profilePhotoTip.php", {
                ajax: { data: $(this).data('querystring'), type: 'post' }
            });
        });
    });
</script>