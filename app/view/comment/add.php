<div id="resultContent">
<div class="post">

<div class="strip">
	<img class="avatar avatar-40 photo" width="107" height="138" src="<?php echo $this->args['book']['image_url'];?>" alt="">
</div>


	<div class="content">
		
		<div class="title">
			<h1 >
				<a href="#"><?php echo $this->args['book']['book_name']; ?></a>
			</h1>
			<a class="titleBack" id="searchsubmit" href="<?php echo myUrl('result/index/searchType/'.$this->args['searchType'].'/searchContent/'.$this->args['searchContent'].'.php'); ?>">返回上页</a>
		</div>
	<div class="text">
		<ul class="list">
			<li>
				<span>作者：</span>
				<a href="<?php echo myUrl('result/index/searchType/author/searchContent/'.$this->args['book']['author'].'.php');?>"><?php echo $this->args['book']['author']; ?></a>
			</li>
			<li><span>分类：</span><a href="<?php echo myUrl('result/index/searchType/category/searchContent/'.$this->args['book']['category'].'.php');?>"><?php echo $this->args['book']['category']; ?></a></li>
			<li><span>出版社：</span><a href="<?php echo myUrl('result/index/searchType/publisher/searchContent/'.$this->args['book']['publisher'].'.php');?>"><?php echo $this->args['book']['publisher']; ?></a></li>
			<li><span>ISBN：</span><?php if (empty($this->args['book']['isbn'])) echo "暂无"; else echo $this->args['book']['isbn']; ?></li>
			<li><span>出版时间：</span><?php echo $this->args['book']['publish_date']; ?></li>
			<li>
				<span>状态：</span>
				<a class="button" href="<?php echo myUrl('result/index/searchType/status/searchContent/'.$this->args['book']['status'].'.php');?>"><?php if ($this->args['book']['status'] == "0") echo "借出"; else echo "在架";?></a>
			</li>
		</ul>

		<a class="bookdetails"><?php echo $this->args['book']['description']; ?></a>
		<div class="clear"> </div>
	</div>
	
	<h3 id="comments"><?php echo count($this->args['comments']);?>条评论</h3>
	
	<ol class="commentlist">
		<?php if (is_array($this->args['comments'])) : ?>
		
		<?php 
	
		//进行分页处理 一页显示10条记录
		$itemsPerPage = 5;
		if(!empty($this->args['page']))  $page = intval($this->args['page']);
		else $page = 1;
		if(!isset($adjacents)) $adjacents = 4;
		
		$commentCount = count($this->args['comments']);
		$tpages = ($commentCount) ? ceil($commentCount/$itemsPerPage) : 1;
			
		//$reload = $_SERVER['PHP_SELF'] . "?action=add&book_id=1&tpages=" . $tpages . "&adjacents=" . $adjacents;
		$reload = myUrl('comment/add/tpages/'.$tpages.'/adjacents/'.$adjacents.'/searchType/'.$this->args['searchType'].'/searchContent/'.$this->args['searchContent'].'/bookId/'.$this->args['book']['book_id']);

		?>
		
		<?php 
			$count = 0;
			$i = ($page-1)*$itemsPerPage;
			while(($count<$itemsPerPage) && ($i<$commentCount)) {		
		?>
		
		
		<li id="comment-2" class="comment byuser comment-author-iDesignEco bypostauthor even thread-even depth-1">
			<div id="div-comment-2" class="comment-body">
				<div class="comment-author vcard">
					<img class="avatar avatar-32 photo" width="32" height="32" src="<?php echo myUrl('images/7e9af47469151f744be2ce915404b238.png'); ?>" alt="">
					<cite class="fn"><?php if($this->args['comments'][$i]['comment_author'] != "no") echo $this->args['comments'][$i]['comment_author']; else echo "害羞的评论人";?></cite>
					<span class="says">评论:</span>
				</div>
				
				<div class="comment-meta commentmetadata">
					<a href="#"><?php echo $this->args['comments'][$i]['comment_date'];?></a>
				</div>
				
				<p><?php if ($this->args['comments'][$i]['comment_content'] != "no") echo $this->args['comments'][$i]['comment_content']; else echo "挥一挥衣袖，不带走一片云彩...";?></p>
				
			</div>
		</li>
		
		<?php 
		$i++;
		$count++;
		}
		?>
	
		<?php
			 require_once(APP_PATH.'config/pagination2.php');
			 if($tpages > 1) echo paginate_two($reload, $page, $tpages, $adjacents);
		?>
		
		<?php endif;?>
	</ol>


	<div id="respond">
		<h3>我来评论这本书</h3>

		<form id="commentform" method="post" action="<?php echo myUrl('comment/addComment.php');?>">

			<p>
				<label><input type="radio" name="level" value="1"/>很差</label> 
				<label><input type="radio" name="level" value="2"/>较差</label> 
				<label><input type="radio" name="level" value="3"/>还行</label> 
				<label><input checked="checked" type="radio" name="level" value="4"/>推荐</label> 
				<label><input type="radio" name="level" value="5"/>力荐</label> 
				<label for="name">
					<small>评价(必须)</small>
				</label>
			</p>

			<p>
				<input id="name" type="text" aria-required="true" tabindex="1" size="22" value="" name="author" />
				<label for="name">
					<small>标题(可选)</small>
				</label>
			</p>
		
			<p>
				<input id="author" type="text" aria-required="true" tabindex="1" size="22" value="" name="author" />
				<label for="author">
					<small>姓名(可选)</small>
				</label>
			</p>

			<p>
				<input id="email" type="text" aria-required="true" tabindex="2" size="22" value="" name="email" />
				<label for="email">
					<small>邮箱(可选)</small>
				</label>
			</p>

			<p>
				<textarea id="comment" tabindex="4" rows="10" cols="60" name="content"></textarea>
			</p>

			<p>
				<input id="submit" class="button" type="submit" value="评论" tabindex="5" name="submit" />
				<input type="hidden" name="bookId" value="<?php echo $this->args['book']['book_id'];?>"/>
				<input type="hidden" name="searchContent" value="<?php echo $this->args['searchContent'];?>"/>
				<input type="hidden" name="searchType" value="<?php echo $this->args['searchType'];?>"/>
			</p>
		</form>
	</div>
</div>
</div>
