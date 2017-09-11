<?php

namespace Asylamba\Modules\Gaia\Model;

class PointLocation
{
    public $pointOnVertex;

    public function pointInPolygon($point, $polygon, $pointOnVertex = true)
    {
        $this->pointOnVertex = $pointOnVertex;

        # Transform string coordinates into arrays with x and y values
        $point = $this->pointStringToCoordinates($point);

        $vertices = array();
        for ($i = 0; $i < count($polygon); $i += 2) {
            $vertices[] = array('x' => $polygon[$i], 'y' => $polygon[$i + 1]);
        }
        # Add the first at the end to close the polygon
        $vertices[] = array('x' => $polygon[0], 'y' => $polygon[1]);

        # Check if the point sits exactly on a vertex
        if ($this->pointOnVertex == true and $this->pointOnVertex($point, $vertices) == true) {
            # sur un angle
            return 1;
        }

        # Check if the point is inside the polygon or on the boundary
        $intersections = 0;
        $verticesCount = count($vertices);

        for ($i = 1; $i < $verticesCount; $i++) {
            $vertex1 = $vertices[$i - 1];
            $vertex2 = $vertices[$i];

            if ($vertex1['y'] == $vertex2['y']
                and $vertex1['y'] == $point['y']
                and $point['x'] > min($vertex1['x'], $vertex2['x'])
                and $point['x'] < max($vertex1['x'], $vertex2['x'])) {
                # sur une ligne
                return 2;
            }

            if ($point['y'] > min($vertex1['y'], $vertex2['y'])
                and $point['y'] <= max($vertex1['y'], $vertex2['y'])
                and $point['x'] <= max($vertex1['x'], $vertex2['x'])
                and $vertex1['y'] != $vertex2['y']) {
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
                if ($xinters == $point['x']) {
                    # sur une ligne
                    return 2;
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $intersections++;
                }
            }
        }
        
        # If the number of edges we passed through is even, then it's in the polygon.
        if ($intersections % 2 != 0) {
            # a l'interieur
            return 3;
        } else {
            # a l'exterieur
            return 0;
        }
    }
    
    public function pointOnVertex($point, $vertices)
    {
        foreach ($vertices as $vertex) {
            if ($point == $vertex) {
                return true;
            }
        }
    }

    public function pointStringToCoordinates($pointString)
    {
        $coordinates = explode(',', $pointString);
        return array('x' => $coordinates[0], 'y' => $coordinates[1]);
    }
}
