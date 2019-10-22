<div id="toolbar">
<button type="submit" id="add_comprehension"><i class="fa fa-plus"></i> Add</button>
<button type="button" class="PreviewWysiwyg"><i class="fa fa-eye"></i> Preview</button>
<button type="button" class="goback"><i class="fa fa-arrow-left"></i> Back</button>
</div><!-- end of toolbar -->
<div class="godown">
<form action="comprehension" name="newComprehension" class="wysiwyg" id="newComprehension">
<div class="specifyInstruction">
<label for="instruction"><i class="fa fa-info"></i> Specify An Instruction</label>
<textarea name="instruction" id="instruction"></textarea>
</div>
<textarea name="comprehension" id="comprehension"></textarea>
</form>
</div>
<div id="previewContent">
</div>
	<script type="text/javascript" src="script/tinymce/tinymce.min.js"></script>
	
	<script>
		//initialize tinymce
		//menubar 'file edit view',
		tinymce.init({
			selector : '#comprehension',
			 menubar: false,
			  theme: 'modern',
			  width: '100%',
   		 	  height: '300',
   		 	  statusbar: false,
   		 	   browser_spellcheck: true,
  				contextmenu: false,
   		 	 // content_css: 'css/style.css',
			  plugins : 'advlist lists charmap print preview table',
			  toolbar:  'undo redo | styleselect | bold italic | alignleft aligncenter alignright table'
 
		});

	</script>