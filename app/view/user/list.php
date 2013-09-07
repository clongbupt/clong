<div class="post userList">

		<div class="strip">
			<img class="avatar avatar-60 photo" width="60" height="60" src="<?php echo myUrl('images/7e9af47469151f744be2ce915404b238_002.png');?>" alt="">
		</div>
		
		<div class="post_body">		
			<?php foreach ($this->args['borrowBooks'] as $borrowBook)?>
			<div class="content">
				<h1 class="title"><a href="#"><?php echo $borrowBook['book_name']; ?></a></h1>
				
				<div class="titbar">
					<span class="author">借出日期：<strong><?php echo $borrowBook['borrow_date']; ?></strong></span>
					<span class="dates">归还日期：<strong><?php echo $borrowBook['return_date']; ?></strong></span>
				</div>
				
			
			</div>
		
		</div>
		
		<div class="clear"> </div>
</div>