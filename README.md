# WHMCS-Free-Domains-Manager

Offering a free domains with your products ?
This hook will notify you when a user has a free domain, without a valid product assigned to it.
Meaning, he is getting a free product – when he shouldn’t !

Some real life example usage –

Client ordered a yearly hosting plan and received a free domain with it.
After couple of months, client cancelled the hosting account, but domain is still listed in WHMCS as a free domain and might be sent to the registrar for renewal !

This hook will look for these criteria (with some more verifications) and will notify you about it.

# Installation

Edit the hook file with a simple code editor (notepad++ is recommended).

Set your desired settings – exclude clients / domains / services, change email template, set department id.

Upload it to your WHMCS hooks folder (“includes/hooks“).

Code examples –

In the given example, a ticket will be opened in department id 4, without any special excludes.

```
function jetserverFreeDomainsManagement_settings()
{
	return array(
		'exclude_clients'	=> array(), 
		'exclude_domains'	=> array(), 
		'exclude_services'	=> array(),
		'ticket_subject' 	=> 'Free Domains Managment',
		'ticket_pre_message' 	=> "*** THIS IS AN AUTOMATED MESSAGE ***\n\n\nFollowing, is a
list of free domains assigned to clients that doesn't apply our free domains policy.\n\n",
		'ticket_dept_id'	=> 4,
		'ticket_priority'	=> 'Low',
	);
}
```

In the given example, a ticket will be opened in department id 4, with the following exeptions – user ids 22,666,235 will be ignored, domain “testdomain.com” will be ignored.

```
function jetserverFreeDomainsManagement_settings()
{
	return array(
		'exclude_clients'	=> array(22,666,235),
		'exclude_domains'	=> array('testdomain.com'), 
		'exclude_services'	=> array(),
		'ticket_subject' 	=> 'Free Domains Managment',
		'ticket_pre_message' 	=> "*** THIS IS AN AUTOMATED MESSAGE ***\n\n\nFollowing, is a
list of free domains assigned to clients that doesn't apply our free domains policy.\n\n",
		'ticket_dept_id'	=> 4,
		'ticket_priority'	=> 'Low',
	);
}
```

# More Information

https://docs.jetapps.com/category/whmcs-addons/whmcs-free-domains-manager
