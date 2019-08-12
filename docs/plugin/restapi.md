# REST-API-Dokumentation
## Abfragen der freien Tische zu gegebener Zeit
**Zugriffsmethode:** GET
**URL:** tischverwaltung/v1/freetables/\<from\>
**Parameter:**
 - \<from\> ist die gewünschte Beginnzeit der Reservierung, als ganzzahliger Unix-Zeitstempel (Anzahl Sekunden seit 1.1.1970 00:00)

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
Beginndatum liegt nach Enddatum: (Status-Code: 500)

    {
	    "code": "invalid_date",
	    "message": "Das Beginndatum darf nicht nach dem Enddatum liegen.",
	    "data": null
	}


Beginndatum liegt in der Vergangenheit: (Status-Code: 500)

    {
	    "code": "invalid_date",
	    "message": "Das Beginndatum der Reservierung darf nicht in der Vergangenheit liegen.",
	    "data": null
	}
	    
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
		seats: [integer]
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
		seats: 5
    }
	    
**Rückgabe:** (Status-Code: 200)

    null
    
**Rückgabe im Fehlerfall:**
Übergebene Daten sind nicht korrekt: (Status-Code: 500)

    {
	    "code": "verification_error",
	    "message": "[entsprechende Fehlermeldung]",
	    "data": null
	}

Dieser Fehler tritt in folgenden Fällen auf:

 - übergebenes Beginndatum liegt weniger als 30 Minuten oder mehr als ein halbes Jahr in der Zukunft
 - es ist kein Tisch angegeben
 - die übergebene ID des Tisches ist keinem Tisch zugeordnet
 - das tables-array enthält Duplikate
 - ein Tisch ist zum gewünschten Zeitpunkt nicht frei
 - es wurde keine gültige E-Mail-Adresse übergeben
