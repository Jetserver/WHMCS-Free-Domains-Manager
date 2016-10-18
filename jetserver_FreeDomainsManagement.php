<?php
/*
*
* Free Domains Management
* Created By Idan Ben-Ezra
*
* Copyrights @ Jetserver Web Hosting
* www.jetserver.net
*
* Hook version 1.0.2
*
**/

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

/*********************
 Free Domains Management Settings
*********************/
function jetserverFreeDomainsManagement_settings()
{
	return array(
		'apiuser'		=> '', // one of the admins username
		'exclude_clients'	=> array(), // example - array(22,666,235) - exclude client ids 22,666 and 235 from been triggered
		'exclude_domains'	=> array(), // example - array('testdomain.com','mydomain.net') - exclude domains testdomain.com and mydomain.net from been triggered
		'exclude_services'	=> array(), // example - array(46,921,347) - exclude services 46,921 and 347 from been triggered
		'ticket_subject' 	=> 'Free Domains Managment',
		'ticket_pre_message' 	=> "*** THIS IS AN AUTOMATED MESSAGE ***\n\n\nFollowing, is a list of free domains assigned to clients that doesn't apply our free domains policy.\n\n",
		'ticket_dept_id'	=> 0,
		'ticket_priority'	=> 'Low',
	);
}
/********************/

function jetserverFreeDomainsManagement_check()
{
	global $CONFIG;

	$settings = jetserverFreeDomainsManagement_settings();

	$emailRows = $domains = array();

	$sql = "SELECT *
		FROM tbldomains
		WHERE recurringamount = 0
		AND status = 'Active'
		" . (sizeof($settings['exclude_clients']) ? "AND userid NOT IN('" . implode("','", $settings['exclude_clients']) . "')" : '') . "
		ORDER BY userid ASC";
	$result = mysql_query($sql);

	while($domain_details = mysql_fetch_assoc($result))
	{
		if(sizeof($settings['exclude_domains']) && in_array($domain_details['domain'], $settings['exclude_domains'])) continue;
		$domains[$domain_details['userid']][] = $domain_details['domain'];
	}
	mysql_free_result($result);

	foreach($domains as $client_id => $client_domains)
	{
		$sql = "SELECT h.id
			FROM tblhosting as h
			INNER JOIN tblproducts as p
			ON h.packageid = p.id
			WHERE h.userid = '{$client_id}'
			" . (sizeof($settings['exclude_services']) ? "AND h.id NOT IN('" . implode("','", $settings['exclude_services']) . "')" : '') . "
			AND h.domainstatus = 'Active'
			AND p.freedomain = 'on'";
		$result = mysql_query($sql);
		$total_hosting = mysql_num_rows($result);

		if(sizeof($client_domains) > $total_hosting)
		{
			$sql = "SELECT *
				FROM tblclients
				WHERE id = '{$client_id}'";
			$result = mysql_query($sql);
			$client_details = mysql_fetch_assoc($result);

			$emailRows[] = "Client ID: {$client_details['id']}\nClient Name: {$client_details['firstname']} {$client_details['lastname']}\nTotal Hosting Accounts with Free Domains: {$total_hosting}\nTotal Free Domains: " . sizeof($client_domains) . " (" . implode(", ", $client_domains) . ")";
		}
	}

	if(sizeof($emailRows))
	{
		$response = localAPI('openticket', array(
			'clientid' 	=> 0,
			'deptid' 	=> $settings['ticket_dept_id'],
			'name'		=> 'Free Domains Managment',
			'email'		=> $CONFIG['Email'],
			'subject'	=> $settings['ticket_subject'],
			'message' 	=> $settings['ticket_pre_message'] . "--------\n" . implode("\n--------\n", $emailRows) . "\n--------",
			'priority' 	=> $settings['ticket_priority'],
		), $settings['apiuser']);
	}
}

add_hook('DailyCronJob', 0, 'jetserverFreeDomainsManagement_check');

?>