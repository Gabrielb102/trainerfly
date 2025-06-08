import { Feature, FeatureCollection, Geometry, GeoJsonProperties } from 'geojson';

type MapboxGeometry = Geometry & {
  type: string; // e.g., "Point", "LineString", "Polygon"
  coordinates: Array<number>; // Depending on the geometry type
}

// Extend the Properties interface to include Mapbox-specific fields
type MapboxProperties = GeoJsonProperties & {
  mapbox_id: string;
}

// Create a type for Mapbox features that includes the extended properties
export type MapboxFeature<G extends MapboxGeometry = MapboxGeometry> = Feature<G, MapboxProperties>;

// Create a type for Mapbox feature collections
export type MapboxFeatureCollection<G extends MapboxGeometry = MapboxGeometry> =
  FeatureCollection<G, MapboxProperties>;
