import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  /* config options here */
  allowedDevOrigins: [
    ...(process.env.NEXT_PUBLIC_APP_URL ? [new URL(process.env.NEXT_PUBLIC_APP_URL).hostname] : []),
    ...(process.env.ALLOWED_DEV_ORIGINS?.split(',').map(origin => origin.trim()).filter(Boolean) || []),
  ],
};

export default nextConfig;
