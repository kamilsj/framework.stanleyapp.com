<?php


class posts
{
	public $db;
	public $mem;
	public $name;
	public $pic;
    public $dbchooser = 0;
	public $cat = 0;
	public $lang;
    public $path = '../../';

    private $postmax      = 30;
    private $commnetmax   = 10;

	public function createForm()
	{
		
			echo'
			
			<div id="postsArea">	
				<table>
					<tr>
					<td><img src="'.$this->pic.'"  width="90" height="90" class="tipped" title="'.$this->name.'" /></td>
					<td><textarea class="textarea" id="post" name="post"></textarea></td>
					</tr>
					<tr><td colspan="2">
						<table>
						<tr><td style="width: 130px;">
                            <!-- Place For Your Code :) -->
						</td>';

                        if($this->dbchooser != 0)
                        {
                            echo '<td>
                                <div style="position:relative;">
                                    <input type="dropbox-chooser" name="selected-file" style="visibility: hidden;"/>
                                </div>
                            </td>';
                        }

			echo		'<td>
							<a href="#" id="addPhotos"><img src="'.$this->path.'gfx/camera.png" class="tipped" title="'.$this->lang->main->addFiles.'" /></a>
						</td>
						<td style="width: 320px; text-align: right;">
						<label>
							<select id="cat" name="cat">';
							
							$tmp = $this->db->query("SELECT * FROM categories");

							while($tmp2 = $tmp->fetch_assoc())
							{
								
								echo '<option value="'.$tmp2['id'].'">'.$tmp2['name'].'</option>';
							
							}							
							
							
			echo		'	</select>
						</label>
						</td>
						<td style="width: 120px; text-align: right;">
							<a href="#" class="big" id="addPost">'.$this->lang->main->add.'</a>
						</td>
						</tr>
						</table>
					</td>
					</tr>
					<tr><td colspan="2"><br />
					<div id="startUploadPhoto">
					    <form>
						    <div id="queue" style="min-height: 60px;" class="tipped" title="Drag photos or files here"></div>
						    <div style="position: relative; left: 230px;"">
						        <input id="file_upload" name="file_upload" type="file" multiple="true"><br />
						        <a class="big" href="javascript:$(\'#file_upload\').uploadifive(\'upload\')">'.$this->lang->posts->upload.'</a>
						    </div>
					    </form>
					    <br />
					    <div id="preview"></div>
					</div>
					</td></tr>
				</table><br /><br />
			</div>
		
			';	
	}
	
	
	public function fetchUsers($id)
	{
		
		if(isset($this->mem))
		{
			$sql   = "SELECT * FROM users WHERE id='$id' LIMIT 1";
			$key    = md5('stanley'.$sql);
			$data3  = $this->mem->get($key);
			
			if(!$data3)
			{
				
				$half = $this->db->query($sql)->fetch_assoc();	
				$this->mem->set($key, $half, 864000);
				$data3 = $this->mem->get($key); 
			}
			
			return $data3;
			
		}
		
	}
	
	public function fetchComments($pid)
	{
		if($pid>0)
		{

            $uid = $_SESSION['id'];

			echo '<div id="mainComments'.$pid.'">';
			
			if(isset($this->mem))
			{				
				$sql     =  "SELECT * FROM comments WHERE pid='$pid' ORDER BY date";
				$key     =  md5('stanley'.$sql);
				$data2    =  $this->mem->get($key);
				
				if(!$data2)
				{
					$tmp_arr = array();
					$ii		 = 0;
					$half	 = $this->db->query($sql);
					
					while($tmp = $half->fetch_assoc())
					{
						$tmp_arr[$ii] = $tmp;
						$ii++;
					}
					
					$this->mem->set($key, $tmp_arr, 864000);
					$data2 = $this->mem->get($key); unset($tmp_arr);
				}
				
				
				
				if(count($data2)>0)
				{
					
					$ii = 0;					
					
					while(@$data2[$ii] && $ii <= $this->commnetmax)
					{

                        if($uid == $data2[$ii]['uid'] && isset($this->pic) && isset($this->name))
                        {
                            $pic   = $this->pic;
                            $name  = $this->name;

                        }else
                        {

						    $userFetch = $this->fetchUsers($data2[$ii]['uid']);
						
						    if(strlen(@$userFetch['img'])>0)	$pic = $userFetch['img']; else $pic = ''.$this->path.'gfx/faces/23.png';
						    if(strlen(@$userFetch['fname'])>0 && strlen($userFetch['sname'])>0) $name = $userFetch['fname'].' '.$userFetch['sname']; else $name = $userFetch['zhname'];
                        }
						
						echo '<div class="comment" id="comment'.$data2[$ii]['id'].'">

							 <table celpadding="0" cellspacing="0">
							 	<tr>
								<td><img src="'.$pic.'" width="25" height="25" class="userProfilePhoto" data-querystring="uid='.$data2[$ii]['uid'].'&name='.$name.'&pic='.$pic.'" /></td>
								<td style="width: 515px;">'.$this->url($data2[$ii]['body']).'</td>
								<td>';

						if($data2[$ii]['uid'] == $uid || $uid == 1)
                        {
                            echo '<a href="#" onclick="deleteComment('.$data2[$ii]['id'].','.$data2[$ii]['pid'].')"><img src="'.$this->path.'gfx/x.gif" /></a>';
                        }

						echo    '</td>
								</td>
							 </table>									
							 </div>';
						
						$ii++;
					}
				}
			
			}
		
			echo '</div>';
		}
	}

    public function evaluatePost($cat, $pid)
    {
        $uid   = $_SESSION['id'];

        $plus  = $this->db->query("SELECT uid FROM evaluate WHERE pid='$pid' AND cat='$cat' AND value=1")->num_rows;
        $minus = $this->db->query("SELECT uid FROM evaluate WHERE pid='$pid' AND cat='$cat' AND value=0")->num_rows;

        $uvote = $this->db->query("SELECT value FROM evaluate WHERE uid='$uid' AND cat='$cat' AND pid='$pid' AND (value=1 OR value=0)");


        echo '<span id="eCount'.$pid.'">'.($plus-$minus).'</span> ';

        if($uvote->num_rows == 0){

            echo '<a href="#" onclick="evaluateComm('.$cat.','.$pid.', 0)"><img id="eMinus'.$pid.'" src="https://c730088.ssl.cf2.rackcdn.com/gfx/down.gif" border="0" /></a><a href="#" onclick="evaluateComm('.$cat.','.$pid.', 1)"><img id="ePlus'.$pid.'" src="https://c730088.ssl.cf2.rackcdn.com/gfx/up.gif" border="0" /></a>';

        }
        else
        {
            $vote = $uvote->fetch_assoc();

            if($vote['value'] == 0)
            {
                echo '<a href="#" onclick="deleteEvComm('.$cat.','.$pid.')"><img id="eMinus'.$pid.'" src="https://c730088.ssl.cf2.rackcdn.com/gfx/down.gif" border="0" /></a><a href="#" onclick="evaluateComm('.$cat.','.$pid.', 1)"><img id="ePlus'.$pid.'" src="https://c730088.ssl.cf2.rackcdn.com/gfx/up2.gif" border="0" /></a>';
            }
            else
            {
                echo '<a href="#" onclick="evaluateComm('.$cat.','.$pid.', 0)"><img id="eMinus'.$pid.'" src="https://c730088.ssl.cf2.rackcdn.com/gfx/down2.gif" border="0" /></a><a href="#" onclick="deleteEvComm('.$cat.','.$pid.')"><img id="ePlus'.$pid.'" src="https://c730088.ssl.cf2.rackcdn.com/gfx/up.gif" border="0" /></a>';
            }


        }
        unset($uvote, $vote);

        return ($plus-$minus);
    }

	public function fetchPosts()
	{

        $postStat = array();

		echo '<div id="postMain">';
			
		if(isset($this->mem))
		{
				if(@$this->cat == 0)
					$sql    = "SELECT * FROM posts ORDER BY date DESC";
				else
					$sql	= "SELECT * FROM posts WHERE cat='".$this->cat."' ORDER BY date DESC"; 
						
				$key     =  md5('stanley'.$sql);
				$data    =  $this->mem->get($key);
				
				if(!$data)
				{
					 $tmp_arr  =   array();
					 $i        =   0;
					 $half     =   $this->db->query($sql);
					 
					 while($tmp = $half->fetch_assoc())
					 {
						 $tmp_arr[$i] = $tmp;
						 $i++;
					 }
						 
					 $this->mem->set($key, $tmp_arr, 864000);
					 $data = $this->mem->get($key); unset($tmp_arr);

                    unset($tmp);
				}
				
				if(count($data)>0)
				{
				
					$i = 0;
					
					while(@$data[$i] && $i <= $this->postmax)
					{

                        $uid  = $_SESSION['id'];
                        $pid  = $data[$i]['id'];
                        $scat = $data[$i]['cat'];

                        if($uid == $data[$i]['uid'] && isset($this->pic) && isset($this->name))
                        {
                            $pic   = $this->pic;
                            $name  = $this->name;

                        }else
                        {

                            $userFetch = $this->fetchUsers($data[$i]['uid']);

                            if(strlen(@$userFetch['img'])>0)	$pic = $userFetch['img']; else $pic = $this->path.'gfx/faces/23.png';
                            if(strlen(@$userFetch['fname'])>0 && strlen($userFetch['sname'])>0) $name = $userFetch['fname'].' '.$userFetch['sname']; else $name = $userFetch['zhname'];
                        }
						$dcat = $this->db->query("SELECT * FROM categories WHERE id='$scat' LIMIT 1")->fetch_assoc();
						
						echo '<div class="post" id="post'.$data[$i]['id'].'">
								<table celpadding="0" cellspacing="0">
									<tr>
										<td><img src="'.$pic.'"  width="30" height="30" /></td>
										<td style="width:550px; text-align: left;">'.$name.'    ';

                        $postStat[$pid] = $this->evaluatePost($scat,$pid);

                        echo		    '<br />
										<span class="dateFormat">';

                        echo            $this->print_day($data[$i]['date']);

                        echo            '</span></td><td>';

                        if($uid == $data[$i]['uid'] || $uid == 1)
                        {
                            echo '<a href="#" onclick="deletePost('.$pid.','.$scat.')"><img src="'.$this->path.'gfx/x.gif" /></a>';
                        }

                        echo            '</td></tr>
									<tr>
										<td valign="top"><div style="background:'.$dcat['color'].';, width: 30px; height: 30px; color: white; font-size: 23px; text-align: center;">';

                        /*
                         * First character is Chinese Character
                         */

                        $han = mb_substr(ucfirst($dcat['name']),0, 3);
                        if(preg_match('/\\p{Han}/u', $han)) echo $han; else echo substr($han, 0, 1);

                        echo        '</div></td>
										<td colspan="2" class="postBody">'.$this->url($data[$i]['body']).'</td>
									</tr>';

                                    if($data[$i]['images'] == 1)
                                    {
                                        echo '<tr><td></td><td colspan="2" style="width: 500px; text-align: center;">';

                                        $tmp = $this->db->query("SELECT * FROM images WHERE pid='$pid'");

                                        while($tmp2 = $tmp->fetch_assoc())
                                        {
                                            echo '<a href="'.str_replace('_cube_', '_optim_', $tmp2['linkCube']).'" class="lightview" data-lightview-group="group'.$pid.'"><img src="'.$tmp2['linkCube'].'" /></a> ';
                                        }
                                        unset($tmp, $tmp2);
                                        echo '</td></tr>';
                                    }elseif($data[$i]['files'] == 1)
                                    {
                                        echo '<tr><td></td><td colspan="2" style="width: 500px; text-align: center;">';

                                        $tmp = $this->db->query("SELECT * FROM files WHERE pid='$pid'");

                                        while($tmp2 = $tmp->fetch_assoc())
                                        {
                                            list($name, $ext) = explode('.', $tmp2['name']);
                                            if(file_exists('gfx/filetype/'.$ext.'.png')) $f = ''.$this->path.'gfx/filetype/'.$ext.'.png'; else $f = ''.$this->path.'gfx/filetype/_blank.png';

                                            echo '<a href="'.$tmp2['link'].'"><img src="'.$f.'" width="50" height="50" class="tipped" title="'.$tmp2['name'].'" /><a/>';

                                            unset($name, $ext, $f);
                                        }
                                        unset($tmp, $tmp2);
                                    }

				        echo		'<tr>
										<td></td>
										<td colspan="2"><div id="commentSpace">';
										
										$this->fetchComments($pid);
										
						echo			'</div></td>
									</tr>
									<tr>
										<td valign="top" align="center"><img src="'.$this->path.'gfx/comment.png" /></td>
										<td colspan="2">

												<table celpadding="0" cellspacing="0">
												<tr>
													<td colspan="2">
													<div id="newComment'.$pid.'"></id>
													</td>
												</tr>		
												<tr>
													<td><img src="'.$this->pic.'" width="25" height="25" /></td>
													<td><input type="text" id="commentBody'.$pid.'" name="commentBody'.$pid.'" class="commentBody" onkeypress="return addcomment(event, '.$pid.')" /></td>
												</tr>
												</table>

										</td>
									</tr>		
								</table>
							  </div><br />';
										
						$i++;				
					}	
				}	
		}		
		
		
			
		echo '</div>';
		unset($tmp, $postStat, $scat, $dcat, $pid);
	
	}
	
	public function create()
	{
		$this->createForm();
		$this->fetchPosts();
		
	}

    public function tag($string)
    {
        $reg = '/#[a-zA-Z0-9]+/';

        if(preg_match_all($reg, $string, $tag))
        {
            for($i=0;$i<count($tag);$i++)
            {
                $val = $tag[$i];

                for($j=0;$j<count($val); $j++)
                {
                    $zm = substr($val[$j], 1);
                    $string = str_replace($val[$j], '<a href="?w=tags&t='.$zm.'"><b>'.$val[$j].'</b></a>', $string);
                }
            }

        }

        return $string;
    }

    public function print_day($new_date, $flg=0){
        /*
         * @TODO implementation of date printing - still something does not work
         */
        if($flg==0)
            $req = strtotime('now') - strtotime($new_date);
        else
            $req = strtotime('now') - $new_date;

        if($req < 60)
        {
            $aaa = (int)($req/60);
            echo $aaa;
            echo $this->lang->date->sec.' '.$this->lang->date->bef;

        }
        elseif($req > 60 && $req < 3600)
        {

            $aaa = (int)($req/60);
            echo $aaa;
            echo $this->lang->date->min.' '.$this->lang->date->bef;

        }
        elseif($req > 3600 && $req < 86400 )
        {
            $aaa = (int)($req/3600);
            echo $aaa;
            echo $this->lang->date->hou.' '.$this->lang->date->bef;
        }
        elseif($req > 86400 && $req < 604800)
        {

            $aaa = (int)($req/86400);
            echo $aaa;
            echo $this->lang->date->day.' '.$this->lang->date->bef;

        }
        elseif($req > 604800 && $req < 2419200)
        {
            $aaa = (int)($req/604800);
            echo $aaa;
            echo $this->lang->date->wee.' '.$this->lang->date->bef;
        }
        elseif($req > 2419200 && $req < 29030400)
        {
            $aaa = (int)($req/2419200);
            echo $aaa;
            echo $this->lang->date->mon.' '.$this->lang->date->bef;

        }
        elseif($req > 29030400)
        {
            $aaa = (int)($req/29030400);
            echo $aaa;
            echo $this->lang->date->yea.' '.$this->lang->date->bef;
        }
        else echo $new_date;




    }
    public function url($string)
    {
        $string = preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $string);
        $string = $this->tag($string);

        return $string;

    }


}