jQuery(document).ready(function($){
	$('.ts-fab-wrapper').each(function(){
		$(this).find('.ts-fab-tabs > div').removeClass( 'visible-tab' );
		$(this).find('.ts-fab-tabs > div:first-child').addClass( 'visible-tab' );
		$(this).find('.ts-fab-list li:first-child').addClass('active');

		var FirstTab = $(this).find('.ts-fab-list li:first-child a');
		if ( FirstTab.length > 0 ) {
			$(FirstTab).closest('.ts-fab-wrapper').find('li').removeClass('active');
			$(FirstTab).parent().addClass('active');
			var CurrentTab = $(FirstTab).attr('href');
			if(CurrentTab.indexOf('#') != -1) {
				CurrentTabExp = CurrentTab.split('#');
				CurrentTab = '#' + CurrentTabExp[1];
			}

			$(FirstTab).closest('.ts-fab-wrapper').find('.ts-fab-tabs > div').removeClass( 'visible-tab' );
		} else {
			var CurrentTab = $(this).find( '.ts-fab-tab:first-child' );
		}

		$(CurrentTab).addClass( 'visible-tab' );
		$(FirstTab).ts_fab_load_tab();
	});

	$('.ts-fab-list li a').click(function() {
		$(this).closest('.ts-fab-wrapper').find('li').removeClass('active');
		$(this).parent().addClass('active');
		var CurrentTab = $(this).attr('href');
		if(CurrentTab.indexOf('#') != -1) {
			CurrentTabExp = CurrentTab.split('#');
			CurrentTab = '#' + CurrentTabExp[1];
		}

		$(this).closest('.ts-fab-wrapper').find('.ts-fab-tabs > div').removeClass( 'visible-tab' );
		$(CurrentTab).addClass( 'visible-tab' );

		$(this).ts_fab_load_tab();

		return false;
	});

});


(function ( $ ) {
	$.fn.ts_fab_load_tab = function() {

		if ( 'youtube' == $(this).data('tab') && ! $(this).parent().hasClass( 'loaded' ) ) {
			var YouTubeUsername = $(this).data( 'youtube-username' );
			var YouTubeWidgetWrapper = $(this).parent().parent().parent().find( '.ts-fab-youtube-widget-wrapper' );
			var YouTubeWidgetCode = '<script src="https://apis.google.com/js/platform.js"></script><div class="g-ytsubscribe" data-channel="' + YouTubeUsername + '" data-layout="default" data-count="default"></div>';
			$( YouTubeWidgetWrapper ).html( YouTubeWidgetCode );

			$(this).parent().addClass( 'loaded' );
		}

		if ( 'twitter' == $(this).data('tab') && ! $(this).parent().hasClass( 'loaded' ) ) {
			var TwitterUsername = $(this).data( 'twitter-username' );
			var TwitterWidgetLocale = $(this).data( 'twitter-locale' );
			var TwitterWidgetCount = $(this).data( 'show-count' );
			var TwitterWidgetWrapper = $(this).parent().parent().parent().find( '.ts-fab-twitter-widget-wrapper' );

			var TwitterWidgetCode = '<iframe allowtransparency="true" frameborder="0" scrolling="no" src="//platform.twitter.com/widgets/follow_button.html?screen_name=' + TwitterUsername + '&lang=' + TwitterWidgetLocale + '" style="width:300px; height:20px;"></iframe>';

			$( TwitterWidgetWrapper ).html( TwitterWidgetCode );

			$(this).parent().addClass( 'loaded' );		
		}

		if ( 'facebook' == $(this).data('tab') && ! $(this).parent().hasClass( 'loaded' ) ) {
			var FacebookUsername = $(this).data( 'facebook-username' );
			var FacebookLocale = $(this).data( 'facebook-locale' );
			var FacebookLoadSDK = $(this).data( 'load-sdk' );
			var FacebookLayout = $(this).data( 'facebook-layout' );
			var FacebookWidgetType = $(this).data( 'widget-type' );
			var FacebookWidgetWrapper = $(this).parent().parent().parent().find( '.ts-fab-facebook-widget-wrapper' );			
			// Do not load SDK twice
			if ( 'yes' == FacebookLoadSDK && ! $('html').hasClass('facebook-sdk-loaded') ) {
				FacebookWidgetCodeSDK = '<div id="fb-root"></div><script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/' + FacebookLocale + '/all.js#xfbml=1";fjs.parentNode.insertBefore(js, fjs);}(document, \'script\', \'facebook-jssdk\'));</script>'
				$('body').prepend( FacebookWidgetCodeSDK );
			}

			FacebookWidgetCode = '<div class="fb-' + FacebookWidgetType + '" data-href="http://www.facebook.com/' + FacebookUsername + '" data-send="false" data-show-faces="false" data-layout="' + FacebookLayout + '"></div>';

			$( FacebookWidgetWrapper ).html( FacebookWidgetCode );

			$(this).parent().addClass( 'loaded' );
			$('html').addClass('facebook-sdk-loaded');	

			try {
				FB.XFBML.parse( FacebookWidgetWrapper[0] ); 
			} catch(ex) {}
		}

		if ( 'linkedin' == $(this).data('tab') && ! $(this).parent().hasClass( 'loaded' ) ) {
			var LinkedInUsername = $(this).data( 'linkedin-url' );
			var LinkedInWidgetWrapper = $(this).parent().parent().parent().find( '.ts-fab-linkedin-widget-wrapper' );

			var LinkedInHeadCode = '<script src="//platform.linkedin.com/in.js" type="text/javascript"></script>';
			var LinkedInWidgetCode = '<script type="IN/MemberProfile" data-id="' + LinkedInUsername + '" data-format="inline" data-related="false"></script>';

			if ( ! $('html').hasClass('linkedin-script-loaded') ) {
				$('head').append( LinkedInHeadCode );
			}
			$( LinkedInWidgetWrapper ).html( LinkedInWidgetCode );

			$('html').addClass('linkedin-script-loaded');	
			$(this).parent().addClass( 'loaded' );		

			try {
				IN.parse( LinkedInWidgetWrapper[0] ); 
			} catch(ex) {}
		}

		if ( 'googleplus' == $(this).data('tab') && ! $(this).parent().hasClass( 'loaded' ) ) {
			var GooglePlusUsername = $(this).data( 'googleplus-username' );
			var GooglePlusLocale = $(this).data( 'width' );
			var GooglePlusLayout = $(this).data( 'locale' );
			var GooglePlusWidgetWrapper = $(this).parent().parent().parent().find( '.ts-fab-googleplus-widget-wrapper' );

			var GooglePlusWidgetCode = '<g:follow href="https://plus.google.com/' + GooglePlusUsername + '" rel="author"></g:follow><script type="text/javascript">window.___gcfg = {lang: ' + GooglePlusLocale + '};(function() {var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;po.src = \'https://apis.google.com/js/plusone.js\';var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);})();</script>';

			$( GooglePlusWidgetWrapper ).html( GooglePlusWidgetCode );

			$(this).parent().addClass( 'loaded' );		
		}

		if ( 'pinterest' == $(this).data('tab') && ! $(this).parent().hasClass( 'loaded' ) ) {
			var PinterestUsername = $(this).data( 'pinterest-username' );
			var PinterestWidgetWrapper = $(this).parent().parent().parent().find( '.ts-fab-pinterest-widget-wrapper' );

			var PinterestWidgetCode = '<a data-pin-do="embedUser" href="http://www.pinterest.com/' + PinterestUsername + '/" data-pin-scale-width="115" data-pin-scale-height="120" data-pin-board-width="600" target="_blank">Visit ' + PinterestUsername + '\'s profile on Pinterest.</a>';
			$( PinterestWidgetWrapper ).html( PinterestWidgetCode );

			pinJs = $('script[src*="assets.pinterest.com/js/pinit.js"]');
			if ( ! $( pinJs ).length ) {
				var PinterestHeadCode = '<script async src="//assets.pinterest.com/js/pinit.js" data-pin-build="parsePinBtns"></script>';
				$('head').append( PinterestHeadCode );

				try {
					window.parsePinBtns( PinterestWidgetWrapper[0] ); 
				} catch(ex) {}
			}

			$(this).parent().addClass( 'loaded' );		
		}

	};
}( jQuery ));