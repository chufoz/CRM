
//Funcion que permite seleccionar todos los checkbox
function seleccionar_todo(formname){
                        form = document.forms[formname]
                        for (i=0;i<form.elements.length;i++){
                            if(form.elements[i].type == 'checkbox')
                            form.elements[i].checked=1
                          }
                      } 
                      
//Funcion que permite deseleccionar todos los checkbox                      
 function deseleccionar_todo(formname){                        
                        form = document.forms[formname]
                        for (i=0;i<form.elements.length;i++){
                            if(form.elements[i].type == 'checkbox')
                            form.elements[i].checked=0;
                        }
                      }
function changeselect(sel){
                var con=sel;
               var dd=document.modulos.softmodule.options [document.modulos.softmodule.selectedIndex].value
               //resultado(dd)
               $.ajax({
                    data:'idsoft='+dd+'&cdb='+con,
                    type: 'POST',
                    dataType: 'json',
                    url: 'reportes/modulos.php',
                    success: function(data){
                       $("#p").html(data)                    
                    }
                });                              
}

function showstatus(val){    
    if(val!='0'){
        document.getElementById('pcheck').style.visibility = 'visible';      
    }
}
function alerta(proc){
    apprise('Deseas  Terminar El Proceso:'+proc+'?', {'verify':true}, function(r) {
                if(r) { 
	document.location='reportes.php?info=1&tproceso=1&proce='+proc;			
                }
    });
}
function calendario(id){
	$( "#"+id+"" ).datepicker({
			showOn: "button",
			buttonImage: "reportes/calendar.png",
			buttonImageOnly: true
		});
                
}

function validabatch(){
     apprise('SELECCIONA DATOS A PROCESAR:', {'animate':true}, function(r) {
                if(r) { 
	document.location='reportes.php?grupos=1';			
                }
    });
}
function valida_envia(){
    //valido el nombre
    if (document.batch.categoria.value.length==0){
       apprise("ESCRIBE LA CATEGORIA",{'animate':true})
       document.batch.categoria.focus()
       return 0;
    }else{
        document.batch.submit(); 
    } 
}

function OnSubmitForm(val,formu,re){
      form = document.forms[formu]
  if(val == 'batch')
  {
   form.action ='reportes.php?fbatch=1'
  }else{
      if(val=='pdf'){
          form.action='reportes.php?pdf=1&r='+re+'&tiprep='+form.name
      }else{
          if(val=='mail'){
          form.action='reportes.php?fmail=1&htm='+re	    
          }
      }
  }
  
  return true;
}
var control = 0;
  function muestraentextarea(correo) {
    var txtarea = document.getElementById('txt');
    cantidad_emails = txtarea.value.split(',');
     
        if (cantidad_emails.length <= 100) {
            if (control == 0) {
                txtarea.value += correo;
            } else if (control <= 50) {
                txtarea.value += ',' + correo;
            }
        }
     
    control += 1;
    //alert(cantidad_emails.length);
    }