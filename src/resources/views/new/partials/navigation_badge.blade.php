@if (isset($badgeValue))
<span
		class="badge bg-color-greenLight  inbox-badge"
		style="@if(!$badgeValue) display: none @endif"
>
    {{is_numeric($badgeValue) ? $badgeValue : ''}}
</span>
@endif