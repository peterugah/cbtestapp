<script type="text/javascript" src="script/tinymce/tinymce.min.js"></script>
	<script>
		//initialize tinymce
		//menubar 'file edit view',
		var inputTraker = 0;
		tinymce.init({
      //update description
			setup : function (editor){
				editor.on('keyup, change, click, keypress, blur'  , function(){
         $content = tinyMCE.activeEditor.getContent();
          var $data = {'u_description' : true , 'description' : $content};
          $.post('include/process.php' , $data , function (result) {  
           
          });
        });
	
	var inp = $('<input type="file" name="pic" accept="image/*" style="display:none">');
            $(editor.getElement()).parent().append(inp);
    editor.addButton('imageupload', {
    text: '',
    icon: 'image',
    		onclick: function(e) { // when toolbar button is clicked, open file select modal
      		inp.trigger('click');
    		}
  });
     inp.on("change",function(){
                var input = inp.get(0);
                var $data = new FormData();
                var file = input.files[0];
                $data.append("image" , file);
                $data.append('upload_image' , true);
                var fr = new FileReader();
                fr.onload = function() {
                    var img = new Image();
                    img.src = fr.result;
                    inp.val('');
                }
                fr.readAsDataURL(file);
                //upload file
                $.ajax({
    url: 'include/process.php',
    type: 'POST',
    data: $data,

    processData: false, // Don't process the files
    contentType: false, // Set content type to false as jQuery will tell the server its a query  string request
    success: function(result) {
    	inputTraker++;
      if(result.trim().indexOf('error:') >= 0){
      	alert(result);
      	return false;
      }
      if(result.trim().indexOf('include/') >= 0 || result.trim().indexOf('error:') < 0){
      	//replace current image tag
      editor.insertContent('<img src="'+result.trim()+'">');
      }
    },
  	});//end of upload file
 });//end of change
	},
			selector : '#description',
			 menubar: false,
			  theme: 'modern',
			  width: '100%',
   		 	  height: '300',
   		 	  statusbar: false,
   		 	   browser_spellcheck: true,
  				contextmenu: false,
   		 	 file_browser_callback: function(field_name, url, type, win) {
    		win.document.getElementById(field_name).value = 'my browser value';
  			},
			  plugins : 'advlist lists charmap print preview table image',
			  toolbar:  'undo redo styleselect bold italic alignleft aligncenter alignright imageupload'
 
		});
	</script>