' ddns_updater
' Author: Fco de Borja Sanchez
' DD: 01/06/2015
'
'  Updates the data in http://coddns.org with the detected IP and
' the data contained in the configuration file passed as first argument
'-------------------------------------------------------------------------

Function HTTPPost(sUrl, sRequest)
  set o = CreateObject("Microsoft.XMLHTTP")
  o.open "POST", sUrl,false
  o.setRequestHeader "Content-Type", "application/x-www-form-urlencoded"
  o.setRequestHeader "Content-Length", Len(sRequest)
  o.send sRequest
  HTTPPost = o.responseText
End Function
 
Function remove_blanks (line)
  sLast=""
  sInit=line
  Do Until sLast = sInit
    sLast = sInit
    sInit = Replace(sInit, " ", "")
  Loop
  remove_blanks=sLast
 
End Function
 
Function ReadConfiguration (config_path)
  Set objFS = CreateObject("Scripting.FileSystemObject")  
  If Not objFS.FileExists(config_path) Then
	WScript.Quit 2
  End If

  Set objFile = objFS.OpenTextFile(config_path, 1) 
  user = ""
  pass = ""
  host = ""
  
  Do Until objFile.AtEndOfStream
	line = remove_blanks(objFile.ReadLine)
	
	If  Left(line,InStr(line,":")-1) = "usuario" Then
	  user = "u=" & Mid(line,InStr(line,":")+1, Len(line))
	Elseif Left(line,InStr(line,":")-1) = "password" Then
	  pass = "p=" & Base64Encode(Mid(line,InStr(line,":")+1, Len(line)))
	Elseif Left(line,InStr(line,":")-1) = "host" Then
	  host = "h=" & Mid(line,InStr(line,":")+1, Len(line))
	End If
  Loop 
  objFile.Close
  
  If ( Len(user) = 0 OR Len(pass)=0 OR Len (host) = 0 ) Then
	WScript.Quit 3
  End If
  
  ReadConfiguration = user & "&" & pass & "&" & host
  
End Function
 
' MAIN PROCEDURAL
 
if WScript.Arguments.Count = 0 then
    WScript.Echo "Missing parameters"
	WScript.Quit 1
end if

sUrl = "http://coddns.org/cliupdate.php"
sRequest = ReadConfiguration (WScript.Arguments(0))
response = HTTPPost (sUrl, sRequest)
WScript.echo response

If Left(response,InStr(response,":")-1) = "ERR" then
	WScript.Quit 4
End If
WScript.Quit 0




'-------------------------------------------------------------------------
'Stream_StringToBinary Function
'2003 Antonin Foller, http://www.motobit.com
'Text - string parameter To convert To binary data
Function Base64Encode(sText)
    Dim oXML, oNode

    Set oXML = CreateObject("Msxml2.DOMDocument.3.0")
    Set oNode = oXML.CreateElement("base64")
    oNode.dataType = "bin.base64"
    oNode.nodeTypedValue =Stream_StringToBinary(sText)
    Base64Encode = oNode.text
    Set oNode = Nothing
    Set oXML = Nothing
End Function

Function Base64Decode(ByVal vCode)
    Dim oXML, oNode

    Set oXML = CreateObject("Msxml2.DOMDocument.3.0")
    Set oNode = oXML.CreateElement("base64")
    oNode.dataType = "bin.base64"
    oNode.text = vCode
    Base64Decode = Stream_BinaryToString(oNode.nodeTypedValue)
    Set oNode = Nothing
    Set oXML = Nothing
End Function

'Stream_StringToBinary Function
'2003 Antonin Foller, http://www.motobit.com
'Text - string parameter To convert To binary data
Function Stream_StringToBinary(Text)
  Const adTypeText = 2
  Const adTypeBinary = 1

  'Create Stream object
  Dim BinaryStream 'As New Stream
  Set BinaryStream = CreateObject("ADODB.Stream")

  'Specify stream type - we want To save text/string data.
  BinaryStream.Type = adTypeText

  'Specify charset For the source text (unicode) data.
  BinaryStream.CharSet = "us-ascii"

  'Open the stream And write text/string data To the object
  BinaryStream.Open
  BinaryStream.WriteText Text

  'Change stream type To binary
  BinaryStream.Position = 0
  BinaryStream.Type = adTypeBinary

  'Ignore first two bytes - sign of
  BinaryStream.Position = 0

  'Open the stream And get binary data from the object
  Stream_StringToBinary = BinaryStream.Read

  Set BinaryStream = Nothing
End Function

'Stream_BinaryToString Function
'2003 Antonin Foller, http://www.motobit.com
'Binary - VT_UI1 | VT_ARRAY data To convert To a string 
Function Stream_BinaryToString(Binary)
  Const adTypeText = 2
  Const adTypeBinary = 1

  'Create Stream object
  Dim BinaryStream 'As New Stream
  Set BinaryStream = CreateObject("ADODB.Stream")

  'Specify stream type - we want To save binary data.
  BinaryStream.Type = adTypeBinary

  'Open the stream And write binary data To the object
  BinaryStream.Open
  BinaryStream.Write Binary

  'Change stream type To text/string
  BinaryStream.Position = 0
  BinaryStream.Type = adTypeText

  'Specify charset For the output text (unicode) data.
  BinaryStream.CharSet = "us-ascii"

  'Open the stream And get text/string data from the object
  Stream_BinaryToString = BinaryStream.ReadText
  Set BinaryStream = Nothing
End Function

'--------------------------------------------------------------

