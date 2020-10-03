<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>VerifyEmail</title>
</head>
<body>
	Hola {{$datos['name']}}, activa tu cuenta dando click en el siguiente link
	<a href="{{$datos['url']}}">Activar Mi Cuenta</a>
 </body>
</html>