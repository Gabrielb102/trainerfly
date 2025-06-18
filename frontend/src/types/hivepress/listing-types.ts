import {Vendor} from './vendor-types'

export interface Review {
  id: number;
  text: string;
  rating: number;
  created_date: string;
  author_name: string;
}

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
  description: string | null;
  reviews: Array<Review>;
  vendor: Vendor;
}
