<?php require('index_phtml.php');?>
<form enctype="multipart/form-data" target="upload_target" method="post" id="media_form" action="<?php echo FrontController::urlFor('photos');?>">
	<fieldset>
		<legend>Media</legend>
		<input type="hidden" name="MAX_FILE_SIZE" value="{$max_filesize}" />
		<section>
			<label for="photo" id="photo_label">Add a photo</label>
			<input type="file" name="photo" id="photo" />
		</section>
		<iframe src="<?php echo FrontController::urlFor('empty');?>" id="upload_target" name="upload_target" style="width:10;height:10;border:none;"></iframe>
	</fieldset>
</form>
<dl id="photos"></dl>
<script src="<?php echo FrontController::urlFor('js');?>prototype.s2.min.js" type="text/javascript"></script>

<script type="text/javascript">
    S2.enableMultitouchSupport = true;
  
    (function(){
      // helper for color wheel
      function hsvToRgb(hue, saturation, value){
        var red=0, green=0, blue=0;
        if (value!=0) {
          var i = Math.floor(hue * 6), f = (hue * 6) - i,
            p = value * (1 - saturation),
            q = value * (1 - (saturation * f)),
            t = value * (1 - (saturation * (1 - f)));
          switch(i){
            case 1: red = q; green = value; blue = p; break;
            case 2: red = p; green = value; blue = t; break;
            case 3: red = p; green = q; blue = value; break;
            case 4: red = t; green = p; blue = value; break;
            case 5: red = value; green = p; blue = q; break;
            case 6:
            case 0: red = value; green = t; blue = p; break;
          } 
        }   
        return {r: red, g: green, b: blue};
      }
      
      // initial position & rotation
      var p = [
        [50, 50, 200, -.5],
        [50, 100, 200, .5],
        [50, 110, 200, .3],
      ], z = 1, FRICTION = 2.5;

      $$('.manipulate').each(function(img,i){
        img.style.cssText += ';position:absolute;'+
          'left:'+(p[i][0])+'px;top:'+(p[i][1]-900)+'px;'+
          'width:'+p[i][2]+'px;';
        img.transform({ rotation: p[i][3] });
        img.morph('left:'+p[i][0]+'px;top:'+p[i][1]+'px',{duration:2,delay:i/2});
         
         // image rotating and scaling
         img.observe('manipulate:update', function(event){
           if(img.full) return;
           
           // limit scaling to 0.35 to 10
           var scale = event.memo.scale < 0.35 ? 0.35 : 
             event.memo.scale > 10 ? 10 : event.memo.scale;
             
           img.style.cssText += 
             ';z-index:'+(z++)+';left:'+(p[i][0]+event.memo.panX)+
             'px;top:'+(p[i][1]+event.memo.panY)+'px;'+
             ';';
            
           img.transform({ rotation: p[i][3]+event.memo.rotation, scale: scale });
           img._x = p[i][0]+event.memo.panX;
           img._y = p[i][1]+event.memo.panY;
           
           event.stop();
         });
                  
         //  tap long to active zoom
         img.observe('contextmenu', function(event){
           if(!img.full){
             img._css = img.style.cssText;
             img.transform({ rotation: 0, scale: 3 });
             
             var dims = document.viewport.getDimensions();
             var dx = img._x+(img.width+40)/2, dy = img._y+(img.height+40)/2;
             
             var cx = -10000-(dx-10000) + dims.width/2, cy = -10000-(dy-10000) + dims.height/2;
             img.full = true;
           } else {
             img.style.cssText = img._css;
             img.full = false;
           }
           event.stop(event);
         });
      });
    })();

	SDDom.addEventListener(window, 'load', function(e){
		SDDom.addEventListener(SDDom('photo'), 'change', photoDidChange);
	});
	var photo = null;
	function didMouseUp(e){
		photo = null;
	}
	function didMouseDown(e){
		photo = e.target;
	}
	function didMouseMove(e){
		if(photo !== null){
			SDDom.setStyles({left: SDDom.pageX(e) + 'px', top:SDDom.pageY(e) + 'px'}, elem);
		}
	}
	function photoWasUploaded(photo_name, file_name, photo_path, width){
		photoDidUpload(photo_name, file_name, photo_path, width);
	}

	function photoDidChange(e){
		if(SDDom('photo_names[' + this.value + ']')){
			alert("you've already added that photo.");
			SDDom.stop(e);
		}else{
			SDDom('media_form').submit();
		}
	}
	function photosDidLoad(request){
		SDDom('list-of-photos').innerHTML = request.responseText;
	};
	
	function photoDidUpload(photo_name, file_name, photo_path, width, error_message){
		if(error_message.length > 0){
			alert(error_message);
		}else{
			SDDom('photo').value = null;
			var dd = SDDom.create('dd');
			dd.innerHTML = photo_name;
			var items = SDDom.findAll('#photos dd');
			var count = 0;
			if(items && items.length > 0){
				count = items.length;
			}
			var hidden_field = SDDom.create('input', {"type":"hidden", "value":photo_name + '=' + file_name, "id":"photo_names[" + photo_name + "]", "name":"photo_names[]"});
			SDDom.append(SDDom('photos'), dd);
			(new SDAjax({method: 'get', DONE: [top, photosDidLoad]})).send(SDDom('media_form').action.replace('photos', 'photos.phtml'));
		}
	};
</script>