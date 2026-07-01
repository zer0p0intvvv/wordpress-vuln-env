<?php

	?>

    </head>
    <body>


    <!-- The Modal -->
    <div id="wpns_feedback_modal_limit_login" class="mo_modal" style="width:90%; margin-left:12%; margin-top:5%; text-align:center; margin-left">

        <!-- Modal content -->
        <div class="mo_wpns_modal-content" style="width:50%;">
            <h3 style="margin: 2%; text-align:center;"><b>Your feedback</b><span id="mo_wpns_close_limit_login" class="mo_wpns_close" style="cursor: pointer">&times;</span>
            </h3>
			<hr style="width:75%;">
			
            <form name="f" method="post" action="" id="mo_wpns_feedback_limit_login">
                <?php wp_nonce_field("mo_wpns_feedback");?>
                <input type="hidden" name="option" value="mo_wpns_feedback_limit_login"/>
                <div>
                    <p style="margin:2%">
					<h4 style="margin: 2%; text-align:center;">Please help us to improve our plugin by giving your opinion.<br></h4>
					<p>Limit Login Attempts</p>
					<div id="smi_rating" style="text-align:center">
					<input type="radio" name="rate" id="angry" value="1"/>
						<label for="angry"><img class="sms" src="<?php echo $imagepath . 'angry.png'; ?>" />
						</label>
						
					<input type="radio" name="rate" id="sad" value="2"/>
						<label for="sad"><img class="sms" src="<?php echo $imagepath . 'sad.png'; ?>" />
						</label>
					
					
					<input type="radio" name="rate" id="neutral" value="3"/>
						<label for="neutral"><img class="sms" src="<?php echo $imagepath. 'normal.png'; ?>" />
						</label>
						
					<input type="radio" name="rate" id="smile" value="4"/>
						<label for="smile">
						<img class="sms" src="<?php echo $imagepath . 'smile.png'; ?>" />
						</label>
						
					<input type="radio" name="rate" id="happy" value="5" checked/>
						<label for="happy"><img class="sms" src="<?php echo $imagepath . 'happy.png'; ?>" />
						</label>
						
					<div id="limit_login_outer" style="visibility:visible"><span id="limit_login_result">Thank you for appreciating our work</span></div>
					</div><br>
					<hr style="width:75%;">

					<div style="text-align:center;">
						
						<div style="display:inline-block; width:60%;">
						<input type="email" id="query_mail" name="query_mail" style="text-align:center; border:0px solid black; border-style:solid; background:#f0f3f7; width:20vw;border-radius: 6px;"
                              placeholder="your email address" required value="<?php echo $email; ?>" readonly="readonly"/>
						
						<input type="radio" name="edit" id="edit" onclick="editName()" value=""/>
						<label for="edit"><img class="editable" src="<?php echo $imagepath . '61456.png'; ?>" />
						</label>
						
						</div>
						<br><br>
						<textarea id="wpns_query_feedback" name="wpns_query_feedback" rows="4" style="width: 60%"
                              placeholder="Tell us what happened!"></textarea>
						<br><br>
						  <input type="checkbox" name="get_reply" value="reply" checked>miniOrange representative will reach out to you at the email-address entered above.</input>
					</div>
					<br>
                   
                    <div class="mo-modal-footer" style="text-align: center;margin-bottom: 2%">
                        <input type="submit" name="miniorange_feedback_submit_limit_login"
                               class="button button-primary button-large" value="Send"/>
						<span width="30%">&nbsp;&nbsp;</span>
                        <input type="button" name="miniorange_skip_feedback_limit_login"
                               class="button button-primary button-large" value="Skip" onclick="document.getElementById('mo_wpns_feedback_form_close_limit_login').submit();"/>
                    </div>
                </div>
                    <script>

                        $("input[type=radio][value=" + 5 + "]").prop('checked', true);

						const limit_login_INPUTS = document.querySelectorAll('#smi_rating input');
						limit_login_INPUTS.forEach(el => el.addEventListener('click', (e) => updateValue(e)));


						function editName(){
							//prevent(this.e)
							document.querySelector('#query_mail').removeAttribute('readonly');
							document.querySelector('#query_mail').focus();
							return false;
						}
						function updateValue(e) {

							document.querySelector('#limit_login_outer').style.visibility="visible";
							var result = 'Thank you for appreciating our work';
							switch(e.target.value){
								case '1':	result = 'Not happy with our plugin ? Let us know what went wrong';
											break;
								case '2':	result = 'Found any issues? Let us know and we\'ll fix it ASAP';
											break;
								case '3':	result = 'Let us know if you need any help';
											break;
								case '4':	result = 'We\'re glad that you are happy with our plugin';
											break;
								case '5':	result = 'Thank you for appreciating our work';
											break;
							}
							document.querySelector('#limit_login_result').innerHTML = result;
                            $("input[type=radio][value=" + e.target.value + "]").prop('checked', true);
							/*var res = e.target.value;
							document.querySelector(res).style.opacity="0";
							alert(res);*/
						}
					</script>
					<style>
					.editable{
						text-align:center;
						width:1em;
						height:1em;
					}
					.sms {
						text-align:center;
					  width: 2vw;
					  height: 2vw;
					  padding: 1vw;
					}

					input[type=radio] {
					  display: none;
					}

					.sms:hover {
					  opacity:0.5;
					  cursor: pointer;
                        border: 2px solid #21ecdc;
					}

					.sms:active {
					  opacity:0.4;
					  cursor: pointer;
                        border: 2px solid #21ecdc;
					}

					input[type=radio]:checked + label > .sms
                    {
					  border: 2px solid #21ecdc;
					}



					</style>
            </form>
            <form name="f" method="post" action="" id="mo_wpns_feedback_form_close_limit_login">
                <?php wp_nonce_field("mo_wpns_skip_feedback");?>
                <input type="hidden" name="option" value="mo_wpns_skip_feedback_limit_login"/>
            </form>

        </div>

    </div>

    <script>
        jQuery('#deactivate-miniorange-limit-login-attempts').click(function () {

            var mo_modal = document.getElementById('wpns_feedback_modal_limit_login');

            var span = document.getElementById("mo_wpns_close_limit_login");

            // When the user clicks the button, open the mo2f_modal

            mo_modal.style.display = "block";
			document.querySelector("#wpns_query_feedback").focus();
            span.onclick = function () {
                mo_modal.style.display = "none";
                jQuery('#mo_wpns_feedback_form_close_limit_login').submit();
            }

            // When the user clicks anywhere outside of the mo2f_modal, mo2f_close it
            window.onclick = function (event) {
                if (event.target === mo_modal) {
                    mo_modal.style.display = "none";
                }
            }
            return false;

        });
    </script><?php


?>