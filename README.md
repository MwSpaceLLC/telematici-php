# Tel Services <img src="https://telematici.agenziaentrate.gov.it/resources/img/AgenziaEntrate_logo_152.png" width="100">
> Small PHP library for byPass [Online Services](https://www.agenziaentrate.gov.it/wps/portal/entrate/servizi) of A E.

PHP Version  | Status  | Require
------------ | ------  | -------
PHP 7.2      | In Dev  | Composer

> Install Library:

`composer require e/namirial-php`


> Start Appliance Object:

```
$namirial = new Appliance\Namirial\Service('ip address of sws');
```
💻 The class will connect via http protocol to your Web Service and load the functions from the WSDL
> Sign File (CAdES,XAdES,PAdES)

```
$namirial->sign('path/to/file/ALB1666197.xml'); 
```
🚀 The appliance will discover the file format automatically by choosing the most appropriate format for you 

> Verify File (CAdES,XAdES,PAdES)
```
$namirial->verify('path/to/file/ALB1666197.p7m'); 
```
🎂 The appliance will verify the signature of the file if it is trusted or not

> Save File (CAdES,XAdES,PAdES)
```
$namirial->save('path/to/file/ALB1666197_signed.xml'); 
```
👻 The appliance will save the file in a directory by creating it if necessary
