<?php

echo '	<br><br>

	

	<div class="mo_wpns_divided_layout" >';

echo '<div class="mo_wpns_small_layout" id="mo_waf">

	  <meta name="viewport" content="width=device-width, initial-scale=1">
				
				<h1>Web Application Firewall</h1>
				Price starting form<br><br>
				<center>
				    <div id="mo_waf_align_price" class="mo_align_pricing">
						<table >
                        <tr style="text-align: center;">
                            <th><h2>No. of Sites</h2></th>
                            <th><h2>Total</h2></th>
                        </tr>
                        <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">1 site   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail">$50 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
                        <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">5 sites   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail">$100 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
                         <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">10 sites   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail">$150 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
               </table>
					</div>		
				</center>
				<a target="_blank" class="button button-primary button-large"  onclick="wpns_upgrade(\'wp_security_waf_plan\')">Upgrade Now</a>
				<a  id="mo_waf_detail" class=" button button-primary button-large mo_wpns_collapsible" onclick="expand(this,\'mo_waf\')"  data-active="false">Details >></a>

				<div class="mo_wpns_content" id="hide_waf">

				<hr class="mo_wpns_line">
				<table class="mo_wpns_settings_table">
					  <tr class="mo_wpns_table_row_layout">
					    <td class="mo_wpns_table_free_text_layout">
					    	<a id="waf_id" class="mo_wpns_button1 mo_wpns_collapsible mo_wpns_free_feature_button" >All Free features ⮞</a>

						   <div class="mo_wpns_waf_free">
							   <table class="mo_wpns_settings_table">
								   <tr class="mo_wpns_table_row_layout mo_wpns_waf_free">
								    <td class="mo_wpns_table_free_col1_layout">OWASP TOP 10 Firewall Rules</td>
								    <td class="mo_wpns_table_free_col2_layout">It covers many popular attacks like SQL Injection, Cross-site Scripting, XML External Entities, Security misconfiguration and others.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
								  <tr class="mo_wpns_table_row_layout mo_wpns_waf_free">
								    <td class="mo_wpns_table_free_col1_layout">Standard Rate Limiting/ DOS Protection</td>
								    <td class="mo_wpns_table_free_col2_layout">You can set your own limit, provide a response and analyze the incoming request violating the limit. By controlling the traffic you can protect your application from these attacks.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
								  <tr class="mo_wpns_table_row_layout mo_wpns_waf_free">
								    <td class="mo_wpns_table_free_col1_layout">IP Blocking and Whitelisting</td>
								    <td class="mo_wpns_table_free_col2_layout">You must always whitelist your IP so that you don\'t get blocked. And always protect malicious IPs that has to be blocked.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr> 
								  <tr class="mo_wpns_table_row_layout mo_wpns_waf_free">
								    <td class="mo_wpns_table_free_col1_layout">Live Traffic and Audit</td>
								    <td class="mo_wpns_table_free_col2_layout">We provide an analysis of all the requests so that it is easier for customers to know more about the traffic on the website and plan actions based on that.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
								  <tr class="mo_wpns_table_row_layout mo_wpns_waf_free">
								    <td class="mo_wpns_table_free_col1_layout">IP Lookup</td>
								    <td class="mo_wpns_table_free_col2_layout">With the IP Lookup feature you can know details about the request with Country, State and City of the IP and take action if needed.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
							   </table>
						   </div>
					    </td>
					  </tr>
				</table>
				<hr class="mo_wpns_line">
				
					 <table class="mo_wpns_settings_table">

                <tr class="mo_wpns_table_row_layout">
							<td class="" colspan=3>
							<a class="mo_wpns_premium_feature" style="text-align:left;">All Premium Features ⮟</a>
							</td>
						</tr>
                <tr class="mo_wpns_table_row_layout">
                    <td class="mo_wpns_table_col1_layout">miniOrange Advanced Firewall Rules</td>
                    <td class="mo_wpns_table_col2_layout">There are advanced rules created by miniOrange that protects advanced attacks. Including these rules makes your site from sophisticated hackers trying to enter your website.</td>
                    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
                </tr> 
				<tr class="mo_wpns_table_row_layout">
                    <td class="mo_wpns_table_col1_layout">Constant Rules updates</td>
                    <td class="mo_wpns_table_col2_layout">Get latest updated rules for researched by miniOrange team constantly working to add them and secure them.</td>
                    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
                </tr>
                <tr class="mo_wpns_table_row_layout">
                    <td class="mo_wpns_table_col1_layout">Realtime IP Blocking</td>
                    <td class="mo_wpns_table_col2_layout">Realtime Blocking will stop the attack from one website to be performed again on another website by taking advantage of other servers on the miniOrange network.</td>
                    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
                </tr>
				<tr class="mo_wpns_table_row_layout">
                    <td class="mo_wpns_table_col1_layout">Advanced Rate Limiting/ DOS Protection</td>
                    <td class="mo_wpns_table_col2_layout">Rate limit based on specific pages and not complete site. Protecting important pages and sections of your site.</td>
                    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
                </tr>
                <tr class="mo_wpns_table_row_layout">
                    <td class="mo_wpns_table_col1_layout">IP Reputation</td>
                    <td class="mo_wpns_table_col2_layout">Every website has a reputation which gives credibility. If your repuation is down your website ranking is affected. Miniorange informs you if you have bad reputation.</td>
                    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
                </tr>
				<tr class="mo_wpns_table_row_layout">
                    <td class="mo_wpns_table_col1_layout">Crawlers and Bot detection</td>
                    <td class="mo_wpns_table_col2_layout">Identify the fake crawlers on your site and ban them. Only allow crawlers like Google and Facebook to crawl your website.</td>
                    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
                </tr>
				<tr class="mo_wpns_table_row_layout">
                    <td class="mo_wpns_table_col1_layout">Country Blocking</td>
                    <td class="mo_wpns_table_col2_layout">You can block the request coming from countries like Russia, Brazil, China and others.</td>
                    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
                </tr>
                <tr class="mo_wpns_table_row_layout">
                    <td class="mo_wpns_table_col1_layout">Advanced IP Lookup</td>
                    <td class="mo_wpns_table_col2_layout">Get complete details about the IP like ISP, IP Proxy or not and so on.</td>
                    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
                </tr>
				<tr class="mo_wpns_table_row_layout">
                    <td class="mo_wpns_table_col1_layout">Advanced Report and Analysis with notification</td>
                    <td class="mo_wpns_table_col2_layout">Get notified about detailed report. Also, get analysis about the traffic and attacks on the website.</td>
                    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
                </tr>
            </table>
				</div>
		</div>';

echo '			<div class="mo_wpns_small_layout" id="mo_login">
			
				<h1>Login and Spam Protection</h1>
				Price starting form<br><br>
				<center>
				<div id="mo_login_align_price" class="mo_align_pricing">
					<table >
                        <tr style="text-align: center;">
                            <th><h2>No. of Sites</h2></th>
                            <th><h2>Total</h2></th>
                        </tr>
                        <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">1 site   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail"> $15 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
                        <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">5 sites   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail"> $35 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
                         <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">10 sites   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail"> $60 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
               </table>
				</div>
				</center>
				<a target="_blank" class="button button-primary button-large" onclick="wpns_upgrade(\'wp_security_login_and_spam_plan\')" >Upgrade Now</a>
				<a id="mo_login_detail" class="button button-primary button-large mo_wpns_collapsible" onclick="expand(this,\'mo_login\')"data-active="false">Details >></a>
				<div class="mo_wpns_content" id="hide_login">

				<hr class="mo_wpns_line">
				<table class="mo_wpns_settings_table">
					  <tr class="mo_wpns_table_row_layout">
					    <td class="mo_wpns_table_free_text_layout">
					    	<a id="login_id" class=" mo_wpns_button1 mo_wpns_collapsible mo_wpns_free_feature_button">All Free Features ⮞</a>
					   		
						   <div class="mo_wpns_login_free">
							   <table class="mo_wpns_settings_table">
									
								   <tr class="mo_wpns_table_row_layout mo_wpns_login_free">
								    <td class="mo_wpns_table_free_col1_layout">Limit login Attempts</td>
								    <td class="mo_wpns_table_free_col2_layout">Limit the number of times a user can try to log in.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
								  <tr class="mo_wpns_table_row_layout mo_wpns_login_free">
								    <td class="mo_wpns_table_free_col1_layout">Enforce Strong Password</td>
								    <td class="mo_wpns_table_free_col2_layout">We check for the passwords of your users against their strength. This helps to enhance security for their accounts as simple passwords can be phished or guessed easily.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
								   <tr class="mo_wpns_table_row_layout mo_wpns_login_free">
								    <td class="mo_wpns_table_free_col1_layout">CAPTCHA on login</td>
								    <td class="mo_wpns_table_free_col2_layout">Different types of CAPTCHA prevents bots from executing the automated scripts that appear in brute force attacks, while still being easy for a human to pass by.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
								  <tr class="mo_wpns_table_row_layout mo_wpns_login_free">
								    <td class="mo_wpns_table_free_col1_layout">SPAM Content and Comment Protection</td>
								    <td class="mo_wpns_table_free_col2_layout">We check if your website is generating SPAM to protect you website getting blocked by search engines. Also we help you to filter comments for malware and phishing urls.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
								  <tr class="mo_wpns_table_row_layout mo_wpns_login_free">
								    <td class="mo_wpns_table_free_col1_layout">Two Factor Authentication</td>
								    <td class="mo_wpns_table_free_col2_layout">Two Factor authentication adds a second layer of security to your accounts. We support QR code, OTP over SMS and Email, Push, Soft token and many more.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
							   </table>
						   </div>
					    </td>
					  </tr>
				</table>

				<hr class=mo_wpns_line>
					<table class="mo_wpns_settings_table">
						<tr class="mo_wpns_table_row_layout">
							<td class="" colspan=3>
							<a class="mo_wpns_premium_feature" style="text-align:left;">All Premium Features ⮟</a>
							</td>
						</tr>

					  <tr class="mo_wpns_table_row_layout">
					    <td class="mo_wpns_table_col1_layout">Blocking time period</td>
					    <td class="mo_wpns_table_col2_layout">You can block IPs either temporarily for time period or permenantly.</td>
					    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					  </tr>
					  <tr class="mo_wpns_table_row_layout">
					    <td class="mo_wpns_table_col1_layout">Common Password Detection</td>
					    <td class="mo_wpns_table_col2_layout">We maintain a database of constantly updating common passwords. We will prevent users from having common passwords that lead to access to hackers. </td>
					    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					  </tr>
					  <tr class="mo_wpns_table_row_layout">
					    <td class="mo_wpns_table_col1_layout">Advanced CAPTHCA</td>
					    <td class="mo_wpns_table_col2_layout">We have Math, word and Google Recaptcha on posts, comments, login and Registration. </td>
					    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					  </tr>
					  <tr class="mo_wpns_table_row_layout">
					    <td class="mo_wpns_table_col1_layout">Two Factor Authentication</td>
					    <td class="mo_wpns_table_col2_layout">We have Auto User Registration for 2fa, Remember Device, 10+ Authentication methods and many other features.</td>
					    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					  </tr>
					  <tr class="mo_wpns_table_row_layout">
					    <td class="mo_wpns_table_col1_layout">Spam Protection and Honeypot</td>
					    <td class="mo_wpns_table_col2_layout">Honeypot on login, registration, comments and posts to identify IPs. CAPTHCA on posts to protect spam.</td>
					    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					  </tr>
					  
					</table>
				</div>
			</div>
';
echo '			<div class="mo_wpns_small_layout" id="mo_scan">
				<h1>Malware Scanner</h1>
				Price starting form<br><br>
				<center>
				<div id="mo_scan_align_price" class="mo_align_pricing">
					<table >
                        <tr style="text-align: center;">
                            <th><h2>No. of Sites</h2></th>
                            <th><h2>Total</h2></th>
                        </tr>
                        <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">1 site   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail">  $15 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
                        <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">5 sites   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail">$35 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
                         <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">10 sites   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail">$60 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
               </table>
				</div>
				</center>
				<a target="_blank" class="button button-primary button-large" onclick="wpns_upgrade(\'wp_security_malware_plan\')" >Upgrade Now</a>
				<a id="mo_scan_detail" class="button button-primary button-large mo_wpns_collapsible" onclick="expand(this,\'mo_scan\')"data-active="false">Details >></a>
				<div class="mo_wpns_content" id="hide_scan">

				<hr class="mo_wpns_line">
				<table class="mo_wpns_settings_table">
					  <tr class="mo_wpns_table_row_layout">
					    <td  class="mo_wpns_table_free_text_layout">
					    	<a id="scan_id" class=" mo_wpns_button1 mo_wpns_collapsible mo_wpns_free_feature_button">All Free Features ⮞</a>
					   		
						   <div class="mo_wpns_scan_free">
							   <table class="mo_wpns_settings_table">
									

								   <tr class="mo_wpns_table_row_layout mo_wpns_scan_free">
								    <td class="mo_wpns_table_free_col1_layout">Repository Version Comparison</td>
								    <td class="mo_wpns_table_free_col2_layout">We check if you have the latest and correct version plugin which has no known issues.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
								  <tr class="mo_wpns_table_row_layout mo_wpns_scan_free">
								    <td class="mo_wpns_table_free_col1_layout">Detect any changes in the files</td>
								    <td class="mo_wpns_table_free_col2_layout">Any changes in the files will be detected and you will be notified.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
								  <tr class="mo_wpns_table_row_layout mo_wpns_scan_free">
								    <td class="mo_wpns_table_free_col1_layout">Malware Detection</td>
								    <td class="mo_wpns_table_free_col2_layout">Detect malware in the files with signatures.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
							   </table>
						   </div>
					    </td>
					  </tr>
				</table>

				<hr class=mo_wpns_line>
					<table class="mo_wpns_settings_table">
				
					 <tr class="mo_wpns_table_row_layout">
										<td class="" colspan=3>
										<a class="mo_wpns_premium_feature" style="text-align:left;">All Premium Features ⮟</a>
										</td>
					</tr>
					  <tr class="mo_wpns_table_row_layout">
			            <td class="mo_wpns_table_col1_layout">Advanced Signatures for Malware Detection</td>
			            <td class="mo_wpns_table_col2_layout">miniOrange has it own premium signatures used to detect more advanced malwares in the files</td>
			            <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
			            </tr>
			            <tr class="mo_wpns_table_row_layout">
			                <td class="mo_wpns_table_col1_layout">Whitelist URLs</td>
			                <td class="mo_wpns_table_col2_layout">Sometimes you have some URLs which are not known by the plugin but are harmless. You can whitelist the URLs so that they are not marked as spam</td>
			                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
			            </tr>
			            <tr class="mo_wpns_table_row_layout">
			                <td class="mo_wpns_table_col1_layout">Blacklisted Domains</td>
			                <td class="mo_wpns_table_col2_layout">We detect any blacklisted domain used in your wordpress so that your site does not get bad reputation</td>
			                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
			            </tr>
			            <tr class="mo_wpns_table_row_layout">
			                <td class="mo_wpns_table_col1_layout">Custom Signatures</td>
			                <td class="mo_wpns_table_col2_layout">If you want any particular string or code to be flagged, we can provide it as a custom signature just for you.</td>
			                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
			            </tr>
			            <tr class="mo_wpns_table_row_layout">
			                <td class="mo_wpns_table_col1_layout">SQL Injection Check</td>
			                <td class="mo_wpns_table_col2_layout">Checks for injected SQL queries which can harm your database and injected shell scripts which can harm your server by executing any commands.</td>
			                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
			            </tr>
			            <tr class="mo_wpns_table_row_layout">
			                <td class="mo_wpns_table_col1_layout">Remote File Inclusion Check</td>
			                <td class="mo_wpns_table_col2_layout">Inclusion of remote files can be harmful as code return in remote files will be executed on your server.</td>
			                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
			            </tr>
			            <tr class="mo_wpns_table_row_layout">
			                <td class="mo_wpns_table_col1_layout">External link Detection</td>
			                <td class="mo_wpns_table_col2_layout">We check for any backlinks or spam links inserted in your website. Spam links can cause a bad reputation for your website.</td>
			                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
			            </tr>
			            <tr class="mo_wpns_table_row_layout">
			                <td class="mo_wpns_table_col1_layout">Action On Malicious Files</td>
			                <td class="mo_wpns_table_col2_layout">You can View, Delete, Ignore or Repair the files flagged as malicious.</td>
			                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
			            </tr>
			            <tr class="mo_wpns_table_row_layout">
			                <td class="mo_wpns_table_col1_layout">Detection Of Trojans and Backdoors</td>
			                <td class="mo_wpns_table_col2_layout">Along with vulnerable code, you will be able to detect Trojans as well as backdoor code snippets.</td>
			                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
			            </tr>
					</table>
				</div>
			</div>
';		
echo '			<div class="mo_wpns_small_layout" id="mo_backup">
				<h1>Encrypted Backup</h1>
				Price starting form<br><br>
				<center>
				<div id="mo_backup_align_price" class="mo_align_pricing">
					<table >
                        <tr style="text-align: center;">
                            <th><h2>No. of Sites</h2></th>
                            <th><h2>Total</h2></th>
                        </tr>
                        <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">1 site   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail">$30 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
                        <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">5 sites   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail">$50 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
                         <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">10 sites   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail">$70 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
                </table>
				</div>
				</center>
				<a target="_blank" class="button button-primary button-large" onclick="wpns_upgrade(\'wp_security_backup_plan\')" >Upgrade Now</a>
				<a id="mo_backup_detail" class="button button-primary button-large mo_wpns_collapsible" onclick="expand(this,\'mo_backup\')"data-active="false">Details >></a>
				<div class="mo_wpns_content" id="hide_backup">

				<hr class="mo_wpns_line">
				<table class="mo_wpns_settings_table">
					  <tr class="mo_wpns_table_row_layout">
					    <td  class="mo_wpns_table_free_text_layout">
					    	<a id="backup_id" class=" mo_wpns_button1 mo_wpns_collapsible mo_wpns_free_feature_button">All Free Features ⮞</a>
					   		
						   <div class="mo_wpns_backup_free">
							   <table class="mo_wpns_settings_table">
								   <tr class="mo_wpns_table_row_layout mo_wpns_backup_free">
								    <td class="mo_wpns_table_free_col1_layout">Database Backup</td>
								    <td class="mo_wpns_table_free_col2_layout">Take backup of your latest database so that you have copy of the latest database if you need to restore.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
								  <tr class="mo_wpns_table_row_layout mo_wpns_backup_free">
								    <td class="mo_wpns_table_free_col1_layout">File Backup</td>
								    <td class="mo_wpns_table_free_col2_layout">Due to some unavoidable events you might need to restore complete or partial website. You can do this with our file backup.</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
							   </table>
						   </div>
					    </td>
					  </tr>
				</table>

				<hr class=mo_wpns_line>
					<table class="mo_wpns_settings_table">
						<tr class="mo_wpns_table_row_layout">
										<td class="" colspan=3>
										<a class="mo_wpns_premium_feature" style="text-align:left;">All Premium Features ⮟</a>
										</td>
									</tr>
					   <tr class="mo_wpns_table_row_layout">
					                <td class="mo_wpns_table_col1_layout">Cloud Backup</td>
					                <td class="mo_wpns_table_col2_layout">Storing the files on remote location is more secure. We provide backups on cloud storage like
										<ul>
									    <li>Google Drive</li>
									    <li>Dropbox</li>
									    <li>Amazon S3 and Many more</li>
									   </ul></td>
					                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					    </tr>
					            <tr class="mo_wpns_table_row_layout">
					                <td class="mo_wpns_table_col1_layout">Encrypted Backup</td>
					                <td class="mo_wpns_table_col2_layout">The backups are encrypted and can only decrypted via key provided to you during encryption.</td>
					                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					            </tr>
					  <tr class="mo_wpns_table_row_layout">
					                <td class="mo_wpns_table_col1_layout">Security</td>
					                <td class="mo_wpns_table_col2_layout">
									<ul>
									    <li>The files are password protected so that only person authorized can check the backup</li>
									    <li>Database backup can be encrypted for security</li>
									    
									   </ul></td>
					                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					            </tr>
					<tr class="mo_wpns_table_row_layout">
					                <td class="mo_wpns_table_col1_layout">Schedule Backup</td>
					                <td class="mo_wpns_table_col2_layout">
									<ul>
									    <li>Support both manual and automated (scheduled) backups</li>
									    <li>Backups Files and Database on separate schedule</li>
									    
									   </ul>
									</td>
					                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					            </tr>
					   </tr>

					  <tr class="mo_wpns_table_row_layout">
					                <td class="mo_wpns_table_col1_layout">Restore and Migration</td>
					                <td class="mo_wpns_table_col2_layout">Simple one-click restore and migration</td>
					                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					            </tr>
						    <tr class="mo_wpns_table_row_layout mo_wpns_backup_free">
								    <td class="mo_wpns_table_free_col1_layout">Password Protected Zip files</td>
								    <td class="mo_wpns_table_free_col2_layout">The files are password protected so that only person authorized can check the backup</td>
								    <td class="mo_wpns_table_free_col3_layout"><b>FREE</b></td>
								  </tr>
					<tr class="mo_wpns_table_row_layout">
					                <td class="mo_wpns_table_col1_layout">Reporting</td>
					                <td class="mo_wpns_table_col2_layout">Generate sophisticated report with notification and alert over email</td>
					                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					            </tr>
					   </tr>
					  <tr class="mo_wpns_table_row_layout">
					                <td class="mo_wpns_table_col1_layout">Scanning</td>
					                <td class="mo_wpns_table_col2_layout">Before creating backup and restore backup, we provide the facility to scan all files so that your backup is free from malicious content.</td>
					                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					            </tr>
					<tr class="mo_wpns_table_row_layout">
					                <td class="mo_wpns_table_col1_layout">Automatic Delete</td>
					                <td class="mo_wpns_table_col2_layout">Support automatic delete backup </td>
					                <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					            </tr>
					</table>
				</div>
			</div>
	';


	echo '<style>
            .mo_pricing_text{
                color: black;
                font-weight: 400;
                font-size: 22px;
                line-height: 25px;
            }
            .mo_pricing_alignment{
            padding-left: 5%;
            }
          </style>		
		<div class="mo_wpns_small_layout mo_wpns_all_in_one_layout mo_all_in_one_pricing" id="mo_all_in_one">
				<h1>All in one</h1>
				Price starting form<br><br>
				<center>
				<div id="mo_all_in_one_align_price" class="mo_align_pricing">
				    <table >
                        <tr style="text-align: center;">
                            <th><h2>No. of Sites</h2></th>
                            <th><h2>Total</h2></th>
                        </tr>
                        <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">1 site   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail"> $95 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
                        <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">5 sites   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail">$180 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
                         <tr>
                        	<td>
	                        	<span class="mo_wpns_products-dollar-detail">10 sites   </span>
								
                        	</td>
                        	<td>
								<span class="mo_wpns_products-dollar-detail">$290 / year</span>
                        	</td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
                </table>
				</div>
				</center>
                    <a target="_blank" class="button button-primary button-large" onclick="wpns_upgrade(\'wp_security_premium_plan\')">Upgrade Now</a>
				<a id="mo_all_in_one_detail" class="button button-primary button-large mo_wpns_collapsible" onclick="expand(this,\'mo_all_in_one\')"data-active="false">Details >></a>
				<div class="mo_wpns_content" id="hide_all_in_one">

			
					<table class="mo_wpns_settings_table">
				
					  <tr class="mo_wpns_table_row_layout">
					    <td class="mo_wpns_table_col1_layout">Web Application Firewall</td>
					    <td class="mo_wpns_table_col2_layout">With MiniOrange WAF protects your sites from different attaks like SQL Injection, Cross-site Scripting, DOS Attacks and many more.</td>
					    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					  </tr>
					  <tr class="mo_wpns_table_row_layout">
					    <td class="mo_wpns_table_col1_layout">Login and Spam Protection</td>
					    <td class="mo_wpns_table_col2_layout">With Features like Two Factor, CAPTCHA, Limit login and others miniOrange WAF protects your users users from attack and Users from SPAM.</td>
					    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					  </tr>
					  <tr class="mo_wpns_table_row_layout">
					    <td class="mo_wpns_table_col1_layout">Malware Scanner</td>
					    <td class="mo_wpns_table_col2_layout">With miniOrange Advance Signatures, Repository Version Comparison, Detecting any file change and others miniOrange finds any issues in your Wordpress files.</td>
					    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					  </tr>
					  <tr class="mo_wpns_table_row_layout">
					    <td class="mo_wpns_table_col1_layout">Encrypted Backup and Recovery</td>
					    <td class="mo_wpns_table_col2_layout">By taking encrypted backup of your website and saving it on Cloud we can restore the backup any time with One-Click restore option </td>
					    <td class="mo_wpns_table_col3_layout"><b>PREMIUM</b></td>
					  </tr>
					</table>
				</div>
			</div>
	</div>

	
';
echo '<form class="plan_redirect" id="wpns_loginform"
                  action="https://login.xecurify.com/moas/login"
                  target="_blank" method="post" style="display:none;">
                <input type="email" name="username" value="'. get_option( "mo_wpns_admin_email" ).'"/>
                <input type="text" name="redirectUrl"
                       value="https://login.xecurify.com/moas/initializepayment"/>
                <input type="text" name="requestOrigin" id="requestOrigin"/>
            </form>
            
            <form class="registration_redirect" id="wpns_registration_form"
                  action="'.$profile_url.'"
                 method="post" style="display:none;">
                
            </form>
            
            ';

$iscustomervalid=Mo_lla_MoWpnsUtility::icr();
echo
    '
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
    var waf=0 , login=0 ,scan=0,backup=0,all=0;
    var iscustomervalid = '.$iscustomervalid.';
    function wpns_upgrade(plan){
    
        if(iscustomervalid){
        jQuery(\'#requestOrigin\').val(plan);
        jQuery(\'#wpns_loginform\').submit();//wpns_registration_form
        }else{
        jQuery(\'#wpns_registration_form\').submit();//wpns_registration_form
        }
        
    }
    function expand(e,id)
    {
    	var waf_price = document.querySelector("#" + id);
	    waf_price.style.transition = "all 2s";
	    var login_flag=0,waf_flag=0,scan_flag=0,backup_flag=0,all_flag=0;
	    var waf_flag=0;
	    switch (id)
	    {	
	    	case "mo_waf":
					    	if(waf==0)
					    	{

					    		waf_price.classList.remove("mo_wpns_small_layout");
						        waf_price.classList.add("mo_wpns_pricing_enlarge_layout");

					    		document.querySelector("#mo_waf_align_price").classList.remove("mo_align_pricing");
					    		document.querySelector("#mo_waf_align_price").classList.add("mo_align_pricing_enlarge");
					    		
								
						        $("#hide_login").hide(1000);
						        $("#mo_login").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_login").addClass("mo_wpns_small_layout");
						        $("#mo_login_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_scan").hide(1000);
						        $("#mo_scan").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_scan").addClass("mo_wpns_small_layout");
						        $("#mo_scan_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_backup").hide(1000);
						        $("#mo_backup").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_backup").addClass("mo_wpns_small_layout");
						        $("#mo_backup_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_all_in_one").hide(1000);
						        $("#mo_all_in_one").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_all_in_one").addClass("mo_wpns_small_layout");
						        $("#mo_all_in_one_align_price").removeClass("mo_align_pricing_enlarge");
						        $("#mo_all_in_one_align_price").addClass("mo_align_pricing");

								document.getElementById("mo_waf_detail").innerHTML = "Minimize <<";
								
								document.getElementById("mo_login_detail").innerHTML = "Details >>";
								document.getElementById("mo_scan_detail").innerHTML = "Details >>";
								document.getElementById("mo_backup_detail").innerHTML = "Details >>";
								document.getElementById("mo_all_in_one_detail").innerHTML = "Details >>";
								
								$(".mo_wpns_waf_free").hide();
						        $("#waf_id").click(function()
								{	
									if(waf_flag==0)
									{
									    $(".mo_wpns_waf_free").show();
										document.getElementById("waf_id").innerHTML = "All Free Features ⮟"; 
									    waf_flag=1;
									}
									else
									{
										$(".mo_wpns_waf_free").hide();
										waf_flag=0;
										
												document.getElementById("waf_id").innerHTML = "All Free Features ⮞"; 
									}
								});
								 $("#waf_id1").click(function()
								{	
									if(waf_flag1==0)
									{
									    $(".mo_wpns_waf_free").show();
									    waf_flag1=1;
									}
									else
									{
										$(".mo_wpns_waf_free").hide();
										waf_flag1=0;
									}
								});

						        waf=1;login=0;scan=0;backup=0;all=0;
					    	}
					    	else
					    	{
					    		waf_price.classList.remove("mo_wpns_pricing_enlarge_layout");
						        waf_price.classList.add("mo_wpns_small_layout");
								document.querySelector("#mo_waf_align_price").classList.remove("mo_align_pricing_enlarge");
					    		document.querySelector("#mo_waf_align_price").classList.add("mo_align_pricing");
						        waf=0;login=0;scan=0;backup=0;all=0;
								document.getElementById("mo_waf_detail").innerHTML = "Details >>"; 
								
					    	}
			break;

			case "mo_login":
					    	if(login==0)
					    	{
					    		waf_price.classList.remove("mo_wpns_small_layout");
						        waf_price.classList.add("mo_wpns_pricing_enlarge_layout");
								
								document.querySelector("#mo_login_align_price").classList.remove("mo_align_pricing");
					    		document.querySelector("#mo_login_align_price").classList.add("mo_align_pricing_enlarge");
								
						        $("#hide_waf").hide(1000);
						        $("#mo_waf").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_waf").addClass("mo_wpns_small_layout");
						        $("#mo_waf_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_scan").hide(1000);
						        $("#mo_scan").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_scan").addClass("mo_wpns_small_layout");
						        $("#mo_scan_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_backup").hide(1000);
						        $("#mo_backup").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_backup").addClass("mo_wpns_small_layout");
						        $("#mo_backup_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_all_in_one").hide(1000);
						        $("#mo_all_in_one").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_all_in_one").addClass("mo_wpns_small_layout");
						        $("#mo_all_in_one_align_price").removeClass("mo_align_pricing_enlarge");
						        $("#mo_all_in_one_align_price").addClass("mo_align_pricing");

						        document.getElementById("mo_login_detail").innerHTML = "Minimize <<"; 
								
								document.getElementById("mo_waf_detail").innerHTML = "Details >>";
								document.getElementById("mo_scan_detail").innerHTML = "Details >>";
								document.getElementById("mo_backup_detail").innerHTML = "Details >>";
								document.getElementById("mo_all_in_one_detail").innerHTML = "Details >>";
								


									$(".mo_wpns_login_free").hide();

									$("#login_id").click(function()
									{	
											if(login_flag==0)
										     {
										     	$(".mo_wpns_login_free").show();
										     	login_flag=1;
												document.getElementById("login_id").innerHTML = "All Free Features ⮟"; 
												
										 	}
										   	else
										   	{
										   		$(".mo_wpns_login_free").hide();
												document.getElementById("login_id").innerHTML = "All Free Features ⮞"; 
										   		login_flag=0;
										   	}
										
										
									});
								

						        login=1;waf=0;scan=0;backup=0;all=0;
					    	}
					    	else
					    	{
					    		waf_price.classList.remove("mo_wpns_pricing_enlarge_layout");
						        waf_price.classList.add("mo_wpns_small_layout");

								document.querySelector("#mo_login_align_price").classList.remove("mo_align_pricing_enlarge");
					    		document.querySelector("#mo_login_align_price").classList.add("mo_align_pricing");
								
						        login=0;waf=0;scan=0;backup=0;all=0;
								document.getElementById("mo_login_detail").innerHTML = "Details >>";
					    	}
			break;

			case "mo_scan":
					    	if(scan==0)
					    	{
					    		waf_price.classList.remove("mo_wpns_small_layout");
						        waf_price.classList.add("mo_wpns_pricing_enlarge_layout");
								
								document.querySelector("#mo_scan_align_price").classList.remove("mo_align_pricing");
					    		document.querySelector("#mo_scan_align_price").classList.add("mo_align_pricing_enlarge");
								
						        $("#hide_waf").hide(1000);
						        $("#mo_waf").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_waf").addClass("mo_wpns_small_layout");
						        $("#mo_waf_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_login").hide(1000);
						        $("#mo_login").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_login").addClass("mo_wpns_small_layout");
						        $("#mo_login_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_backup").hide(1000);
						        $("#mo_backup").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_backup").addClass("mo_wpns_small_layout");
						        $("#mo_backup_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_all_in_one").hide(1000);
						        $("#mo_all_in_one").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_all_in_one").addClass("mo_wpns_small_layout");
						        $("#mo_all_in_one_align_price").removeClass("mo_align_pricing_enlarge");
						        $("#mo_all_in_one_align_price").addClass("mo_align_pricing");
								document.getElementById("mo_scan_detail").innerHTML = "Minimize <<"; 
								
								document.getElementById("mo_waf_detail").innerHTML = "Details >>";
								document.getElementById("mo_login_detail").innerHTML = "Details >>";
								document.getElementById("mo_backup_detail").innerHTML = "Details >>";
								document.getElementById("mo_all_in_one_detail").innerHTML = "Details >>";
								
						        $(".mo_wpns_scan_free").hide();
								$("#scan_id").click(function()
								{	
									if(scan_flag==0)
									{
									    $(".mo_wpns_scan_free").show();
										document.getElementById("scan_id").innerHTML = "All Free Features ⮟"; 
												
									    scan_flag=1;
									}
									else
									{
										$(".mo_wpns_scan_free").hide();
										scan_flag=0;
										document.getElementById("scan_id").innerHTML = "All Free Features ⮞"; 
												
									}
								});

						        scan=1;login=0;waf=0;backup=0;all=0;
					    	}
					    	else
					    	{
					    		waf_price.classList.remove("mo_wpns_pricing_enlarge_layout");
						        waf_price.classList.add("mo_wpns_small_layout");
								document.querySelector("#mo_scan_align_price").classList.remove("mo_align_pricing_enlarge");
					    		document.querySelector("#mo_scan_align_price").classList.add("mo_align_pricing");
						        scan=0;login=0;waf=0;backup=0;all=0;
								document.getElementById("mo_scan_detail").innerHTML = "Details >>";
					    	}
			break;

			case "mo_backup":
					    	if(backup==0)
					    	{
					    		waf_price.classList.remove("mo_wpns_small_layout");
						        waf_price.classList.add("mo_wpns_pricing_enlarge_layout");
								
								document.querySelector("#mo_backup_align_price").classList.remove("mo_align_pricing");
					    		document.querySelector("#mo_backup_align_price").classList.add("mo_align_pricing_enlarge");
								
						        $("#hide_waf").hide(1000);
						        $("#mo_waf").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_waf").addClass("mo_wpns_small_layout");
						        $("#mo_waf_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_login").hide(1000);
						        $("#mo_login").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_login").addClass("mo_wpns_small_layout");
						        $("#mo_login_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_scan").hide(1000);
						        $("#mo_scan").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_scan").addClass("mo_wpns_small_layout");
						        $("#mo_scan_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_all_in_one").hide(1000);
						        $("#mo_all_in_one").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_all_in_one").addClass("mo_wpns_small_layout");
						        $("#mo_all_in_one_align_price").removeClass("mo_align_pricing_enlarge");
						        
						        $("#mo_all_in_one_align_price").addClass("mo_align_pricing");		        
								document.getElementById("mo_backup_detail").innerHTML = "Minimize <<"; 
								
								document.getElementById("mo_waf_detail").innerHTML = "Details >>";
								document.getElementById("mo_login_detail").innerHTML = "Details >>";
								document.getElementById("mo_scan_detail").innerHTML = "Details >>";
								document.getElementById("mo_all_in_one_detail").innerHTML = "Details >>";
								
						        $(".mo_wpns_backup_free").hide();
								$("#backup_id").click(function()
								{	
									if(backup_flag==0)
									{
									    $(".mo_wpns_backup_free").show();
									    backup_flag=1;
										document.getElementById("backup_id").innerHTML = "All Free Features ⮟"; 
												
									}
									else
									{
										$(".mo_wpns_backup_free").hide();
										backup_flag=0;
										document.getElementById("backup_id").innerHTML = "All Free Features ⮞"; 
									}
								});

						        backup=1;scan=0;login=0;waf=0;all=0;
					    	}
					    	else
					    	{
					    		waf_price.classList.remove("mo_wpns_pricing_enlarge_layout");
						        waf_price.classList.add("mo_wpns_small_layout");
								
								document.querySelector("#mo_backup_align_price").classList.remove("mo_align_pricing_enlarge");
					    		document.querySelector("#mo_backup_align_price").classList.add("mo_align_pricing");
								
								document.getElementById("mo_backup_detail").innerHTML = "Details >>";
						        backup=0;scan=0;login=0;waf=0;all=0;
					    	}
			break;

			case "mo_all_in_one":
					    	if(all==0)
					    	{
					    		waf_price.classList.remove("mo_all_in_one_pricing");

					    		waf_price.classList.remove("mo_wpns_small_layout");
						        waf_price.classList.add("mo_wpns_pricing_enlarge_layout");
								
								document.querySelector("#mo_all_in_one_align_price").classList.remove("mo_align_pricing");
					    		document.querySelector("#mo_all_in_one_align_price").classList.add("mo_align_pricing_enlarge");

						        $("#hide_waf").hide(1000);
						        $("#mo_waf").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_waf").addClass("mo_wpns_small_layout");
						        $("#mo_waf_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_login").hide(1000);
						        $("#mo_login").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_login").addClass("mo_wpns_small_layout");
						        $("#mo_login_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_scan").hide(1000);
						        $("#mo_scan").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_scan").addClass("mo_wpns_small_layout");
						        $("#mo_scan_align_price").removeClass("mo_align_pricing_enlarge");

						        $("#hide_backup").hide(1000);
						        $("#mo_backup").removeClass("mo_wpns_pricing_enlarge_layout");
						        $("#mo_backup").addClass("mo_wpns_small_layout");
						        $("#mo_backup_align_price").removeClass("mo_align_pricing_enlarge");
								document.getElementById("mo_all_in_one_detail").innerHTML = "Minimize <<";
								
								document.getElementById("mo_waf_detail").innerHTML = "Details >>";
								document.getElementById("mo_login_detail").innerHTML = "Details >>";
								document.getElementById("mo_scan_detail").innerHTML = "Details >>";
								
								
						        $(".mo_wpns_all_in_one_free").hide();
								$("#all_id").click(function()
								{	
									if(all_flag==0)
									{
									    $(".mo_wpns_all_in_one_free").show();
									    all_flag=1;
									}
									else
									{
										$(".mo_wpns_all_in_one_free").hide();
										all_flag=0;
									}
								});

						        all=1;backup=0;scan=0;login=0;waf=0;
					    	}
					    	else
					    	{
					    		waf_price.classList.add("mo_all_in_one_pricing");

					    		waf_price.classList.remove("mo_wpns_pricing_enlarge_layout");
						        waf_price.classList.add("mo_wpns_small_layout");
								
								document.querySelector("#mo_all_in_one_align_price").classList.remove("mo_align_pricing_enlarge");
					    		document.querySelector("#mo_all_in_one_align_price").classList.add("mo_align_pricing");
						        all=0;backup=0;scan=0;login=0;waf=0;
								document.getElementById("mo_all_in_one_detail").innerHTML = "Details >>";
								
					    	}
			break;
	    }
    	    	
    }

        
    </script>';
	    



?>
<script>
var coll = document.getElementsByClassName("mo_wpns_collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.display === "block") {
      content.style.display = "none";
    } else {
      content.style.display = "block";
    }
  });
}
</script>