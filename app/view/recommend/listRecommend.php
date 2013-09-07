<div id="resultContent">
	
	<?php if (!empty($this->args['recommendBooks'])&&is_array($this->args['recommendBooks'])) :?>
		<?php 
	
		//进行分页处理 一页显示10条记录
		$itemsPerPage = 5;
		if(!empty($this->args['page']))  $page = intval($this->args['page']);
		else $page = 1;
		if(!isset($adjacents)) $adjacents = 4;
		
		$bookCount = count($this->args['recommendBooks']);
		$tpages = ($bookCount) ? ceil($bookCount/$itemsPerPage) : 1;
			
		$reload = myUrl('result/index/tpages/'.$tpages.'/adjacents/'.$adjacents);

		?>
	
	<?php
		 require_once(APP_PATH.'config/pagination2.php');
		 if ($tpages >1) echo paginate_two($reload, $page, $tpages, $adjacents);
	?>
	
	<?php 
		$count = 0;
		$i = ($page-1)*$itemsPerPage;
		while(($count<$itemsPerPage) && ($i<$bookCount)) {		
	?>
	
	<div class="post">
	
		<div class="strip">
			<img class="avatar avatar-60 photo" width="60" height="60" src="<?php echo myUrl('images/7e9af47469151f744be2ce915404b238_002.png');?>" alt="">
		</div>
		
		<div class="post_body">		
		
			<div class="content">
				<h1 class="title"><a href="#"><?php echo $this->args['recommendBooks'][$i]['book_name']; ?></a></h1>
				
				<div class="titbar">
					<span class="author">作者：<strong><?php echo $this->args['recommendBooks'][$i]['author']; ?></strong></span>
					<span class="dates">isbn号：<strong><?php echo $this->args['recommendBooks'][$i]['isbn']; ?></strong></span>
					<span class="publisher">出版社：<strong><?php echo $this->args['recommendBooks'][$i]['publisher']; ?></strong></span>
					<span class="status" >推荐指数：<span class="button" id="status-<?php echo $this->args['recommendBooks'][$i]['recommend_id'];?>"><?php echo $this->args['recommendBooks'][$i]['recommend_num']; ?></span></span>
				</div>
				
				<div class="detail" id="detail-<?php echo $this->args['recommendBooks'][$i]['recommend_id'];?>">
					<?php if ($this->args["recommendIdArray"][$i] != "1") :?>
					<a id="button-<?php echo $this->args['recommendBooks'][$i]['recommend_id'];?>" class="button ajax" href="#" >推荐这本书</a>
					<?php else :?>
					<span class='new_button'>已推荐</span>
					<?php endif;?>
						<input type="hidden" name="recommendId" value="<?php echo $this->args['recommendBooks'][$i]['recommend_id'];?>"/>
						<input type="hidden" name="recommendNum" value="<?php echo $this->args['recommendBooks'][$i]['recommend_num'];?>"/>
						<span id="tishi"></span>
					<a class="button" href="<?php echo myUrl('recommend/add.php');?>">推荐一本新书</a>
				</div>
			
			</div>
		
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
		if ($tpages >1) echo paginate_two($reload, $page, $tpages, $adjacents);
	?>
	
	<?php else : ?>
	<div class="post">
		<?php echo "哇塞！您动作真快，恭喜您成为推荐图书第一人！"; ?>
		<a id="searchsubmit" href="<?php echo myUrl('recommend/add.php');?>">推荐图书</a>
	</div>
	<?php endif; ?>
	
	<a style="display:none;" id= "ajaxUrl" href="<?php echo myUrl('recommend/ajaxAddNum.php');?>" />
</div>
	