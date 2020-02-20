<div class="col-xs-12 col-sm-12 col-md-12  jobHeader jobHeader1 jobtablet">
	<div class="col-xs-12 col-sm-6 col-md-12 paddingtabright">
		<h4>Top Keywords from this role</h4>
		@if(!empty($keywords))
			<?php $i = 0; ?>
			@foreach($keywords as $key => $value)
				<?php $i++; ?>
				<div class="col-xs-12 col-sm-12 col-md-12 mainCapapuity tablet">
					<?php echo $i; ?>. {{$key}} ({{$value}})
				</div>
			@endforeach
		@endif		
	</div>
</div>