import {Vendor} from './vendor-types'

export interface Listing {
  id: number;
  title: string;
  url: string;
  latitude: number;
  longitude: number;
  location: string;
  image: string | null;
  price: number | null;
  category: string | null;
  vendor: Vendor;
}
