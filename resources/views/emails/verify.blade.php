<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>
<body>

<section class="jumbotron text-center">
    <div class="container">
        <h1 class="jumbotron-heading">Kita Horas - Basar Registrierung abschliessen</h1>
        <p class="lead text-muted">Nur noch ein Schritt und Sie haben die Registrierung abgeschlossen. <br/>
            Bitte klicken Sie "Registrierung abschliessen":</p>
        <br/>
        <a href="{{ URL::to('register/verify/' . $confirmation_code) }}" class="btn btn-secondary my-2">Registrierung abschliessen</a>
        <br/>
        <br/>
        oder kopieren Sie diesen Link in einen Browser: {{ URL::to('register/verify/' . $confirmation_code) }}

        <br/>
        <br/>

        Nur wenn Sie die Registrierung abschliessen werden wir Ihre Basarnummern reservieren !!!
        <br>
        Sie haben folgende Informationen bestätigt:
        <ul >
            <li >Ihre Daten werden ausschliesslich für den Basar verwendet.</li>
            <li >Ihre Daten werden nachdem Basar gelöscht</li>
        </ul>

        <p>

            <a href="http://www.kita-horas.de/basare/" class="btn btn-secondary my-2">Unsere Basar Regeln</a>
        </p>
    </div>
</section>

</body>
</html>