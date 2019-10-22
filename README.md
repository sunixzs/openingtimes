# Openingtimes

A small PHP-Class to parse a configuration file with opening time informations to HTML output.

## Classes

The main class is located in `Openingtimes.php`.

There are some usecases defined which inherit the main class:

* `OpeningtimesToday.php` to show what's on today
* `OpeningtimesWeek.php` to show the whole week as table
* `OpeningtimesTooltip.php` to show pure HTML
* `OpeningtimesFuture.php` to show only dates in the future as table

Use one of these classes as template to create your own output.

## Configuration

The configuration of opening times for each weekday, specific days and the tooltip is defined in `Data/Openingtimes.txt`. 

There are some definitions for the weekdays:

```TEXT
monday     = 09:00-19:00
tuesday    = 09:00-19:00
wednesday  = 10:00-12:00|13:00-20:00
thursday   = 09:00-19:00
friday     = 09:00-19:00
saturday   = 14:00
sunday     = geschlossen
```

...and some for special days:

```TEXT
2019-08-16 = Wegen Urlaub bis 17.Aug. geschlossen!
2019-12-24 = Geschlossen. Frohe Weihnachten!
```

...and also some for special output:

```TEXT
tooltip = <strong>Winterpause</strong>
tooltip = <br />Vom 24.12. bis 07.01. geschlossen.
```

Have a look into this file to get more informations about defining the values.

## Get it into HTML

A simple implementation in HTML could be XHR like in `example.html`:

``` HTML
<div id="openingtimes-today"></div>
<script>
    (function () {
        var request = new XMLHttpRequest();
        request.open("GET", "OpeningtimesToday.php");
        request.addEventListener('load', function (event) {
            if (request.status >= 200 && request.status < 300) {
                document.querySelector("#openingtimes-today").innerHTML = request.responseText;
            } else {
                console.warn(request.statusText, request.responseText);
            }
        });
        request.send();
    })();
</script>
```

Maybe store the XHR response in the browser session to avoid fetching the data on each pageload.
