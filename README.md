# MSP NoSpam

This module will check the user IP against malicious users database.<br />
It can stop **Catalog Harvesters**, **Comment Spammers** and **Malicious users**.<br />

> Member of **MSP Security Suite**
>
> See: https://github.com/magespecialist/m2-MSP_Security_Suite

## Installing on Magento2:

**1. Get a free honeypot account**

Go to http://www.projecthoneypot.org/ and register for free.<br />
 Once registered go to http://www.projecthoneypot.org/httpbl_configure.php and get a **free API key**.

**2. Install using composer**

From command line: 

`composer require msp/nospam`<br />
`php bin/magento setup:upgrade`

**3. Enable and configure from your Magento backend config**

<img src="https://raw.githubusercontent.com/magespecialist/m2-MSP_NoSpam/master/screenshots/config.png" />

Set your API key according to the one you obtained from Honeypot website.
 
Remember to flush your cache.

## Threat detected

<img src="https://raw.githubusercontent.com/magespecialist/m2-MSP_NoSpam/master/screenshots/detected.png" />

## Logged entries ##

You can browse and search logged events for blocked or non-blocked requests in **System > MSP Security Suite > Events Report**.

