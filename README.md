# Tel Services <img src="https://telematici.agenziaentrate.gov.it/resources/img/AgenziaEntrate_logo_152.png" width="100">
> Small PHP library for byPass [Online Services](https://www.agenziaentrate.gov.it/wps/portal/entrate/servizi) of A E.

PHP Version  | Status  | Require
------------ | ------  | -------
PHP 7.2      | In Dev  | Composer

> Install Library:

` composer require ethical/telematici-php`

> Start Appliance Object:

```
$telematici = new Ethical\Telematici\Service('path/to/serviceaccountkey/project-name.json');
```
💻 The class will connect via api to Google Cloud Api Vision Services (environment variables)
> Check Valid Fiscal Code Exist

```
if($telematici->validCF('FISCALCODE'))
{
    // Fiscal Code Valid !
} else {
    // Fiscal Code Not Valid
}
```
🚀 The system checks the tax code by confirming the captcha through Api Vision

> Check Valid Vat Number
```
if($telematici->validPIVA('VATNUMBER'))
{
    // Vat Number Valid !
} else {
    // Vat Number Not Valid
}
```
🎂 The system checks the vat number by confirming the captcha through Api Vision

> Work With Cycles And Log
```
$log->info('+1');
```
🎂 How does the script work?

Simple, the script tries to read the contents of the captcha until it finds a string, then passes the string to the captcha, if it fails it runs the loop again.

The cycles are saved via LOG and this can also last 8/9 seconds and generate about 20/30 calls Api Vision to be able to find a match.

Read the costs carefully: [Api Vision Pricing](https://cloud.google.com/vision/pricing?hl=it)

##Veary Important For Use

👻 This script is for TEST purposes only. It's exclusively for testing the power of Google Cloud Api Vision
* YOU CAN NOT USE IT FOR COMMERCIAL PURPOSES
* IT IS NOT A LEGAL SCRIPT