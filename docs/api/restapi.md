# REST-API-Dokumentation

## Abfragen der freien Tische zu gegebener Zeit

**Zugriffsmethode:** GET

**URL:** tischverwaltung/v1/freetables/\<from\>/\<numberOfSeats\>

**Parameter:**

- \<from\> ist die gewünschte Beginnzeit der Reservierung, als ganzzahliger Unix-Zeitstempel (Anzahl Sekunden seit 1.1.1970 00:00)
- \<numberOfSeats\> ist die Anzahl an benötigten Plätzen

**Rückgabe:** (Status-Code: 200)

    [
        {
    	    "id": [integer],
    	    "title": [string],
    	    "isOutside": [bool],
    	    "seats": [integer]
    	},
    	...
    ]

**Rückgabe im Fehlerfall:**

Angaben sind nicht korrekt: (Status-Code: 500)

    {
        "code": "invalid_data",
        "message": "[Fehlermeldung]",
        "data": null
    }

Dieser Fehlertritt in folgenden Fällen auf:

- Das Beginndatum der Reservierung liegt in der Vergangenheit
- Das Beginndatum der Reservierung liegt weiter als ein halbes Jahr in der Zukunft
- Das Beginndatum der Reservierung liegt nicht weiter als canReservateInMinutes in der Zukunft
- Die Anzahl der Personen ist kleiner gleich 0
- Der übergebene Zeitstempel liegt außerhalb der Öffnungszeiten

<br>

Zu viele Personen betroffen: (Status-Code: 500)

    {
        "code": "tooMuchPersons",
        "message": "[Fehlermeldung",
        "data": null
    }

Dieser Fehler tritt in folgenden Fällen auf:

- Die Anzahl der Personen überschreitet maxAmountOfPersons

<br>

Keine freien Tische gefunden: (Status-Code: 500)

    {
        "code": "noSuitableTables",
        "message": "[Fehlermeldung]",
        "data": null
    }

Dieser Fehler tritt in folgenden Fällen auf:

- Es wurden keine passendenden Tische gefunden.

## Einfügen einer neuen Reservierung

**Zugriffsmethode:** POST

**URL:** tischverwaltung/v1/savenewreservation/

**Parameter:**

    {
        from: [integer],
        tables: [
    	    [integer],
    	    ...
    	],
    	firstname: [string],
    	lastname: [string],
    	mail: [string],
    	phonenumber: [string],
    	numberOfSeats: [integer]
    }

Beispiel:

     {
    	from: 1565604065,
    	tables: [
    	    103, 105, 106
    	],
    	firstname: "Max",
    	lastname: "Mustermann",
    	mail: "max@mustermann.de",
    	phonenumber: "06641050678",
    	numberOfSeats: 5
    }

**Rückgabe:** (Status-Code: 200)

    null

**Rückgabe im Fehlerfall:** Übergebene Daten sind nicht korrekt: (Status-Code: 500)

    {
        "code": "verification_error",
        "message": "[entsprechende Fehlermeldung]",
        "data": null
    }

Dieser Fehler tritt in folgenden Fällen auf:

- übergebenes Beginndatum liegt weniger als 30 Minuten oder mehr als ein halbes Jahr in der Zukunft
- Beginndatum liegt nach dem Enddatum
- das Beginndatum liegt außerhalb der Öffnungszeitne
- es ist kein Tisch angegeben
- die übergebene ID des Tisches ist keinem Tisch zugeordnet
- das tables-array enthält Duplikate
- ein Tisch ist zum gewünschten Zeitpunkt nicht frei
- Anzahl der Personen nicht größer gleich 1
- zu viele Personen von der Reservierung betroffen
- alle gebuchten Tische zusammen haben nicht die gewünschte Anzahl an Sitzplätzen
- zu viele Plätze an den reservierten Tischen bleiben leer
- es wurde keine gültige E-Mail-Adresse übergeben
- der Parameter tables ist kein Array

## Abfrage der zur Verfügung stehenden Uhrzeiten

Ein normaler HTML-DateTimePicker hat leider den Nachteil, dass er weder in Safari noch in Internet Explorer funktioniert. Ferner ist damit die Eingabe der gewünschten Uhrzeit minutengenau möglich, was bei einer Reservierungssoftware eher unerwünscht ist (eine Reservierung auf 15:00 oder 15:15 anstatt 15:04 ist vollkommen ausreichend).

**Zugriffsmethode:** GET

**URL:** tischverwaltung/v1/gettimeslots/

**Rückgabe:** (Status-Code: 200)

Das Feld "display" entspricht dem Zeitstempel als String, welcher dem Benutzer angezeigt werden kann.

    [
    	[	// Montag
    		{
    			display: [string],
    		}, ...
    	],
    	[	// Dienstag
    		...
    	],
    	[	// Mittwoch
    		...
    	],
    	[	// Donnerstag
    		...
    	],
    	[	// Freitag
    		...
    	],
    	[	// Samstag
    		...
    	],
    	[	// Sonntag
    		...
    	],
    ]

Beispiel:

    [
    	{
    		display: "00:05",
    		timestamp: 300
    	}
    	, ...
    ]
