<?php

//采集《鬼吹灯》到 wordpress
//有问题请联系 郎中(Hostloc@郎郎中)
//后面会多写一点采集的东西发上来
//有什么好的采集内容可以给我建议
//这里想感谢下A大，组长，咩咩，校长，黑手，cpuer（真挚的）
//博客:http://rojey.me
//2012/12/24

//***************  配置 ***************************
$db_host = 'localhost' ; 	//Wordpress数据库地址
$db_user = 'root';			//Wordpress数据库用户名
$db_pass = '';				//Wordpress数据库密码
$db_name = 'wordpress';		//Wordpress数据库名
//*************************************************

header("Content-Type:text/html;charset=utf-8");
echo '<h2>《鬼吹灯》采集系统 <small>（共751篇）</small></h2><style>a{text-decoration:none;color: #3B8DD1;}a:hover{text-decoration:underline;color: #B50101;}</style>';
if (isset($_GET['success'])) {
	if (!($_GET['p']==752)){
		$pp = $_GET['p'] + 1;
		echo '<h4>采集成功，自动进入下一篇，进行第 <span style="color:red">'.$pp.'</span> 篇</h4>';
	}
}
if (isset($_GET['error'])) {
	echo '<h4>采集失败,请联系<a href="http://www.hostloc.com/space-uid-19152.html">郎中</a></h4>';
}
function linkSql(){
	global $db_host,$db_user,$db_pass;
	$conn = mysql_connect($db_host,$db_user,$db_pass);
	return $conn;
}
function selectDB(){
	global $db_name;
	$select = mysql_select_db($db_name,linkSql());
	mysql_query("SET NAMES UTF8");
	return $select;
}
function query($sql){
	selectDB();
	$result = mysql_query($sql);
	return $result;
}
function addpost($title,$content,$time){
	$sql = "INSERT INTO `wp_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`)
	VALUES 
	(NULL, '1', '$time', '$time', '$content', '$title', '', 'publish', 'open', 'open', '', '', '', '', '$time', '$time', '', '0', '', '0', 'post', '', '0');";
	$addpost = query($sql);
	return $addpost;
}
if (isset($_GET['p'])) {
	$url = "http://www.guichuideng.org/"; 
	$fcontents = file_get_contents($url);
	$time = Date('Y-m-d H:i:s',time()-1000+$_GET['p']);
	$i = 0;
	//获取所有文章标题
	preg_match_all('/\">(.*?)<\/a><\/td>/',$fcontents,$titles);//正则匹配
	foreach ($titles[1] as $title){
		$post[]=array("title"=>$title);
	}
	//获取所有文章路径
	preg_match_all('/<td><a href=\"(.*?)\">/',$fcontents,$hrefs);//正则匹配
	foreach ($hrefs[1] as $href){
		$post2[] = array("href"=>$href);
	}
	//合并所有标题和路径
	while ( $i<=751 ){
		$me = array_merge($post[$i], $post2[$i]);
		$p[] = $me;
		$i++;
	}
	$page = $_GET['p'];
	if ( $page <= 751){
		$fc= str_replace("\r\n","",str_replace("\r","",str_replace("\n","",file_get_contents($p[$page]['href']))));//替换换行
		preg_match('/<div style="clear:both"><\/div>(.*?)<p align="center">/',$fc,$content);//正则匹配
		$do = addpost($p[$page]['title'],$content[1],$time);
		if ($do){
			$page++;
			echo '<script>self.location="post.php?success=1&p='.$page.'"</script>';
		}
		else {
			header('Location:post.php?error=1');
		}
	}
	else {
		echo '<h4 style="color:red">恭喜，全部采集完成!</h4>';	
	}
}
else {
	if(!isset($_GET['error'])){
		echo '<h4><a href ="post.php?success=1&p=0">点击开始采集</a></h4>';
	}
}
?>