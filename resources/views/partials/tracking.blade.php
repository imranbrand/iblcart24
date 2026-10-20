{{-- Facebook Pixel + Google Analytics 4. Prints nothing until IDs are set in .env --}}
@php
    $fbPixel = trim((string) config('shop.fb_pixel_id', ''));
    $gaId    = trim((string) config('shop.ga_id', ''));
@endphp

@if($fbPixel !== '')
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init','{{ $fbPixel }}');fbq('track','PageView');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $fbPixel }}&ev=PageView&noscript=1" alt=""></noscript>
@endif

@if($gaId !== '')
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
<script>
window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}
gtag('js',new Date());gtag('config','{{ $gaId }}');
</script>
@endif

<script>
/* iblTrack('ViewContent' | 'AddToCart' | 'Purchase', {...}) - safe even if no pixel is set */
window.iblTrack=function(ev,d){
    try{
        if(window.fbq){fbq('track',ev,d||{});}
        if(window.gtag){
            var m={ViewContent:'view_item',AddToCart:'add_to_cart',Purchase:'purchase'};
            var p=d||{};
            gtag('event',m[ev]||ev,{currency:p.currency,value:p.value,transaction_id:p.transaction_id,
                items:(p.contents||[]).map(function(c){return {item_id:String(c.id),quantity:c.quantity,price:c.item_price};})});
        }
    }catch(e){}
};
</script>