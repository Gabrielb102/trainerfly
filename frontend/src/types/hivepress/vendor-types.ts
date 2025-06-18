export interface Vendor {
  id: number;
  name: string;
  image: string | null;
  rating: number | null;
  rating_count: number | null;
  descriptors: Array<string>;
}
