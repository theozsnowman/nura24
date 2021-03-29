<footer class="footer">
	<span class="text-right">
	{{ __('Version') }} {{ config('nura.version') ?? NULL }}
	</span>
	
	<span class="float-right">
		&copy; {{ __('Powered by') }} 
		@if(! ($config->site_meta_author ?? null) || ! ($config->license_key ?? null) || ! $sys_valid_license_key)		
		<a target="_blank" href="https://nura24.com"><b>Nura24</b></a>
		@else 
		@if($config->site_meta_author_url ?? null)
		<a target="_blank" href="{{ $config->site_meta_author_url }}"><b>{{ $config->site_meta_author }}</b></a>
		@else
		<b>{{ $config->site_meta_author }}</b>
		@endif
		@endif
	</span>

</footer>

