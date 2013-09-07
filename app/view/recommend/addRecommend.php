<div id="resultContent">
<div class="post">

<div class="strip">
	<img class="avatar avatar-40 photo" width="40" height="40" src="<?php echo myUrl('images/7e9af47469151f744be2ce915404b238.png');?>" alt="">
</div>


	<div class="content">
		
	<div id="respond">
		<h3>我来推荐一本书</h3>
		<div class="title">
			<a class="titleBack" id="searchsubmit" href="<?php echo myUrl('recommend/index.php'); ?>">返回上页</a>
		</div>
		<form id="commentform" method="post" action="<?php echo myUrl('recommend/addRecommend.php');?>">
		
			<p>
				<input id="name" type="text" aria-required="true" tabindex="1" size="22" value="" name="book_name" />
				<label for="name">
					<small>图书名称(必须)</small>
				</label>
			</p>
			
			<p>
				<input id="name" type="text" aria-required="true" tabindex="1" size="22" value="" name="isbn" />
				<label for="name">
					<small>图书ISBN号(必须)</small>
				</label>
			</p>
			
			<p>
				<select id="name"  aria-required="true" tabindex="1"  name="version" >
					<option value ="1">第一版</option>
					<option value ="2">第二版</option>
					<option value ="3">第三版</option>
					<option value ="4">第四版</option>
					<option value ="5">第五版</option>
					<option value ="6">第六版</option>
					<option value ="7">第七版</option>
					<option value ="8">第八版</option>
					<option value ="9">第九版</option>
					<option value ="10">第十版</option>
				</select>
				<label for="name">
					<small>图书版本(必须)</small>
				</label>
			</p>
			
			<p>
				<input id="author" type="text" aria-required="true" tabindex="1" size="22" value="" name="author" />
				<label for="author">
					<small>图书作者(必须)</small>
				</label>
			</p>
		
			<p>
				<input id="author" type="text" aria-required="true" tabindex="1" size="22" value="" name="publisher" />
				<label for="author">
					<small>图书出版社(必须)</small>
				</label>
			</p>
			
			<p>
				<input id="author" type="text" aria-required="true" tabindex="1" size="22" value="" name="price" />
				<label for="author">
					<small>图书价格(必须)</small>
				</label>
			</p>

			<p>
				<input id="author" type="text" aria-required="true" tabindex="1" size="22" value="" name="recommender" />
				<label for="author">
					<small>推荐人(可选)</small>
				</label>
			</p>
			
			<p>
				<input id="email" type="text" aria-required="true" tabindex="2" size="22" value="" name="recommender_email" />
				<label for="email">
					<small>推荐人邮箱(必须)</small>
				</label>
			</p>

			<p>
				<textarea id="comment" tabindex="4" rows="10" cols="60" name="content"></textarea>
			</p>

			<p>
				<input id="submit" class="button" type="submit" value="推荐" tabindex="5" name="submit" />
			</p>
		</form>
	</div>
</div>
</div>
