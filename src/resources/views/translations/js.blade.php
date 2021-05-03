var obj = {!! json_encode($translates)  !!};
window.__t = function(phrase){
if (typeof obj[phrase] != "undefined") {
return obj[phrase]; } else {

fetch('{{$lang == 'ua' ? '' : '/'. $lang}}/auto_translate', {
	method: 'POST',
	headers: {
		'Content-Type': 'application/json;charset=utf-8',
		'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').getAttribute('content')
	},
	body: JSON.stringify({
		phrase : phrase,
	})
})
.then(response => response.text())
.then(result => phrase = result)

return phrase;
}}
