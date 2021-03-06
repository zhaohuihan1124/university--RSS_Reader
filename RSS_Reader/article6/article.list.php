<?php 
	require_once('connect.php');

	/*****增加分页功能的两个步骤*****
		1.修改原sql语句：
			原sql语句是从数据库表中获取所有数据，修改后的sql语句只取一页中应显示的数据内容(注意sql语句的内容)
		2.增加底部显示：
			增加底部的选页功能(注意链接的跳转页面)
		*只适用于article.list.php，因为不涉及到where子句，即条件查询；有条件查询的情况是不一样的
	*/
	//1.修改sql语句
	//设定每一页显示的记录数
	$pagesize=10;

	//取得记录总数$rs，计算总页数用
	$rs=mysql_query("select count(*) from spider_rss");
	$myrow = mysql_fetch_array($rs);
	$numrows=$myrow[0];
	//计算总页数
	$pages=intval($numrows/$pagesize);
	if ($numrows%$pagesize)	$pages++;
	//设置页数
	if (isset($_GET['page'])){
		$page=intval($_GET['page']);
	}
	else{
	//设置为第一页 
	$page=1;
	}
	//计算记录偏移量
	$offset=$pagesize*($page - 1);
	//读取指定记录数
	$query=mysql_query("select * from spider_rss order by id desc limit $offset,$pagesize");
	//end 修改sql语句
	
	
	/*//原sql语句
	$sql = "select * from spider_rss";
	$query = mysql_query($sql);
	*/
	if($query&&mysql_num_rows($query)){
		while($row = mysql_fetch_assoc($query)){
			$data[] = $row;
		}
	}

	$sql2 = "select distinct Source from spider_rss";
	$query2 = mysql_query($sql2);
	if($query2&&mysql_num_rows($query2)){
		while($row2 = mysql_fetch_assoc($query2)){
			$data2[] = $row2;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>文章查询系统</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="default.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="wrapper">
<!-- start header -->
<div id="header">
	<div id="logo">
		<h1><a href="article.list.php">^_^&nbsp;咩咩阅读<sup></sup></a></h1>
		<h2></h2>
	</div>
	<div id="menu">
		<ul>
			<li class="active"><a href="article.list.php">文章</a></li>
			<li><a href="about.php">关于我们</a></li>
			<li><a href="contact.php">联系我们</a></li>
			<li><a href="start.spider.php">搜索最新内容</a></li>
		</ul>
	</div>
</div>
<!-- end header -->
</div>

<!-- start page -->
<div id="page">
	<!-- start content -->	
	<div id="content">
	<?php
		if(empty($data)){	
			echo "当前没有文章，请管理员在后台添加文章";
		}else{
			foreach($data as $value){
	?>
		<div class="post">
			<h1 class="title"><a href="<?php echo $value['Url']?>"><?php echo $value['Title']?></a><span style="color:#ccc;font-size:14px;">　　来源：<!--作者放置到这里--><?php echo $value['Source']?></span></h1>
			<div class="entry">
				<!--描述search放置到这里 eg:<?php echo $value['Title']?> -->
				<font  style="font-size:80%">
				<?php 
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp";
					//1.得到文章的详情内容
					//$desc=html_entity_decode($value['Description'],ENT_QUOTES,"UTF-8");
					$desc=htmlspecialchars_decode($value['Description']);//有中文的时候，最好用 htmlspecialchars ，否则可能乱码
					
					//2.去掉html和php标记
					$final=strip_tags($desc);
					
					//3.打印部分内容
					//echo substr($final,0,604);
					echo mb_substr($final,0,300,'utf-8');
				?>
				</font>
			</div>
			<div class="meta">
				<p class="links"><a href="article.show.php?Id=<?php echo $value['Id']?>" class="more">查看详细</a>&nbsp;&nbsp;&raquo;&nbsp;&nbsp;</p>
			</div>
		</div>
	<?php
			}
		}
	?>
	
	<!-- 2.分页的底部显示 -->
	<?php
		echo "<div align='center'>共有".$pages."页(".$page."/".$pages.")";
		for ($i=1;$i< $page;$i++){
			echo "<a href='article.list.php?page=".$i."'>[".$i ."]</a> ";
		}
		echo "[".$page."]";
		for ($i=$page+1;$i<=$pages;$i++){
			echo "<a href='article.list.php?page=".$i."'>[".$i ."]</a> ";
		}
		echo "</div>";
	?>
	<!-- 2.end 分页的底部显示-->
	
	
	</div>
	<!-- end content -->
	<!-- start sidebar -->
	<div id="sidebar">
		<ul>
			<!-- start subscribe -->
			<li id="search">
				<h2><b class="text">Subscribe</b></h2>
				<form method="get" action="article.add.php">
					<fieldset>
					<input type="text" id="s" name="url" value="" placeholder="http://www.zhihu.com/rss" />
					<input type="submit" id="x" value="Add" />
					</fieldset>
				</form>
			</li>
			<!-- end subscribe -->
			<li id="search">
				<h2><b class="text">Search</b></h2>
				<form method="get" action="article.search.php">
					<fieldset>
					<input type="text" id="s" name="key" value="" />
					<input type="submit" id="x" value="Search" />
					</fieldset>
				</form>
			</li>
			<!-- start classify -->
			<li id="search">
				<h2><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;订阅信息分类查询</b></h2>
			</li>
			
			<?php
				if(empty($data2)){	

				}else{
					foreach($data2 as $value2){
			?>
				<div class="post">

					<h2 class="title"><a href="article.classify.php?Source=<?php echo $value2['Source']?>"><?php echo $value2['Source']?></a>
						<a href="article.del.php?Source=<?php echo $value2['Source']?>"><input type="submit" id="x"  value="del" /></a>
					</h2>
				</div>
			<?php
					}
				}
			?>
			<!-- end classify -->

		</ul>
	</div>
	<!-- end sidebar -->
	<div style="clear: both;">&nbsp;</div>
</div>
<!-- end page -->
<!-- start footer -->
<div id="footer">
	<p id="legal"></p>
</div>
<!-- end footer -->
</body>
</html>
