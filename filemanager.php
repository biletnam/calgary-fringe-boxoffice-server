<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>
	apnscp - File Manager</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="Apis Networks hosting control panel" name="description" />
<link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" href="http://getap.is/s/cp/css/reset.css?1249726254" type="text/css" /><link rel="stylesheet" href="http://getap.is/s/cp/css/core.css?1306430790" type="text/css" /><link rel="stylesheet" href="/apps/filemanager/FileManager.css?1301164929" type="text/css" /><link rel="stylesheet" href="http://getap.is/s/cp/css/filetree.css?1306430790" type="text/css" /><link rel="stylesheet" href="http://getap.is/s/cp/css/modal.css?1263970607" type="text/css" /><link rel="stylesheet" href="http://getap.is/s/cp/css/upload.css?1301164932" type="text/css" /><style type="text/css">
	#ui-storage-gauge .ui-gauge-used {
		width:76%;
	}

	#ui-bandwidth-gauge .ui-gauge-used {
		width:23%;
	}
</style>
<script type="text/javascript" src="http://getap.is/s/cp/js/jquery.js?1303842525"></script><script type="text/javascript" src="http://getap.is/s/cp/js/superfish.js?1263111184"></script><script type="text/javascript" src="http://getap.is/s/cp/js/bgiframe.js?1263111184"></script><script type="text/javascript" src="http://getap.is/s/cp/js/apnscp_init.js?1263111184"></script><script language="JavaScript" type="text/javascript">
//<![CDATA[
var session = {id: 'cbl7o260vomhijahgoi7pc14c3',
	user     : 'calgaryfringe',
	domain   : 'calgaryfringe.ca',
	appName  : 'File Manager',
	appId    : 'filemanager',
	debug    : 0};
$(document).ready(function() {$("body").removeClass("nojs").addClass("js");
				$("#ui-postback-more").click(function() {
					if ($(this).parent().next().hasClass("hide"))
						$(this).parent().next().hide().removeClass("hide");
					$(this).parent().next().slideToggle("medium");
					return false; });
				});//]]>
</script>

</head>
<body class="ff ff3 nojs">
<div id="page">
<div id="ui-transition"></div>
<div id="wrap">

	<!-- head -->
	<div id="ui-header">
		<h1 id="logo" class="title">
			Apis Networks
		</h1>
		<a href="/apps/dashboard" rel="dashboard" class="ui-fast-action" id="ui-fa-dashboard">Dashboard</a>
		<a href="mailto:matt%2Bfeedback@apisnetworks.com" rel="feedback" class="ui-fast-action" id="ui-fa-feedback">Feedback</a>
		<!-- <a href="#" rel="chuser" class="ui-fast-action" id="ui-fa-chuser">Change User</a> -->
		
					<a href="/apps/feedback" rel="testimonial" class="ui-fast-action" id="ui-fa-testimonial">Add Testimonial</a>
				
		<a href="/logout" rel="logout" class="ui-fast-action" id="ui-fa-logout">Logout</a>
		<span id="ui-fa-label" class="ui-fast-action"></span>
		<div id="ui-account-gauges">
			<div class="ui-gauge-cluster ui-gauge-warn"  id="ui-storage-cluster">
				<span id="ui-storage-label" class="ui-gauge-heading ui-gauge-label">Storage</span>
				<div id="ui-storage-gauge" class="ui-gauge">
					<div class="ui-gauge-used ui-gauge-slice"></div><div class="ui-gauge-free ui-gauge-slice"></div><div class="ui-gauge-slice ui-gauge-cap"></div>
				</div>
				<div class="ui-label-cluster ">
				<span class="ui-gauge-label ui-label-percentage">76%</span>
				<span class="ui-gauge-label ui-label-free">315 MB</span>
				</div>
			</div>
						<div class="ui-gauge-cluster ui-gauge-normal" id="ui-bandwidth-cluster">
				<span id="ui-bandwidth-label" class="ui-gauge-heading ui-gauge-label">Bandwidth</span>
				<div id="ui-bandwidth-gauge" class="ui-gauge">
					<div class="ui-gauge-used ui-gauge-slice"></div><div class="ui-gauge-free ui-gauge-slice"></div><div class="ui-gauge-slice ui-gauge-cap"></div>
				</div>
				<div class="ui-label-cluster">
					<span class="ui-gauge-label ui-label-percentage">23%</span>
					<span class="ui-gauge-label ui-label-free">23 GB</span>
				</div>
			</div>
						<a class="ui-refresh ui-gauge-cluster ui-gauge-label" id="ui-gauge-refresh">refresh</a>
		</div>
		<div id="ui-search">
			<form method="post" action="/apps/search" id="ui-search-form">
			<span class="ui-ajax-indicator"></span>
			<input type="text" name="q" id="ui-search-query" class="noq" value="Search" accesskey="s" />
			</form>
		</div>
	</div>
	<!-- body -->

	<div id="ui-content">
		<div id="ui-nav-glow">
		<div id="nav-container">
				<div id="quickmenu-container">
			<ul id="ui-menu" class="nav"><li class="ui-menu-category "><a href="#"><img src="/images/template/app-icons/account.png" class="png24" border="0" alt="Account"/>  Account</a><ul><li><a href="/apps/summary" target="_self" title="Summary">Summary</a></li><li><a href="/apps/changeinfo" target="_self" title="Change Information">Change Information</a></li><li><a href="/apps/loginhistory" target="_self" title="Login History">Login History</a></li><li><a href="/apps/feedback" target="_self" title="Add Testimonial">Add Testimonial</a></li><li><a href="/apps/billinghistory" target="_self" title="Billing History">Billing History</a></li><li><a href="/apps/changebilling" target="_self" title="Change Billing">Change Billing</a></li><li><a href="/apps/referrals" target="_self" title="Client Referrals">Client Referrals</a></li></ul></li><li class="ui-menu-category "><a href="#"><img src="/images/template/app-icons/users.png" class="png24" border="0" alt="Users"/>  Users</a><ul><li><a href="/apps/useradd" target="_self" title="Create User">Create User</a></li><li><a href="/apps/usermanage" target="_self" title="Manage Users">Manage Users</a></li><li><a href="/apps/userdefaults" target="_self" title="Set User Defaults">Set User Defaults</a></li></ul></li><li class="ui-menu-category "><a href="#"><img src="/images/template/app-icons/mail.png" class="png24" border="0" alt="Mail"/>  Mail</a><ul><li><a href="/apps/mailboxroutes" target="_self" title="Manage Mailboxes">Manage Mailboxes</a></li><li><a href="/apps/webmail" target="_self" title="Webmail">Webmail</a></li><li><a href="/apps/mailertable" target="_self" title="Mail Routing">Mail Routing</a></li><li><a href="/apps/majordomo" target="_self" title="Mailing Lists">Mailing Lists</a></li><li><a href="/apps/saconfig" target="_self" title="SpamAssassin Config">SpamAssassin Config</a></li><li><a href="/apps/vacation" target="_self" title="Vacation Responder">Vacation Responder</a></li></ul></li><li class="ui-menu-category ui-menu-active"><a href="#"><img src="/images/template/app-icons/files.png" class="png24" border="0" alt="Files"/>  Files</a><ul><li><a href="/apps/filemanager" target="_self" title="File Manager">File Manager</a></li><li><a href="/apps/diskbd" target="_self" title="Storage Usage">Storage Usage</a></li><li><a href="/apps/webdisk" target="_self" title="WebDisk">WebDisk</a></li></ul></li><li class="ui-menu-category "><a href="#"><img src="/images/template/app-icons/databases.png" class="png24" border="0" alt="Databases"/>  Databases</a><ul><li><a href="/apps/changemysql" target="_self" title="MySQL Manager">MySQL Manager</a></li><li><a href="/apps/phpmyadmin" target="_self" title="phpMyAdmin">phpMyAdmin</a></li><li><a href="/apps/mysqlbackup" target="_self" title="MySQL Backups">MySQL Backups</a></li></ul></li><li class="ui-menu-category "><a href="#"><img src="/images/template/app-icons/dns.png" class="png24" border="0" alt="DNS"/>  DNS</a><ul><li><a href="/apps/dns" target="_self" title="DNS Manager">DNS Manager</a></li><li><a href="/apps/domainmanager" target="_self" title="Addon Domains">Addon Domains</a></li><li><a href="/apps/spf" target="_self" title="SPF Setup">SPF Setup</a></li><li><a href="/apps/traceroute" target="_self" title="Traceroute">Traceroute</a></li><li><a href="/apps/whois" target="_self" title="Whois">Whois</a></li></ul></li><li class="ui-menu-category "><a href="#"><img src="/images/template/app-icons/web.png" class="png24" border="0" alt="Web"/>  Web</a><ul><li><a href="/apps/subdomains?mode=add" target="_self" title="Subdomains">Subdomains</a></li><li><a href="/apps/webdav" target="_self" title="WebDAV">WebDAV</a></li><li><a href="/apps/frontpage" target="_self" title="FrontPage Extensions">FrontPage Extensions</a></li></ul></li><li class="ui-menu-category "><a href="#"><img src="/images/template/app-icons/goodies.png" class="png24" border="0" alt="Dev"/>  Dev</a><ul><li><a href="/apps/prlanguages" target="_self" title="Code Frameworks">Code Frameworks</a></li><li><a href="/apps/verco" target="_self" title="Version Control">Version Control</a></li><li><a href="/apps/packman" target="_self" title="Package Manager">Package Manager</a></li><li><a href="/apps/soapkeys" target="_self" title="API Keys">API Keys</a></li><li><a href="/apps/crontab" target="_self" title="Crontab">Crontab</a></li></ul></li><li class="ui-menu-category "><a href="#"><img src="/images/template/app-icons/reports.png" class="png24" border="0" alt="Reports"/>  Reports</a><ul><li><a href="/apps/bandwidthbd" target="_self" title="Bandwidth Breakdown">Bandwidth Breakdown</a></li><li><a href="/apps/bandwidthstat" target="_self" title="Bandwidth Statistics">Bandwidth Statistics</a></li><li><a href="/apps/quotatracker" target="_self" title="Storage Tracker">Storage Tracker</a></li><li><a href="/apps/urchin" target="_self" title="Urchin">Urchin</a></li><li><a href="/apps/stats" target="_self" title="Server Info">Server Info</a></li><li><a href="/apps/logrotate" target="_self" title="Log Rotation">Log Rotation</a></li></ul></li><li class="ui-menu-category "><a href="#"><img src="/images/template/app-icons/help.png" class="png24" border="0" alt="Help"/>  Help</a><ul><li><a href="http://apisnetworks.com/resource_center" target="_self" title="Resource Center">Resource Center</a></li><li><a href="http://forums.apisnetworks.com/" target="_self" title="Forums">Forums</a></li><li><a href="http://guide.apisnetworks.com/" target="_self" title="Wiki">Wiki</a></li><li><a href="http://apnscp.com/phpdoc/" target="_self" title="API Docs">API Docs</a></li><li><a href="/apps/troubleticket" target="_self" title="Trouble Tickets">Trouble Tickets</a></li></ul></li></ul>		</div>
				<!-- application index -->
               	<div id="ui-breadcrumbs" class="ui-breadcrumbs">
       		<div class="ui-menu-border ui-menu-border-l"></div>
			<div class="ui-menu-border ui-menu-border-r"></div>
			<ul class="ui-breadcrumb-apps">
			<li class="ui-breadcrumb ui-app-active ui-app-first"><a href="/apps/filemanager" target="_self">File Manager</a></li><li class="ui-breadcrumb "><a href="/apps/diskbd" target="_self">Storage Usage</a></li><li class="ui-breadcrumb "><a href="/apps/webdisk" target="_self">WebDisk</a></li>			</ul>
			<div class="clear"></div>
		</div>
				</div>
		</div>
				<div class="clear">&nbsp;</div>
				<div class="ui-app-container" id="ui-app-container">
					<div id="ui-help-container">
				<h2 id="ui-overview" class="ui-overview title">Overview</h2>
				<a id="ui-wiki-link" class="ui-wiki-link" title="Access Wiki Entry" href="http://wiki.apisnetworks.com/index.php/File_Manager">Wiki</a>
									<div id="ui-help-blurb"><span class="ui-help-blurb-more ui-help-blurb">Manage files on the account, edit permissions, download remote files, 
		and extract compressed files.</span>

											<a href="#" class="ui-overview-link  ui-expandable ui-collapsed" id="ui-overview-link">more</a>
						<div class="ui-help hide" id="ui-help">
						<ul>
    <li>Click the directory name to access the directory</li>
    <li>Click the file name to download the file</li>
    <li>Click a folder or file icon to view/change related properties</li>
   </ul>
   <b>Note:</b> Files that are added to the clipboard can be copied or moved to the currently displayed directory.						</div>
											</div>
					<!--  end #ui-help-blurb -->
								</div>
					<div>&nbsp;</div>
					<div id="ui-postback" class="ui-postback ui-pb-msg-failed-parent ui-postback-success">
				<div id="ui-postback-gloss">
					<div id="ui-postback-result">
						<span id="ui-postback-response" class="png24 ui-postback-response ui-pb-msg-failed">
						Action failed						</span>
												<a href="#" id="ui-postback-toggle-details" class="png24 ui-postback-toggle-details"><span>Hide Details</span></a>
											</div>
									<div id="ui-postback-details">
						<ul class="ui-postback-msg">
						<li title="error" class="png24 ui-postback-msg-error">Upload failed! No file uploaded</li>						</ul>
					</div>
								</div>
				<div class="clear"></div>
			</div>
			<div>&nbsp;</div>
		
		<!--  app  -->
		<div class="ui-app" id="ui-app">
			<!-- main object data goes here... -->
<form method="post" enctype="multipart/form-data" action="filemanager.php?cwd=/var/www/boxoffice">
<table width="100%" id="fm_table" class="tbl-filemanager">

    <tr>
        <td colspan="6" class="head5 left">
        Current Path: /var/www/boxoffice        </td>
    </tr>
    <tr>
        <td colspan="6" class="head3" style="text-align:left;">
            Change Directory: <select name="Jump_To" id="dir_nav">
            <option value="">--- Common Paths ---</option>
            <option value="/home/calgaryfringe">Home Directory</option>
            <option value="/var/www/html/">Main Website HTML</option>
            <option value="/var/log/httpd/">Apache Logs</option>
            <option value="/tmp/">/tmp</option>
            </select>
        </td>
    </tr>
    <tr id="dir-hdr">
    <td class="head1" width="25" align="center">
    	<input type="checkbox" id="select_all" />    </td>
    <td class="head1 name" >Name</td>
    <td class="head1 right">Size</td>
    <td class="head1 left">Owner</td>
    <td class="head1 actions">Actions</td>
</tr>
<tr id="file-root">
    <td class="" align="center">
        &nbsp;
    </td>
    <td class=" name" align="left">

        <a class="node node-parent-dir" href="filemanager?cwd=%2Fvar%2Fwww">
        Parent Directory
        </a>
    </td>
    <td class="right"></td>
    <td class="left owner">calgaryfringe</td>
    <td class="action center"></td>
</tr>
<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/PHPExcel" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2FPHPExcel" class=""><img src="/images/apps/filemanager/folder.gif" alt="" title="Edit Permissions" align="middle" /></a>                    <span class="name">
                    	                    		<a href="filemanager?cwd=%2Fvar%2Fwww%2Fboxoffice%2FPHPExcel">
                    	<span class="filename">PHPExcel</span></a>                    </span>
                    
                </td>
    <td class="right size"></td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	<span class="spacer"></span><a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2FPHPExcel" class="ui-action ui-action-properties">Properties</a><a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>        <span class="spacer"></span>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/sessions" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fsessions" class=""><img src="/images/apps/filemanager/folder.gif" alt="" title="Edit Permissions" align="middle" /></a>                    <span class="name">
                    	                    		<a href="filemanager?cwd=%2Fvar%2Fwww%2Fboxoffice%2Fsessions">
                    	<span class="filename">sessions</span></a>                    </span>
                    
                </td>
    <td class="right size"></td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	<span class="spacer"></span><a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fsessions" class="ui-action ui-action-properties">Properties</a><a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>        <span class="spacer"></span>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/tmp" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Ftmp" class=""><img src="/images/apps/filemanager/folder.gif" alt="" title="Edit Permissions" align="middle" /></a>                    <span class="name">
                    	                    		<a href="filemanager?cwd=%2Fvar%2Fwww%2Fboxoffice%2Ftmp">
                    	<span class="filename">tmp</span></a>                    </span>
                    
                </td>
    <td class="right size"></td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	<span class="spacer"></span><a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Ftmp" class="ui-action ui-action-properties">Properties</a><a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>        <span class="spacer"></span>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/admin.js" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fadmin.js" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fadmin.js"><span class="filename">admin.js</span></a></span>
                    
                </td>
    <td class="right size">1.98 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fadmin.js">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fadmin.js" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fadmin.js">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2FkbWluLmpz" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/admin.js?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/admin.php" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fadmin.php" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fadmin.php"><span class="filename">admin.php</span></a></span>
                    
                </td>
    <td class="right size">5.15 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fadmin.php">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fadmin.php" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fadmin.php">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2FkbWluLnBocA==" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/admin.php?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/BoxOffice-0_7_21.src.zip" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_21.src.zip" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_21.src.zip"><span class="filename">BoxOffice-0_7_21.src.zip</span></a></span>
                    
                </td>
    <td class="right size">46.42 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    				<a title="Open" class="ui-action ui-action-open-archive" href="filemanager.php?co=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_21.src.zip">Open</a>
		<a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_21.src.zip" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_21.src.zip">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL0JveE9mZmljZS0wXzdfMjEuc3JjLnppcA==" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/BoxOffice-0_7_21.src.zip?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/BoxOffice-0_7_22.src.zip" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_22.src.zip" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_22.src.zip"><span class="filename">BoxOffice-0_7_22.src.zip</span></a></span>
                    
                </td>
    <td class="right size">92.05 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    				<a title="Open" class="ui-action ui-action-open-archive" href="filemanager.php?co=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_22.src.zip">Open</a>
		<a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_22.src.zip" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_22.src.zip">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL0JveE9mZmljZS0wXzdfMjIuc3JjLnppcA==" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/BoxOffice-0_7_22.src.zip?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/BoxOffice-0_7_23.src.zip" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_23.src.zip" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_23.src.zip"><span class="filename">BoxOffice-0_7_23.src.zip</span></a></span>
                    
                </td>
    <td class="right size">271.03 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    				<a title="Open" class="ui-action ui-action-open-archive" href="filemanager.php?co=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_23.src.zip">Open</a>
		<a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_23.src.zip" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice-0_7_23.src.zip">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL0JveE9mZmljZS0wXzdfMjMuc3JjLnppcA==" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/BoxOffice-0_7_23.src.zip?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/boxoffice.css" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fboxoffice.css" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fboxoffice.css"><span class="filename">boxoffice.css</span></a></span>
                    
                </td>
    <td class="right size">155.00 B</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fboxoffice.css">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fboxoffice.css" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fboxoffice.css">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2JveG9mZmljZS5jc3M=" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/boxoffice.css?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/boxoffice.html" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fboxoffice.html" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fboxoffice.html"><span class="filename">boxoffice.html</span></a></span>
                    
                </td>
    <td class="right size">640.00 B</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fboxoffice.html">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fboxoffice.html" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fboxoffice.html">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2JveG9mZmljZS5odG1s" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/boxoffice.html?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/BoxOffice.jar" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice.jar" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice.jar"><span class="filename">BoxOffice.jar</span></a></span>
                    
                </td>
    <td class="right size">54.86 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252FBoxOffice.jar">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice.jar" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice.jar">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL0JveE9mZmljZS5qYXI=" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/BoxOffice.jar?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/boxoffice2.html" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fboxoffice2.html" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fboxoffice2.html"><span class="filename">boxoffice2.html</span></a></span>
                    
                </td>
    <td class="right size">372.00 B</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fboxoffice2.html">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fboxoffice2.html" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fboxoffice2.html">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2JveG9mZmljZTIuaHRtbA==" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/boxoffice2.html?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/BoxOffice2.jar" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice2.jar" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice2.jar"><span class="filename">BoxOffice2.jar</span></a></span>
                    
                </td>
    <td class="right size">50.15 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252FBoxOffice2.jar">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice2.jar" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2FBoxOffice2.jar">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL0JveE9mZmljZTIuamFy" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/BoxOffice2.jar?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/boxofficeform.png" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fboxofficeform.png" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fboxofficeform.png"><span class="filename">boxofficeform.png</span></a></span>
                    
                </td>
    <td class="right size">54.01 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fboxofficeform.png">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fboxofficeform.png" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fboxofficeform.png">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2JveG9mZmljZWZvcm0ucG5n" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/boxofficeform.png?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/dbfuncs_admin.php" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fdbfuncs_admin.php" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fdbfuncs_admin.php"><span class="filename">dbfuncs_admin.php</span></a></span>
                    
                </td>
    <td class="right size">12.70 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fdbfuncs_admin.php">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fdbfuncs_admin.php" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fdbfuncs_admin.php">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2RiZnVuY3NfYWRtaW4ucGhw" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/dbfuncs_admin.php?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/dbfuncs_boxoffice.php" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fdbfuncs_boxoffice.php" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fdbfuncs_boxoffice.php"><span class="filename">dbfuncs_boxoffice.php</span></a></span>
                    
                </td>
    <td class="right size">72.47 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fdbfuncs_boxoffice.php">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fdbfuncs_boxoffice.php" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fdbfuncs_boxoffice.php">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2RiZnVuY3NfYm94b2ZmaWNlLnBocA==" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/dbfuncs_boxoffice.php?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/dbfuncs_carbonpop.php" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fdbfuncs_carbonpop.php" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fdbfuncs_carbonpop.php"><span class="filename">dbfuncs_carbonpop.php</span></a></span>
                    
                </td>
    <td class="right size">3.70 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fdbfuncs_carbonpop.php">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fdbfuncs_carbonpop.php" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fdbfuncs_carbonpop.php">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2RiZnVuY3NfY2FyYm9ucG9wLnBocA==" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/dbfuncs_carbonpop.php?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/dblib.php" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fdblib.php" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fdblib.php"><span class="filename">dblib.php</span></a></span>
                    
                </td>
    <td class="right size">3.71 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fdblib.php">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fdblib.php" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fdblib.php">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2RibGliLnBocA==" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/dblib.php?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/dbtest.php" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fdbtest.php" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fdbtest.php"><span class="filename">dbtest.php</span></a></span>
                    
                </td>
    <td class="right size">1.84 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fdbtest.php">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fdbtest.php" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fdbtest.php">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2RidGVzdC5waHA=" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/dbtest.php?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/dbui.php" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fdbui.php" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fdbui.php"><span class="filename">dbui.php</span></a></span>
                    
                </td>
    <td class="right size">4.99 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fdbui.php">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fdbui.php" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fdbui.php">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2RidWkucGhw" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/dbui.php?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/favicon.ico" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Ffavicon.ico" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Ffavicon.ico"><span class="filename">favicon.ico</span></a></span>
                    
                </td>
    <td class="right size">1.12 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Ffavicon.ico">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Ffavicon.ico" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Ffavicon.ico">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2Zhdmljb24uaWNv" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/favicon.ico?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/Final_Box_Office_Reports_2011_Template.xls" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2FFinal_Box_Office_Reports_2011_Template.xls" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2FFinal_Box_Office_Reports_2011_Template.xls"><span class="filename">Final_Box_Office_Reports_2011_Template.xls</span></a></span>
                    
                </td>
    <td class="right size">493.50 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252FFinal_Box_Office_Reports_2011_Template.xls">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2FFinal_Box_Office_Reports_2011_Template.xls" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2FFinal_Box_Office_Reports_2011_Template.xls">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL0ZpbmFsX0JveF9PZmZpY2VfUmVwb3J0c18yMDExX1RlbXBsYXRlLnhscw==" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/Final_Box_Office_Reports_2011_Template.xls?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/fringe_logo_100x100_trans.png" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Ffringe_logo_100x100_trans.png" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Ffringe_logo_100x100_trans.png"><span class="filename">fringe_logo_100x100_trans.png</span></a></span>
                    
                </td>
    <td class="right size">10.00 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Ffringe_logo_100x100_trans.png">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Ffringe_logo_100x100_trans.png" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Ffringe_logo_100x100_trans.png">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2ZyaW5nZV9sb2dvXzEwMHgxMDBfdHJhbnMucG5n" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/fringe_logo_100x100_trans.png?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/fringe_logo_2011_for_forms.png" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Ffringe_logo_2011_for_forms.png" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Ffringe_logo_2011_for_forms.png"><span class="filename">fringe_logo_2011_for_forms.png</span></a></span>
                    
                </td>
    <td class="right size">119.92 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Ffringe_logo_2011_for_forms.png">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Ffringe_logo_2011_for_forms.png" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Ffringe_logo_2011_for_forms.png">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2ZyaW5nZV9sb2dvXzIwMTFfZm9yX2Zvcm1zLnBuZw==" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/fringe_logo_2011_for_forms.png?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/gateway.php" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fgateway.php" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fgateway.php"><span class="filename">gateway.php</span></a></span>
                    
                </td>
    <td class="right size">4.98 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fgateway.php">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fgateway.php" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fgateway.php">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2dhdGV3YXkucGhw" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/gateway.php?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/index.php" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Findex.php" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Findex.php"><span class="filename">index.php</span></a></span>
                    
                </td>
    <td class="right size">4.00 B</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Findex.php">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Findex.php" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Findex.php">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2luZGV4LnBocA==" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/index.php?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/jre-6u26-windows-i586.exe" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fjre-6u26-windows-i586.exe" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fjre-6u26-windows-i586.exe"><span class="filename">jre-6u26-windows-i586.exe</span></a></span>
                    
                </td>
    <td class="right size">15.85 MB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fjre-6u26-windows-i586.exe">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fjre-6u26-windows-i586.exe" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fjre-6u26-windows-i586.exe">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2pyZS02dTI2LXdpbmRvd3MtaTU4Ni5leGU=" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/jre-6u26-windows-i586.exe?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/jre-6u26-windows-x64.exe" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fjre-6u26-windows-x64.exe" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fjre-6u26-windows-x64.exe"><span class="filename">jre-6u26-windows-x64.exe</span></a></span>
                    
                </td>
    <td class="right size">16.14 MB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fjre-6u26-windows-x64.exe">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fjre-6u26-windows-x64.exe" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fjre-6u26-windows-x64.exe">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL2pyZS02dTI2LXdpbmRvd3MteDY0LmV4ZQ==" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/jre-6u26-windows-x64.exe?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/phpinfo.php" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fphpinfo.php" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fphpinfo.php"><span class="filename">phpinfo.php</span></a></span>
                    
                </td>
    <td class="right size">20.00 B</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fphpinfo.php">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fphpinfo.php" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fphpinfo.php">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL3BocGluZm8ucGhw" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/phpinfo.php?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/rptfuncs.php" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Frptfuncs.php" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Frptfuncs.php"><span class="filename">rptfuncs.php</span></a></span>
                    
                </td>
    <td class="right size">681.00 B</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Frptfuncs.php">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Frptfuncs.php" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Frptfuncs.php">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL3JwdGZ1bmNzLnBocA==" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/rptfuncs.php?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/ssnfuncs.php" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fssnfuncs.php" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fssnfuncs.php"><span class="filename">ssnfuncs.php</span></a></span>
                    
                </td>
    <td class="right size">1.82 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fssnfuncs.php">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fssnfuncs.php" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fssnfuncs.php">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL3NzbmZ1bmNzLnBocA==" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/ssnfuncs.php?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/ticket_quickcheck.sql.txt" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fticket_quickcheck.sql.txt" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fticket_quickcheck.sql.txt"><span class="filename">ticket_quickcheck.sql.txt</span></a></span>
                    
                </td>
    <td class="right size">505.00 B</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fticket_quickcheck.sql.txt">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fticket_quickcheck.sql.txt" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fticket_quickcheck.sql.txt">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL3RpY2tldF9xdWlja2NoZWNrLnNxbC50eHQ=" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/ticket_quickcheck.sql.txt?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/uifuncs.php" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fuifuncs.php" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fuifuncs.php"><span class="filename">uifuncs.php</span></a></span>
                    
                </td>
    <td class="right size">64.29 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fuifuncs.php">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fuifuncs.php" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fuifuncs.php">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL3VpZnVuY3MucGhw" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/uifuncs.php?');">Delete</a>    </td>
	</tr>
	<tr class="entry file">
    <td class="" align="center">
                    <input type="checkbox" name="file[]" class="checkbox" value="/var/www/boxoffice/xlfuncs.php" />
            </td>
    <td class="name " align="left">
        <a href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fxlfuncs.php" class=""><img src="/images/apps/filemanager/file.gif" alt="" title="Edit Permissions" align="middle" /></a>                        <span class="name"><a href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fxlfuncs.php"><span class="filename">xlfuncs.php</span></a></span>
                    
                </td>
    <td class="right size">8.33 KB</td>
    <td class="left owner">calgaryfringe</td>
    <td class=" actions center">
    	        	<a title="Edit" class="ui-action ui-action-edit" href="/apps/filemanager.php?cwd=%2Fvar%2Fwww%2Fboxoffice&amp;f=%252Fvar%252Fwww%252Fboxoffice%252Fxlfuncs.php">Edit</a>
        <a title="Properties" href="/apps/filemanager.php?ep=%2Fvar%2Fwww%2Fboxoffice%2Fxlfuncs.php" class="ui-action ui-action-properties">Properties</a>        <a title="Download" class="ui-action ui-action-download" href="?download=%2Fvar%2Fwww%2Fboxoffice%2Fxlfuncs.php">Download</a>
        <a href="#" title="Rename" class="ui-action ui-action-rename">Rename</a>                <a title="Delete" class="ui-action ui-action-delete" href="?d=L3Zhci93d3cvYm94b2ZmaWNlL3hsZnVuY3MucGhw" onClick="return confirm('Are you sure you want to delete /var/www/boxoffice/xlfuncs.php?');">Delete</a>    </td>
	</tr>
	<tr>
	<td colspan="6"><hr /></td>
</tr>

<tr>
<td class=" right disk-folder" colspan="3">
	<span id="folder-selected">
	Selected Files: <span id="selected-size"></span> MB (<span id="selected-count"></span> items)
	</span>

	<span id="folder-unselected">
	Current Folder:
	<span id="folder-size">33.35 MB</span> (<span id="folder-count">34</span> items)
	</span>
</td>
<td class="right disk-total" colspan="3">
	<span id='disk-free'>640.63 MB</span> Free :: <span id='disk-total'>1333.33 MB</span> Total</td>
</tr>
<tr>
    <td class="head4" colspan="6" align="left">Commands</td>
</tr>
<tr>
    <td class="center"><img src="/images/apps/filemanager/clipboard.gif" name="Clipboard" /></td>
    <td colspan="5" class="" align="left"><input type="submit" name="Add_Clipboard" class="secondary" value="Add Selected File(s) to Clipboard" /></td>
</tr>
<tr>
    <td class="center"><img src="/images/apps/filemanager/remove-files.gif" name="Delete_Icon" /></td>
    <td colspan="5" class="" align="left"><input type="submit"name="Delete" class="secondary" title="Delete" value="Delete Selected File(s)" onClick="return confirm('Are you sure you want to delete the checked files?');" /> <input id="recursive" type="checkbox" name="Force" /> <label for="recursive">recursive</label></td>
</tr>
<tr>
    <td class="center"><img src="/images/apps/filemanager/folder-new.gif"  /></td>
    <td colspan="5" class="" align="left"><label for="Directory_Name" class="hinted">My_Directory/</label><input type="text" id="Directory_Name" name="Directory_Name" width="10" />&nbsp;<input type="submit" class="secondary" value="Create Directory" name="Create" /></td>
</tr>
<tr>
    <td class="center"><span class="ui-action ui-action-download">download</span></td>
    <td colspan="5" class="" align="left">Remote URL: <label for="remote_url" class="hinted">http://wordpress.org/latest.zip</label><input type="text" size="50" name="remote_url" id="remote_url" value="" /> <input type="submit" class="secondary" value="Download File and Extract" name="Download_Remote" /></td>
</tr>

<tr>
    <td class="center"><img src="/images/apps/filemanager/symlink-new.gif"  /></td>
    <td colspan="5" class="" align="left">
        Symlink Name: <label for="symlink_name" class="hinted">mainwebsite_html</label><input id="symlink_name" type="text" name="symlink_name" width="10" />&nbsp;
        Target: <input type="text" name="symlink_target" size="40" value="/var/www/boxoffice" id="browse_target" />&nbsp;
        <input type="button" name="browse_path" id="browse_path" class="secondary" value="Browse..." />&nbsp;
        <input type="submit" value="Create Symlink" name="Create_Symlink" class="secondary" />
    </td>
</tr>

<tr>
    <td colspan="6" class="head5" style="text-align:left;">
    Upload File to /var/www/boxoffice    </td>
</tr>

<tr>
    <td width="28" class="center top">
    <img src="/images/apps/filemanager/file-upload.gif" alt="Upload File Help" />
    </td>

    <td class="" colspan="5" align="left">
    <div id="progressbar"></div>
    <input type="hidden" id="UPLOAD_PROGRESS_KEY" name="APC_UPLOAD_PROGRESS" value="4e38b8199bdfa" />
    <div style="position:relative;overflow:visible;height:100%;width:100%;">
    <div id="container-upload">
    	<span id="upload-select-slice">
	    Upload file &nbsp; <input type="file" class="multiupload" name="uploaded_file[]" id="upload_file[]" value="Browse..." />
		</span>
		<span id="upload-saveas-slice">
		And save as <input type="text" name="uploaded_saveas[]" value="" />
		</span>
	</div>
	<div class="clear"></div>
	<div id="container-upload-options">
		<input type="submit" name="Upload" id="upload" value="Upload" class="primary" />
		<input type="checkbox" name="upload_overwrite" id="upload_overwrite" /> <label for="upload_overwrite"> overwrite destination file on conflict</label>
	</div>
	</div>
	<div id="container-upload-list"></div>
	<div class="clear"></div>
    <span class="note">Max upload size is 512M &mdash;
    	use <a href="http://wiki.apisnetworks.com/index.php/Hosting_Quickstart">WebDisk/FTP</a> for larger files.</span>
    <div class="clear"></div>
    <span class="note">Tip: a compressed file may be uploaded,
    	then extracted within the File Manager.</span>
	<img src="/images/gauge/modal-fill.gif" class="preload" />
	<img src="/images/gauge/modal-empty.gif" class="preload" />
    <img src="/images/upload-completed.gif" class="preload" />
    <img src="/images/modalclose.png" class="preload" />
    <img src="/images/modaltb.png" class="preload" />

    </td>
</tr>
</table>
</form>
		</div>
    	<!-- end main content of ensim_container.php -->
</div>
</div>
</div>
<div id="ui-footer-clear"></div>
</div>
<div class="ui-footer" id="ui-footer">
	<div class="ui-footer-border"></div>
	<div class="ui-footer-wrap">
		<span class="ui-login-curuser"><label>Current User:</label> calgaryfringe@calgaryfringe.ca</span>
	<span class="ui-login-last">
	<label>Last Login:</label> July 31st, 2011 at 2:21 PM EDT (-0400 GMT)	</span>

	<div class="ui-version-info">
		<div class="col2">
		apnscp v2.0 r1165 (20110720) &mdash; <a href="/apps/changelog">changelog</a>
		</div>
		<div class="col2">
		<a class="ui-ssl-toggle" id="ssl-toggle-disabled" href="https://cp.echelon.apisnetworks.com/apps/filemanager.php?cwd=/var/www/boxoffice">insecure</a>		<a class="ui-action ui-action-label ui-action-app-index" id="ui-app-index-link" href="/apps/sitemap">Application Index</a>
		</div>
	</div>

	<span class="ui-copyright">
		&copy; 2011 Apis Networks
	</span>
	</div>
</div>
<script type="text/javascript" src="http://getap.is/s/cp/js/jui.js?1263111184"></script><script type="text/javascript" src="http://getap.is/s/cp/js/apnscp.js?1306430790"></script><script type="text/javascript" src="/apps/filemanager/filemanager.js?1280968115"></script><script type="text/javascript" src="http://getap.is/s/cp/js/filetree.js?1306430790"></script><script type="text/javascript" src="http://getap.is/s/cp/js/modal.js?1301164935"></script><script type="text/javascript" src="http://getap.is/s/cp/js/upload.js?1273166199"></script><script type="text/javascript">
	$(window).load(function() { upload_id='4e38b8199bdfa'; apnscp.hinted();for (var i=0; i < 3; i++) { $('#ui-postback').effect('highlight',{color: "#de5532"},500);}
				$('#ui-postback-toggle-details').toggle(
				function() {
					$('#ui-postback-details').slideUp();
					$("span",this).text('Show Details');
					return false;
				},
				function () {
					$('#ui-postback-details').slideDown();
					$("span",this).text('Hide Details');
					return false;
				});});var __esprit_fm  = "list";
				var __esprit_cwd = "/var/www/boxoffice";</script>
</body>
</html>
