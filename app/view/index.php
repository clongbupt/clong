<div id="content" class="content" >
	<div class="searchbox">
		<div class="title">
			<span  class="cur" >搜索</span>
		</div>
		
		<div class="text">
			<div  class="search">
				<form target="_blank"  method="post" action="result/index.php">
					<select name="searchType">
						<option selected="" value="any">任意词</option>
						<option value="anyOn">在架</option>
						<option value="bookName">书名</option>
						<option value="author">作者</option>
						<option value="category">分类</option>
						<option value="ISBN">ISBN</option>
						<option value="publisher">出版社</option>
					</select>
					
					<input class="input" type="text" name="searchContent"/>
					
					<input class="submit" type="submit" value="搜索" />
				
				</form>
			</div>
			
			<ul >			
				<li class="notice"><font>＊</font>该版本可以支持对任意词进行搜索，支持在架搜索</li>
				<li class="notice"><font>＊</font>图书馆的图书分类主要分为：人文和技术两类</li>
			</ul>
		</div>
	</div>	
</div>