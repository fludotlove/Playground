Benchmark
=========

A simple benchmarking class.

Example Usage
-------------

First include the benchmarking class into your project.

    require '../path/to/Benchmark.php';

### Add a marker.
To add a marker use the static `addMarker` method. You need to provide a name for each marker so it can be referenced later.

    Benchmark::addMarker('myMarkerName');
    
### Calculate the time between 2 markers.
To calculate the time elapsed between 2 markers use the `calculateMarkerToMarker` method.

    Benchmark::calculateMarkerToMarker('firstMarker', 'secondMarker');
    
### Calculate the time from a marker to now.
To calculate the elapsed time from a marker to now, use the `calculateMarkerToNow` method.

    Benchmark::calculateMarkerToNow('myMarkerName');

### Display more or less acuracy.
You can display more of less accuracy in the calculation methods by passing another argument to the calculate methods.

    Benchmark::calculateMarkerToMarker('firstMarker', 'secondMarker', 2); // Only show 2 decimal places.
    Benchmark::calculateMarkerToNow('myMarkerName', 10); // Show 10 decimal places.