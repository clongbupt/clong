<div id="resultContent">
	<?php if (!empty($this->args['books'])&&is_array($this->args['books'])) :?>
	
		<?php 
	
		//进行分页处理 一页显示10条记录
		$itemsPerPage = 5;

		if(!empty($this->args['page']))  $page = intval($this->args['page']);
		else $page = 1;
		
		if(!isset($adjacents)) $adjacents = 4;
		
		$bookCount = count($this->args['books']);
		$tpages = ($bookCount) ? ceil($bookCount/$itemsPerPage) : 1;
			
		//$reload = $_SERVER['PHP_SELF'] . "?action=result&tpages=" . $tpages . "&adjacents=" . $adjacents;
		$reload = myUrl('result/index/tpages/'.$tpages.'/adjacents/'.$adjacents.'/searchType/'.$this->args['searchType'].'/searchContent/'.$this->args['searchContent']);
		?>
	
	<?php
		require_once(APP_PATH.'config/pagination2.php');
		if($tpages > 1) echo paginate_two($reload, $page, $tpages, $adjacents);
	?>
	
	<?php 
		$count = 0;
		$i = ($page-1)*$itemsPerPage;
		while(($count<$itemsPerPage) && ($i<$bookCount)) {		
	?>
	
	<div class="post">
	
		<div class="strip">
			<img class="avatar avatar-60 photo" width="71" height="92" src="<?php echo $this->args['books'][$i]['image_url'];?>" alt="">
		</div>
		
		<div class="post_body">		
		
			<div class="content">
				<h1 class="title"><a href="<?php echo myUrl('comment/add/bookId/'.$this->args['books'][$i]['book_id'].'/searchType/'.$this->args['searchType'].'/searchContent/'.$this->args['searchContent'].'.php'); ?>"><?php echo $this->args['books'][$i]['book_name']; ?></a></h1>
				
				<div class="titbar">
					<span class="author">作者：<strong><?php echo $this->args['books'][$i]['author']; ?></strong></span>
					<span class="publisher">出版社：<strong><?php echo $this->args['books'][$i]['publisher']; ?></strong></span>
					<span class="dates">所属分类：<strong><?php echo $this->args['books'][$i]['category']; ?></strong></span>
					<span class="status">状态：<span id="searchsubmit"><?php if ($this->args['books'][$i]['status']== "0") echo "借出"; else echo "在架"; ?></span></span>
				</div>
				
				<div class="detail"><a id="searchsubmit" href="<?php echo myUrl('comment/add/bookId/'.$this->args['books'][$i]['book_id'].'/searchType/'.$this->args['searchType'].'/searchContent/'.$this->args['searchContent'].'.php'); ?>">发表评论</a></div>
			
			</div>
			<?php if($this->args['comments'][$i] != "none") :?>
				<div class="comment_snippets">
					<ul>
					<?php foreach ($this->args['comments'][$i] as $key => $comment) : ?>
					<?php if ($key <=4) : ?>
					<li>
						<span class="author">
							<img class="avatar avatar-28 photo" width="28" height="28" src="<?php echo myUrl('images/7e9af47469151f744be2ce915404b238.png'); ?>" alt="">
							<?php if($comment['comment_author'] != "no") echo $comment['comment_author']; else echo "害羞的评论人";?>
						</span>
						<?php if ($comment['comment_content'] != "no") echo $comment['comment_content']; else echo "挥一挥衣袖，不带走一片云彩...";?>
						<!-- <a href="http://demo.idesigneco.com/ecomicro/?p=6#comment-2">>>more</a> -->
					</li>
					<?php endif;?>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>
		
		</div>
		
		<div class="clear"> </div>
	</div>
		
	<?php 
		$i++;
		$count++;
		}
	?>
	
	<?php
		require_once(APP_PATH.'config/pagination2.php');
		if($tpages > 1) echo paginate_two($reload, $page, $tpages, $adjacents);
	?>
	
	<?php else : ?>
	<div class="post">
		<?php echo "对不起！没有您要找的图书。"; ?>
		<a id="searchsubmit" href="index.php">返回</a>
	</div>
	<?php endif; ?>
</div>
	