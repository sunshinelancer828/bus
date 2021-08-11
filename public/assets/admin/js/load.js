(function($) {
		"use strict";
		
	$(document).ready(function() {
      //cart item remove code
    $('.cart-remove').on('click', () => {
        $(this).parent().parent().remove();
    });
      //cart item remove code ends

    /*  Bootstrap colorpicker js  */
    $('.cp').colorpicker();
    // Colorpicker Ends Here

    // IMAGE UPLOADING :)
    $(".img-upload").on( "change", () => {
      var imgpath = $(this).parent();
      var file = $(this);
      readURL(this,imgpath);
    });

    function readURL(input, imgpath) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();
          reader.onload = function (e) {
            imgpath.css('background', 'url('+e.target.result+')');
          }
          reader.readAsDataURL(input.files[0]);
      }
    }
    // IMAGE UPLOADING PRODUCT :)
    $(document).on('click', 'label[for="image-upload"]', async () => {        
      const pickerOpts = {
        types: [{
            description: 'Images',
            accept: { 'image/*': ['.png', '.gif', '.jpeg', '.jpg'] }
          },],
        excludeAcceptAllOption: true,
        multiple: false
      };

      // store a reference to our file handle
      let fileHandle;
      
      // open file picker
      [fileHandle] = await window.showOpenFilePicker(pickerOpts);
      if (!fileHandle) {
        // User cancelled, or otherwise failed to open a file.
        return;
      }
    
      // get file contents
      const fileData = await fileHandle.getFile();
      let fileReader = new FileReader();
      fileReader.readAsText(fileData);
      fileReader.onloadend = readImageFile;  
      // fileReader.readAsDataURL(fileData);
    });

    function readImageFile(e) {

      var imgpath = $('#image-preview');

      //Initiate the JavaScript Image object.
      var image = new Image();
      image.src = e.target.result;
      image.onerror = (msg, url, lineNo, columnNo, error) => {
        console.log('img loading error: ', msg);
      }

      //Validate the File Height and Width.
      image.onload = () => {
        var height = this.height;
        var width = this.width;

        if (height < 600 && width < 600) {
          if (height != width) {
            $('.img-alert').html('Image must have square size.');
            $('.img-alert').removeClass('d-none');
            $('#image-upload').val(''); 
            $('#image-upload').prop('required', true);
            imgpath.css('background', 'url()');

          } else {
            $('.img-alert').html("Image height and width must be 600 x 600...........");
            $('.img-alert').removeClass('d-none');
            $('#image-upload').val(''); 
            $('#image-upload').prop('required', true);
            imgpath.css('background', 'url()');
          }

        } else {
          if (height != width) {
            $('.img-alert').html('Image must have square size.');
            $('.img-alert').removeClass('d-none');
            $('#image-upload').val(''); 
            $('#image-upload').prop('required', true);
            imgpath.css('background', 'url()');

          } else {
            $('.img-alert').addClass('d-none');
            imgpath.css('background', 'url(' + e.target.result + ')');

            if ($("#is_photo").length > 0) {
              $("#is_photo").val('1')
            }
          }
        }
      };
    }

    $(".img-upload-p").on( "change", function() {
      var imgpath = $(this).parent();
      readURLp(this, imgpath);
    });

    function readURLp(input, imgpath) {

      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = readImageFile;
      }
        
      reader.readAsDataURL(input.files[0]);        
    }

        // IMAGE UPLOADING ENDS :)

        // GENERAL IMAGE UPLOADING :)
        $(".img-upload1").on( "change", function() {
          var imgpath = $(this).parent().prev().find('img');
          var file = $(this);
          readURL1(this,imgpath);
        });

        function readURL1(input,imgpath) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
              imgpath.attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
        // GENERAL IMAGE UPLOADING ENDS :)


    // Text Editor

          // NIC EDITOR :)
                var elementArray = document.getElementsByClassName("nic-edit");
                for (var i = 0; i < elementArray.length; ++i) {
                  nicEditors.editors.push(
                    new nicEditor().panelInstance(
                      elementArray[i]
                    )
                  );
      $('.nicEdit-panelContain').parent().width('100%');
      $('.nicEdit-panelContain').parent().next().width('98%');
                }
  //]]>
        // NIC EDITOR ENDS :)

          // NIC EDITOR FULL :)
                var elementArray = document.getElementsByClassName("nic-edit-p");
                for (var i = 0; i < elementArray.length; ++i) {
                  nicEditors.editors.push(
                    new nicEditor({fullPanel : true}).panelInstance(
                      elementArray[i]
                    )
                  );
      $('.nicEdit-panelContain').parent().width('100%');
      $('.nicEdit-panelContain').parent().next().width('98%');
                }
  //]]>
        // NIC EDITOR FULL ENDS :)


        // Check Click :)
        $(".checkclick").on( "change", function() {
            if(this.checked){
             $(this).parent().parent().parent().next().removeClass('showbox');
            }
            else{
             $(this).parent().parent().parent().next().addClass('showbox');
            }
            
        });
        // Check Click Ends :)


        // Check Click1 :)
        $(".checkclick1").on( "change", function() {
            if(this.checked){
             $(this).parent().parent().parent().parent().next().removeClass('showbox');
            }
            else{
             $(this).parent().parent().parent().parent().next().addClass('showbox');
            }
            
        });
        // Check Click1 Ends :)

      //  Alert Close
      $("button.alert-close").on('click',function(){
        $(this).parent().hide();
      });

	});

// Drop Down Section Ends

})(jQuery);
