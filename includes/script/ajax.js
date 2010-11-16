$(document).ready(function() {
	var asIndex=0,q;
	if(localStorage['deltatheme']&&localStorage['deltatheme']!='0') {
		document.getElementsByTagName('link')[0].setAttribute('href','includes/themes/'+localStorage['deltatheme']);
	} else {
		localStorage['deltatheme']=document.getElementsByTagName('link')[0].getAttribute('href');
	}
	$('#topQ').attr('autocomplete',"off");
	$('#topQ').keydown(function(e) {
		switch(e.keyCode) {
		case 39://right
			break;
		case 37://left
			break;
		case 38://top
			asIndex--;
			if(asIndex<0) asIndex=$(".suggestion").length-1;
			$("#selectedSuggestion").attr('id','');
			$(".suggestion")[asIndex].setAttribute('id','selectedSuggestion');
			$('#topQ').attr('value',$("#selectedSuggestion").html());
			break;
		case 40://down
			if(asIndex<$(".suggestion").length-1) asIndex++;
			else asIndex=0;
			$("#selectedSuggestion").attr('id','');
			$(".suggestion")[asIndex].setAttribute('id','selectedSuggestion');
			$('#topQ').attr('value',$("#selectedSuggestion").html());
			break;
		}
	});
	$('#topQ').keyup(function(e) {
		switch(e.keyCode) {
		case 39://right
		case 37://left
		case 38://top
		case 40://down
		 return;
		}
		if(q==$('#topQ').attr('value') && $('#topAutoSuggest').html()!="") return;
		q=$('#topQ').attr('value');
		if(q.length>0) {
			$.post('ajax.php',{q:q},function(data) {
				//asIndex=0;
				$('#topAutoSuggest').html(data);
				$(".suggestion").click(function() {
					$('#topQ').attr('value',$(this).html());
					$('#topAutoSuggest').html('');
					str=$(this).html();
					//while(str.indexOf('+')&&str.indexOf('+')!=-1) str.replace('+','%2B');
					document.forms['topF'].submit()
				});
			});
}
		else $('#topAutoSuggest').html('');
	});
	$('#topQ').focus(function() {
		$('#topQ').keyup();
	});
	$('#topQ').focusout(function() {
	});
	$(".suggestion").keypress(function(e) {
		$('#topQ').attr('value','ds');
	});

	$("#pSettings").slideUp(0);
	$("#googleMore").click(function() {
		$("#pSettings").slideToggle(0);
		if(localStorage['deltatheme']=='fbtheme.css') toggleBG();
	});
	$('#pSettings a').click(function() {
		switch(this.getAttribute('id')) {
		case "fb": localStorage['deltatheme']='fbtheme.css'; break;
		case "google": localStorage['deltatheme']='google-theme.css'; break;
		case "bing": localStorage['deltatheme']='bing-theme.css'; break;
		}
		window.location="./";
	});

	function toggleBG() {
		if(document.getElementById('googleMore').style.backgroundColor=="rgb(107, 132, 183)")
		document.getElementById('googleMore').style.backgroundColor="#627AAD";
		else
		document.getElementById('googleMore').style.backgroundColor="#6B84B7";
	}
});

