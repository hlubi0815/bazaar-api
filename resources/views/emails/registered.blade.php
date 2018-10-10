<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>

<div class="highlight-clean">
    <div class="container">
        <div class="intro">
            <h2 class="text-center">Kita Horas - Basar Registrierung </h2>
            <p class="text-center" style="height:24px;">Hallo wir haben für Sie folgende Basarnummern registriert: </p>
        </div>
        <div class="row">
            <div class="col-4 offset-4">
                <ul >
                    @foreach ($salenumber as $number)
                        <li style="list-style-type: none;">Nummer: {{ $number}}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-4 offset-4">
                <p>Wir möchten Sie auf folgendes hinweisen: </p>
                <p>Ihre Daten werden ausschliesslich für den Basar verwendet.</p>
                <p>Ihre Daten werden nachdem Basar gelöscht </p>
            </div>
        </div>
        <div class="row">1
            <div class="col-8 offset-3">
            <div class="buttons">
                <a href="http://www.kita-horas.de/basare/winter-kinderkleider-basar/" class="btn btn-primary my-2">Basar informationen</a>
                <a href="http://www.kita-horas.de/basare/" class="btn btn-primary my-2">Unsere Basar Regeln</a>
                <a href="http://www.kita-horas.de/basare/basar-vorlagen/" class="btn btn-primary my-2">Ettiketten Vorlagen</a>
            </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>