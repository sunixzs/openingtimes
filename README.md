# Openingtimes

A small PHP-Class to parse a configuration file with opening time informations to HTML output.

The main class is located in `Openingtimes.php`.

In the other classes there are some usecases defined to provide an output for `...Today.php`, the whole `...Week.php` as table and for a `...Tooltip.php` to show additional informations defined in the configuration.  
Use one of these classes as template to create your own which inherits from `Openingtimes`.

## Configuration

The configuration of opening times for each weekday, specific days and the tooltip is defined in `Data/Openingtimes.txt`. Have a look into this file to get more informations.

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

Maybe store the XHR in the browser session to avoid fetching the data on each pageload.
