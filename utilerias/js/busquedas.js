function checkTodos (id,pID) {
				
				   $( "#" + pID + " :checkbox").attr('checked', $('#' + id).is(':checked')); 
				   
   			}