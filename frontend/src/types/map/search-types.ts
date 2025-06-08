/* src/types/mapbox-searchbox.d.ts
   ------------------------------------------------------------------ */
export type FeatureType =
  | 'poi'       // point of interest
  | 'category'  // category suggestion
  | 'address'   // address-level result

/* ----------  Context sub-layers ----------------------------------- */

interface ContextBase {
  /** Canonical id (usually Mapbox Tileset feature id) */
  id: string;
  /** Human-readable name (e.g. "Nevada", “Clark County”) */
  name: string;
}

export interface CountryContext extends ContextBase {
  /** ISO-3166-1 alpha-2 (e.g. “US”) */
  country_code: string;
  /** ISO-3166-1 alpha-3 (e.g. “USA”) */
  country_code_alpha_3?: string;
}

export interface RegionContext extends ContextBase {
  /** ISO-3166-2 short code (e.g. “NV”) */
  region_code: string;
  /** Full ISO-3166-2 code (e.g. “US-NV”) */
  region_code_full: string;
}

export interface AddressContext extends ContextBase {
  /** “742” in “742 Evergreen Terrace” */
  address_number?: string;
  /** “Evergreen Terrace” in “742 Evergreen Terrace” */
  street_name?: string;
}

/** All possible context layers that may appear */
export interface SuggestionContext {
  country?: CountryContext;
  region?:   RegionContext;
  postcode?: ContextBase;
  district?: ContextBase;
  place?:    ContextBase;
  locality?: ContextBase;
  neighborhood?: ContextBase;
  address?:  AddressContext;
  street?:   ContextBase;
}

/* ----------  Top-level Suggestion object -------------------------- */

export interface SearchTypes {
  /* required ------------------------------------------------------- */
  name:         string;
  mapbox_id:    string;
  feature_type: FeatureType;
  place_formatted: string;
  context:      SuggestionContext;
  language:     string;

  /* optional ------------------------------------------------------- */
  name_preferred?:  string;
  address?:         string;
  full_address?:    string;

  /* UI / icon helpers */
  maki?: string;

  /* POI / brand taxonomy */
  poi_category?:     string[];
  poi_category_ids?: string[];
  brand?:            string[];
  brand_id?:         string[];

  /* External cross-refs & custom metadata */
  external_ids?: Record<string, string>;
  metadata?:     Record<string, unknown>;

  /* Distance / routing enhancements */
  distance?:        number; // meters
  eta?:             number; // minutes
  added_distance?:  number; // meters
  added_time?:      number; // minutes
}
