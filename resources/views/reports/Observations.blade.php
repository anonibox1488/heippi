<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Reporte</title>
</head>
<body>
		<table>
    <thead>
    <tr>
        <th>Hospital</th>
        <th>Medico</th>
        <th>Especialidad</th>
        <th>Paciente</th>
        <th>Estado de Salud</th>
        <th>Observacion</th>
    </tr>
    </thead>
    <tbody>
    	@foreach($observations as $observation)
        <tr>
            <td>{{ $observation->hospital }}</td>
            <td>{{ $observation->medico }}</td>
            <td>{{ $observation->specialty }}</td>
            <td>{{ $observation->paciente }}</td>
            <td>{{ $observation->health_condition }}</td>
            <td>{{ $observation->observation }}</td>
        </tr>
    	@endforeach
    </tbody>
</table>
</body>
</html>