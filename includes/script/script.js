//script for admin page
$(document).ready(function() {
	$("#checkNewBooks").click(function() {
		$("#displayResults").html("Loading... Please wait");
		$("#displayResults").load("admin.php?action=checknew",function() {
			tableRows=document.getElementById("detectTable").getElementsByTagName('tr');
				$('#temp').html("total number of files : "+tableRows.length);
			for(var i=0;i<tableRows.length;i++) {//add a new element at the begining
				newCell =tableRows[i].insertCell(0);
				newCell.innerHTML="<input type=\"checkbox\" "+((i==0)?"id=\"mainSelector\" ":"class=\"indiSelectors\" ")+"checked=\"checked\">";
			}
			$('#mainSelector').click(function() {
				$('.indiSelectors').click();
			});
			$('#addFilesButton').click(function() {
			for(var i=1;i<tableRows.length;i++) {
				filepath=tableRows[i].childNodes[1].innerHTML;
				filename=tableRows[i].childNodes[2].innerHTML;
				//while(filepath.indexOf(" ")&&filepath.indexOf(" ")!=-1)
				//filepath=filepath.replace(" ","+");
				//while(filename.indexOf(" ")&&filename.indexOf(" ")!=-1)
				//filename=filename.replace(" ","+");
				/*q="admin.php?addfile=1&filepath="+filepath+"&filename="+filename;
				$('#currProcess').load(q);*/
				q="admin.php?addfile=1&filepath="+filepath+"&filename="+filename;
				$.post('admin.php',{addfile:"1",filepath:filepath,filename:filename},function(data) {
					$('#currProcess').html(data);
				});
			}
			$("#checkNewBooks").click();
			});
		});
	});
});
