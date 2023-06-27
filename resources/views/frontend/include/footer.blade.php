<footer>
	<div class="main-footer">
		<div class="container">
			<div class="space-flex">
				<div class="footer-logo">
					<a href="{{url('/')}}" class="alboumi-logo"> 
						<img src="{{asset('public/assets/frontend/img/Alboumi_Logo.png')}}">
					</a><?php 
					if(false)
					{
						?><a href="" class="parent-company">
							<img src="{{asset('public/assets/frontend/img/Company.png')}}">
							<p class="s1">Parent Company </p>
						</a><?php
					}					
					?><a href="https://www.ashrafsbahrain.com" target="_blank" class="bitmap">
						<img src="{{asset('public/assets/frontend/img/Bitmap.png')}}">
					</a>
				</div>
				<div class="footer-menu collapse-footer footerTitle0">
					<p class="s1"></p>
					<ul></ul>
				</div>
				<div class="footer-menu collapse-footer footerTitle1">
					<p class="s1"></p>
					<ul></ul>
				</div>
				<div class="footer-static-menu follow-us localLabels0">
					<p class="s1"></p>
					<ul>
						<li class="footer-social">
							<a id="fb"><img src="{{asset('public/assets/frontend/img/Facebook1.svg')}}"></a>
							<a id="insta"><img src="{{asset('public/assets/frontend/img/Instagram.svg')}}"></a>
							<a id="youtube"><img src="{{asset('public/assets/frontend/img/Youtube.svg')}}"></a>
							<a id="twitter"><img src="{{asset('public/assets/frontend/img/Twitter.svg')}}"></a>
						</li>
					</ul>
				</div>
				<div class="footer-static-menu localLabels1" style="display: none;">
					<p class="s1"></p>
					<div class="QR-sec">
						<div>
							<a href=""><img src="{{asset('public/assets/frontend/img/app-store.png')}}"></a>
							<a href=""><img src="{{asset('public/assets/frontend/img/google-play.png')}}"></a>
						</div>
						<img class="OR-code" src="{{asset('public/assets/frontend/img/QRCode.png')}}">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="copy-right">
		<div class="container">
			<div class="row">
				<div class="col-12 col-sm-6 text-center text-sm-left col-md-6">
					<p>Copyright @ {{ date('Y') }} Alboumi</p>
				</div>
				<div class="col-12 col-sm-6 text-center text-sm-right col-md-6">
					<p>Made by <a href="https://magnetoitsolutions.com/" target="_blank"> Magneto IT Solutions</a></p>
				</div>
			</div>
		</div>
	</div>
</footer>