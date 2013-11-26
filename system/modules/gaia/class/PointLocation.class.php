<?php
Class PointLocation {
	var $pointOnVertex;

	function pointInPolygon($point, $polygon, $pointOnVertex = TRUE) {
		$polygon[] = $polygon[0];
		$this->pointOnVertex = $pointOnVertex; 

		# Transform string coordinates into arrays with x and y values
		$point = $this->pointStringToCoordinates($point);
		$vertices = array(); 
		foreach ($polygon as $vertex) {
			$vertices[] = $this->pointStringToCoordinates($vertex);
		}

		# Check if the point sits exactly on a vertex
		if ($this->pointOnVertex == TRUE and $this->pointOnVertex($point, $vertices) == TRUE) {
			# sur un angle
			return 1;
		}

		# Check if the point is inside the polygon or on the boundary
		$intersections = 0; 
		$verticesCount = count($vertices);

		for ($i=1; $i < $verticesCount; $i++) {
			$vertex1 = $vertices[$i - 1]; 
			$vertex2 = $vertices[$i];

			if ($vertex1['y'] == $vertex2['y'] 
				AND $vertex1['y'] == $point['y']
				AND $point['x'] > min($vertex1['x'], $vertex2['x'])
				AND $point['x'] < max($vertex1['x'], $vertex2['x'])) { 
					# sur une ligne
					return 2;
			}

			if ($point['y'] > min($vertex1['y'], $vertex2['y'])
				AND $point['y'] <= max($vertex1['y'], $vertex2['y'])
				AND $point['x'] <= max($vertex1['x'], $vertex2['x'])
				AND $vertex1['y'] != $vertex2['y']) { 
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
	
	function pointOnVertex($point, $vertices) {
		foreach($vertices as $vertex) {
			if ($point == $vertex) { return TRUE; }
		}
	}

	function pointStringToCoordinates($pointString) {
		$coordinates = explode(',', $pointString);
		return array('x' => $coordinates[0], 'y' => $coordinates[1]);
	}
}