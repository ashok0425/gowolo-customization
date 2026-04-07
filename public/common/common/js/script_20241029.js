/******** Common Function for input *********/
$(".decimal-only").on("keypress",function(evt)
{
  var charCode = (evt.which) ? evt.which : evt.keyCode;
  if (charCode != 46 && charCode > 31  && (charCode < 48 || charCode > 57))
     return false;

 // '.' decimal point
  if (charCode === 46) {

    // Allow only 1 decimal point
    if (($(this).val()) && ($(this).val().indexOf('.') >= 0))
      return false;
    else
      return true;
  }
  return true;
});

$(".digit-only").on("keypress",function(evt)
{
  var charCode = (evt.which) ? evt.which : evt.keyCode;
  if (charCode > 31  && (charCode < 48 || charCode > 57))
     return false;
    if (charCode === 46) {
        return false;
    }

  return true;
});

$(".alpha-space").on('change keypress paste', function(event){

   if( event.type == 'paste'){
        var copied_text =  event.originalEvent.clipboardData.getData('text');
        var remove_special_char = copied_text.replace(/^[a-zA-Z .]+/g, "");
        $(this).val($(this).val()+remove_special_char  );
        event.preventDefault();

  }else{
		var inputValue = event.which;
		if(event.key === '.'){
			return true;
		}

        if(!(inputValue >= 65 && inputValue <= 120) && (inputValue != 32 && inputValue != 0) ) {
              event.preventDefault();
        }
   }


});

//Toggle button Loading
function loadButton(element){
	var element = $(""+element+"");
	var loadStatus = element.data("loading");
	if(loadStatus == "loading"){
		element.data("loading", "normal");
		element.html(element.data("text"));
		element.prop('disabled', false);
	}else{
		element.prop('disabled', true);
		element.data("text", element.html());
		element.data("loading", "loading");
		element.html('<i class="fas fa-spinner fa-spin"></i> &nbsp;'+element.data("loading-text"));
	}
}

//Log the data
function lg(value){
	console.log(value);
}

//Notification


function initiateNotify(type, message){
	if(notify != ""){
		notify.close();
	}
		notify = $.notify(message,{
						allow_dismiss: true,
						type: type,
						placement: {
							from: 'top',
							align: 'right'
						},
						content: {icon: 'fa fa-bell'},
						time: 10,
					});
	/* }else{
		notify.update({'type': type, 'message': message});
	} */

}


function notifySuccess(message){
	initiateNotify('success', '<strong>Success</strong> '+message);
}

function notifyError(message){
	initiateNotify('error', '<strong>Error!</strong> '+message);
}

function notifyWarning(message){
	initiateNotify('warning', '<strong>Warning!</strong> '+message);
}

function getnotifys(mod) {
    if(mod != null)
    {
       
        $.ajax({
            url: "/notify/getnotifys",
            type:"GET",
            dataType: "json",
            data: {mod:mod},
            success:function(data) {
                if(data.success == 1) {
                  
                    var div = '';
                    
                    if(mod == 1){ //mail


                        if(data.data.length == 0){

                            div += ` <li>
                                <div class="dropdown-title">No New Notifications</div>
                            </li>`;


                        } else {
                            $.each(data.data,function(i,value){
                                var profile_pic = (value.user.profile_pic != null) ? PUBLIC_PATH+'/'+value.user.profile_pic : "{{asset('assets/admin/images/default-avatar.png')}}";
                               
                                div += `<li>
                                    <a href="/mail-management/create" style="text-decoration:none;color:#000">
                                        <div class="dropdown-title" style="display: flex;flex-direction:column;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="avatar-sm ">

                                                    <img src="${profile_pic}" alt="" class="avatar-img rounded-circle" style="width:40px !important; height:40px !important;"  onerror="this.src='${error}'">
                                                </div>
                                            <div calss="">
                                                <h5 style="font-size:0.8rem;">${ (value.module_details != null) ? (value.module_details.sender != null) ? value.module_details.sender.name : "LCP" : "LCP"}<b></b></h5>
                                                <h5 style="font-size:0.8rem;"><b>${ (value.module_details != null) ? value.module_details.subject : "" }</b></h5>
                                            </div>
                                            <i style="font-size:0.6rem;color:#000 !important">${ (value.module_details != null) ? moment(value.module_details.created_at).format(dateFormat) : moment(value.created_at).format(dateFormat) }</i>
                                        </div>
                                    </a>
                                </li>`;
                            });
                            div += `<li>
                                    <div class="dropdown-title">
                                        <a href="/mail-management/create" style="text-decoration:none;color:#000">Read All</a>
                                    </div>
                                </li>`;
                        }
                        $("#mail-notify").html(div);

                    } else {
                        if(data.data.length == 0){

                            div += `<li>
                                        <div class="dropdown-title">No New Notifications</div>
                                    </li>`;

                        } else {
                            $.each(data.data,function(i,value){
                                var routeUrl = '';
                                var text = '';
                                console.log(value.unreadnotifys.module)
                                if(value.unreadnotifys.module == announce_config){
                                    routeUrl = APP_URL+"/announcement/index";
                                    text = "Announcement";
                                } else if(value.unreadnotifys.module == newsevents_config){
                                    routeUrl = APP_URL+"/news-and-events/create";
                                    text = "Calendar";
                                } else if(value.unreadnotifys.module == materials_config) {
                                    routeUrl = APP_URL+"/upload-materials/index";
                                    text = "Materials";
                                } else if(value.unreadnotifys.module == ticket_config) {
                                    routeUrl = APP_URL+"/reply/"+value.unreadnotifys.module_details.ticket.id;
                                    text = "Ticket Message";
                                } else if(value.unreadnotifys.module == siteinfo_config) {
                                    routeUrl = APP_URL+"/support/site-info-list/";
                                    text = "Site Information";
                                } else if(value.unreadnotifys.module == 7) {
                                    routeUrl = APP_URL+"/community-post-group/group-post-list/"+value.unreadnotifys.module_id;
                                    text = "New Post";
                                } else if(value.unreadnotifys.module == 8) {
                                    routeUrl = APP_URL+"/e-wallet/transfer-details";
                                    text = "Congratulations you earned a Bonus";
                                } else if(value.unreadnotifys.module == 9) {
                                    routeUrl = APP_URL+"/dashboard";
                                    text = "Congratulations you are Star Ambassador";
                                } else if(value.unreadnotifys.module == 10) {
                                    routeUrl = APP_URL+"/notify/post/"+value.unreadnotifys.id;
                                    if(value.unreadnotifys.comment)
                                    {
                                        text = value.unreadnotifys.comment;
                                    }else{
                                        text = "Liked your Post";
                                    }
                                }
                                else if(value.unreadnotifys.module == 11) {
                                    routeUrl = APP_URL+"/notify/post/"+value.unreadnotifys.id;
                                    if(value.unreadnotifys.comment)
                                    {
                                        text = value.unreadnotifys.comment;
                                    }else{
                                        text = "Comment your Post";
                                    }
                                }
                                else if(value.unreadnotifys.module == 12) {
                                    routeUrl = APP_URL+"/notify/post/"+value.unreadnotifys.id;
                                    if(value.unreadnotifys.comment)
                                    {
                                        text = value.unreadnotifys.comment;
                                    }else{
                                        text = "Share your Post";
                                    }
                                }
                                else if(value.unreadnotifys.module == 13) {
                                    routeUrl = APP_URL+"/e-wallet/transfer-details";
                                    text = "Product Commission Added";
                                }
                                else if(value.unreadnotifys.module == 14) {
                                    routeUrl = APP_URL+"/e-wallet/transfer-details";
                                    text = "Congrutulations Points Added";
                                }
                                 else if(value.unreadnotifys.module == 15) {
                                    routeUrl = APP_URL+"/e-wallet/transfer-details";
                                    text = "Congrutulations Rewards Added";
                                }
                                 else if(value.unreadnotifys.module == 16) {
                                    routeUrl = APP_URL+"/e-wallet/transfer-details";
                                    text = "Congrutulations Package Commission Added";
                                }
                                 else if(value.unreadnotifys.module == 17) {
                                    routeUrl = APP_URL+"/e-wallet/transfer-details";
                                    text = "Congrutulations Package Points Added";
                                }
                                  else if(value.unreadnotifys.module == 18) {
                                    routeUrl = APP_URL+"/e-wallet/transfer-details";
                                    text = "Congrutulations Package Rewards Added";
                                }
                                  else if(value.unreadnotifys.module == 19) {
                                    routeUrl = APP_URL+"/package/view";
                                    if(value.unreadnotifys.comment)
                                    {
                                        text = value.unreadnotifys.comment;
                                    }else{
                                        text = "Purchased your package";
                                    }
                                }
                                 else if(value.unreadnotifys.module == 20) {
                                    routeUrl = APP_URL+"/customization/view/"+value.unreadnotifys.module_id;
                                    text = value.unreadnotifys.comment;
                                }
								var uprofile_pic = (value.unreadnotifys.posteduserimg != null) ? value.unreadnotifys.posteduserimg : "{{asset('assets/admin/images/default-avatar.png')}}";
								
								var postuname=value.unreadnotifys.createusername;
								var testpic=value.unreadnotifys.posteduserimg;
                               /* div += `<li>
                                        <div class="dropdown-title">

                                            <a href="${routeUrl}" ${(value.unreadnotifys.module == ticket_config) ? 'target="_blank"' : ''} style="text-decoration:none;color:#000;text-align:left">${text}</a>
                                        </div>
                                    </li>`;
									*/
									
									div += `<li>
                                        <div class="notifications-item">
										<img src="${uprofile_pic}" alt="${postuname}" onerror="this.src='${error}'" text="${uprofile_pic}" width="30px" height="30px">
										<div class="text">
												<h4>${postuname}</h4>
												<p><a href="${routeUrl}" ${(value.unreadnotifys.module == ticket_config) ? 'target="_blank"' : ''} style="text-decoration:none;color:#000;text-align:left">${text}</a></p>
											</div>                                            
                                        </div>
                                    </li>`;
                            });
                        }

                        $("#other-notify").html(div);
                    }
                }
            }
        });
    }
}

function readasnotify(activity_name,mod_notify) {

    if(activity_name != null)
    {
        $.ajax({
            url: "/notify/readasnotify",
            type:"GET",
            dataType: "json",
            data: {activity_name:activity_name,mod:mod_notify},
            success:function(data) {
                if(data.success == 1) {
                    if(mod_notify == 1){ //mail
                        $(".mail-notify").text(data.data);
                    } else {
                        $(".other-notify").text(data.data);
                    }
                }
            }
        });
    }
}

// function imageSize(filename,filewidth,fileheight){

//     var file = filename;

//     img = new Image();
//     var imgwidth = 0;
//     var imgheight = 0;
//     var maxwidth = filewidth;
//     var maxheight = fileheight;

//     imgwidth = this.width;
//     imgheight = this.height;

//     if(imgwidth == maxwidth && imgheight == maxheight){

//         // var formData = new FormData();
//         // formData.append('fileToUpload', $('#file')[0].files[0]);
//         $("#imagesizeerror").text("");
//         $("#addRowButton").prop('disabled',false);
//     } else {
//         $("#imagesizeerror").text("Image size must be "+maxwidth+"X"+maxheight);
//         $("#addRowButton").prop('disabled',true);
//     }
// }


jQuery.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
	if(oSettings.oFeatures.bServerSide === false){
		var before = oSettings._iDisplayStart; oSettings.oApi._fnReDraw(oSettings);
		// iDisplayStart has been reset to zero - so lets change it back
		oSettings._iDisplayStart = before; oSettings.oApi._fnCalculateEnd(oSettings);
	}
		//draw the 'current' page
		oSettings.oApi._fnDraw(oSettings);
};


