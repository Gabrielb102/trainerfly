import { Feature, FeatureCollection, Geometry, GeoJsonProperties } from 'geojson';

// Extend the Properties interface to include Mapbox-specific fields
type MapboxProperties = GeoJsonProperties & {
  mapbox_id: string;
}

// Create a type for Mapbox features that includes the extended properties
export type MapboxFeature<G extends Geometry = Geometry> = Feature<G, MapboxProperties>;

// Create a type for Mapbox feature collections
export type MapboxFeatureCollection<G extends Geometry = Geometry> =
  FeatureCollection<G, MapboxProperties>;
