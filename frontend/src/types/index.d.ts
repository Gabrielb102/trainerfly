type LocalizedData = {
  baseAPIURL: string;
  baseWebURL: string;
  nonce: string;
  mapboxAPIKey: string;
};

declare global {
  const localized: LocalizedData;
}

export {}; // makes this a module, which helps it be recognized
// Gabriel, Look further into this
