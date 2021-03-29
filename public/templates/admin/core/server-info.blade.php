	<div class="card-header">

		@include('admin.core.layouts.menu-tools')		

	</div>
	<!-- end card-header -->	

	<div class="card-body">
		
		<?php 						
			ob_start();
			phpinfo();
			$pinfo = ob_get_contents();
			ob_end_clean();
			$pinfo = preg_replace( '%^.*<body>(.*)</body>.*$%ms','$1',$pinfo);
			echo $pinfo;
		?>
									
	</div>	
	<!-- end card-body -->								
				
