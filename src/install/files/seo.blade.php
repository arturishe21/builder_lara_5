@if (isset($page) && $page)
	<title>{{$page->getSeoTitle()}}</title>
	<meta name="description" content="{{$page->getSeoDescription()}}"/>
	<meta property="og:title" content="{{$page->getSeoTitle()}}"/>
	<meta property="og:type" content="website"/>
	<meta property="og:url" content="{{url()->current()}}"/>
	<meta property="og:description" content="{{$page->getSeoDescription()}}"/>
	<meta property="og:image" content="{{ $page->picture ? $page->getImgPath(200, 200) : asset('/svg/logo.svg') }}"/>
	@if ($page->seo && $page->seo->is_seo_noindex)
		<meta name="robots" content="noindex"/>
	@endif
	@if ($page->seo && $page->seo->t("seo_canonical"))
		<link rel="canonical" href="{{$page->seo->t("seo_canonical")}}"/>
	@endif

	@if(isset($alternatePageUrl, $defaultHrefUrl) && count($alternatePageUrl) > 1)
		@foreach($alternatePageUrl as $lang => $dataLink)
			<link rel="alternate" href="{{ $dataLink['href'] }}" hreflang="{{ $dataLink['hreflang'] }}"/>
		@endforeach
		<link rel="alternate" hreflang="x-default" href="{{ $defaultHrefUrl }}" />
	@endif
@endif
