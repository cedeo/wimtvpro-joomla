jQuery.noConflict();
jQuery(document).ready(function(){ 
	
	jQuery(".icon-32-download").click(function(){
		
		var id = jQuery(this).attr("id").split("|");
		var basePath = jQuery(this).attr("rel");
		var uri =  basePath + "videos/" + id[0] + "/download";
		if (id[1]!=""){
			var file = id[1].split(".");
			uri = uri + "?ext=" + file[1] + "&filename=" + file[0];
		} 
		jQuery("body").append("<iframe src=\"" + uri + "\" style=\"display:none;\" />");
		
	});
	
	
	jQuery(".cc_set").click(function(){
		jQuery(".cc_set").removeClass("selected");
		jQuery(this).addClass("selected");
	});	

	
	jQuery(".icon_RemoveshowtimeInto").click(function(){
		callRemoveShowtime(jQuery(this));
	});
	
	jQuery(".free,.cc,.ppv").click(function(){
		callPutShowtime(jQuery(this));					
	});
	jQuery(".icon_Putshowtime").click(function(){
		callViewForm(jQuery(this));
	});
	
	
	function callViewForm(element){
		element.parent().parent().children(".formVideo").fadeToggle("slow");
	}
	
	function callPutShowtime(element){
		var thisclass = element.attr("class");
		var coid = element.parent().parent().children("a").children(".icon_Putshowtime").attr("id");
		if (thisclass.indexOf("free") >= 0){
			jQuery(location).attr('href','index.php?option=com_wimtvpro&view=putvideos&put=free&layout=edit&coid=' + coid);
		}else if (thisclass.indexOf("cc") >= 0){	
			jQuery(location).attr('href','index.php?option=com_wimtvpro&view=putvideos&put=cc&layout=edit&coid=' + coid);
		} else if (thisclass.indexOf("ppv") >= 0){
			jQuery(location).attr('href','index.php?option=com_wimtvpro&view=putvideos&put=ppv&layout=edit&coid=' + coid);
		}
	}	
	
	
	function callRemoveShowtime(element){
		
		var sepId =   element.attr("id").split('|');  //[0] ContentId ; [1] ShowtimeId
		var page = element.attr("rel");
		
		jQuery.ajax({
				context: this,
				//url:  "index.php?option=com_wimtvpro&task=removeShowtime&tmpl=component", 		      
				//url : "components/com_wimtvpro/includes/script.php",
				url : "index.php?option=com_wimtvpro&task=mymedias.removeShowtime&user=nothing",
				type: "GET",
				dataType: "html",
				data: "coid=" + sepId[0] + "&id=" + sepId[1] , 

			success: function(response) {
				//console.log (response);
				//redirect
				
						jQuery(location).attr('href','index.php?option=com_wimtvpro&view=' + page);
			},
			error: function(request,error) {
				alert(request.responseText);
			}	
		});
	}
	
	
	//Check video File Upload
	
	jQuery("#jform_videoFileWimtv").change(function(){
		
		fileName = jQuery(this).val();
	    fileTypes = [ "", "mov", "mpg", "avi", "flv", "mpeg", "mp4", "mkv", "m4v" ];
	    if (!fileName) {
	      return;
	    }

	    dots = fileName.split(".");
	    // get the part AFTER the LAST period.
	    fileType = "." + dots[dots.length - 1];

	    if (fileTypes.join(".").indexOf(fileType.toLowerCase()) != -1) {
	      return TRUE;
	    } else {
	      alert("Please only upload files that end in types: \n\n"
	      + (fileTypes.join(" ."))
	      + "\n\nPlease select a new file and try again.");
	      jQuery(this).val("");
	    }
		
	})
	
	jQuery('.icon_selectPlay').click(function() {
	  	jQuery(".playlist").removeClass("selected");
	  	 jQuery(this).parent().addClass("selected");
	  });

	jQuery('.icon_playlist').click(function() {    
		callInsertIntoPlayList(jQuery(this));	   
	});

	jQuery('.icon_deletePlay').click(function() {
	  	var nameNewPlaylist = jQuery(this).parent().children("input").val();
	  	//remove from DB
	  	var idPlayList = jQuery(this).parent().attr("rel");
	  	//add to DB
	  	jQuery.ajax({
		    context: this,
		    url : "index.php?option=com_wimtvpro&task=mystreamings.deletePlaylist&user=nothing&idPlayList=" + idPlayList,
		    
		    success: function(response){
		  		jQuery(this).parent().remove();
		  		var count = jQuery(".playlist").size();
		  		jQuery(".new").children("input").val("Playlist " + count);
		  	},
		    error: function(jqXHR, textStatus, errorThrown){alert(errorThrown);} 
		  });
	  	
	  });

	
	function callInsertIntoPlayList(elem) {
			var contentIdAdd = elem.attr("rel");

			var playlistId = "";
			jQuery(".playlist").each(function(i) {
			  var classe = jQuery(this).attr("class");
			  if (classe=="playlist selected"){
			    playlistId = jQuery(this).attr("rel");
			  }
			});
			if (playlistId == ""){
				alert (Joomla.JText._('COM_WIMTVPRO_MSG_PLAYLIST_ERROR'));
			}else{
		
			
				jQuery.ajax({
			    context: this,
		        url : "index.php?option=com_wimtvpro&task=mystreamings.addPlaylist&user=nothing&idPlayList=" + playlistId + "&id=" + contentIdAdd,
			   
			    success: function(response){
			  		//jQuery(this).hide();
			  		if (response!="") 
			  			alert(response);
			  		else {
				  		jQuery(".playlist").each(function(i) {
			   			  var classe = jQuery(this).attr("class");
			   			  if (classe=="playlist selected"){
			   			    var counter = parseInt(jQuery(this).children(".counter").html());
			   			    jQuery(this).children(".counter").html(counter +1);
			   			  }
			   			  alert (Joomla.JText._('COM_WIMTVPRO_MSG_PLAYLIST_ADDEDVIDEO'));
			   			});
			   		}

			  	},
			    error: function(jqXHR, textStatus, errorThrown){alert(errorThrown);} 
			  });
			}	   			
		}
	/*

	function callviewVideothumbs (element){
		var id = element.parent().parent().parent().parent("li").attr("id");
		jQuery(".icon_viewVideo").colorbox({
	        html:function(){
		    var stateView = jQuery(this).attr("rel").split('|');
		    var roles = jQuery.ajax({
						context: this,
						url:  url_pathPlugin + "scripts.php", 
						type: "GET",
						dataType: "html",
						async: false, 
						data:{ 
							namefunction: "getRoles",
							id : id						
						},
						success: function(response) {
							roles = response;
						}
			}).responseText;
			var users = jQuery.ajax({
						context: this,
						url:  url_pathPlugin + "scripts.php", 
						type: "GET",
						dataType: "html",
						async: false, 
						data:{ 
							namefunction: "getUsers",
							id : id
						},
						success: function(response) {
							users = response; 	
						}
			}).responseText;

								
				text  = '<b>Who should see the video?</b><br/>';
				
				text += '<select onChange="viewWho(this);" id="users" multiple="multiple" style="height:100px;width:270px">';
				text += '<option value="All">Everybody</option>';
				text += '<option value="No">Nobody (only administrator)</option>';
				text += '<optgroup label="Users" id="optUsers">';
				text += users;
				text += '</optgroup>';
				text += '<optgroup label="Roles" id="optRoles">';
				text += roles;
				text += '</optgroup>';
				text += '</select>';
				text += '<br/><p class="description">(Multiselect with CTRL) You are selected <br/><strong id="AddUser"></strong>  <strong id="AddRole"></strong></p>';
				
				text  += '<b>Where should see the video for public user? (If you are selected Everyone)</b>';
				
				text += '<p class="viewThumbs';
				if (stateView[0]=="0") text += " selected";
				text += '" id="0">Invisible</p>';
				text += '<p class="viewThumbs';
				if (stateView[0]=="1") text += " selected";
				text += '" id="1">Only into widget</p>';
				text += '<p class="viewThumbs';
				if (stateView[0]=="2") text += " selected";
				text += '" id="2">Only into pages</p>';
				text += '<p class="viewThumbs';
				if (stateView[0]=="3") text += " selected";
				text += '" id="3">Into widget and page</p>';
				text += '<div class="action"><span class="form_save">Save</span><span class="icon_sync2" style="display:none;">Loading...</span></div>';
				text += '<br/><br/>';
				text += '&nbsp;&nbsp;&nbsp;';
				
				return text;
			},
			onComplete: function(){
				
				var stateView = jQuery(this).attr("rel").split('|');
				jQuery(".viewThumbs").click(function(){
					jQuery(".viewThumbs").removeClass("selected");
					jQuery(this).addClass("selected");
					
				});
								

				jQuery("#users option[value='" + stateView[1]  + "']").attr('selected', 'selected');
				jQuery(".form_save").click(function(){
					var state = jQuery(".selected").attr("id");
					
					var blkstr = jQuery.map(jQuery("#users").val(), function(val,index) {
					     return val;
					}).join(",");
					
					if (state=="0") state += "|No";		
                    else state += "|" + blkstr;
					//alert (id);
					jQuery.ajax({
						context: this,
						url:  url_pathPlugin + "scripts.php", 
						type: "GET",
						dataType: "html",
						data:{ 
							state : state, 
							id : id,
							namefunction: "StateViewThumbs" 
						},
						
						beforeSend: function(){ 
							//jQuery(this).parent().hide(); 
							//jQuery(this).parent().parent().children(".loader").show(); 	
							//jQuery(this).colorBox(wimtvproAddShowtime);	
							jQuery(".icon_sync2").show();	
							jQuery(".form_save").hide();		   
						},
						success: function(response) {
							jQuery.colorbox.close();
							alert ("Change State view successfully");
							element.parent().parent().children(".icon").children("span").attr("rel",state); 	
						}
					});
				});
			}
		});
		
	}	

	
	function putST(element,namefunction,licenseType,paymentMode,ccType,pricePerView,pricePerViewCurrency,changeClass,coId,id){
		jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "scripts.php", 
			type: "GET",
			dataType: "html",
			data:{ 
				coId : coId, 
				id : id,
				namefunction: namefunction, 
				licenseType: licenseType,
				paymentMode:paymentMode,
				ccType:ccType,
				pricePerView:pricePerView,
				pricePerViewCurrency:pricePerViewCurrency
			},
			
			beforeSend: function(){ 
				jQuery(".icon_sync2").show();	
				jQuery(".form_save").hide();			   
			},
			success: function(response) {
		    
				var json =  jQuery.parseJSON(response);
				console.log (json.messages);
				var result = json.result;
				
				if (result=="SUCCESS"){
					jQuery.colorbox.close();
					element.parent().parent().children(".icon").children("span").hide();				 	
					element.parent().parent().children(".icon").children("span." + changeClass).show();
					element.parent().parent().children(".icon").children("span." + changeClass).attr("id", json.showtimeIdentifier);			        		
					
					element.parent().parent().children(".icon").children("a.viewThumb").show();
					url=  "admin/config/embedded/" + id + "/" + json.showtimeIdentifier;
					element.parent().parent().children(".icon").children("a.viewThumb").attr("id",url);
					element.parent().remove();			        		
				} else {
				    var message = json.messages[0].field + ":" + json.messages[0].message; 
					jQuery(this).parent().hide(); 
					jQuery(this).parent().parent().children(".loader").show();
					jQuery(".icon_sync2").hide();	
					jQuery(".form_save").show();
					alert (message);
				}
			},
			error: function(request,error) {
				alert(request.responseText);
			}	
		});
	}
	function callViewForm(element){
		element.parent().parent().children(".formVideo").fadeToggle("slow");
	}
	
	
	jQuery(".icon_sync0").click(function(){
		jQuery.ajax({
			context: this,
			url:   url_pathPlugin  + "sync.php",
			dataType: "html",
			data: {sync:true,showtime:jQuery("ul.items").attr("id")},
			type: "GET",
			beforeSend: function(){ 
				jQuery(this).removeClass();
				jQuery(this).addClass("icon_sync1");
				jQuery("ul.items li").remove(); 						   
			},
			complete: function(){ 
				jQuery(this).removeClass();
				jQuery(this).addClass("icon_sync0");
			},
			success: function(response) {	
				jQuery("ul.items").html(response);
				jQuery("a.viewThumb").click( function(){
                  jQuery(this).colorbox({href:jQuery(this).attr("id")});
                });
                jQuery("a.wimtv-thumbnail").click( function(){
				    if( jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").length  ) {
						var url = jQuery(this).parent().children(".headerBox").children(".icon").children("a.viewThumb").attr("id");
						jQuery(this).colorbox({href:url});
					}
				});
                
				jQuery(".icon_Putshowtime,.icon_AcquiPutshowtime").click(function(){
					callViewForm(jQuery(this));					
				});
			
				
				jQuery(".free,.cc,.pay").click(function(){
					callPutShowtime(jQuery(this));					
				});
				jQuery(".icon_remove").click(function(){
				callRemoveVideo(jQuery(this));
				});
				jQuery('.icon_playlist').click(function() {    
					callInsertIntoPlayList(jQuery(this));	   
				});
				callviewVideothumbs(jQuery(this));
			},
			
			error: function(response) {	
				jQuery("ul.items").html(response);
			}
		});
	});
	jQuery(".free,.cc,.ppv").click(function(){
		callPutShowtime(jQuery(this));					
	});
	jQuery(".icon_Putshowtime,.icon_AcquiPutshowtime").click(function(){
		callViewForm(jQuery(this));
	});
	jQuery(".icon_AcqRemoveshowtime,.icon_Removeshowtime,.icon_RemoveshowtimeInto").click(function(){
		callRemoveShowtime(jQuery(this));
	});
	
	jQuery(".icon_viewVideo").click(function(){
		callviewVideothumbs(jQuery(this));
	});
	
	
	jQuery(".icon_remove").click(function(){
		callRemoveVideo(jQuery(this));
	});
	
	//Request new URL for create a wimlive Url
	jQuery(".createUrl").click(function(){
	  console.log("Click");
	  jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "scripts.php", 
			type: "GET",
			dataType: "html",
			data:{
				namefunction: "urlCreate",
				titleLive: jQuery("#edit-name").val()	
			},
			success: function(response) {
			  var json =  jQuery.parseJSON(response);
			  var result = json.result;
			  if (result=="SUCCESS"){
			  	jQuery("#edit-url").attr("readonly", "readonly");
			  	jQuery("#edit-url").attr("value", json.liveUrl);
			  	jQuery(this).hide();
				jQuery(".removeUrl").show();
			  } else {
			    //alert (response);
			    alert ("Insert a password for live streaming is required");
			    jQuery(".passwordUrlLive").show();
			    jQuery(".createPass").click(function(){
			     jQuery.ajax({
			     context: this,
			     url:  url_pathPlugin + "scripts.php", 
			     type: "GET",
			     dataType: "html",
			     data:{
				  namefunction: "passCreate",
				  newPass: jQuery("#passwordLive").val()
			     },
                 success: function(response) {
                 	alert (response);
                 	jQuery(".passwordUrlLive").hide();
                 }
			    });
	            });
			  }
			},
			error: function(request,error) {
				alert(request.responseText);
			}	
		});
     });
   jQuery(".removeUrl").click(function(){
     jQuery(this).hide();
     jQuery(".createUrl").show();
     jQuery("#edit-url").removeAttr("readonly");
     jQuery("#edit-url").removeAttr("disabled");
     jQuery("#edit-url").val("");	
   });
      
});



function viewCategories(obj){
    jQuery("#addCategories").html("You are selected");
    var selectedArray = new Array();
    count = 0;
    for (i=0; i<obj.options.length; i++) {
      if (obj.options[i].selected) {
        selectedArray[count] = obj.options[i].value;
        valueSelected = obj.options[i].value;
        count++;
        jQuery("#addCategories").append("<br/>" + valueSelected);
      }
    }
}

function viewWho(obj){  
    var selectedArray = new Array();
    count = 0;
    jQuery("#AddUser").html("");
    jQuery("#AddRole").html("");

    for (i=0; i<obj.options.length; i++) {
      if (obj.options[i].selected) {
        
        selectedArray[count] = obj.options[i].value;
        valueSelected = obj.options[i].text;
        count++;
		
        if ( obj.options[i].parentNode.id == "optUsers"){
            if (jQuery("#AddUser").html() == "") jQuery("#AddUser").html("Users:");
        	jQuery("#AddUser").append(" " + valueSelected);
        }
        if ( obj.options[i].parentNode.id == "optRoles"){
        	if (jQuery("#AddRole").html() == "") jQuery("#AddRole").html("Roles:");
        	jQuery("#AddRole").append(" " + valueSelected);
        }
        	
      }
    }
    for (i=0; i<obj.options.length; i++) {
    	if ((obj.options[i].selected) && (obj.options[i].parentNode.id != "optUsers") && (obj.options[i].parentNode.id != "optRoles")) {   		
    	    jQuery("#AddUser").html(obj.options[i].text);
        	jQuery("#AddRole").html("");
        	jQuery(obj).children().children().removeAttr("selected");
        	jQuery(obj).children().removeAttr("selected");
        	jQuery(obj).children().eq(i).attr("selected","selected");
        
        }
    }

    
}


function wimtvpro_TestFileType() {	
    fileName = jQuery("input[name=\"files[videoFile]\"]").val();
    fileTypes = [ "", "mov", "mpg", "avi", "flv", "mpeg", "mp4", "mkv", "m4v" ];
    if (!fileName) {
      return;
    }

    dots = fileName.split(".");
    // get the part AFTER the LAST period.
    fileType = "." + dots[dots.length - 1];

    if (fileTypes.join(".").indexOf(fileType.toLowerCase()) != -1) {
      return TRUE;
    } else {
      alert("Please only upload files that end in types: \n\n"
      + (fileTypes.join(" ."))
      + "\n\nPlease select a new file and try again.");
      jQuery("input[name=\"files[videoFile]\"]").val("");
    }
}

jQuery(document).ready(function() { 
  jQuery("ul.itemsPublic li a").colorbox();
  jQuery('.buttonInsert').click(function() {
      var width = jQuery(this).parent().children('.w').val();
      var height = jQuery(this).parent().children('.h').val();
      var id = jQuery(this).attr('id');
	  jQuery.ajax({
	    context: this,
        url:  url_pathPlugin + "scripts.php", 
	    type: "GET",
	    data:{ 
	      HFrame : height,
	      WFrame : width,
	      id : id,
	      namefunction: "getIFrameVideo"
	    },
	    success: function(response){
	  	  var win = window.dialogArguments || opener || parent || top;
          win.send_to_editor(response); 
	    },
	    error: function(jqXHR, textStatus, errorThrown){alert(errorThrown);} 
	  });
  });
  jQuery('.buttonInsertPlayList').click(function() {
      var id = jQuery(this).attr('id');
	  jQuery.ajax({
	    context: this,
        url:  url_pathPlugin + "pages/embeddedPlayList.php", 
	    type: "GET",
	    data:{ 
	      id : id,
	      page:true
	    },
	    success: function(response){
	  	  var win = window.dialogArguments || opener || parent || top;
          win.send_to_editor(response); 
	    },
	    error: function(jqXHR, textStatus, errorThrown){alert(errorThrown);} 
	  });
  });
    
  //Playlist
  functionPlaylist();
  
  jQuery('.icon_playlist').click(function() {    callInsertIntoPlayList(jQuery(this));	   });

	function callInsertIntoPlayList(elem) {
   			var contentIdAdd = elem.attr("rel");

   			var playlistId = "";
   			jQuery(".playlist").each(function(i) {
   			  var classe = jQuery(this).attr("class");
   			  if (classe=="playlist selected"){
   			    playlistId = jQuery(this).attr("rel");
   			  }
   			});
   			if (playlistId == ""){
   				alert ("First, You must selected a playlist");
   			}else{
   		
   			
   				jQuery.ajax({
				    context: this,
			        url:  url_pathPlugin + "script_playlist.php", 
				    type: "GET",
				    data:{
				      idPlayList : playlistId,
				      id : contentIdAdd,
				      namefunction: "AddVideoToPlaylist"
				    },
				    success: function(response){
				  		//jQuery(this).hide();
				  		if (response!="") 
				  			alert(response);
				  		else {
					  		jQuery(".playlist").each(function(i) {
				   			  var classe = jQuery(this).attr("class");
				   			  if (classe=="playlist selected"){
				   			    var counter = parseInt(jQuery(this).children(".counter").html());
				   			    jQuery(this).children(".counter").html(counter +1);
				   			  }
				   			  alert ("The video is insert into playlist selected!");
				   			});
				   		}

				  	},
				    error: function(jqXHR, textStatus, errorThrown){alert(errorThrown);} 
				  });
   			}
   			
   			   			
	}

  
  function functionPlaylist(){
  
         
	   jQuery('.playlist input.title').change(function() {
	    jQuery(this).parent().children('.icon_modTitlePlay').show();
	   });
	   
	   jQuery('.icon_selectPlay').click(function() {
	  	jQuery(".playlist").removeClass("selected");
	  	 jQuery(this).parent().addClass("selected");
	  });
	
	jQuery(".icon_viewPlay").click(function () {
		var id= jQuery(this).parent().attr("rel");
		jQuery(this).colorbox({href:  url_pathPlugin + "pages/embeddedPlayList.php?id=" + id});
	});
	
	  jQuery('.icon_createPlay').click(function() {
	  	var nameNewPlaylist = jQuery(this).parent().children("input").val();
	  	//ID = playlist_##
	  	var count = jQuery(".playlist").size();
	  	count  = count + 1; 
	  	//add to DB
	  	jQuery.ajax({
		    context: this,
	        url:  url_pathPlugin + "script_playlist.php", 
		    type: "GET",
		    data:{ 
		      namePlayList : nameNewPlaylist,
		      namefunction: "createPlaylist"
		    },
		    success: function(response){
		        
		  		var newRiga = '<div class="playlist new" id="playlist_' +  count  + '" rel=""><span class="icon_selectPlay" style="visibility:hidden"></span><input type="text" value="Playlist ' +  count  + '" /><span class="icon_createPlay"></span></div>';
		  		jQuery(this).parent().parent().append(newRiga);
		  		jQuery(this).parent().removeClass("new");
		  		jQuery(this).parent().append('(<span class="counter">0</span>)<span class="icon_deletePlay"></span><span class="icon_modTitlePlay"></span><span class="icon_viewPlay"></span>');
		  		jQuery(this).parent().children("input").addClass("title");
		  		jQuery(this).parent().attr("rel",response);
		  		jQuery(this).parent().children(".icon_selectPlay").attr("style","");
		  		jQuery(".playlist").removeClass("selected");
		  		jQuery(this).parent().addClass("selected");
		  		jQuery(this).remove();
		  		functionPlaylist();
		  		
		  		
		  	},
		    error: function(jqXHR, textStatus, errorThrown){alert(errorThrown);} 
		  });
	  
	  	
	  });
	  
	  jQuery('.icon_modTitlePlay').click(function() {
	  	var nameNewPlaylist = jQuery(this).parent().children("input").val();
	  	//ID = playlist_##
	  	var idPlayList = jQuery(this).parent().attr("rel"); 
	  	//add to DB
	  	jQuery.ajax({
		    context: this,
	        url:  url_pathPlugin + "script_playlist.php", 
		    type: "GET",
		    data:{
		      idPlayList : idPlayList,
		      namePlayList : nameNewPlaylist,
		      namefunction: "modTitlePlaylist"
		    },
		    success: function(response){
		  		jQuery(this).hide();
		  	},
		    error: function(jqXHR, textStatus, errorThrown){alert(errorThrown);} 
		  });
	  
	  	
	  });
	
	  
	  jQuery('.icon_deletePlay').click(function() {
	  	var nameNewPlaylist = jQuery(this).parent().children("input").val();
	  	//remove from DB
	  	var idPlayList = jQuery(this).parent().attr("rel");
	  	//add to DB
	  	jQuery.ajax({
		    context: this,
	        url:  url_pathPlugin + "script_playlist.php", 
		    type: "GET",
		    data:{
		      idPlayList : idPlayList,
		      namefunction: "removePlaylist"
		    },
		    success: function(response){
		  		jQuery(this).parent().remove();
		  		var count = jQuery(".playlist").size();
		  		jQuery(".new").children("input").val("Playlist " + count);
		  	},
		    error: function(jqXHR, textStatus, errorThrown){alert(errorThrown);} 
		  });
	  	
	  });
  }


  //End Playlist


	jQuery('#edit-sandbox').change (function() {
        if(jQuery(this).value == "No") {
            jQuery('#sandbox').attr('href','http://www.wim.tv/wimtv-webapp/userRegistration.do?execution=e1s1');
            jQuery('#site').html('www.wim.tv');
        } else {
            jQuery('#sandbox').attr('href','http://www.wim.tv/wimtv-webapp/userRegistration.do?execution=e1s1');
            jQuery('#site').html('peer.wim.tv');
        }
     });
     
    
    
    jQuery(".termsLink").colorbox({width:"80%", height:"80%", iframe:true, href:jQuery(this).attr("href")});
    */
	

}); 