<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<script type="text/javascript">
function updatesum() {
document.form.sum.value = (document.form.sum1.value -0) + (document.form.sum2.value -0);
}
//--></script>

<body>

<form name="form" >
Enter a number:
<input name="sum1" onChange="updatesum()" />
and another number:
<input name="sum2" onChange="updatesum()" />
Their sum is:
<input name="sum" readonly style="border:0px;">
</form>

</body>
</html>