<form id="commentform" method="post" action="<?php echo myUrl("user/search.php");?>">

<div class="content userSearch">
				
	<h3>借书信息查询</h3>
				
	<p class="label">
		<label for="username">
			<small>请输入您的工号:</small>
		</label>
	</p>
	
	<p class="input">
		<input type="text" aria-required="true" tabindex="1" size="21" name="userId" />
				
	</p>
	
	<p class="submit">
		<input style="cursor: pointer;" id="submit" class="button" type="submit" value="查&nbsp;&nbsp;询" tabindex="5" name="submit" />
	</p>
			
</div>

</form>